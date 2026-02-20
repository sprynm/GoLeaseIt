<?php
App::uses('UrlCacheAppHelper', 'UrlCache.View/Helper');

/**
 * CMS core-level helper
 *
 * This class provides the link between the app's AppHelper and the Cake core Helper.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsAppHelper.html
 * @package      Cms.View.Helper  
 * @since        Pyramid CMS v 1.0
 */
class CmsAppHelper extends UrlCacheAppHelper {

   /***
	* Checks for a scheme (http https ftp) and adds http if there isn't one. 
	**/
	public function addScheme($url, $scheme = 'http://') {
		if($url == "") { return ""; }
		return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
	}


	public function emailize($text) {
		
		 if (!is_string($text)) {
			return;
		}
		
		$explosion = explode(' ', $text);
		
		foreach($explosion as $k => $v) {
		//	if(preg_match('/\[[\w\.]+@[\w]+\.[\w]+\]/', $v)) {	
			
			if(preg_match('/(?<!mailto:)\[[\w\.]+@([\w]+\.[\w]+)+\]/', $v)) {	
				#debug($v);
				$v = str_replace('[', '', $v);
				$v = str_replace(']', '', $v);
				$explosion[$k] = str_replace('@', '<span class="obfuscate" style="display:none;">obfu!!scat?ion for the \'confusion of \'spambots</span>@<span class="obfuscate" style="display:none;">null</span>', $v);
				
			}
		}
		$text = implode(" ", $explosion); 
		return $text;
	}
	
	public function listAliases() {
		$aliases = ClassRegistry::init('PluginTools.InstalledPlugin')->find('list', array('fields' => array('alias')));
		$names = ClassRegistry::init('PluginTools.InstalledPlugin')->find('list', array('fields' => array('name')));
		
		return array_merge($aliases, $names);

	}

}