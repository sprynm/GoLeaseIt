<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * GalleriesActivation class
 *
 * Performs tasks related to Galleries plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classGalleriesActivation.html
 * @package      Cms.Plugin.Galleries.Lib  
 * @since        Pyramid CMS v 1.0
 */
class GalleriesActivation extends PluginActivation {

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Media.AttachmentVersion' => array(
			array(
				'model' => 'Gallery', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'thumb', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 100, 
				'height' => 100
			),
			array(
				'model' => 'Gallery', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'medium', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 300, 
				'height' => 300
			),
			array(
				'model' => 'Gallery', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'large', 
				'type' => 'fit', 
				'convert' => 'image/jpeg', 
				'width' => 800, 
				'height' => 600
			)
		)
	);

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'galleries', 'controller' => 'galleries', 'action' => 'admin_edit', 'description' => 'Add and edit galleries')
			, 'Group'	=> array('Group' => array(2))
		),
		array(
			'Permission' => array('plugin' => 'galleries', 'controller' => 'galleries', 'action' => 'admin_delete', 'description' => 'Delete galleries')
		)

	);
}