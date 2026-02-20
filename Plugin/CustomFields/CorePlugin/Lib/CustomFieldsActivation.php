<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * CustomFieldsActivation class
 *
 * Performs tasks related to plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCustomFieldsActivation.html
 * @package      Cms.Plugin.Prototype.Lib  
 * @since        Pyramid CMS v 1.0
 */
class CustomFieldsActivation extends PluginActivation {

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'custom_fields', 'controller' => '*', 'action' => 'admin', 'description' => 'Custom field management')
		)
	);

}