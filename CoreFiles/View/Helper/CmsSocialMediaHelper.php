<?php 
/**
 * Social Media Helper.
 *
 * Generate buttons for sharing pages on social media. 
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2014, Shannon Graham
 *
 * Licensed under The GPL.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsSocialMediaHelper.html
 * @package		 Cms.Plugin.Helper 
 * @since		 Pyramid CMS v 1.0
 */
class CmsSocialMediaHelper extends AppHelper {

/**
 * Helpers
 */
	public $helpers = array(
		'Html' => array('className' => 'AppHtml')
	);

/**
 * Takes the name of the current plugin and checks the plugin's settings to see which social media buttons are wanted. 
 *
 * @param string $plugin
 * @return string The HTML
 */
	public function socialMediaButtons($plugin, $title = null, $url = null) {
		
		$camelizedPlugin = Inflector::camelize($plugin);
		
		$buttons = Configure::read('Settings.' . $camelizedPlugin . '.share');
				
		$a2aUrl = '';
		$a2aTitle = '';
		
		if($url) {
			$a2aUrl = "data-a2a-url='$url'";
		}
		
		if($title) {
			$a2aTitle = "data-a2a-title='$title'";
		}
		
		if (!is_array($buttons)) {
			return '';	
		}

		$printShareButtons = false;
		
		$output = "<!-- AddToAny BEGIN -->
		<div class='share'><strong>Share: </strong><div class='a2a_kit a2a_kit_size_32 a2a_default_style' $a2aUrl $a2aTitle>";
		
		if(isset($buttons['all']) && $buttons['all'] == true) {
			$output .= '<a class="a2a_dd" href="https://www.addtoany.com/share_save"></a>';
			unset($buttons['all']);
			$printShareButtons = true;
		}
		
		foreach ($buttons as $k => $v) {
			if($v == true) {
				$output .= '<a class="a2a_button_' . $k . '"></a>';
				$printShareButtons = true;
			}
		}

		if($printShareButtons === true) {
			return $output . '<script type="text/javascript" src="//static.addtoany.com/menu/page.js"></script>
			<!-- AddToAny END --></div></div>';
		}		
	}
}