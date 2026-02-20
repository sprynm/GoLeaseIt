<?php
/**
 * CmsNavigationMenusController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsNavigationMenusController.html
 * @package		 Cms.Plugin.Navigation.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsNavigationMenusController extends NavigationAppController {

/**
 * Admin record edit function
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	public function admin_edit($id = null) {
		if ($id) {
			$navigationMenuItems = $this->NavigationMenu->NavigationMenuItem->find('all', array(
				'order' => 'NavigationMenuItem.lft ASC',
				'conditions' => array(
					'navigation_menu_id' => $id
				)
			));
			$this->set(compact('navigationMenuItems'));
		}
		
		parent::admin_edit($id);
	}
	
}
