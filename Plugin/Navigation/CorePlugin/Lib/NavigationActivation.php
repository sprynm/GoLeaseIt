<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * NavigationActivation class
 *
 * Performs tasks related to Navigation plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classNavigationActivation.html
 * @package      Cms.Plugin.Navigation.Lib  
 * @since        Pyramid CMS v 1.0
 */
class NavigationActivation extends PluginActivation {

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Navigation.NavigationMenu' => array(
			'NavigationMenu' => array( 
				'name' => 'Main Navigation', 
				'sitemap' => 1, 
				'PublishingInformation' => array(
					'model' => 'NavigationMenu',
					'published' => 1
				) 
			)
			, 'NavigationMenuItem' => array(
				array(
					'name' => 'Home', 
					'foreign_model' => 'Page', 
					'foreign_key' => 1,
					'PublishingInformation' => array(
						'model' => 'NavigationMenuItem',
						'published' => 1
					) 
				)
				, array(
					'name' => 'Contact', 
					'foreign_model' => 'Page', 
					'foreign_key' => 2,
					'PublishingInformation' => array(
						'model' => 'NavigationMenuItem',
						'published' => 1
					) 
				)
			)
		)
	);

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'navigation_menus', 'action' => 'admin_edit', 'description' => 'Add and edit navigation menus')
		),
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'navigation_menus', 'action' => 'admin_delete', 'description' => 'Delete navigation menus')
		),
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'navigation_menu_items', 'action' => 'admin_edit', 'description' => 'Add and edit navigation menu items')
		),
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'navigation_menu_items', 'action' => 'admin_reorder', 'description' => 'Reorder navigation menu items')
			, 'Group' => array('Group' => 2)
		),
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'navigation_menu_items', 'action' => 'admin_delete', 'description' => 'Delete navigation menu items')
		),
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'admin_navigation_items', 'action' => 'admin_edit', 'description' => 'Add and edit custom admin nav links')
		),
		array(
			'Permission' => array('plugin' => 'navigation', 'controller' => 'admin_navigation_items', 'action' => 'admin_delete', 'description' => 'Delete custom admin nav links')
		)

	);

/**
 * after schema update callback
 *
 * @see CmsPluginActivation::afterSchemaUpdate
 */
	public function afterSchemaUpdate($schemaVersion) {
		// Publishing added to CmsNavigationMenuItem model - all existing blocks must be published
		if ($schemaVersion == '2') {
			$Item = ClassRegistry::init('Navigation.NavigationMenuItem');
			$items = $Item->find('list');
			$data = array();
			foreach ($items as $key => $val) {
				$Item->id = $key;
				$data[] = $Item->publish($key, true);
			}
			ClassRegistry::init('Publishing.PublishingInformation')->saveAll($data);
		}
	}

}