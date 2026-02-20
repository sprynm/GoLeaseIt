<?php
/**
 * CmsPagesHelper class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPagesHelper.html
 * @package      Cms.Plugin.Pages.View.Helper 
 * @since        Pyramid CMS v 1.0
 */
class CmsPagesHelper extends AppHelper {

/**
 * Returns true if page $id is published.
 *
 * @param integer $id
 * @return boolean
 */
	public function isPublished($id) {
		return ClassRegistry::init('Pages.Page')->isPublished($id);
	}

/**
 * A wrapper to Page::parentsArePublished() to see whether a page's parents are published.
 *
 * @param integer $id
 * @return boolean
 */
	public function parentsArePublished($id) {
		return ClassRegistry::init('Pages.Page')->parentsArePublished($id);
	}

}