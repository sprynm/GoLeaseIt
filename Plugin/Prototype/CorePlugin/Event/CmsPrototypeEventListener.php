<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsPrototypeEventListener class
 *
 * Event Listener for the Prototype plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPrototypeEventListener.html
 * @package		 Cms.Core.Plugin.Prototype.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsPrototypeEventListener extends CmsEventListener {

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
		'Cms.pluginPaths' => array(
			'callable' => 'onPluginPaths'
		),
		'Component.Preview.beforeRender' => array(
			'callable' => 'onPreview'
		),
		'Controller.Sitemap.sitemapItems' => array(
			'callable' => 'onSitemapItems'
		),
		'Model.Revision.beforeRestore' => array(
			'callable' => 'onBeforeRestore'
		),
		'View.beforeRender' => array(
			'callable' => 'onViewBeforeRender'
		)
	);

/**
 * Helpers
 */
	protected $_helpers = array(
		'Prototype.Prototype',
		'Prototype.PrototypeLink'
	);

/**
 * Admin nav listener
 *
 * @param object event
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$items = array(
			Configure::read('Plugins.Prototype.alias') => array(
				'link' => '#'
				, 'children' => array(
					'Add New Instance' => array(
						'link' => array(
							'plugin' => 'prototype', 
							'controller' => 'prototype_instances', 
							'action' => 'edit'
						)
					),
					'Manage Instances' => array(
						'link' => array(
							'plugin' => 'prototype', 
							'controller' => 'prototype_instances', 
							'action' => 'index'
						)
					)
				)
			)
		);

		// Add any loaded and active prototype instances to the admin nav.
		$instances = ClassRegistry::init('Prototype.PrototypeInstance')->find('all', array(
			'fields' => array('slug', 'name'),
			'published' => true,
			'cache' => true
		));

		$instances = Hash::combine($instances, '{n}.PrototypeInstance.slug', '{n}.PrototypeInstance.name');

		foreach ($instances as $slug => $name) {
			$link = array(
				'plugin' => 'prototype',
				'controller' => 'prototype_items',
				'action' => 'index',
				'admin' => true,
				'instance' => $slug
			);
			
			if (CmsPlugin::loaded($name) && AccessControl::isAuthorized($link)) {
				$items[Configure::read('Plugins.' . $name . '.alias')] = array(
					'link' => $link
				);
			}
		}

		$event->result = Set::merge($event->result, $items);
	}

/**
 * Event listener to parse ThemedModel data before a saveAll() so that it's saved properly, since you can't
 * edit such information in beforeSave().
 *
 * @param object event
 * @return void
 */
	public function onBeforeRestore($event) {
		$data = $event->data['data'];
		$Model = $event->data['Model'];

		if ($Model->name != 'PrototypeItem') {
			return;
		}

		if (empty($event->result)) {
			$event->result = $data;
		}

		if (isset($event->result['PrototypeInstance'])) {
			unset($event->result['PrototypeInstance']);
		}
	}

/** 
 * Fixes PrototypeItem variable name before rendering a revision preview action.
 *
 * @param object $event
 * @return void
 */
	public function onPreview($event) {
		$data = $event->data['data'];
		$Controller = $event->data['Controller'];
		
		if (empty($event->result)) {
			$event->result = $data;
		}
		
		//rename the prototypeItem view var
		if (isset($data['prototypeItem']) && $Controller->name == "PrototypeItems") {
			$event->result['item'] = $data['prototypeItem'];
		}
		
		//rename the prototypeInstance view var
		if (isset($data['prototypeInstance']) && $Controller->name == "PrototypeInstances") {
			$instance = $data['prototypeInstance'];
			$event->result['instance'] = $instance;
			$event->result['_instance'] = $instance;
			//set titles the same as the controller does
			$Controller->set('pageIntro', $instance['PrototypeInstance']['description']);
			$name = $instance['PrototypeInstance']['head_title'] ? $instance['PrototypeInstance']['head_title'] : $instance['PrototypeInstance']['name'];
			$Controller->PageSettings->setTitle($name, $instance['PrototypeInstance']['name']);
		}
	}

/**
 * Listener function for View.beforeRender.
 *
 * @param object event
 * @return void
 */
	public function onViewBeforeRender($event) {
		$this->_loadFeaturedItems($event);
	}

/**
 * Loads any featured items that need to be loaded for the current layout.
 * The controller object for the current request is in $event->subject.
 *
 * @param object event from onBeforeRender
 * @return void
 */
	protected function _loadFeaturedItems($event) {
		$instances = ClassRegistry::init('Prototype.PrototypeInstance')->find('all', array(
			'cache' => true
		));

		if (!$instances) {
			return;
		}

		foreach ($instances as $instance) {
			if (!$instance['PrototypeInstance']['use_featured_items']) {
				continue;
			}

			if ($instance['PrototypeInstance']['number_of_featured_items'] < 1 && !$instance['PrototypeInstance']['all_items_featured']) {
				continue;
			}

			if (!$instance['PrototypeInstance']['autoload_featured_items_in_layouts']) {
				continue;
			}

			$layouts = Cms::commaExplode($instance['PrototypeInstance']['autoload_featured_items_in_layouts']);
			if (count($layouts) < 1) {
				continue;
			}
			
			if (!in_array($event->subject->layout, $layouts) && !in_array('all', $layouts)) {
				continue;
			}

			// Got this far - there are featured items to load and set in the controller ($event->subject).
			//remove special characters and spaces, and convert to underscored
			$name = 'featured' . Inflector::slug( Inflector::camelize($instance['PrototypeInstance']['name']), '');
			$Item = ClassRegistry::init('Prototype.PrototypeItem');

			// Return all items
			if ($instance['PrototypeInstance']['all_items_featured']) {
				$query = $Item->summaryQuery($instance);
				$$name = $Item->find('all', $query);
			} else {
				// Return a certain number of items.
				$$name = $Item->findFeatured($instance, $instance['PrototypeInstance']['number_of_featured_items']);
			}
			$event->subject->set(compact($name));
			
			//add the variable to the debug panel since it's already been populated at this point
			if (!empty($event->subject->viewVars['debugToolbarPanels'])) {
				$event->subject->viewVars['debugToolbarPanels']['variables']['content'][$name] = $$name;
			}
		}		
	}

