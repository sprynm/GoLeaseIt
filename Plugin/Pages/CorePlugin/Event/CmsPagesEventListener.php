<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsPagesEventListener class
 *
 * Event Listener for the Pages plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPagesEventListener.html
 * @package		 Cms.Core.Plugin.Pages.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsPagesEventListener extends CmsEventListener {

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Component.AdminNav.pluginNav' => array(
			'callable' => 'onAdminNavPluginNav'
		),
		'Controller.NavigationMenuItems.navItems' => array(
			'callable' => 'onNavItems'
		),
		'Controller.Sitemap.sitemapItems' => array(
			'callable' => 'onSitemapItems'
		),
		'Plugin.remove' => array(
			'callable' => 'onPluginRemove'
		)
	);

/**
 * Components to load
 */
	protected $_components = array(
		'Pages.PageSettings'
	);

/**
 * Helpers
 */
	protected $_helpers = array(
		'Pages.PageBlock'
	);

/**
 * Constructor - sets the priority for controller construct for this event listener to a higher
 * priority so that the page settings component is loaded before others.
 *
 * @return void
 */
	public function __construct() {
		parent::__construct();
		$this->_coreEvents['Controller.construct']['priority'] = 0;
	}
/**
 * Admin nav listener
 *
 * @param object event
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$items = array(
			Configure::read('Plugins.Pages.alias') => array(
				'link' => '#'
				, 'children' => array(
					'Add a New Page' => array(
						'link' => array(
							'plugin' => 'pages', 
							'controller' => 'pages', 
							'action' => 'add', 
							'admin' => true
						)
					), 
					'Manage Pages' => array(
						'link' => array(
							'plugin' => 'pages', 
							'controller' => 'pages', 
							'action' => 'index', 
							'admin' => true
						)
					), 
					'Banner Image' => array(
						'link' => array(
							'plugin' => 'media', 
							'controller' => 'attachment_versions', 
							'action' => 'edit', 
							'Page'
						)
					), 
					'Manage Default Page Fields' => array(
						'link' => array(
							'plugin' => 'pages', 
							'controller' => 'pages', 
							'action' => 'default_fields',
							'admin' => true
						)
					)
				)
			)
		);
		
		$event->result = Set::merge($event->result, $items);
	}
	
/**
 * Nav listener
 *
 * @param object event
 * @return void
 */
	public function onNavItems($event) {
		$items = array('Pages' => array(
			'User Pages' => array()
		));

		$Page = ClassRegistry::init('Pages.Page');

		$conditions = array('OR' => array(
			'Page.plugin <=' => '',
			'Page.plugin' => null
		));
		$pages = $Page->find('all', array(
			'conditions' => $conditions,
			'order' => 'Page.lft ASC'
		));
		$pageList = $Page->generateTreeList($conditions, '{n}.Page.id', '{n}.Page.page_heading');

		foreach ($pages as $page) {
			$items['Pages']['User Pages'][] = array(
				'name' => $pageList[$page['Page']['id']],
				'model' => 'Page',
				'key' => $page['Page']['id']
			);
		}

		if (AccessControl::inGroup('Super Administrator')) {
			$items['Pages']['Super Admin Pages'] = array();

			$superPages = $Page->find('all', array(
				'conditions' => array(
					'Page.plugin >' => ''
				),
				'order' => 'Page.page_heading ASC'
			));

			foreach ($superPages as $page) {
				$items['Pages']['Super Admin Pages'][] = array(
					'name' => $page['Page']['page_heading'],
					'model' => 'Page',
					'key' => $page['Page']['id']
				);
			}
		}

		$event->result = Set::merge($event->result, $items);
	}

/**
 * Plugin removal listener - deletes Page records that have a plugin value matching the 
 * underscored plugin name.
 *
 * @param object event
 * @return void
 */
	public function onPluginRemove($event) {
		$plugin = Inflector::underscore($event->data['plugin']);
		ClassRegistry::init('Pages.Page')->deleteAll(array('plugin' => $plugin));
	}

/**
 * Sitemap listener. Either XML or HTML depending on $event->data['type'].
 *
 * @param object event
 * @return void
 */
	public function onSitemapItems($event) {
		if ($event->data['type'] == 'xml') {
			$pages = ClassRegistry::init('Pages.Page')->findForSitemap();
			$items = array();
			foreach ($pages as $page) {
				$items[] = array(
					'loc' => $page['Page']['url'],
					'lastmod' => $page['Page']['modified'],
					'changefreq' => 'weekly',
					'priority' => '0.5'
				);
			}
			$event->result = Set::merge($event->result, $items);
		}
	}

}
