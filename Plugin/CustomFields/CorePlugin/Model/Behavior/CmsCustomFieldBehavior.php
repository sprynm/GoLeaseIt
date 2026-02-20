<?php
/**
 * CmsCustomFieldBehavior class
 *
 * Primarily responsible for validating custom field values in a form submission.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCustomFieldBehavior.html
 * @package		 Cms.Plugin.CustomFields.Model.Behavior
 * @since		 Pyramid CMS v 1.0
 */
class CmsCustomFieldBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings:
 *
 * - foreignKey: corresponds to foreign_key in custom_fields table
 * - foreignModel: corresponds to model in custom_fields table
 * - modelName: model to use for custom field
 * - searchFields: basic model fields to search in addition to custom fields, if searching is enabled
 */
	protected $_defaults = array(
		'foreignKey' => false,
		'foreignModel' => null,
		'modelName' => 'CustomFields.CustomField',
		'searchFields' => array('name')
	);
	
/**
 * Whether custom validation should be enabled. Can be disabled manually.
 */
	protected $_validationEnabled = true;

/**
 * Intializer
 *
 * @param   object $Model
 * @param   array $config
 * @throws CakeException
 * @return  void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $settings);
		if (!$this->settings[$Model->alias]['foreignModel']) {
			$this->settings[$Model->alias]['foreignModel'] = $Model->name;
		}

		$Model->hasMany['CustomFieldValue'] = array(
			'className' => 'CustomFields.CustomFieldValue', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'CustomFieldValue.model' => $this->settings[$Model->alias]['foreignModel']
			), 
			'dependent' => true
		);
	}

/**
 * Adds custom field search fields to $filterArgs for $Model before a search is executed. Called in the
 * controller when a search is submitted.
 *
 * @param object $Model
 * @param array $query Query data
 * @param array optional array of arguemnts for the custom field find: 'model', 'foreignKey', 'group'.
 * @return void
 */
	public function addDynamicSearch($Model, $query, $fieldOptions = array()) {
		$defaults = array(
			'model' => $this->settings[$Model->alias]['foreignModel'],
			'foreignKey' => null,
			'group' => null
		);
		$fieldOptions = Hash::merge($defaults, $fieldOptions);
		
		if (!isset($Model->filterArgs)) {
			$Model->filterArgs = array();
		}

		$customFields = ClassRegistry::init($this->settings[$Model->alias]['modelName'])->findForModel($fieldOptions['model'], $fieldOptions['foreignKey'], $fieldOptions['group']);
		$customFields = Hash::combine($customFields, '{n}.CustomField.name', '{n}');
		foreach ($query as $key => $val) {
			if (isset($Model->filterArgs[$key])) {
				continue;
			}
			if (!isset($customFields[$key])) {
				continue;
			}
			$Model->filterArgs[$key] = array('name' => $key, 'type' => 'subquery', 'method' => 'customSearchFilter', 'field' => $this->settings[$Model->alias]['foreignModel'] . '.id');
		}
	}

/**
 * Sets up validation rules if applicable.
 *
 * @return boolean
 */
	public function beforeValidate(Model $Model) {
		if (!$this->_validationEnabled) {
			return true;
		}

		$fields = $Model->findCustomFields($Model);
		if (!$fields) {
			return true;
		}

		$rules = $this->_buildValidationRules($Model, $fields);
		$Model->validate = array_merge($Model->validate, $rules);
		return true;
	}

/**
 * Adds a find() subquery for a model search on a custom search field.
 *
 * @param object $Model
 * @param array $data Query data
 * @return array
 */
	public function customSearchFilter($Model, $data, $field) {
		if (!$data[$field['name']]) {
			return array();
		}

		$fieldName = $field['name'];
		
		$Custom = ClassRegistry::init('CustomFields.CustomFieldValue');
		$Custom->Behaviors->attach('Search.Searchable');

		$query = $Custom->getQuery('all', array(
			'conditions' => array(
				'CustomFieldValue.custom_field_id' => $data[$fieldName . '_id'],
				'CustomFieldValue.model' => $this->settings[$Model->alias]['foreignModel'],
				'CustomFieldValue.val LIKE' => '%' . $data[$fieldName] . '%'
			),
			'fields' => array('CustomFieldValue.foreign_key')
		));

		return $query;
	}

