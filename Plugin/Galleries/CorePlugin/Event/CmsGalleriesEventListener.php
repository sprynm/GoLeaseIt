<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsGalleriesEventListener class
 *
 * Event Listener for the Galleries plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsGalleriesEventListener.html
 * @package		 Cms.Core.Plugin.Galleries.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsGalleriesEventListener extends CmsEventListener {

/**
 * Helpers
 */
	protected $_helpers = array(
		'Galleries.GalleryBlock'
	);

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
 * Admin nav listener
 *
 * @param object $event
 * @return void
 */
	public function onAdminNavPluginNav($event) {
		$pages = array(
			Configure::read('Plugins.Galleries.alias') => array(
				'link' => '#'
				, 'children' => array(
					'Manage Galleries' => array(
						'link' => array(
							'plugin' => 'galleries',
							'controller' => 'galleries',
							'action' => 'index',
							'admin' => true
						)
					),
					'Image Versions' => array(
						'link' => array(
							'plugin' => 'media',
							'controller' => 'attachment_versions',
							'action' => 'edit',
							'Gallery',
							'admin' => true
						)
					)
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
		$galleries = ClassRegistry::init('Galleries.Gallery')->findForTinyMce();
		$galleries = Set::combine($galleries, '{n}.Gallery.id', '{n}.Gallery.name');

		$items = array(
			Configure::read('Plugins.Galleries.alias') => array(
				'blockType' => 'Gallery',
				'options' => $galleries
			)
		);
		
		$event->result = Set::merge($event->result, $items);
	}

}