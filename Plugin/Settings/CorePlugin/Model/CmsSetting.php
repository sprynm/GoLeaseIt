<?php
App::uses('String', 'Utility');

/**
 * Settings model file
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsSetting.html
 * @link		 https://radarhill.lighthouseapp.com/projects/44179/site-and-plugin-settings
 * @package      Cms.Plugin.Settings.Model  
 * @since        Pyramid CMS v 1.0
 */
class CmsSetting extends SettingsAppModel {
	
/**
 * Display field
 */
	public $displayField = 'key';

/**
 * 
 */
	public $actsAs	= array(
				'Sortable'
				,
			);

/**
 * hasMany associations
 */
	public $hasMany = array(
		'Image' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Image.model' => 'Setting', 
				'Image.group' => 'Image'
			), 
			'dependent' => true, 
			'order' => 'Image.rank ASC, Image.id ASC'
		), 
		'Document' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Document.model' => 'Setting', 
				'Document.group' => 'Document'
			), 
			'dependent' => true, 
			'order' => 'Document.rank ASC'
		)
	);

  public $virtualFields = array(
    'tinyMceOption' => 'REPLACE(CONCAT(UPPER(SUBSTRING(SUBSTRING_INDEX(Setting.key, \'.\', -1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(Setting.key, \'.\', -1) FROM 2))), \'_\', \' \')'
  );

  
/**
 * Whitelist for methods that can be executed to load setting options.
 */
	protected $_allowedMethods = array(
		'VrebListings.Model.VrebListingFeed.searchResultOrder' // A list of ordering fields for VREB Listings search results
	);

/**
 * Finds settings for admin index. Optionally restricted by a plugin name.
 *
 * @param string $plugin Optional
 * @return array
 */
	public function findForEdit($plugin = null) {
		$conditions = array('Setting.display' => 1);
		
		if (!AccessControl::inGroup('Super Administrator')) {
			$conditions['Setting.super_admin'] = 0;
		}

		if ($plugin) {
			$conditions['Setting.key LIKE'] = $plugin . '.%';
		}

		$rawSettings = $this->find('all', array(
			'conditions' => $conditions,
			'order' => array( 'Setting.rank ASC', 'Setting.id ASC' )
			, 'contain' => array('Image', 'Document',)
		));

		$settings = array();
		foreach ($rawSettings as $key => $val) {
			// Parse a possible dynamic method execution for options
			if (!empty($val['Setting']['method']) && in_array($val['Setting']['method'], $this->_allowedMethods)) {
				$options = $this->optionsFromMethod($val['Setting']['method']);
				if (!$options) {
					continue;
				} else {
					$val['Setting']['options'] = $options;
				}
			} else {
				// Account for possible dynamic options
				$options = $val['Setting']['options'];

				//
				if($val['Setting']['type'] == 'image' || $val['Setting']['type'] == 'document')
				{

				} elseif (preg_match('/^\{(.*)\}/', $options, $match)) {
					// Match contains the entire string in [0] and the matched part in [1] - we just want the latter.
					$options = $this->parseDynamicOptions($match[1]);
					$val['Setting']['options'] = $options;
				} else if (!empty($options)) {
					$split = String::tokenize($options);
					foreach ($split as $key => $setting) {
						$split[$key] = ltrim(rtrim($setting, ')'), '(');
					}
					$val['Setting']['options'] = array_combine($split, $split);
				}
			}

			$settings = Hash::insert($settings, $val['Setting']['key'], $val);
		}

		return $settings;
	}

/**
 * Loads a key/value list of settings and returns for loading into Configure.
 *
 * @return array
 */
	public function findForLoad() {
		$settings = $this->find('list', array(
			'fields' => array('key', 'value')
			, 'sort' => false
		));
		
		return $settings;
	}
  
/**
 * Finds for TinyMCE event listener
 *
 * @return array
 */
	public function findForTinyMce() {
		$blocks = $this->find('all', array(
			'fields' => array('key', 'tinyMceOption'),
			'conditions' => array('Setting.key LIKE \'Site.Contact.%\'', 'Setting.value != \'\''),
			'published' => true,
			'cache' => true, 
			'sort' => false
		));
		
		return $blocks;
	}
  
/**
 * Saves a Setting's value when passed the key.
 *
 * @param string $key
 * @param mixed $value
 * @return boolean
 */
	public function saveByKey($key, $value) {
		$setting = $this->findByKey($key);
		if (!$setting) {
			return false;
		}

		$this->id = $setting['Setting']['id'];
		return $this->saveField('value', $value);
	}

/**
 * Saves $data to the attachments table should an image or document be included in the form submission.
 *
 * @param array $data
 * @return boolean
 */
	public function saveImage($data) {
	// Check first to see if we need to do anything.
		if(!isset($data['Image']) || empty($data['Image']))
		{
		// No image uploaded bail-out.
			return;
		}
	// Assign the array to a variable.
		foreach($data['Image'] AS $anImage)
		{
		//
			$thisData[]	= array('id' => $anImage['id'], 'alternative' => $anImage['alternative'] );
		}
	// Initiate the model for saving purposes.
		$Model = new Model(
				array(
					'table'		=> 'attachments'
					, 'name'	=> 'Image'
					, 'alias'	=> 'Image'
					,
				)
		);
	// Save.
		return $Model->saveMany($thisData, array('validate' => false));
	}
}