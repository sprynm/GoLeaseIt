<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * PagesActivation class
 *
 * Performs tasks related to Pages plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classPagesActivation.html
 * @package      Cms.Plugin.Pages.Lib  
 * @since        Pyramid CMS v 1.0
 */
class PagesActivation extends PluginActivation {

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Media.AttachmentVersion' => array(
			array(
				'model'		=> 'Page'
				, 'foreign_key'	=> null
				, 'group'	=> 'Image'
				, 'name'	=> 'thumb'
				, 'type'	=> 'fitCrop'
				, 'convert'	=> 'image/jpeg'
				, 'width'	=> 100
				, 'height'	=> 100
				,
			)
			, array(
				'model'		=> 'Page'
				, 'foreign_key'	=> null
				, 'group'	=> 'Image'
				, 'name'	=> 'banner-lrg'
				, 'type'	=> 'fitCrop'
				, 'convert'	=> 'image/jpeg'
				, 'width'	=> 1920
				, 'height'	=> 700
				,
			)
			, array(
				'model'		=> 'Page'
				, 'foreign_key'	=> null
				, 'group'	=> 'Image'
				, 'name'	=> 'banner-med'
				, 'type'	=> 'fitCrop'
				, 'convert'	=> 'image/jpeg'
				, 'width'	=> 1440
				, 'height'	=> 700
				,
			)
			, array(
				'model'		=> 'Page'
				, 'foreign_key'	=> null
				, 'group'	=> 'Image'
				, 'name'	=> 'banner-sm'
				, 'type'	=> 'fitCrop'
				, 'convert'	=> 'image/jpeg'
				, 'width'	=> 800
				, 'height'	=> 450
				,
			)
			, array(
				'model'		=> 'Page'
				, 'foreign_key'	=> null
				, 'group'	=> 'Image'
				, 'name'	=> 'banner-xsm'
				, 'type'	=> 'fitCrop'
				, 'convert'	=> 'image/jpeg'
				, 'width'	=> 540
				, 'height'	=> 375
				,
			)
			,
		)
	);

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array('Permission' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'admin_add', 'description' => 'Add new pages')),
		array(
			'Permission' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'admin_preview', 'description' => 'Preview pages'),
			'Group' => array('Group' => array(2))
		),
		array('Permission' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'admin_edit', 'description' => 'Edit pages'),
			'Group' => array('Group' => array(2))
		),
		array(
			'Permission' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'admin_delete', 'description' => 'Delete pages')
		),
		array('Permission' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'admin_reorder', 'description' => 'Reorder pages'))
	);

/**
 * Settings to be installed
 */
	protected $_settings = array(
		array(
			'key' => 'Pages.Offline.page_title',
			'value' => 'Coming Soon',
			'description' => 'The title of the "coming soon" (offline mode) page.',
			'type' => 'text',
			'super_admin' => 1
		),
		array(
			'key' => 'Pages.Offline.page_content',
			'value' => '<p>Coming Soon!</p>',
			'description' => 'The content of the "coming soon" (offline mode) page.',
			'type' => 'wysiwyg',
			'super_admin' => 1
		),
		array(
			'key' => 'Pages.PageOptions.banner_image',
			'value' => 1,
			'description' => 'Banner Image Option',
			'type' => 'checkbox',
			'super_admin' => 0
		)
	);

/**
 * after schema update callback
 *
 * @see CmsPluginActivation::afterSchemaUpdate
 */
	public function afterSchemaUpdate($schemaVersion) {
		switch ($schemaVersion) {
			case '7': // Adding settings
				$Setting = ClassRegistry::init('Settings.Setting');
				$Setting->saveAll($this->_settings);
			break;
		}

		AppCache::clear();
	}

}