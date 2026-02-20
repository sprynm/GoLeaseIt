<?php
/**
 * Validate HABTM Behavior core class file
 *
 * Adds proper model validation for verifying the presence of a minimum
 * number of records in a HABTM relationship.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsValidateHabtmBehavior.html
 * @package      Cms.Model.Behavior
 * @since        Pyramid CMS v 1.0
 */
class CmsValidateHabtmBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 * 
 * - exclude: HABTM relationships to exclude during validation, if any
 */
	protected $_defaults = array(
		'exclude' => array()
	);

/**
 * Configuration method.
 *
 * @param object $Model
 * @param array $config
 * @return boolean
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $config);
		return true;
	}

/**
 * beforeValidate
 * 
 * Converts any HABTM to a validation-ready form.
 *
 * @param object $Model
 * @return boolean
 */
	public function beforeValidate(Model $Model) {
		foreach ($Model->hasAndBelongsToMany as $key => $val) {
			if (isset($Model->data[$key][$key]) && !in_array($key, $this->settings[$Model->alias]['exclude'])) {
				$Model->data[$Model->alias][$key] = $Model->data[$key][$key];
				if (is_array($Model->data[$Model->alias][$key])) {
					$Model->data[$Model->alias][$key] = array_unique($Model->data[$Model->alias][$key]);
				}
			}
		}

		return true;
	}

/**
 * beforeSave
 *
 * Remove any nested HABTM data that may have been inserted
 * in the beforeValidate callback.
 *
 * @param object $Model
 * @return boolean
 */
	public function beforeSave(Model $Model) {
		foreach ($Model->hasAndBelongsToMany as $key => $val) {
			if (isset($Model->data[$Model->alias][$key]) && !in_array($key, $this->settings[$Model->alias]['exclude'])) {
				unset($Model->data[$Model->alias][$key]);
			}
		}
		
		return true;
	}

/**
 * Validation rule that ensures a HABTM field has at least $min value(s) selected.
 *
 * @param object $Model
 * @param array $check
 * @param integer $min - optional
 */
	public function minHabtm(Model $Model, $check, $min = 1) {
		$value = array_values($check);
		$value = $value[0];
		
		if (!$value || !is_array($value)) {
			return false;
		}

		return (count($value) >= $min);
	}
}
