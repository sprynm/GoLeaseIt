<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsCustomFieldsEventListener class
 *
 * Event Listener for the CustomFields plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCustomFieldsEventListener.html
 * @package		 Cms.Core.Plugin.CustomFields.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsCustomFieldsEventListener extends CmsEventListener {

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Plugin.remove' => array(
			'callable' => 'onPluginRemove'
		)
	);

/**
 * Helpers
 */
	protected $_helpers = array(
		'CustomFields.CustomField'
	);

/**
 * Plugin removal listener - deletes CustomField and CustomFieldValue records whose 'model' value matches a model
 * in the plugin.
 *
 * @param object event
 * @return void
 */
	public function onPluginRemove($event) {
		$models = CmsPlugin::models($event->data['plugin']);
		ClassRegistry::init('CustomFields.CustomField')->deleteAll(array('model' => $models));
		ClassRegistry::init('CustomFields.CustomFieldValue')->deleteAll(array('model' => $models));
	}

}