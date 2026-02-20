<?php
/**
 * CmsExpandableBehavior class
 *
 * Allows for extra model record data to be stored in the extra_fields table.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsExpandableBehavior.html
 * @package		 Cms.Plugin.CustomFields.Model.Behavior
 * @since		 Pyramid CMS v 1.0
 */
class CmsExpandableBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Configuration method.
 *
 * @param object $Model
 * @param array $config
 * @return boolean
 */
	public function setup(Model $Model, $config = array()) {
		$base = array(
			'schema' => $Model->schema()
		);
		
		if (isset($settings['with'])) {
			$conventions = array(
				'foreignKey', 
				$Model->hasMany[$settings['with']]['foreignKey']
			);
			return $this->settings[$Model->alias] = array_merge($base, $conventions, $settings);
		}
		
		if (!isset($settings['validateAgainst'])) {
			$settings['validateAgainst'] = $Model->alias . 'Field';
		}
		
		foreach ($Model->hasMany as $assoc => $option) {
			if (strpos($assoc, 'Field') !== false) {
				$conventions = array(
					'with' => $assoc, 
					'foreignKey' => $Model->hasMany[$assoc]['foreignKey']
				);
				$this->settings[$Model->alias] = array_merge($base, $conventions, !empty($settings) ? $settings : array());
			}
		}
				
		return true;
	}

/**
 * Adds query contains for the extra fields.
 *
 * @param	object	$Model
 * @param	array	$queryData
 * @return	mixed
 */
	public function beforeFind(Model $Model, $queryData) {
		if (!isset($queryData['contain']) || !in_array($this->settings[$Model->alias]['with'], (array)$queryData['contain'])) {
			if (!isset($queryData['contain'])) {
				$queryData['contain'] = array();
			}
			$queryData['contain'][] = $this->settings[$Model->alias]['with'];
		}
		
		if (!isset($queryData['conditions']['extraFields']) || !is_array($queryData['conditions']['extraFields'])) {
			return $queryData;
		}
		
		$limiterName = $this->settings[$Model->alias]['with'] . 'Limiter';
		$joins = array();
		foreach ($queryData['conditions']['extraFields'] as $key => $val) {
			$joins[] = array(
				'table' => 'custom_field_values', 
				'alias' => $limiterName, 
				'type' => 'INNER', 
				'foreignKey' => false, 
				'conditions' => array(
					$limiterName . '.key = \'' . $key . '\'', 
					$limiterName . '.val = \'' . $val . '\'', 
					$limiterName . '.model = \'' . $Model->alias . '\'', 
					$limiterName . '.foreign_key = ' . $Model->alias . '.id'
				)
			);
		}
		$queryData['joins'] = $joins;
		unset($queryData['conditions']['extraFields']);
		return $queryData;
	}
	
/**
 * Formats extra fields after find.
 *
 * @param	object	$Model
 * @param	array	$results
 * @param	boolean $primaryOPTIONAL
 * @return	array
 */
	public function afterFind(Model $Model, $results, $primary = true) {
		extract($this->settings[$Model->alias]);
		if (!Set::matches('/' . $with, $results)) {
			return;
		}
		
		foreach ($results as $i => $item) {
			foreach ($item[$with] as $field) {
				if (isset($results[$i][$Model->alias][$field['key']])) {
					//skip this field value if it's already been set to something else for the model
					continue;
				}
				
				$results[$i][$Model->alias][$field['key']] = $field['val'];
				
				//grab the associated attachments for the field
				$fullField = $Model->{$with}->find('first', array('conditions'=>array("$with.id"=>$field['id']), 'contain'=>array('Attachment')));
				$customField = ClassRegistry::init("CustomFields.CustomField")->find('first', array('conditions'=>array("CustomField.id"=>$field['custom_field_id'])));
				if (!empty($customField['CustomField']['type'])) {
					if (in_array($customField['CustomField']['type'], array('image', 'file', 'document'))) {
						$results[$i][$Model->alias][$field['key']] = $field;
						$results[$i][$Model->alias][$field['key']]['Attachment'] = $fullField['Attachment'];
					} else if ($customField['CustomField']['type'] == 'checkbox' && json_decode($field['val'])) {
						//decode the json value if this field is a set of checkboxes
						$results[$i][$Model->alias][$field['key']] = json_decode($field['val']);
					}
				}
			}
		}
		return $results;
	}
	