/**
 * Nav listener
 *
 * @param object event
 * @return void
 */

	public function onNavItems($event) {
		$Instance = ClassRegistry::init('Prototype.PrototypeInstance');

		$instances = $Instance->find('all', array(
			'cache' => true,
			'published' => true,
		));
		
		$items = array();

		foreach ($instances as $instance) {
			
			if($instance['PrototypeInstance']['allow_instance_view'] == true) {
				$items[$instance['PrototypeInstance']['name']]['Index'][] = array(
					'name' => 'Index',
					'model' => 'PrototypeInstance',
					'key' => $instance['PrototypeInstance']['id']
				);
			}
			
			
			if ($instance['PrototypeInstance']['use_categories'] && $instance['PrototypeInstance']['allow_category_views']) {
				$categories = $Instance->PrototypeCategory->find('all', array(
					'conditions' => array(
						'prototype_instance_id' => $instance['PrototypeInstance']['id']
					),
					'published' => true,
					'cache' => true
				));
				
				$items[$instance['PrototypeInstance']['name']]['Categories'] = array();
				foreach ($categories as $key => $val) {
					$items[$instance['PrototypeInstance']['name']]['Categories'][] = array(
						'name' => $val['PrototypeCategory']['name'],
						'model' => 'PrototypeCategory',
						'key' => $val['PrototypeCategory']['id']
					);
				}
			}

			if ($instance['PrototypeInstance']['allow_item_views']) {
				$returnItems = $Instance->PrototypeItem->find('all', array(
					'conditions' => array(
						'prototype_instance_id' => $instance['PrototypeInstance']['id']
					),
					'published' => true,
					'cache' => true
				));
				
				$items[$instance['PrototypeInstance']['name']]['Items'] = array();
				foreach ($returnItems as $key => $val) {
					$items[$instance['PrototypeInstance']['name']]['Items'][] = array(
						'name' => $val['PrototypeItem']['name'],
						'model' => 'PrototypeItem',
						'key' => $val['PrototypeItem']['id']
					);
				}
			}
		}

		$event->result = Set::merge($event->result, $items);
	}

/**
 * Cms::pluginPaths listener - adds prototype paths to the plugin paths if the plugin
 * being viewed is a prototype instance. $event contains a $data array with a 'plugin'
 * key that contains the name of the plugin.
 *
 * @param object event
 * @return void
 */
	public function onPluginPaths($event) {
		$params = Router::getRequest();
		if (!isset($params['instance'])) {
			return;
		}

		$instance = ClassRegistry::init('Prototype.PrototypeInstance')->findBySlug($params['instance']);

		if ($instance) {
			$paths = array(
				APP . 'Plugin' . DS . 'Prototype' . DS . 'View' . DS . $params['instance'] . DS,
				APP . 'Plugin' . DS . 'Prototype' . DS . 'CorePlugin' . DS . 'View' . DS . $params['instance'] . DS,
				CMS . 'Plugin' . DS . 'Prototype' . DS . 'View' . DS . $params['instance'] . DS
			);

			$event->result = Set::merge($event->result, $paths);
		}
	}

/**
 * Sitemap XML feed listener
 *
 * @param object event
 * @return void
 */
	public function onSitemapItems($event) {
		if ($event->data['type'] == 'xml') {
			$instances = ClassRegistry::init('Prototype.PrototypeInstance')->findForSitemap($event->data['type']);
			$categories = ClassRegistry::init('Prototype.PrototypeCategory')->findForSitemap($event->data['type']);
			$items = ClassRegistry::init('Prototype.PrototypeItem')->findForSitemap($event->data['type']);
			
			$data = array();
			foreach ($instances as $instance) {
				$data[] = array(
					'loc' => $instance['PrototypeInstance']['url'],
					'lastmod' => $instance['PrototypeInstance']['modified'],
					'changefreq' => 'yearly',
					'priority' => '0.5'
				);
			}
			
			foreach ($categories as $category) {
				$data[] = array(
					'loc' => $category['PrototypeCategory']['url'],
					'lastmod' => $category['PrototypeCategory']['modified'],
					'changefreq' => $category['PrototypeCategory']['changefreq'],
					'priority' => '0.5'
				);
			}

			foreach ($items as $item) {
				$data[] = array(
					'loc' => $item['PrototypeItem']['url'],
					'lastmod' => $item['PrototypeItem']['modified'],
					'changefreq' => $item['PrototypeItem']['changefreq'],
					'priority' => '0.5'
				);
			}

			$event->result = array_merge($event->result, $data);
		}
	}
}