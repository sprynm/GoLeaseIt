<?php
/**
 * CmsEmailFormsController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormsController.html
 * @package		 Cms.Plugin.EmailForms.Controller  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormsController extends EmailFormsAppController {

/**
 * Displayed upon successful form submission.
 * 
 * @throws NotFoundException
 * @return void
 */
	public function success($form = null) {
		$emailForm = $this->EmailForm->findForDisplay($form);

		if (!$emailForm) {
			throw new NotFoundException('CmsEmailFormsController::success: Form not found.');
		}

		$this->PageSettings->setTitle($emailForm['EmailForm']['success_title']);		
		$this->set(compact('emailForm'));
	}

/**
 * Admin edit function
 * 
 * @var integer id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('pages', ClassRegistry::init('Pages.Page')->generateTreeList(array('action_map' => '')));

		parent::admin_edit($id);

		// If, after all of the data has been initialized, there are no form groups, add a default.
		if (empty($this->request->data) || !isset($this->request->data['EmailFormGroup']) || empty($this->request->data['EmailFormGroup'])) {
			$this->request->data['EmailFormGroup'] = array($this->EmailForm->EmailFormGroup->addDefault(true));
		}
	}

/**
 * Admin index function
 *
 * @return void
 */
	public function admin_index() {
		$conditions = array();
		if (!AccessControl::inGroup('Super Administrator')) {
			$conditions['EmailForm.super_admin'] = 0;
		}
		$this->AutoPaginate->setPaginate(array(
			'conditions' => $conditions
		));
		$emailForms = $this->paginate();
		$this->set(compact('emailForms'));
	}

/**
 * Adds a new group via AJAX.
 *
 * @return void
 */
	public function admin_new_group() {
		if (!$this->request->is('ajax')) {
			exit();
		}
		
		if (!isset($this->request->data['groupNum'])) {
			$groupNum = 0;
		} else {
			$groupNum = $this->request->data['groupNum'];
		}
		
		if (!isset($this->request->data['fieldCount'])) {
			$fieldCount = 0;
		} else {
			$fieldCount = $this->request->data['fieldCount'];
		}
		
		$this->layout = null;
		$this->autoLayout = false;
		
		$this->set('group', $this->EmailForm->EmailFormGroup->addDefault());
		$this->set(compact('fieldCount', 'groupNum'));
	}

/**
 * For reordering form groups and fields This uses a custom save function instead of the TreeSort plugin's
 * stuff because (1) the format of the data is different, and (2) groups and fields do not use the left/right
 * tree traversal architecture.
 *
 * @param integer email form ID - for reloading the groups and fields
 * @return void
 */
	public function admin_reorder($emailFormId) {
		if (!$this->request->is('ajax') || empty($this->request->data)) {
			die();
		}

		$saveData = array('EmailFormGroup' => array());
		foreach ($this->request->data as $key => $val) {
			if ($val['parent_id'] < 1) { // No parent - it's a group
				$rank = count($saveData['EmailFormGroup']);
				$saveData['EmailFormGroup'][$val['id']] = array(
					'id' => $val['id'],
					'rank' => $rank,
					'EmailFormField' => array()
				);
			} else { // Parent - it's a field
				$rank = count($saveData['EmailFormGroup'][$val['parent_id']]['EmailFormField']);
				$saveData['EmailFormGroup'][$val['parent_id']]['EmailFormField'][] = array(
					'id' => $val['id'],
					'rank' => $rank,
					'foreign_key' => $val['parent_id']
				);
			}
		}

		$this->EmailForm->EmailFormGroup->saveAll($saveData['EmailFormGroup'], array('deep' => true, 'validate' => false));
		$emailForm = $this->EmailForm->find('edit', array('conditions' => array('EmailForm.id' => $emailFormId)));
		$this->set('groups', $emailForm['EmailFormGroup']);

		$this->layout = 'ajax';
	}

}