<?php
App::uses('PluginActivation', 'PluginTools.Lib');
App::uses('CmsCodeGenerator', 'Lib');
/**
 * MediaActivation class
 *
 * Performs tasks related to plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classGuestbooksActivation.html
 * @package      Cms.Plugin.Media.Lib  
 * @since        Pyramid CMS v 1.0
 */
class MediaActivation extends PluginActivation {




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
			)
		)
	);

	
/** 
 * Permissions data
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'media', 'controller' => 'attachment_versions', 'action' => 'admin_add_version', 'description' => 'Add new image versions')
		),
		array(
			'Permission' => array('plugin' => 'media', 'controller' => 'attachment_versions', 'action' => 'admin_edit', 'description' => 'Edit image versions')
		),
		array(
			'Permission' => array('plugin' => 'media', 'controller' => 'attachment_versions', 'action' => 'admin_regenerate', 'description' => 'Regenerate versions of images')
		),
		array(
			'Permission' => array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'admin', 'description' => 'Upload and manage attachments'),
			'Group' => array('Group' => array(2))
		)
	);

/**
 * after schema update callback
 *
 * @see CmsPluginActivation::afterSchemaUpdate
 */
	public function afterSchemaUpdate($schemaVersion) {
		switch ($schemaVersion) {
			// Fixing a bad permission
			case '2':
				$Perm = ClassRegistry::init('Users.Permission');
				$permission = $Perm->find('first', array(
					'conditions' => array('plugin' => 'media', 'controller' => 'attachments', 'action' => '*')
				));
				if ($permission) {
					$Perm->id = $permission['Permission']['id'];
					$Perm->saveField('action', 'admin');
				}
			break;

			// Updating alternative text size and adding a trait
			case '3':
				$path = 'Plugin' . DS . 'Media' . DS . 'Trait';
				CmsCodeGenerator::generate(
					CMS . $path,
					APP . $path,
					'Media'
				);
			break;
		}
	}

}