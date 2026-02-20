<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsContentBlocksEventListener class
 *
 * Event Listener for the ContentBlocks plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsContentBlocksEventListener.html
 * @package		 Cms.Core.Plugin.ContentBlocks.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsContentBlocksEventListener extends CmsEventListener {

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
 * Helpers
 */
	protected $_helpers = array(
		'ContentBlocks.ContentBlock'
	);

/**
 * Admin nav listener
 *
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$pages = array(
			Configure::read('Plugins.ContentBlocks.alias') => array(
				'link' => array(
					'plugin' => 'content_blocks',
					'controller' => 'content_blocks',
					'action' => 'index',
					'admin' => true
				)
			)
		);
		
		$event->result = Set::merge($event->result, $pages);
	}

/**
 * Listener for TinyMCE content blocks select list plugin
 *
 * @param object $event
 * @return void
 */
	public function onTinyMceBlockLists($event) {
		$blocks = ClassRegistry::init('ContentBlocks.ContentBlock')->findForTinyMce();
		$blocks = Set::combine($blocks, '{n}.ContentBlock.id', '{n}.ContentBlock.name');

		$items = array(
			Configure::read('Plugins.ContentBlocks.alias') => array(
				'blockType' => 'ContentBlock',
				'options' => $blocks
			)
		);
		
		$event->result = Set::merge($event->result, $items);
	}

}