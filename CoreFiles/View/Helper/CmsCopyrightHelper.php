<?php
/**
 * CmsCopyrightHelper class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsCopyrightHelper.html
 * @package      Cms.View.Helper  
 * @since        Pyramid CMS v 1.0
 */
class CmsCopyrightHelper extends AppHelper {

/**
 * Displays copyright year range.
 *
 * @return  string
 */
	public function year() {        
		$year = Configure::read('Settings.Site.copyright_start_year');
		$current = date("Y");
		if ($year == $current) {
			return $year;
		}
		
		return "{$year} - {$current}";
	}

/**
 * Displays copyright name.
 *
 * @return  string
 */
	public function name() {        
		return Configure::read('Settings.Site.copyright_name');
	}

}
