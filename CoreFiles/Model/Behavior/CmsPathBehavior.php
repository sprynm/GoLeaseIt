<?php
/**
 * CmsPathBehavior class
 * 
 * Works in tandem with with the SluggableBehavior to create full paths to models (i.e the slug for
 * the record and all of its parents). The model should also be using TreeBehavior, or some variant 
 * thereof that uses the left/right MPTT tree design paradigm.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPathBehavior.html
 * @package      Cms.Model.Behavior
 * @since        Pyramid CMS v 1.0
 */
class CmsPathBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 * 
 * - slugField: the field that the behavior should use to generate the path.
 * - excludeField: if not null, path info will not update if the record has this field and field is not empty.
 */
	protected $_defaults = array(
		'slugField' => 'slug',
		'excludeField' => null
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
 * Updates path information.
 *
 * @param object $Model
 * @param boolean $created
 * @return void
 */
	public function afterSave(Model $Model, $created) {
		$item = $Model->find('first', array(
			'conditions' => array(
				$Model->primaryKey => $Model->id
			),
			'theme' => false
		));

		// This catches items that have been soft-deleted.
		if (!$item) {
			return;
		}

		$this->_updatePath($Model, $item);
		$this->_updateChildPaths($Model, $item);
	}

/**
 * Updates the paths of all children of $item. Called by afterSave.
 *
 * @param object $Model
 * @param array the record
 * @return boolean
 */
	protected function _updateChildPaths($Model, $item) {
		$children = $Model->children($item[$Model->alias][$Model->primaryKey]);
		if (empty($children)) {
			return false;
		}

		foreach ($children as $key => $val) {
			if (isset($children[$key]['ThemedModel'])) {
				unset($children[$key]['ThemedModel']);
			}
			$this->_updatePath($Model, $val);
		}
		
		return true;
	}
/**
 * Updates the path of a record based on its slug field and its parents' slugs. Called by afterSave.
 *
 * @param object $Model
 * @param array the record
 * @return boolean
 */
	protected function _updatePath($Model, $item) {
		if (!isset($item[$Model->alias])) {
			return false;
		}

		// Creates excludeField and slugField variables
		extract($this->settings[$Model->alias]);

		if (isset($item[$Model->alias][$excludeField]) && $item[$Model->alias][$excludeField]) {
			return false;
		}

		$path = array();
		
		$parents = $Model->getPath(
			$item[$Model->alias][$Model->primaryKey],
			array($Model->primaryKey, $slugField)
		);

		foreach ((array)$parents as $parent) {
			$slug = trim($parent[$Model->alias][$slugField], '/');
			$path[] = $slug;
		}

		if (empty($path)) {
			$path = '';
		} else if (count($path) > 1) {
			$path = implode('/', $path);
		} else {
			$path = $path[0];
		}

		$path = ltrim($path, '/');

		// Only save if the path has changed
		if ($path == $item[$Model->alias]['path']) {
			return true;
		}

		$data = array($Model->alias => array(
			$Model->primaryKey => $item[$Model->alias][$Model->primaryKey],
			'path' => $path
		));
		
		return $Model->save($data, array('validate' => false, 'callbacks' => false));
	}

}