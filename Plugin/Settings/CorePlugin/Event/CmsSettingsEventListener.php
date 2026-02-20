<?php
App::uses('CmsEventListener', 'Event');

/**
 *
 * CmsSettingsEventListener class
 *
 * Event Listener for the Settings plugin.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsSettingsEventListener.html
 * @package      Cms.Core.Plugin.Settings.Event
 * @since        Pyramid CMS v 1.0
 */
class CmsSettingsEventListener extends CmsEventListener {

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Component.AdminNav.pluginNav' => array(
			'callable' => 'onAdminNavPluginNav'
		),
		'Helper.TinyMce.blockLists' => array(
			'callable' => 'onTinyMceBlockLists'
		)
	);

/**
 * Helpers to load
 */
	protected $_helpers = array('Settings.Settings', 'Settings.SettingsBlock');

/**
 * Admin nav listener
 *
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$items = array(
			Configure::read('Plugins.Administration.alias') => array(
				'link' => '#',
				'children' => array(
					'Settings' => array(
						'link' => array(
							'plugin' => 'settings',
							'controller' => 'settings',
							'action' => 'index',
							'admin' => true
						)
					)
					, 'Image Versions' => array(
						'link' => array(
							'plugin' => 'media',
							'controller' => 'attachment_versions',
							'action' => 'edit',
							'Setting',
							'admin' => true
						)
					)
				)
			)
		);

		$event->result = Set::merge($event->result, $items);
	}
		
/**
 * Listener for TinyMCE content blocks select list plugin
 *
 * @param object $event
 * @return void
 */
	public function onTinyMceBlockLists($event) {
	//
		$blocks = ClassRegistry::init('Settings.Setting')->findForTinyMce();
	//
		$blocks = Set::combine($blocks, '{n}.Setting.key', '{n}.Setting.tinyMceOption');
	//
		$items = array(
			Configure::read('Plugins.Settings.alias') => array(
				'blockType' => 'Setting',
				'options' => $blocks
			)
		);
	//
		$event->result = Set::merge($event->result, $items);
	}
}