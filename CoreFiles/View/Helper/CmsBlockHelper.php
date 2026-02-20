<?php
/**
 * CmsBlockHelper class
 *
 * Base ABSTRACT block helper class meant to be extended by other classes specific to block types.
 * Replaces block placeholders in view content with dynamic content.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsBlockHelper.html
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
abstract class CmsBlockHelper extends AppHelper {

/**
 * Returns true if the request is an admin request
 *
 * @return boolean
 */
	public function adminCheck() {
		return (isset($this->request->prefix) && $this->request->prefix == 'admin');
	}

/**
 * Searches $content for valid dynamic blocks with type = $type and rturns the matches.
 *
 * For each match, the preg_match_all array looks like this:
 * [0]: the whole string (which will be used in str_replace)
 * [1]: the opening <p> tag, if any (inserted by the WYSIWYG automatically) (unset before returning)
 * [2]: the content of the block identifier
 * [3]: the closing <p> tag, if any (unset before returning)
 *
 * @param string the content
 * @param string the type
 * @return array the matches
 */
	protected function _getMatches($content, $type) {
		preg_match_all('/(<(?:p|span|div) class="block[^"]*">)?(\{\{block type="' . $type . '" id="([\w]+)"\}\})(<\/(?:p|span|div)>)?/', $content, $matches, PREG_SET_ORDER);
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