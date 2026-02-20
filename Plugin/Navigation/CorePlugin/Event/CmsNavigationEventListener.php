<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsNavigationEventListener class
 *
 * Event Listener for the Navigation plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsNavigationEventListener.html
 * @package		 Cms.Core.Plugin.Navigation.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsNavigationEventListener extends CmsEventListener {

/**
 * Helpers to load
 */
	protected $_helpers = array(
		'Navigation.Navigation'
	);

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Component.AdminNav.pluginNav' => array(
			'callable' => 'onAdminNavPluginNav'
		)
	);

/**
 * Admin nav listener
 *
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$items = array(
			Configure::read('Plugins.Navigation.alias') => array(
				'link' => '#'
				, 'children' => array(
					'Navigation Menus' => array(
						'link' => array(
							'plugin' => 'navigation', 
							'controller' => 'navigation_menus', 
							'action' => 'index'
						)
					), 
					'Custom Admin Nav' => array(
						'link' => array(
							'plugin' => 'navigation', 
							'controller' => 'admin_navigation_items', 
							'action' => 'index'
						)
					)
				)
			)
		);
		
		$event->result = Set::merge($event->result, $items);
		
		// Add in custom nav items
		$event->result = Set::merge($event->result, $this->_customAdminNav());
	}

/**
 * Finds and returns custom admin nav items based on user permissions.
 *
 * @return array
 */
	protected function _customAdminNav() {
		$items = array();

		$groupIds = ClassRegistry::init('Users.GroupsUser')->find('list', array(
				'conditions' => array('GroupsUser.user_id' => Authsome::get('User.id')),
				'fields' => array('GroupsUser.id', 'GroupsUser.group_id')
			));
			
		$conditions = array();
		if (!AccessControl::inGroup('Super Administrator')) {
			$conditions['AdminNavigationItemsGroup.group_id'] = $groupIds;
		}

		ClassRegistry::init('Navigation.AdminNavigationItem')->bindModel(array('hasOne' => array('AdminNavigationItemsGroup')));
		$items = ClassRegistry::init('Navigation.AdminNavigationItem')->find('all', array(
			'conditions' => $conditions,
			'contain' => array('AdminNavigationItemsGroup'),
			'published' => true
		));

		$navItems = array();
		foreach ($items as $item) {
			$navItems[$item['AdminNavigationItem']['name']] = array('link' => $item['AdminNavigationItem']['link']);
		}
		
		return $navItems;
	}
	
}
