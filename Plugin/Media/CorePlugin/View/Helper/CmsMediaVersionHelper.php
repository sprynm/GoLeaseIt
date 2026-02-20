<?php
/**
 * CmsMediaVersionHelper class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsMediaVersionHelper.html
 * @package		 Cms.Plugin.Media.View.Helper 
 * @since		 Pyramid CMS v 1.0
 */
class CmsMediaVersionHelper extends AppHelper {

/**
 * Returns a list of crop types
 *
 * @return array
 */	
	public function cropTypeList() {
		return Set::combine(ClassRegistry::init('Media.AttachmentVersion')->cropTypes, '{n}.name', '{n}.display_name');
	}

/**
 * Returns a list of image conversion types
 *
 * @return array
 */
	public function imageConvertList() {
		$list = ClassRegistry::init('Media.AttachmentVersion')->imageConvertTypes;
		return array_combine($list, $list);
	}
	
}
