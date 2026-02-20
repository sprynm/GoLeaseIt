<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * MetasActivation class
 *
 * Performs tasks related to Metas plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classMetasActivation.html
 * @package      Cms.Plugin.Metas.Lib  
 * @since        Pyramid CMS v 1.0
 */
class MetasActivation extends PluginActivation {

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Metas.MetaKey' => array(
			array(
				'MetaKey' => array('name' => 'description', 'type' => 'textarea', 'allow_default' => false)
			),
			array(
				'MetaKey' => array('name' => 'application-name', 'type' => 'text')
			),
			array(
				'MetaKey' => array('name' => 'robots', 'type' => 'text' , 'value' => 'NOODP')
			), 
			array(
				'MetaKey' => array('name' => 'og:image', 'type' => 'text' , 'value' => '')
			)
		)
	);

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'metas', 'controller' => 'meta_keys', 'action' => 'admin_new', 'description' => 'Add new meta keys')
		),
		array(
			'Permission' => array('plugin' => 'metas', 'controller' => 'meta_keys', 'action' => 'admin_index', 'description' => 'Edit meta keys')
		)
	);

/**
 * Settings to be installed
 */
	protected $_settings = array(
		array(
			'key' => 'Metas.use_defaults',
			'value' => '1',
			'description' => 'If enabled, default meta tags will be displayed when none are available for the current resource being viewed.',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Metas.automatic_output',
			'value' => '1',
			'description' => 'If checked, metas will automatically be put into the HTML if found.',
			'type' => 'checkbox',
			'super_admin' => 1
		)
	);
	
}