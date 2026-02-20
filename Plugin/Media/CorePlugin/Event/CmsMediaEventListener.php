<?php
App::uses('CmsMediaEventListener', 'Event');
/**
 *
 * CmsMediaEventListener class
 *
 * Event Listener for the Media plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsMediaEventListener.html
 * @package		 Cms.Core.Plugin.Media.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsMediaEventListener extends CmsEventListener {

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Behavior.Copyable.beforeCopy' => array(
			'callable' => 'onBeforeCopy'
		),
		'Plugin.remove' => array(
			'callable' => 'onPluginRemove'
		),
		'Model.saveAll' => array(
			'callable' => 'cleanAttachments',
			'priority' => 9999
		)
	);

/**
 * Helpers to load
 */
	protected $_helpers = array(
		'Media.MediaVersion',
		'Media.Media'
	);

/**
 * Copyable behavior listener - sets the "file" array so that the attachment 'transfers' the file
 * properly and executes the save.
 *
 * @param object event
 * @return void
 */
	public function onBeforeCopy($event) {
		$data = $event->data['data'];
		$Model = $event->data['Model'];

		$assocs = $this->_mediaAssociations($Model);
		if (!$assocs) {
			return;
		}

		foreach ($assocs as $assoc => $className) {
			if (!isset($data[$assoc])) {
				continue;
			}

			if (isset($data[$assoc][0])) {
				foreach ($data[$assoc] as $key => $val) {
					if (isset($val['path']) && !empty($val['path'])) {
						$data[$assoc][$key]['file'] = MEDIA_TRANSFER . $val['path'];
					}
				}
			} else {
				if (isset($data[$assoc]['path']) && !empty($data[$assoc]['path'])) {
					$data[$assoc]['file'] = MEDIA_TRANSFER . $data[$assoc]['path'];
				} 
			}
		}

		$event->result = Hash::merge((array)$event->result, $data);
	}

/**
 * Plugin removal listener - deletes AttachmentVersion records whose 'model' value matches a model
 * in the plugin.
 *
 * @param object event
 * @return void
 */
	public function onPluginRemove($event) {
		$models = CmsPlugin::models($event->data['plugin']);
		ClassRegistry::init('Media.AttachmentVersion')->deleteAll(array('model' => $models));
	}

/**
 * Does a couple of things during the Model.saveAll event:
 * - Removes empty attachments to avoid erroneous validation errors.
 * - Deletes any attachments marked as 'delete' and removes them from the array as well
 *
 * @param object event
 * @return void
 */
	public function cleanAttachments($event) {
		$data = $event->data['data'];
		$Model = $event->data['Model'];

		$assocs = $this->_mediaAssociations($Model);
		if (!$assocs) {
			return;
		}

		if (empty($event->result)) {
			$event->result = $data;
		}

		foreach ($assocs as $assoc => $className) {
			if (!isset($data[$assoc])) {
				continue;
			}

			if (isset($data[$assoc][0])) {
				foreach ($data[$assoc] as $key => $val) {
					if (isset($val['delete']) && $val['delete'] == 1) {
						$Model->{$assoc}->delete($val['id']);
						unset($event->result[$assoc][$key]);
					} else if (ClassRegistry::init($className)->isEmptyFile($val)) {
						unset($event->result[$assoc][$key]);
					} 
				}
			} else {
				if (isset($data[$assoc]['delete']) && $data[$assoc]['delete'] === 1) {
					$Model->{$assoc}->delete($data[$assoc]['id']);
					unset($event->result[$assoc]);
				} else if (ClassRegistry::init($className)->isEmptyFile($data[$assoc])) {
					unset($event->result[$assoc]);
				} 
			}

			if (array_key_exists($assoc, $event->result) && empty($event->result[$assoc])) {
				unset($event->result[$assoc]);
			}
		}
	}

/**
 * Used by the Model.saveAll event callbacks - finds the Media.Attachment associations of $Model.
 *
 * @param object Model
 * @return array
 */
	protected function _mediaAssociations($Model) {
		$assocs = array();
		$keys = array('hasMany', 'hasOne');
		foreach ($keys as $assoc) {
			foreach ($Model->{$assoc} as $key => $val) {
				if (!isset($val['className'])) {
					continue;
				}

				if ($val['className'] == 'Media.Attachment') {
					$assocs[$key] = $val['className'];
					continue;
				}

				list($plugin, $className) = pluginSplit($val['className']);
				App::uses($plugin . 'AppModel', $plugin . '.Model');
				App::uses($className, $plugin . '.Model');
				if (class_exists($className) && in_array('Attachment', class_parents($className))) {
					$assocs[$key] = $val['className'];
				}			
			}
		}
		return $assocs;
	}

}