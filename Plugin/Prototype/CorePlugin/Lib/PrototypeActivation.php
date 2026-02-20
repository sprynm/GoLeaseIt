<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * PrototypeActivation class
 *
 * Performs tasks related to plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classPrototypeActivation.html
 * @package      Cms.Plugin.Prototype.Lib  
 * @since        Pyramid CMS v 1.0
 */
class PrototypeActivation extends PluginActivation {

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Media.AttachmentVersion' => array(
			array(
				'model' => 'PrototypeInstance', 
				'foreign_key' => null, 
				'group' => 'Item Image', 
				'name' => 'thumb', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 100, 
				'height' => 100
			),
			array(
				'model' => 'PrototypeInstance', 
				'foreign_key' => null, 
				'group' => 'Item Image', 
				'name' => 'medium', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 300, 
				'height' => 300
			),
			array(
				'model' => 'PrototypeInstance', 
				'foreign_key' => null, 
				'group' => 'Item Image', 
				'name' => 'large', 
				'type' => 'fit', 
				'convert' => 'image/jpeg', 
				'width' => 800, 
				'height' => 600
			),
			array(
				'model' => 'PrototypeInstance', 
				'foreign_key' => null, 
				'group' => 'Category Image', 
				'name' => 'thumb', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 100, 
				'height' => 100
			),
			array(
				'model' => 'PrototypeInstance', 
				'foreign_key' => null, 
				'group' => 'Category Image', 
				'name' => 'medium', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 300, 
				'height' => 300
			),
			array(
				'model' => 'PrototypeInstance', 
				'foreign_key' => null, 
				'group' => 'Category Image', 
				'name' => 'large', 
				'type' => 'fit', 
				'convert' => 'image/jpeg', 
				'width' => 800, 
				'height' => 600
			)
			,
		)
	);

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'prototype', 'controller' => '*', 'action' => 'admin', 'description' => 'Prototype instance management')
		),
		array(
			'Permission' => array('plugin' => 'prototype', 'controller' => 'prototype_categories', 'action' => 'admin_reorder', 'description' => 'Change order of prototype categories', 'value' => '1',),
			'Group' => array('Group' => array(2))
		),
		array(
			'Permission' => array('plugin' => 'prototype', 'controller' => 'prototype_items', 'action' => 'admin_reorder', 'description' => 'Change order of prototype items', 'value' => '1',),
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
			// Add missing permission descriptions for instances
			case '3':
				$db = ConnectionManager::getDataSource('default');
				$db->cacheSources = false;

				$instances = ClassRegistry::init('Prototype.PrototypeInstance')->find('all');
				$instanceSlugs = Hash::combine($instances, '{n}.PrototypeInstance.slug', '{n}.PrototypeInstance.name');

				$Permission = ClassRegistry::init('Users.Permission');

				$permissions = $Permission->find('all', array(
					'conditions' => array(
						'Permission.plugin' => 'prototype',
						'Permission.controller' => array_keys($instanceSlugs),
						'Permission.action' => 'admin',
						'Permission.description' => null
					)
				));

				foreach ($permissions as $key => $val) {
					$Permission->id = $val['Permission']['id'];
					$Permission->saveField('description', $instanceSlugs[$val['Permission']['controller']] . ' management');
				}
				AppCache::clear();
			break;

			// Item and category reorder permissions
			case '4':
				$data = array(
					array(
						'Permission' => array('plugin' => 'prototype', 'controller' => 'prototype_categories', 'action' => 'admin_reorder', 'description' => 'Change order of prototype categories')
					),
					array(
						'Permission' => array('plugin' => 'prototype', 'controller' => 'prototype_items', 'action' => 'admin_reorder', 'description' => 'Change order of prototype items')
					)
				);

				foreach ($data as $key => $data) {
					ClassRegistry::init('Users.Permission')->create();
					ClassRegistry::init('Users.Permission')->saveAll($data, array('deep' => true));
				}
			break;
		}

	}

}