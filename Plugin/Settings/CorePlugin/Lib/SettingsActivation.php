<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * SettingsActivation class
 *
 * Performs tasks related to Settings plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classSettingsActivation.html
 * @package      Cms.Plugin.Settings.Lib  
 * @since        Pyramid CMS v 1.0
 */
class SettingsActivation extends PluginActivation {

/** 
 * Permission data
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'admin_index', 'description' => 'Edit settings'),
			'Group' => array('Group' => array(2))
		),
		array(
			'Permission' => array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'admin_key_index', 'description' => 'View settings add/edit index')
		),
		array(
			'Permission' => array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'admin_key_edit', 'description' => 'Add and edit site settings')
		),
		array(
			'Permission' => array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'admin_delete', 'description' => 'Delete site settings')
		)
	);

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Media.AttachmentVersion' => array(
			array(
				'model' => 'Setting', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'thumb', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 100, 
				'height' => 100
			),
			array(
				'model' => 'Setting', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'medium', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 300, 
				'height' => 300
			),
			array(
				'model' => 'Setting', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'large', 
				'type' => 'fit', 
				'convert' => 'image/jpeg', 
				'width' => 800, 
				'height' => 600
			),
		)
	);

/**
 * Settings to be installed
 */
	protected $_settings = array(
		array(
			'key'		=> 'HeaderNotice.display_header_notice'
			, 'value'	=> 0
			, 'type'	=> 'checkbox'
			,
		)
		, array(
			'key'		=> 'HeaderNotice.link'
			, 'value'	=> '/contact'
			,
		)
		, array(
			'key'		=> 'HeaderNotice.link_text'
			, 'value'	=> 'Learn More'
			,
		)
		, array(
			'key'		=> 'HeaderNotice.text'
			, 'value'	=> 'Short piece of notice/announcement text.'
			,
		)
		, array(
			'key'		=> 'LegalNotice.enabled'
			, 'value'	=> 0
			, 'type'	=> 'checkbox'
			,
		)
		, array(
			'key'		=> 'LegalNotice.text'
			, 'value'	=> 'We use analytics to understand traffic and improve site content.'
			, 'type'	=> 'wysiwyg'
			,
		)
		, array(
			'key'		=> 'LegalNotice.dismiss_label'
			, 'value'	=> 'Got it'
			,
		)
		, array(
			'key'		=> 'LegalNotice.storage_key'
			, 'value'	=> 'legal_notice_dismissed'
			,
		)
		, array(
			'key'		=> 'LegalNotice.version'
			, 'value'	=> '1'
			,
		)
		,
	);
}
