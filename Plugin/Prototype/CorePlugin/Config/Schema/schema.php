<?php
/**
 * PrototypeSchema class
 *
 * Schema information for the Prototype plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classPrototypeSchema.html
 * @package		 Cms.Plugin.Prototype.Config.Schema 
 * @since		 Pyramid CMS v 1.0
 */
class PrototypeSchema extends CmsSchema {

	public $name = 'Prototype';

	public function before($event = array()) {
		return true;
	}

/**
 * Inserts some core settings.
 */
	public function after($event = array()) {
	}
		
	public $prototype_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'rank' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'prototype_instance_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'head_title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'slug' => array('column' => 'slug', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $prototype_categories_prototype_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'prototype_category_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'prototype_item_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $prototype_instances = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'use_categories' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'public' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'footer_text' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'layout' => array('type' => 'string', 'null' => true, 'default' => 'default', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'item_order' => array('type' => 'string', 'null' => true, 'default' => 'PrototypeItem.rank ASC', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'allow_instance_view' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'allow_category_views' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'allow_item_views' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'item_image_type' => array('type' => 'string', 'null' => true, 'default' => 'none', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'item_document_type' => array('type' => 'string', 'null' => true, 'default' => 'none', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'category_image_type' => array('type' => 'string', 'null' => true, 'default' => 'none', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'category_document_type' => array('type' => 'string', 'null' => true, 'default' => 'none', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'item_summary_pagination' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'item_summary_pagination_limit' => array('type' => 'integer', 'null' => true, 'default' => '10'),
		'use_featured_items' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'all_items_featured' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'number_of_featured_items' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'autoload_featured_items_in_layouts' => array('type' => 'string', 'length' => 150, 'null' => true, 'default' => 'home'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'name_field_label' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'head_title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'category_changefreq' => array('type' => 'string', 'null' => false, 'default' => 'yearly', 'length' => 20, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'use_page_banner_images' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'fallback_to_instance_banner_image' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'use_page_banner_image_categories' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'use_page_banner_image_items' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'item_changefreq' => array('type' => 'string', 'null' => false, 'default' => 'monthly', 'length' => 20, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'override_title_format' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'slug' => array('column' => 'slug', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $prototype_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'rank' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'prototype_instance_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'featured' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'head_title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'slug' => array('column' => 'slug', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);
}