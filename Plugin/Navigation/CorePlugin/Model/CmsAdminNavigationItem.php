<?php
/**
 * CmsAdminNavigationItem class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAdminNavigationItem.html
 * @package		 Cms.Plugin.Navigation.Model 
 * @since		 Pyramid CMS v 1.0
 */
class CmsAdminNavigationItem extends NavigationAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Publishing.Publishable',
		'Copyable',
		'Versioning.SoftDelete'
	);

/**
 * Validation array
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty', 
			'required' => true, 
			'message' => 'A name is required.'
		), 
		'link' => array(
			'rule' => 'notEmpty', 
			'required' => true, 
			'message' => 'A link is required.'
		)
	);

/**
 * HABTM associations
 */
	public $hasAndBelongsToMany = array(
		'Group' => array(
			'className' => 'Group', 
			'joinTable' => 'admin_navigation_items_groups', 
			'foreignKey' => 'admin_navigation_item_id', 
			'associationForeignKey' => 'group_id', 
			'unique' => true
		)
	);

/**
 * Edit query options
 */
	protected $_editQuery = array(
		'contain' => array('Group')
	);

}
