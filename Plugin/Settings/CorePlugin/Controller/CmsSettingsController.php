<?php
/**
 * CmsSettingsController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsSettingsController.html
 * @package      Cms.Plugin.Settings.Controller  
 * @since        Pyramid CMS v 1.0
 */
class CmsSettingsController extends SettingsAppController {

/**
 * Settings key edit function.
 *
 * @return void
 */
	public function admin_key_edit($id = null) {
		if (!empty($this->request->data)) {
			if (isset($this->{$this->modelClass}->validateAdmin)) {
				$this->{$this->modelClass}->setValidation('admin');
			}

			if ($this->{$this->modelClass}->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave(array(
					'return' => array('action' => 'key_index')
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->{$this->modelClass}->find('edit', array(
				'conditions' => array($this->modelClass . '.id' => $id)
				, 'contain' => array('Image', 'Document',)
			));
		}
	}

/**
 * The main action - handles displaying and editing the various site settings.
 *
 * @return void
 */
	public function admin_index($plugin = null) {
	//
		if (!empty($this->request->data)) {
		//
			$this->{$this->modelClass}->saveImage($this->request->data);
		//
			if ($this->{$this->modelClass}->saveAll($this->request->data['Setting'], array('deep' => true))) {
				$this->Notify->handleSuccessfulSave(array(
					'return' => array('action' => 'index')
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}
	//
		$settings = $this->Setting->findForEdit($plugin);
	//
		$this->set(compact('settings'));
	
	}

/** 
 * Index for adding/editing settings, only for super admins.
 *
 * @return void
 */
	public function admin_key_index() {
		//
		$name = lcfirst($this->name);
		//
		if (method_exists($this->{$this->modelClass}, 'containedModels')) {
			//
			$contain = $this->{$this->modelClass}->containedModels();
		//
		} else {
			//
			$contain = null;
		}
		//
		$this->Setting->order = array('Setting.rank ASC', 'Setting.key ASC');
		//
		$options		= array();
		//
		if ($this->request->query('change_order')) {
			//
			$options['limit']	= 999;
		}
		//
		$options['contain']	= $contain;
		//
		$this->AutoPaginate->setPaginate($options);
		//
		${$name} = $this->paginate();
		//
		$this->set(compact($name));
		//
		//parent::admin_index();
	}

}