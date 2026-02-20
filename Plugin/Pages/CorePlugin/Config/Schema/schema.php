<?php
/**
 * PagesSchema class
 *
 * Schema information for the Pages plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classPagesSchema.html
 * @package		 Cms.Plugin.Pages.Config.Schema 
 * @since		 Pyramid CMS v 1.0
 */
class PagesSchema extends CmsSchema {

	public $name = 'Pages';

	public function before($event = array()) {
		return true;
	}

/**
 * Inserts some core settings.
 */
	public function after($event = array()) {
		if (isset($event['create'])) {
			$data = array();
			switch ($event['create']) {
				case 'pages':
					$data = array(
						array(
							'internal_name' => 'Home Page', 
							'title' => 'Home', 
							'page_heading' => 'Home', 
							'content' => '<p>This is home.</p>', 
							'lft' => 1, 
							'rght' => 2, 
							'layout' => 'home', 
							'protected' => true,
							'published' => 1
						)
					);
				break;
				
				default:
				break;
			}

			if (!empty($data)) {
				$this->insertData($event['create'], $data);
			}
		}
	}
		
/**
 * Pages table
 */
	public $pages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'internal_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'page_heading' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'content' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 150, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),	
		'path' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 255, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'layout' => array('type' => 'string', 'null' => false, 'default' => 'default', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'plugin' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 75, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'controller' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 75, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'action' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 75, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'extra' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 75, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'protected' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'exclude_sitemap' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'password' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'published' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'extra_header_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'extra_footer_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'schema_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'override_title_format' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'super_admin' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1), 
			'parent_id' => array('column' => 'parent_id', 'unique' => 0), 
			'lft' => array('column' => 'lft', 'unique' => 0), 
			'rght' => array('column' => 'rght', 'unique' => 0), 
			'slug' => array('column' => 'slug', 'unique' => 0),
			'path' => array('column' => 'path', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

}