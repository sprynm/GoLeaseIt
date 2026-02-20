<?php
/**
 * CmsCustomField class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCustomField.html
 * @package		 Cms.Plugin.CustomFields.Model	
 * @since		 Pyramid CMS v 1.0
 */
class CmsCustomField extends CustomFieldsAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Sortable' => array(
			'group' => 'model'
		)
	);

/**
 * hasMany associations
 */
	public $hasMany = array(
		'CustomFieldValue' => array(
			'className' => 'CustomFields.CustomFieldValue',
			'foreign_key' => 'custom_field_id',
			'dependent' => true
		)
	);

/**
 * Pre-save data, used to track changes in afterSave for changing the key names in custom_field_values.
 */
	protected $_original = array();

/**
 * Strip out empty versions before model save. If a version has no name, then 
 * it gets deleted.
 *
 * @param	object	$Model
 * @return	boolean
 */
	public function beforeSave($options = array()) {
		//if there's no name then delete it
		if (isset($this->data[$this->alias]['name']) && !$this->data[$this->alias]['name']){
			$this->data = array();
		} else {
			//copy the name to the label if the label was empty then slugify the name
			if ( isset($this->data[$this->alias]['label']) && $this->data[$this->alias]['label'] == "" ) {
				$this->data[$this->alias]['label'] = $this->data[$this->alias]['name'];
			}
			//if this is a new field
			if (empty($this->data[$this->alias]['id']) && !in_array($this->data[$this->alias]['model'], array('Product'))) {
				//remove invalid characters from the name
				$this->data[$this->alias]['name'] = Inflector::slug(strtolower($this->data[$this->alias]['name']));
			}
			
			//checkboxes can only have the 'At least one option is selected' validation type so override the value
			if (in_array($this->data[$this->alias]['type'], array('checkbox', 'select'))) {
				$this->data[$this->alias]['validate'] = 'multiple';
			} else if (in_array($this->data[$this->alias]['type'], array('radio', 'image', 'document', 'file'))) {
				//radios and file types just need to be set to notEmpty
				//the type for files don't really matter but for consistency set them to be the same validation type
				$this->data[$this->alias]['validate'] = 'notEmpty';
			}
			
			//for the aforementioned types clear the placeholder field since it doesn't do anything for them
			if (in_array($this->data[$this->alias]['type'], array('checkbox', 'select', 'radio', 'image', 'document', 'file'))) {
				$this->data[$this->alias]['placeholder'] = "";
			}
			// 
			if (isset($this->data[$this->alias]['type']) && in_array($this->data[$this->alias]['type'], array('file', 'document'))) {
				//
				$this->data[$this->alias]['validate']	= 'validateUpload';
			}
		}
				
		if ($this->id) {
			$this->_original = $this->findById($this->id);
		}
		
		return true;
	}

/**
 * Changes the keys in custom_field_values if the name has changed.
 *
 * @see Model::afterSave
 */
	public function afterSave($created) {
		if ($created) {
			return;
		}
		
		$record = $this->findById($this->id);
		if (!$record || !isset($record[$this->alias])) {
			return;
		}

		if ($record[$this->alias]['name'] != $this->_original[$this->alias]['name']) {
			$this->CustomFieldValue->updateAll(
				array('CustomFieldValue.key' => '"' . $record[$this->alias]['name'] . '"'),
				array('CustomFieldValue.custom_field_id' => $record[$this->alias]['id'])
			);
		}
	}

/**
 * Returns an array of custom fields for $model and optionally $foreignKey and $group.
 *
 * @param string model
 * @param integer foreign key
 * @param string group
 * @return array
 */
	public function findForModel($model, $foreignKey = null, $group = null) {
		$conditions = array(
			$this->alias . '.model' => $model,
			$this->alias . '.foreign_key' => $foreignKey
		);
		if ($group) {
			$conditions[$this->alias . '.group'] = $group;
		}
		
		//merge in model templated fields
		$conditions['OR'] = array(
			array( $this->alias . '.foreign_key' => $conditions[$this->alias . '.foreign_key'] )
			, array( $this->alias . '.foreign_key' => '0' )
		);
		
		unset($conditions[$this->alias . '.foreign_key']);
		
		return $this->find('all', array(
			'conditions' => $conditions
		));
	}

}