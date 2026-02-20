<?php
App::uses('BlockHelper', 'View/Helper');

/**
 * CmsSettingsBlockHelper class
 *
 * Replaces content block placeholders in view content with dynamic content.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsSettingsBlockHelper.html
 * @package		 Cms.Plugin.Settings.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsSettingsBlockHelper extends BlockHelper {

/**
 * Searches $content for settings block placeholders and replaces with the proper data.
 *
 * @see Helper::afterRenderFile
 * @return string the modified content
 */
	public function afterRenderFile($viewFile, $content) {
		$matches = $this->_getMatches($content, 'Setting');
		if (empty($matches)) {
			return $content;
		}
    
		foreach ($matches as $key => $val) {
      $output = Configure::read('Settings.'.($val[0]));
			$content = str_replace($val[1], $output, $content);
		}
		
		return $content;
	}
	
  protected function _getMatches($content, $type) {
    preg_match_all('/(<(?:p|span|div) class="block[^"]*">)?(\{\{block type="' . $type . '" id="([^"]*)"\}\})(<\/(?:p|span|div)>)?/', $content, $matches, PREG_SET_ORDER);
		foreach ($matches as $key => $val) {
      if (!empty($matches[$key][1]) && !empty($matches[$key][4])) {
        $matches[$key] = [$matches[$key][3], $matches[$key][0]];
      } else {
        $matches[$key] = [$matches[$key][3], $matches[$key][2]];
      }
		}
    return $matches;
  }
	
}