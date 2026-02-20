<?php
/**
 * CmsSluggableBehavior class
 * 
 * Generates slugs for models.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsSluggableBehavior.html
 * @package      Cms.Model.Behavior
 * @since        Pyramid CMS v 1.0
 */
class CmsSluggableBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 * 
 * - label: the field name that contains the string used to generate the slug.
 * - slug: the name of the field used to hold the slug.
 * - separator: separator string to use for replacing non-alphanumeric characters.
 * - length: max length for a slug.
 * - overwrite: whether to overwrite a slug if <label> changed.
 */
	protected $_defaults = array(
		'label' => 'name',
		'slug' => 'slug',
		'separator' => '-',
		'length' => 100,
		'overwrite' => false
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

		// If the model doesn't have a "slug" validate setting, add it so that forward slashes can't be put in the slug manually.
		if (!isset($Model->validate['slug'])) {
			$Model->validate['slug'] = array(
				'rule' => 'noSlashes',
				'message' => 'The slug may not contain forward slashes.',
				'required' => false
			);
		}

		return true;
	}

/**
 * Gets the slug set up before save.
 *
 * @param object $Model Model about to be saved.
 * @return boolean true if save should proceed, false otherwise
 */
	public function beforeSave(Model $Model) {
		
		// No label field, or empty label - return.
		if (!$Model->hasField($this->settings[$Model->alias]['label']) || empty($Model->data[$Model->alias][$this->settings[$Model->alias]['label']])) {
			return true;
		}
		
		if(!empty($Model->data[$Model->name]['slug'])) {
			return true;
		}
		
		// No slug field - return.
		if (!$Model->hasField($this->settings[$Model->alias]['slug'])) {
			return true;
		}

		// Existing record and no overwrite - return.
		if (!empty($Model->id) && !$this->settings[$Model->alias]['overwrite']) {
			return true;
		}
	
		$slug = $this->_slug($Model, $Model->data[$Model->alias][$this->settings[$Model->alias]['label']]);

		// Search for slug collision
		$conditions = array($this->settings[$Model->alias]['slug'] . ' LIKE' => $slug . '%');
		if (!empty($Model->id)) {
			$conditions[$Model->alias . '.' . $Model->primaryKey . ' !='] = $Model->id;
		}
		
		$existing = $Model->find('all', array(
			'conditions' => $conditions,
			'fields' => array(
				$Model->primaryKey,
				$this->settings[$Model->alias]['slug']
			)
		));
		
		$matching = Set::extract($existing, '{n}.' . $Model->alias . '.' . $this->settings[$Model->alias]['slug']);
		
		// Collision!
		if (!empty($matching)) {
			$startSlug = $slug;
			$index = 1;
			
			// Keep attaching numbers until a free slug is found.
			for ($index = 1; $index > 0; $index++) {
				$newSlug = $startSlug . $this->settings[$Model->alias]['separator'] . $index;
				if (!in_array($newSlug, $matching)) {
					$slug = $newSlug;
					break;
				}
			}

		}
		
		// Add the generated slug to the model data.
		if (!empty($Model->whitelist) && !in_array($this->settings[$Model->alias]['slug'], $Model->whitelist)) {
			$Model->whitelist[] = $this->settings[$Model->alias]['slug'];
		}
		
		$Model->data[$Model->alias][$this->settings[$Model->alias]['slug']] = $slug;

		return true;
	}

/**
 * Generates a slug for $string.
 *
 * @param object Model
 * @param string $string
 * @return string
 */
	protected function _slug(Model $Model, $string) {
		return Cms::slug($string, $this->settings[$Model->alias]['separator'], $this->settings[$Model->alias]['length']);
	}

}