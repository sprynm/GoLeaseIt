<?php
App::uses('Setting', 'Settings.Model');

/**
 * CmsSettingsComponent class
 *
 * Reads records from the settings table and writes them to Configure. Settings are
 * available as: Configure::read('Package(.subpackage).key')
 *
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsSettingsComponent.html
 * @package		 Cms.Plugin.Settings.Controller.Component
 * @since		 Pyramid CMS v 1.0
 */
class CmsSettingsComponent extends Component {

/**
 * Cache configuration to use
 */
	public $cacheName = 'settings';

/**
 * Initializer - this is where the work is done.
 *
 * @param object
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_loadSettings();
	}

/**
 * Loads settings - called by initialize()
 *
 * @return void
 */
	protected function _loadSettings() {
		$settings = Cache::read('loadSettings', $this->cacheName);
		if ($settings === false) {
			$settings = ClassRegistry::init('Settings.Setting')->findForLoad();
			Cache::write('loadSettings', $settings, $this->cacheName);
		}
		
		foreach ($settings as $key => $val) {
			Configure::write('Settings.' . $key, $val);
		}
	}

}