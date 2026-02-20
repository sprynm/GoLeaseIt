<?php
/**
 * CmsPrototypeAppController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeAppController.html
 * @package      Cms.Plugin.Prototype.Controller 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeAppController extends AppController {

/**
 * Components
 */
	public $components = array(
		'Prototype.PrototypeBreadcrumbs'
	);

/**
 * Sets up the $prototypeInstance controller and view variable based on the current
 * prototype instance. Adding in the $banner variable.
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$instance = ClassRegistry::init('Prototype.PrototypeInstance')->find('first', array(
			'conditions' => array(
				'slug' => $this->request->instance
			)
			, 'contain'	=> array(
				'Image'
				,
			)
			, 'cache' => true
			,
		));
		//
		$this->instance = $instance;
		//
		$this->set('instance', $this->instance);
		//
		$this->set('_instance', $this->instance);
	}

/**
 * Sets the layout to match the chosen instance layout if necessary.
 *
 * @return  mixed
 */
	public function beforeRender() {
		if (!$this->Admin->isAdminAction() && isset($this->viewVars['instance']['PrototypeInstance']['layout'])) {
			$this->layout = $this->viewVars['instance']['PrototypeInstance']['layout'];
		}
		
		parent::beforeRender();
	}
}
