<?php
/**
 * CmsDynamicValidationBehavior core class
 *
 * Change validation array on the fly.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsDynamicValidationBehavior.html
 * @package      Cms.Model.Behavior
 * @since        Pyramid CMS v 1.0
 */
class CmsDynamicValidationBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 * 
 * - exclude: HABTM relationships to exclude during validation, if any
 */
	protected $_defaults = array();

/**
 * Original validation rules - used to restore validation if needed.
 */
	protected $_original = array();

/**
 * Configuration method.
 *
 * @param object $Model
 * @param array $config
 * @return boolean
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $config);
		$this->_original[$Model->alias] = $Model->validate;
		return true;
	}

/**
 * Dynamically adjust the validation array for a model. 
 *
 * Can optionally specify an _import key, which will import rules from the 
 * base validation array.
 *
 * @param string $key
 * @throws CakeException
 * @return boolean
 */
	public function setValidation(Model $Model, $key = null) {
		if (!$key) {
			$Model->validate = $this->_original[$Model->alias];
			return true;
		}

		// Allow for "none" to skip validation altogether.
		if ($key == 'none') {
			$Model->validate = array();
			return true;
		}
		
		$key = 'validate' . ucfirst($key);
		
		$classVars = get_class_vars($Model->alias);
		if (!in_array($key, $classVars)) {
			throw new CakeException("DynamicValidationBehavior: attempting to access non-existent validation array");
		}

		$rules = $Model->$key;

		if (isset($rules['_import'])) {
			foreach ($rules['_import'] as $import) {
				$rules[$import] = $this->$_original[$Model->alias][$import];
			}
			unset($rules['_import']);
		}

		$Model->validate = $rules;

		return true;
	}

}