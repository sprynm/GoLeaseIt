<?php
/**
 * CmsAttachmentVersion class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAttachmentVersion.html
 * @package		 Cms.Plugin.Media.Model 
 * @since		 Pyramid CMS v 1.0
 */
class CmsAttachmentVersion extends MediaAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Versioning.SoftDelete'
	);

/**
 * Crop types
 */	
	public $cropTypes = array(
		array(
			'name' => 'fit',
			'display_name' => 'Fit Inside',
			'description' => 'Resizes media proportionally keep both sides within given dimensions'
		),
		array(
			'name' => 'fitInsideBG',
			'display_name' => 'Fit Inside w/bg',
			'description' => 'Resizes media proportionally keep both sides within given dimensions and allows you to set the padding (back ground) colour.'
		),
		array(
			'name' => 'fitOutside',
			'display_name' => 'Fit Outside',
			'description' => 'Resizes media proportionally keeping smaller side within corresponding dimensions'
		),
		array(
			'name' => 'fitCrop',
			'display_name' => 'Crop & Fit',
			'description' => 'Resizes media proportionally keep both sides within given dimensions'
		),
		array(
			'name' => 'zoom',
			'display_name' => 'Zoom',
			'description' => 'Enlarges media proportionally by factor of 2'
		),
		array(
			'name' => 'zoomCrop',
			'display_name' => 'Crop & Zoom',
			'description' => 'First crops an area (given by dimensions and enlarged by factor of 2) out of the center of the media, then resizes that cropped area to given dimensions'
		),
		array(
			'name' => 'crop',
			'display_name' => 'Crop',
			'description' => 'Crops media to provided dimensions'
		),
	);

/**
 * Image convert types
 */
	public $imageConvertTypes = array(
		'image/jpeg',
		'image/png'
	);
	
/**
 * Validation array
 */
	public $validate = array(
		'width' => array(
			'rule' => array('range', 1, 10001),
			'message' => 'Please enter a number between 1 and 10000.'
		),
		'height' => array(
			'rule' => array('range', 1, 10001),
			'message' => 'Please enter a number between 1 and 10000.'
		),
		'compression' => array(
			'rule' => array('range', 0, 9.9),
			'message' => 'The compression value must be less than 10 and at least 0. The larger the number the more compression (0 for none)',
			'allowEmpty' => true
		)
	);

/**
 * Gets attachment versions for $model and optionally $foreignKey and formats them in preparation
 * for image regeneration.
 * 
 * @param string model
 * @param integer foreign key
 * @param string group
 * @return array
 */
	public function findForRegen($model, $foreignKey = null, $group = null, $version = null, $formatVersions = true) {
		//
		$conditions = array('AttachmentVersion.model' => $model);
		//
		$conditions['AttachmentVersion.foreign_key']	= $foreignKey ? $foreignKey : null;
		//
		if ($group) {
			$conditions['AttachmentVersion.group']	= $group;
		}
		//
		if ($version) {
			$conditions['AttachmentVersion.name']	= $version;
		}
		//
		$conditions['AttachmentVersion.deleted']	= false;
		//
		$versions = $this->find('all', array(
			'conditions' => $conditions
		));
		//if model is a CcustomFieldValue and there were no versions found then attempt to find the versions belonging to the associated CustomField
		if (empty($versions) && $model == 'CustomFieldValue'){
			//find the custom field value and determine the CustomField id
			$customFieldValue = ClassRegistry::init('CustomFields.CustomFieldValue')->findById($foreignKey);
			if (!empty($customFieldValue['CustomFieldValue']['custom_field_id'])){
				return $this->findForRegen('CustomField', $customFieldValue['CustomFieldValue']['custom_field_id'], $group, $version, $formatVersions);
			}
		}
		
		if ($formatVersions) {
			$versions = $this->formatVersions($versions);
		}
		
		return $versions;
	}

/**
 * Formats versions
 *
 * @var array
 * @return array
 */
	public function formatVersions($data) {
		$versions = array();
		foreach ($data as $key => $val) {
			$versions[$val[$this->alias]['name']] = array(
				'convert' => array(
					$val[$this->alias]['convert']
					//since the image might have had transparent pixels we need to specify the new background color
					, !empty($val[$this->alias]['bgcolour']) ? $val[$this->alias]['bgcolour']: '#ffffff'
				),
				$val[$this->alias]['type'] => array(
					$val[$this->alias]['width']
					, $val[$this->alias]['height']
					, !empty($val[$this->alias]['bgcolour']) ? $val[$this->alias]['bgcolour']: '#ffffff'
					, !empty($val[$this->alias]['gravity']) ? $val[$this->alias]['gravity']: 'center'
					,
				)
			);
			//if there's a valid compression value set then insert the compress instruction last
			if ( !empty($val[$this->alias]['compression']) && floatval($val[$this->alias]['compression']) < 10 &&  floatval($val[$this->alias]['compression']) > 0 ){
				$versions[$val[$this->alias]['name']]['compress'] = floatval($val[$this->alias]['compression']);
			}
		}

		return $versions;
	}
	
/**
 * Deletes cached version file after versions are saved.
 *
 * @param	boolean $created
 * @return	boolean
 */
	public function afterSave($created) {
		Cache::delete('dynamic_media_versions', '_cake_core_');
				
		return true;
	}
	
/**
 * Strip out empty versions before model save. If a version has no name, then 
 * it gets deleted.
 *
 * @param	object	$Model
 * @return	boolean
 */
	public function beforeSave($options = array()) {
		if (array_key_exists('name', $this->data[$this->alias]) && !$this->data[$this->alias]['name']) {
			$this->data = array();
		}
		
		return true;
	}
}
