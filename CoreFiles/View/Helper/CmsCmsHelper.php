<?php
/**
 * CmsCmsHelper class
 *
 * Contains some utility methods for views that don't fit into any other helper, or are of
 * extremely general use.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCmsHelper.html
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsCmsHelper extends AppHelper {

/**
 * Returns the current version as set in the Configure settings.
 *
 * @return string
 */
	public function version() {
		return Configure::read('Cms.version');
	}

}