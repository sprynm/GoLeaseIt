<?php
/**
 * CmsPrototypeAppModel class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeAppModel.html
 * @package      Cms.Plugin.Prototype.Model 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeAppModel extends AppModel {

/**
 * Adds a prototype_instance_id condition to find queries if necessary, i.e. if $this->instanceId
 * is set as a model property.
 *
 * @param string type
 * @param array options
 */
	public function find($type = 'first', $options = array()) {
		if (isset($this->instanceId)) {
			if (!isset($options['conditions'])) {
				$options['conditions'] = array();
			}
			$options['conditions'][$this->alias . '.prototype_instance_id'] = $this->instanceId;
		}

		return parent::find($type, $options);
	}
}
