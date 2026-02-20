<?php
/**
 * CmsCustomFieldsController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCustomFieldsController.html
 * @package		 Cms.Plugin.CustomFields.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsCustomFieldsController extends CustomFieldsAppController {

/**
 * Admin edit
 *
 * @return void
 */
	public function admin_edit($model = null, $foreignKey = null) {
		if (!$model) {
			$this->redirect(array('plugin' => 'administration', 'controller' => 'dashboard', 'action' => 'index'));
		}

		if (!empty($this->data)) {
			if ($this->CustomField->saveAll($this->data['CustomField'])) {
				$this->Notify->handleSuccessfulSave(array(
					'return' => $this->request->here,
					'continue' => '/' . $this->request->here
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		$conditions = array('CustomField.model' => $model);
		$conditions['CustomField.foreign_key'] = $foreignKey ? $foreignKey : null;

		$customFields = $this->CustomField->find('all', array(
			'conditions' => $conditions
		));
		$customFields = Set::combine($customFields, '{n}.CustomField.id', '{n}.CustomField');
		$dataCustomFields = Set::combine($this->data, 'CustomField.{n}.id', 'CustomField.{n}');
		$customFields = Set::merge($customFields, $dataCustomFields);
		$this->set(compact('customFields'));
		
		$this->set('alias', 'CustomField');

		$this->set(compact('foreignKey', 'model'));
	}
	
/**
 * Adds a new field via AJAX.
 *
 * @return void
 */	
	public function admin_new_field($model, $group = null) {
		if (!$this->request->is('ajax')) {
			exit();
		}

		if (!isset($this->request->data['alias'])) {
			echo "Missing alias.";
			exit();
		} 
		
		$alias = $this->request->data['alias'];
		
		if (!isset($this->request->data['count'])) {
			$count = 0;
		} else {
			$count = $this->request->data['count'];
		}

		$this->layout = null;
		$this->autoLayout = false;

		$newField = $this->CustomField->create();
		$this->set('field', $newField['CustomField']);

		$this->set(compact('count', 'alias', 'model', 'group'));
	}
			
}