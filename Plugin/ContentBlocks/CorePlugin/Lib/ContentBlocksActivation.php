<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * ContentBlocksActivation class
 *
 * Performs tasks related to ContentBlocks plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classContentBlocksActivation.html
 * @package      Cms.Plugin.Pages.Lib  
 * @since        Pyramid CMS v 1.0
 */
class ContentBlocksActivation extends PluginActivation {

/** 
 * Permissions plugin data
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'content_blocks', 'controller' => 'content_blocks', 'action' => 'admin_edit', 'description' => 'Add and edit content blocks')
			, 'Group'	=> array('Group' => array(2))
		),
		array(
			'Permission' => array('plugin' => 'content_blocks', 'controller' => 'content_blocks', 'action' => 'admin_delete', 'description' => 'Delete content blocks')
		)
	);

/**
 * Settings to be installed
 */
	protected $_settings = array(
		array(
			'key' => 'ContentBlocks.administrators_add_new',
			'value' => '0',
			'title' => 'Allow non-super administrators to add new content blocks',
			'description' => 'Whether regular administrators can add new content blocks.',
			'type' => 'checkbox',
			'super_admin' => true
		)
	);

/**
 * after schema update callback
 *
 * @see CmsPluginActivation::afterSchemaUpdate
 */
	public function afterSchemaUpdate($schemaVersion) {
		// Publishing added to CmsContentBlock model - all existing blocks must be published
		if ($schemaVersion == '2') {
			$ContentBlock = ClassRegistry::init('ContentBlocks.ContentBlock');
			$blocks = $ContentBlock->find('list');
			$data = array();
			foreach ($blocks as $key => $val) {
				$ContentBlock->id = $key;
				$data[] = $ContentBlock->publish($key, true);
			}
			ClassRegistry::init('Publishing.PublishingInformation')->saveAll($data);
		}
	}

}