<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsMetasEventListener class
 *
 * Event Listener for the Metas plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsMetasEventListener.html
 * @package		 Cms.Core.Plugin.Metas.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsMetasEventListener extends CmsEventListener {

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Component.AdminNav.pluginNav' => array(
			'callable' => 'onAdminNavPluginNav'
		),
		'Component.AdminTab.behaviors' => array(
			'callable' => 'onAdminTabBehaviors'
		)
	);

/**
 * Components
 */
	protected $_components = array(
		'Metas.Meta'
	);

/**
 * Helpers
 */
	protected $_helpers = array(
		'Metas.Meta'
	);

/**
 * Admin nav listener
 *
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$items = array(
			Configure::read('Plugins.Metas.alias') => array(
				'link' => array(
					'plugin' => 'metas',
					'controller' => 'meta_keys',
					'action' => 'index'
				)
			)
		);

		$event->result = Set::merge($event->result, $items);
	}

/**
 * Admin tab listener - add the Metas tab if the user has access.
 *
 * @return void
 */
	public function onAdminTabBehaviors($event) {
		if (AccessControl::isAuthorized(array('plugin' => 'metas', 'controller' => 'meta_keys', 'action' => 'admin_index'))) {
			$info = array('MetaTag' => array(
				'name' => 'Metas',
				'plugin' => 'Metas'
			));

			$event->result = Set::merge($event->result, $info);
		}
	}

}