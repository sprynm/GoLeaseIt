<?php
/**
 * SettingsSchema class
 *
 * Schema information for the Settings plugin.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classSettingsSchema.html
 * @package      Cms.Plugin.Settings.Config.Schema  
 * @since        Pyramid CMS v 1.0
 */
class SettingsSchema extends CmsSchema {

	public $name = 'Settings';

	public function before($event = array()) {
		return true;
	}

/**
 * Inserts some core settings.
 */
	public function after($event = array()) {
		if (isset($event['create'])) {
			switch ($event['create']) {
				case 'settings':
					$data = array(
						array('key' => 'Site.name', 'value' => 'Pyramid CMS', 'title' => 'Website Name', 'description' => 'The name of the website.', 'super_admin' => 1),
						array('key' => 'Site.title_separator', 'value' => ' - ', 'title' => '', 'description' => 'One or more characters to separate the different parts of the page title.', 'super_admin' => 1),
						array('key' => 'Site.common_head_title', 'value' => ' ', 'title' => '', 'description' => 'Appears in the title of every page.', 'super_admin' => 1),
						array('key' => 'Site.status', 'value' => '1', 'title' => 'Site Online', 'description' => 'Whether the site is publicly visible.', 'type' => 'checkbox', 'super_admin' => 1),
						array('key' => 'Site.maintenance_mode', 'value' => '0', 'title' => 'Maintenance Mode', 'description' => 'Whether the site is displaying the Maintenance Mode layout.', 'type' => 'checkbox', 'super_admin' => 1),
						array('key' => 'Site.copyright_name', 'value' => '', 'title' => '', 'description' => 'The name to appear in the footer copyright disclaimer', 'super_admin' => 1),
						array('key' => 'Site.copyright_start_year', 'value' => date('Y'), 'title' => '', 'description' => 'The starting year of the bottom copyright disclaimer.', 'super_admin' => 1),
						array('key' => 'Site.email', 'value' => 'admin@changeme.com', 'title' => '', 'description' => 'General email address for the site.', 'super_admin' => 1),
						array('key' => 'Site.Contact.name', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.address', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.city', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.province_state', 'value' => '', 'title' => 'Province / State', 'description' => ''),
						array('key' => 'Site.Contact.postal_zip', 'value' => '', 'title' => 'Postal / Zip Code', 'description' => ''),
						array('key' => 'Site.Contact.country', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.phone', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.toll_free', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.fax', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Contact.email', 'value' => '', 'title' => '', 'description' => ''),
						array('key' => 'Site.Google.maps_api_key', 'value' => 'AIzaSyDEdJuShLnz7fHy043t_vG1h7KdnmeHkSU', 'title' => 'Google maps API key', 'description' => 'The API key for Google maps for this domain.', 'super_admin' => 1),
						array('key' => 'Site.Google.gtm_association_code', 'value' => '', 'title' => 'Google Tag Manager ID', 'description' => 'Google Tag Manager association code.', 'type' => 'text', 'super_admin' => 1),
						array('key' => 'Site.Bing.verification_code', 'value' => '', 'title' => '', 'description' => 'The content of the meta verification tag supplied by Bing.', 'super_admin' => 1),
						array('key' => 'Site.default_pagination_limit', 'value' => 24, 'super_admin' => 1),
						array('key' => 'Site.SocialMedia.facebook', 'type' => 'url'),
						array('key' => 'Site.SocialMedia.twitter', 'type' => 'url'),
						array('key' => 'Site.SocialMedia.instagram', 'type' => 'url'),
						array('key' => 'Site.SocialMedia.youtube', 'type' => 'url', 'title' => 'YouTube'),
						array('key' => 'Site.SocialMedia.linkedin', 'type' => 'url', 'title' => 'LinkedIn'),
						array('key' => 'Site.Footer.industry_identifier'),
						array('key' => 'Site.Footer.portfolio_link', 'type' => 'url')
					);
					$this->insertData('settings', $data);
				break;
			}
		}
	}

/**
 * settings table
 */
	public $settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'rank' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'key' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => 'text', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'editable' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'super_admin' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'options' => array('type' => 'text', 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'method' => array('type' => 'string', 'length' => 100, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'display' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'key' => array('column' => 'key', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

}