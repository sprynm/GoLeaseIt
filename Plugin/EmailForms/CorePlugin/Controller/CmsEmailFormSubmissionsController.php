<?php
App::uses('CakeTime', 'Utility');

/**
 * CmsEmailFormSubmissionsController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormSubmissionsController.html
 * @package		 Cms.Plugin.EmailForms.Controller  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormSubmissionsController extends AppController {

/**
 * Helpers
 */
	public $helpers = array(
		'Csv'
	);

/**
 * Admin delete
 *
 * @return void
 */
	public function admin_delete($id) {
	//
		if(!$id) {
			throw new NotFoundException(__('Submission not found.'));
		}
	//
		$this->EmailFormSubmission->updateAll(
			array('EmailFormSubmission.deleted_date' => 'NOW()', 'EmailFormSubmission.deleted' => 1),
			array('EmailFormSubmission.id' => $id)
		);
	//
		$this->Notify->success('The Submission has been deleted.');
	//
		$this->redirect($this->referer());

	}

/**
 * Read-only admin edit.
 *
 * @param integer submission id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$id) {
			$this->redirect(array('action' => 'index'));
		}
		
		$this->request->data = $this->{$this->modelClass}->find('edit', array(
			'conditions' => array($this->modelClass . '.id' => $id),
		));
		
		$this->request->data['EmailForm'] = ClassRegistry::init('EmailForm')->find('edit', array(
			'conditions' => array('EmailForm.id' => $this->request->data['EmailFormSubmission']['email_form_id']),
		));
		
		if (!$this->request->data) {
			$this->Notify->error("Submission not found.");
			$this->redirect(array('controller' => 'email_forms', 'action' => 'index'));
		}
	}
	
/**
 * Exports submissions to a CSV
 *
 * @param integer email form id
 * @return void
 */
	public function admin_export($formId) {
		$emailForm = $this->EmailFormSubmission->EmailForm->findById($formId);
		if (!$emailForm) {
			$this->Notify->error('Form not found.');
			$this->redirect($this->referer());
		}
		
		$this->layout = null;
		$this->autoLayout = false;

		$submissions = $this->EmailFormSubmission->findAllByEmailFormId($formId);
		$filename = Inflector::underscore(strtolower($emailForm['EmailForm']['name']));
		$generated = CakeTime::nice();
		$this->set(compact('emailForm', 'submissions', 'filename', 'generated'));
	}

/**
 * View submissions for an email form.
 * Also added multiple delete capabilities.
 *
 * @param integer email form id
 * @return void
 */
	public function admin_index($formId = null) {
		if (!$formId) {
			return $this->_masterIndex();
		}

		$emailForm = $this->EmailFormSubmission->EmailForm->findById($formId);
		
		
		
		if (!$emailForm) {
			$this->Notify->error('Form not found.');
			$this->redirect($this->referer());
		}
		
		$this->set(compact('emailForm'));

		if ($this->request->is('post')) {
			foreach($this->request->data['EmailFormSubmission'] AS $id) {
				if($id > 0 ) {
					$this->EmailFormSubmission->delete($id);
				}
			}
		}
		
 		$this->AutoPaginate->setPaginate(array(
			'conditions' => array('EmailFormSubmission.email_form_id' => $formId),
			'order' => array('EmailFormSubmission.created' => 'desc')
		)); 
 		$emailFormSubmissions = $this->paginate();
		$this->set(compact('emailFormSubmissions'));
	}

/**
 * Index view that displays email forms with links to submissions.
 *
 * @return void
 */
	protected function _masterIndex() {
		$emailForms = $this->EmailFormSubmission->EmailForm->find('all');
		$this->set(compact('emailForms'));
		$this->render('_admin_master_index');
	}
}
