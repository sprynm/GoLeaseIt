<?php
/**
 * CmsAdminNavigationItemsController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsAdminNavigationItemsController.html
 * @package      Cms.Plugin.Navigation.Controller 
 * @since        Pyramid CMS v 1.0
 */
class CmsAdminNavigationItemsController extends NavigationAppController {

/**
 * Admin edit function
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	public function admin_edit($id = null) {
		$this->set('groups', $this->AdminNavigationItem->Group->find('list'));
		parent::admin_edit($id);
	}

}
