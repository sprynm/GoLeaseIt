<?php
/**
 * CmsNavigationMenu class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsNavigationMenu.html
 * @package		 Cms.Plugin.Navigation.Model 
 * @since		 Pyramid CMS v 1.0
 */
class CmsNavigationMenu extends NavigationAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Publishing.Publishable',
		'Versioning.SoftDelete',
		'Copyable'
	);

/**
 * hasMany associations
 */
	public $hasMany = array(
		'NavigationMenuItem' => array(
			'className' => 'Navigation.NavigationMenuItem', 
			'foreignKey' => 'navigation_menu_id',
			'order'	=> 'NavigationMenuItem.lft'
		)
	);

}
