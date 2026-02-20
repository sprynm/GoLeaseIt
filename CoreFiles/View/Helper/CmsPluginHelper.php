<?php
/**
 * CmsPluginHelper class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPluginHelper.html
 * @package      Cms.View.Helper
 * @since        Pyramid CMS v 2.0
 */
class CmsPluginHelper extends AppHelper {
	
	private $_plugins = array();
	
/**
 * Returns the plugin name for the first plugin a model name is found to be included, returns false if the model wasn't found
 */
	
	public function getModelsPlugin($model) {
		$plugins = $this->_initPluginContents();
		foreach ((array)$plugins as $pluginName => $contents) {
			if (in_array($model, $contents['models'])){
				return $pluginName;
			}
		}
		return false;
	}
	
	public function _initPluginContents() {
		if (!empty($this->_plugins)){
			return $this->_plugins;
		}
		
		//$plugins = App::objects();
		//debug($plugins);
		
		$plugins = ClassRegistry::init('InstalledPlugin')->find('all', array(
				'conditions' => array( 'active' => true )
		));
		
		
		foreach ( $plugins as $plugin ) {
			$this->_plugins[$plugin['InstalledPlugin']['name']] = array(
				'models' => App::objects($plugin['InstalledPlugin']['name'] . ".Model")
				, 'controllers' => App::objects($plugin['InstalledPlugin']['name'] . ".Controller")
				, 'components' => App::objects($plugin['InstalledPlugin']['name'] . ".Component")
				, 'behaviors' => App::objects($plugin['InstalledPlugin']['name'] . ".Behavior")
			);
		}
		
		return $this->_plugins;
	}
	
}
?>