/**
 * Handles extra field saving.
 *
 * @param	object	$Model
 * @return	void
 */
	public function afterSave(Model $Model, $created) {
		if (!$Model->data) {
			return;
		}
		//
		extract($this->settings[$Model->alias]);
		//
		$fields = array_diff_key($Model->data[$Model->alias], $schema);
		$id = $Model->id;

		foreach ($fields as $key => $val) {
			// Skip *_id fields because they are designed to be custom_field_id values in the custom_field_values table.
			// unless there's a *_id_id field too
			if (substr($key, -3) == '_id' && !isset($fields[$key.'_id'])) {
				continue;
			}
			
			$attachment = null;
			
			//if the value is an array it probably has an attachment associated
			if (is_array($val)) {
				if (!empty($val['Attachment'])) {
					$attachment = $val['Attachment'];
					$val = '';
				} else {
					//encode the array value
					$val = json_encode($val);	
				}
			}
			if ($this->settings[$Model->alias]['validateAgainst'] !== false) {
				if (App::import('Model', $validateAgainst)) {
					$validator = new $validateAgainst();
					$validField = $validator->find('count', array(
						'conditions' => array(
							$validateAgainst . '.name' => $key
						)
					));
					if ($validField < 1) {
						continue;
					}
				}
			}
			//
			$field = null;
			// create the empty field value for saving
			$customFieldValue = array('CustomFieldValue' => array());
			// Add a possible custom_field_id value.
			if (isset($fields[$key . '_id'])) {
				$customFieldValue['CustomFieldValue']['custom_field_id'] = $fields[$key . '_id'];
				//find the field value based on the custom_field_id rather than the key
				$fieldValue = $Model->{$with}->find('first', array(
					'fields' => array(
							$with . '.id'
						), 
						'conditions' => array(
							$with . '.' . $foreignKey => $id,
							$with . '.model' => $Model->alias,
							$with . '.custom_field_id' => $customFieldValue['CustomFieldValue']['custom_field_id']
						), 
						'recursive' => -1
					));
			} else {
				//only find by the field value by the key if the custom field id field was not present
				$fieldValue = $Model->{$with}->find('first', array(
					'fields' => array(
						$with . '.id'
					), 
					'conditions' => array(
						$with . '.' . $foreignKey => $id, 
						$with . '.key' => $key,
						$with . '.model' => $Model->alias
					), 
					'recursive' => -1
				));
			}

			//set fields for saving
			if ($fieldValue) {
				$customFieldValue['CustomFieldValue']['id'] = $fieldValue[$with]['id'];
			} else {
				//special cases for prototype items
				$CustomField = ClassRegistry::init('CustomFields.CustomField');
				//
				if ($Model->alias == 'VrebListing') {
					//
					$CustomField->setDataSource('vreb_rets');
				}
				//
				if ($Model->name == 'PrototypeItem' || $Model->name == 'PrototypeCategory') {
					$field = $CustomField->find('first', array(
						'conditions'=>array(
							'CustomField.name' => $key
							, 'CustomField.model' => 'PrototypeInstance'
							, 'CustomField.group' => $Model->alias
							, 'CustomField.foreign_key' => $Model->data[$Model->alias]['prototype_instance_id']
						)
					));
				} else {
					//find the customfield and it's id
					$field = $CustomField->find('first', array(
						'conditions'=>array(
							'CustomField.name' => $key
							, 'CustomField.model' => $Model->alias
							//allow for default fields for a model by intersecting results with 0 valued foreign keys 
							, 'OR' => array(
								array( 'CustomField.foreign_key' => $id )
								, array( 'CustomField.foreign_key' => '0' )
								, array( 'CustomField.foreign_key IS NULL' )
							)
						)
					));
				}

				if (empty($field)){
					//since there's no CustomField associated with this key, model, foreign_key trio skip it
					continue;
				}
				
				//if the field value wasn't found we need to initialize the custom_field_id, foreign_key, key, and model values for it
				$customFieldValue[$Model->{$with}->alias]['custom_field_id'] = $field[$CustomField->alias]['id'];
				$customFieldValue['CustomFieldValue'][$foreignKey] = $id;
				$customFieldValue['CustomFieldValue']['key'] = $key;
				$customFieldValue['CustomFieldValue']['model'] = $Model->alias;

			}
			
			$customFieldValue['CustomFieldValue']['val'] = $val;
			
			if (!empty($attachment)) {
				//set the attachment for the field if there is one
				$customFieldValue['CustomFieldValue']['Attachment'] = $attachment;
			}

			$Model->{$with}->saveAll($customFieldValue, array('deep'=>true));
		}
	}
}