/**
 * Toggles $_validationEnabled, which, if false, disables the custom field validation.
 *
 * @param object Model
 * @param boolean
 * @return boolean
 */
	public function customValidation(Model $Model, $enabled = true) {
		$this->_validationEnabled = $enabled;
		return $this->_validationEnabled;
	}

/**
 * Finds custom fields for $Model. Can be overriden by the model for special cases.
 *
 * @param object $Model
 * @return array
 */
	public function findCustomFields(Model $Model) {
		$foreignKey = null;
		if ($this->settings[$Model->alias]['foreignKey'] == true && isset($this->data[$Model->alias][$Model->primaryKey])) {
			$foreignKey = $this->data[$Model->alias][$Model->primaryKey];
		}
		return ClassRegistry::init($this->settings[$Model->alias]['modelName'])->findForModel($this->settings[$Model->alias]['foreignModel'], $foreignKey);
	}

/**
 * Generates simple search (Google-style) conditions by searching across basically all product fields
 * as well as the custom fields.
 *
 * @param object $Model
 * @param string $search 
 * @return array an array of find() conditions
 */
	public function simpleSearch($Model, $data = array()) {
		$fields = $this->settings[$Model->alias]['searchFields'];
		
		$conditions = array('OR' => array());
		foreach ($fields as $field) {
			$conditions['OR'][$Model->alias . '.' . $field . ' LIKE'] = '%' . $data['search'] . '%';
		}

		$Custom = ClassRegistry::init('CustomFields.CustomFieldValue');
		$Custom->Behaviors->attach('Search.Searchable');

		$query = $Custom->getQuery('all', array(
			'conditions' => array(
				'CustomFieldValue.model' => $this->settings[$Model->alias]['foreignModel'],
				'CustomFieldValue.val LIKE' => '%' . $data['search'] . '%'
			),
			'fields' => array('CustomFieldValue.foreign_key')
		));
		
		$conditions['OR'][] = $Model->alias . '.' . $Model->primaryKey . ' IN (' . $query . ')';
		return $conditions;
	}

/**
 * Builds validation rules from $fields in preparation for saving a form with custom fields.
 *
 * @param array fields
 * @return array
 */
	protected function _buildValidationRules($Model, $fields) {
		$fieldKey = explode('.', $this->settings[$Model->alias]['modelName']);
		if (count($fieldKey) == 2) {
			$fieldKey = $fieldKey[1];
		} else {
			$fieldKey = $fieldKey[0];
		}

		$validation = array();
		
		foreach ($fields as $field) {
			//
			if (
				!isset($field[$fieldKey]['validate'])
				||
				empty($field['CustomField']['validate'])
				||
				in_array($field['CustomField']['type'], array('image','document','file'))
				||
				!$field[$fieldKey]['required']
			) {
				continue;
			}
			
			//checkboxes can only have the 'At least one option is selected' validation type so override the value
			if (in_array($field[$fieldKey]['type'], array('checkbox', 'select'))) {
				$field[$fieldKey]['validate'] = 'multiple';
			} else if ($field[$fieldKey]['type'] == 'radio') {
				//radios just need to be set to notEmpty
				$field[$fieldKey]['validate'] = 'notEmpty';
			}

			$validation[$field[$fieldKey]['name']] = array(
				'rule' => $field[$fieldKey]['validate'], 
				'required' => $field[$fieldKey]['required'], 
				'message' => $field[$fieldKey]['validate_message'], 
				'allowEmpty' => $field[$fieldKey]['required'] == 1 ? false : true
			);
		}

		return $validation;
	}
	
}