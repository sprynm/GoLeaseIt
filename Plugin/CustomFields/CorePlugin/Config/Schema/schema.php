<?php
/**
 * CustomFieldsSchema class
 *
 * Schema information for the CustomFields plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCustomFieldsSchema.html
 * @package		 Cms.Plugin.CustomFields.Config.Schema 
 * @since		 Pyramid CMS v 1.0
 */
class CustomFieldsSchema extends CmsSchema {

	public $name = 'CustomFields';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}
	// 
	public $custom_field_values = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'model' => array('type' => 'string', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'null' => false, 'default' => '', 'length' => 75),
		'custom_field_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'key' => array('type' => 'string', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'null' => false, 'default' => '', 'length' => 255),
		'val' => array('type' => 'text', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'null' => false, 'default' => 'string', 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'foreign' => array('column' => array('model', 'foreign_key'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);
	// 
	public $custom_fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'default' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => 'string', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'required' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'validate' => array('type' => 'string', 'null' => false, 'default' => 'notEmpty', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'validate_message' => array('type' => 'string', 'null' => false, 'default' => 'This field is required.', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'rank' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'options' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'model' => array('type' => 'string', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'null' => false, 'default' => '', 'length' => 75),
		'display_label' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'group' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'placeholder' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'autocomplete' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'css_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'div_css_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'group_with' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'merge_content' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

}