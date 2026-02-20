<?php
App::uses('CakeTime', 'Utility');

/**
 * CmsPagesController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPagesController.html
 * @package		 Cms.Plugin.Pages.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsPagesController extends PagesAppController {

/** 
 * Attached components
 */
	public $components = array(
		'TreeSort.TreeSort'
	);

/**
 * Helpers
 */
	public $helpers = array(
		'Pages.Pages'
	);

/**
 * Delete - makes sure that a protected page isn't deleted.
 *
 * @param id
 * @return void
 */
	public function admin_delete($id = null) {
		$page = $this->Page->findById($id);
		if (!$page || (!AccessControl::inGroup('Super Administrator') && ($page['Page']['protected'] || $page['Page']['action_map']))) {
			$this->Notify->error("That page cannot be deleted.");
			$this->redirect($this->referer());
		}
		parent::admin_delete($id);
	}

/**
 * Admin add function - for adding a new page.
 *
 * @return void
 */
	public function admin_add() {
		$this->set('pages', $this->Page->generateTreeList(array('action_map' => '')));
		$this->set('layouts', $this->Admin->getLayoutFiles(AccessControl::inGroup('Super Administrator')));
		parent::admin_add();
	}

/**
 * Admin edit function - for editing an existing page.
 *
 * @param	integer $id Optional
 * @return	void
 */
	public function admin_edit($id = null) {
		if (!$id) {
			$this->redirect(array('action' => 'add'));
		}
		
		//set groups for group checkboxes
		$this->set('groups', $this->Page->getGroupsForInputs());
		
		$this->set('pages', $this->Page->generateTreeList(array('action_map' => '')));
		$this->set('layouts', $this->Admin->getLayoutFiles(AccessControl::inGroup('Super Administrator')));

		// Autosave - attempt to save, then return the page ID.
		if ($this->request->is('ajax') && !empty($this->request->data)) {
			if ($this->Page->saveAll($this->request->data, array('deep' => true))) {
				$this->layout = null;
				$this->autoLayout = false;
				echo json_encode(array("recordId" => $this->Page->id, "timeSaved" => CakeTime::niceShort()));
			}
			exit();
		} else if (!empty($this->request->data)) {
			if ($this->Page->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave();
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->Page->find('first', array(
				'conditions' => array(
					'Page.id' => $id
				), 'contain' => array('Image', 'PageField')
			));
		}
		
		$path = "";
		if ($this->request->data && $id) {
			$path = str_replace($this->request->data['Page']['slug'], '', $this->request->data['Page']['path']);
		}
		//
		if ($id) {
			//
			$modelEmailForm	= ClassRegistry::init('EmailForms.EmailForm');
			//
			$email_form	= $modelEmailForm->findByRedirectPageId($id);
			//
			if ($email_form) {
				//
				$email_form_array	= $modelEmailForm->findForDisplay($email_form['EmailForm']['id']);
				//
				if ($email_form_array) {
					//
					$emailFormMergeGroups	= Hash::extract($email_form_array, 'EmailFormGroup.{n}');
					// 
					if ($emailFormMergeGroups) {
						//
						foreach ($emailFormMergeGroups AS $array) {
							//
							if (isset($array['EmailFormField'])) {
								//
								$names	= Hash::extract($array['EmailFormField'], '{n}.name');
								//
								foreach ($names AS $name) {
									//
									$emailFormMergeFields[]	= '[' . $name . ']';
								}
							}
						}
					}
					//
					$this->set(array('emailFormMergeFields'), array($emailFormMergeFields));
				}
			}
		}
		//
		$this->set(compact('path'));
	}

/**
 * Temporary function to fix page path and lft/rght values.
 *
 * @return void
 */
	public function admin_fix() {
		if (!AccessControl::inGroup('Super Administrator')) {
			echo "Sorry";
			die();
		}
		$this->Page->updateAll(
			array('Page.lft' => null, 'Page.rght' => null),
			array('Page.plugin !=' => '')
		);
		$this->Page->recover('parent');
		echo "Done";
		die();
	}

/**
 * Index
 *
 * @return	void
 */
	public function admin_index() {
		if (!Cms::minVersion('1.0.4')) {
			return $this->_adminIndexOld();
		}

		$pages = $this->Page->find('all', array(
			'conditions' => array('OR' => array(
				'Page.plugin <=' => '',
				'Page.plugin' => null
			)),
			'order' => 'Page.lft ASC'
		));

		$superPages = $this->Page->find('all', array(
			'conditions' => array(
				'Page.plugin >' => ''
			),
			'order' => 'Page.title ASC'
		));

		$this->set(compact('pages', 'superPages'));
	}

/**
 * Old admin index prior to 1.0.4 and page tree reorganization.
 *
 * @return void
 */
	protected function _adminIndexOld() {
		$pages = $this->Page->find('all', array(
			'order' => 'Page.lft ASC'
		));

		$this->set(compact('pages'));
	}
/**
 * Previews a page record - i.e. uses requestAction() to display the page associated with $id.
 *
 * @param integer id - revision id
 * @return void
 */
	public function admin_preview($id) {
		//
		$page = $this->Page->findForView($id);
		//
		if (!$page) {
			//
			$this->Notify->error("Page not found or not available for preview.");
			//
			$this->redirect($this->referer());
		}

		// Replace page data with preview data from URL vars
		foreach ($this->params['url'] as $key => $val) {
			$page['Page'][$key] = $val;
		}
		
		$this->layout = null;
		$this->autoLayout = false;
		$this->autoRender = false;

		$action = $this->requestAction(
			$this->Page->link($page),
			array(
				'return', 
				'bare' => false, 
				'autoRender' => true,
				'data' => array(
					'preview' => true, 
					'page' => $page
				)
			)
		);

		return $action;
	}

/**
 * Calls the TreeSortComponent's reorder function.
 *
 * @return void
 */
	public function admin_reorder() {
		return $this->TreeSort->reorder($this, 'after');
	}

/**
 * Allows editing the default fields and their default values for pages
 * CustomFields with a foreign key of 0 are considered defaults for their models
 */
	public function admin_default_fields() {
		$CustomField = ClassRegistry::init('CustomFields.CustomField');
		if (!empty($this->request->data['PageField'])) {
			$fields = array();
			
			foreach ($this->request->data['PageField'] as $field){
				$field['foreign_key'] = 0;
				$fields[]['CustomField'] = $field;
			}
			
			//don't proceed to overwrite the attempted save if the save failed for some reason
			if (!$CustomField->saveAll($fields)){
				return;
			}
		} 
		$fields = $CustomField->find('all', array(
			'conditions'=> array(
				'foreign_key = 0'
				, 'model' => $this->{$this->modelClass}->name
			)
		));
		
		$this->request->data['PageField'] = Hash::extract($fields, '{n}.CustomField');
	}

/**
 * Basic view function to display CMS pages.
 *
 * @throws NotFoundException
 * @return void
 */
	public function view($path = null) {
		// Check if viewing preview
		if (!empty($this->params['data']['preview'])) {
			// If so, use page data passed in
			$page = $this->params['data']['page'];
		} else {
			if (is_numeric($path)){
				$link = $this->Page->link($path);
				if (!empty($link)) {
					$this->redirect($link);
					return;
				}
			}
			//
			$page = $this->Page->findForView($this->request->path);
			//
			if (!$page) {
				//
				throw new NotFoundException('Page Not Found');
			} else {
				//
				$path		= isset($page['Page']['path'])
						? $page['Page']['path']
						: '';
				// add a route for this page at the top of the list to help with generating pagination
				$connect	= $path
						? '/' . $path . '/*'
						: '/';
				//
				Router::connect(
					$connect
					, array(
						'plugin' => 'pages'
						, 'controller' => 'pages'
						, 'action' => 'view'
						, 'path' => $path
					)
				);
				//
				Router::promote();
			}
			
		}
		//
		$banner	= isset($page['Image']) ? $page['Image']: array();
		//
		if ($this->Session->read('formSuccessFormData')) {
			//
			$formSuccessFormData	= $this->Session->read('formSuccessFormData');
			//
			$this->set(array('formSuccessFormData'), array($formSuccessFormData));
			//
			$this->Session->delete('formSuccessFormData');
		}
		//
		if ($this->Session->check('formSuccessRequestData')) {
			//
			$formSuccessRequestData	= $this->Session->read('formSuccessRequestData');
			//
			$this->set(array('formSuccessRequestData'), array($formSuccessRequestData));
			// 
			$page['Page']['content']	= $this->Page->strReplace(
								$formSuccessRequestData['EmailFormSubmission']
								, $page['Page']['content']
							);
			//
			$this->Session->delete('formSuccessRequestData');
		}
		// Possible "refresh download" for downloading a file via Javascript set from some other place in the CMS.
		if ($this->Session->read('refreshDownload')) {
			//
			$this->set('refreshDownload', $this->Session->read('refreshDownload'));
			//
			$this->Session->delete('refreshDownload');
		}
		//
		$this->set(array('page', 'banner'), array($page, array('Image' => $banner)));
		//
		$grantedAccess = array();
		//
		if ($this->Session->read('Page.granted_access')) {
			//
			$grantedAccess = explode(',', $this->Session->read('Page.granted_access'));
		}
		//
		if ($page['Page']['password'] && !AccessControl::inGroup('Super Administrator') && !in_array((string)$page['Page']['id'], $grantedAccess)) {
			//
			if (!empty($this->request->data)) {
				//
				if ($this->request->data['Page']['password'] == $page['Page']['password']) {
					//
					$grantedAccess[] = $page['Page']['id'];
					//
					$this->Session->write('Page.granted_access', implode(',', $grantedAccess));
					//
					$this->redirect(Router::url('/' . $this->request->path, true));
					//
					die();
				}
				//
				$this->Notify->error('The password you entered was incorrect. Please try again.');
			}
			//
			$this->render('access');
		}
	}
}