<?php
/**
 * CmsMetaKeysController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsMetaKeysController.html
 * @package      Cms.Plugin.Metas.Controller 
 * @since        Pyramid CMS v 1.0
 */
class CmsMetaKeysController extends AppController {

/**
 * Small array of input types for metas.
 */
	public $inputTypes = array(
		'text' => 'Text',
		'textarea' => 'Textarea'
	);
	
/**
 * Admin edit
 *
 * @return void
 */
	public function admin_index() {
		$this->set('types', $this->inputTypes);
		
		if (empty($this->request->data)) {
			$this->request->data = $this->MetaKey->find('all');
			return;
		}

		if ($this->MetaKey->saveAll($this->request->data['MetaKey'])) {
			$this->Notify->handleSuccessfulSave(array(
				'return' => $this->request->here
			));
		} else {
			$this->Notify->handleFailedSave();
		}
	}

/**
 * Adds a new meta key via AJAX.
 *
 * @return void
 */
	public function admin_new() {
		if (!$this->request->is('ajax')) {
			exit();
		}

		if (!isset($this->request->data['count'])) {
			$count = 0;
		} else {
			$count = $this->request->data['count'];
		}

		$this->layout = null;
		$this->autoLayout = false;

		$new = $this->MetaKey->create();
		$this->set('item', $new['MetaKey']);
		$this->set('types', $this->inputTypes);
		$this->set(compact('count'));		
	}
}
