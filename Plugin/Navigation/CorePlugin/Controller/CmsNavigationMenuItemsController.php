<?php
/**
 * CmsNavigationMenuItemsController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsNavigationMenuItemsController.html
 * @package		 Cms.Plugin.Navigation.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsNavigationMenuItemsController extends NavigationAppController {
	
/**
 * Attached components
 */
	public $components = array(
		'TreeSort.TreeSort'
	);

/**
 * Admin edit function.
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	public function admin_edit($menuId, $id = null) {
		// Plugin aliases are loaded so that plugin names can be looked up for insertion in the DB
		// rather than aliases.
		$aliases = CmsPlugin::pluginAliases();
		$navigationMenu = $this->NavigationMenuItem->NavigationMenu->findById($menuId);

		$navigationMenuItems = $this->NavigationMenuItem->generateTreeList(array(
			'navigation_menu_id' => $menuId
		));
		
		$event = CmsEventManager::dispatchEvent('Controller.NavigationMenuItems.navItems', $this);
		$targetUrls = $event->result;

		$pages = array('Pages' => $targetUrls['Pages']);
		unset($targetUrls['pages']);		
		ksort($targetUrls);
		$targetUrls = array_merge($pages, $targetUrls);
		$this->set(compact('navigationMenu', 'navigationMenuItems', 'targetUrls', 'aliases'));
		
		if (!empty($this->request->data)) {
			if ($this->NavigationMenuItem->saveAll($this->request->data)) {
				$this->Notify->handleSuccessfulSave(array(
					'return' => array(
						'plugin' => 'navigation', 
						'controller' => 'navigation_menus', 
						'action' => 'edit', 
						$menuId
					),
					'continue' => array(
						$menuId,
						$this->{$this->modelClass}->id
					)
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		} else if ($id && empty($this->request->data)) {
			$this->request->data = $this->NavigationMenuItem->find('edit', array(
				'conditions' => array(
					'NavigationMenuItem.id' => $id
				)
			));
		}
	}
	
/**
 * Calls the TreeSortComponent's reorder function.
 *
 * @return	boolean
 */
	public function admin_reorder() {
		return $this->TreeSort->reorder($this);
	}

}
