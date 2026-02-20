<?php
/**
 * EmailFormsSchema class
 *
 * Schema information for the EmailForms plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classEmailFormsSchema.html
 * @package		 Cms.Plugin.EmailForms.Config.Schema 
 * @since		 Pyramid CMS v 1.0
 */
class EmailFormsSchema extends CmsSchema {

	public $name = 'EmailForms';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $email_form_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_form_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rank' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $email_form_submissions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'email_form_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'data' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);
	
	public $email_forms = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'recipient' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'use_recipient_list' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'recipient_list_label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'recipient_list_lft' => array('type' => 'integer', 'null' => true, 'default' => 0),
		'subject_template' => array('type' => 'string', 'null' => true, 'default' => '%website_name% %form_name% Inquiry from %name%', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'content_template' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'auto_response_enabled' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'auto_response_subject_template' => array('type' => 'string', 'null' => true, 'default' => '%website_name% confirmation for %form_name%', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'auto_response_content_template' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'attach_file' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'math_captcha' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'success_text' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'success_title' => array('type' => 'string', 'null' => false, 'default' => 'Thank You', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'submit_button_text' => array('type' => 'string', 'null' => false, 'default' => 'Send', 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'cc' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'bcc' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'super_admin' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'submit_button_onclick' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'redirect_page_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $email_form_recipients = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'email_form_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'subject_template' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'content_template' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'auto_response_subject_template' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'auto_response_content_template' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'redirect_page_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		//JSON array of fields (IDs) which should display while this option is selected
		'displayed_fields' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'rank' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);
}