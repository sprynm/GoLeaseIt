<?php
/**
 * CmsCustomFieldHelper class
 *
 * Aids in displaying custom fields in the admin area (primarily).
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCustomFieldHelper.html
 * @package		 Cms.Plugin.CustomFields.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsCustomFieldHelper extends AppHelper {

/**
 * Field element - can be overriden.
 */
	public $fieldElement = 'CustomFields.admin/field';

/**
 * Helpers to load
 */
	public $helpers = array(
		'Form' => array('className' => 'AppForm'),
		'Html' => array('className' => 'AppHtml'), 
		'Media' => array('className' => 'MediaHelper')
	);

/**
 * Validation types for dynamic fields (email forms, extra fields, etc).
 */
	protected $_validateTypes = array(
		'alphaNumeric' => 'Only letters and numbers',
		'date' => 'YYYY-MM-DD format',
		'validateUpload' => '.gif, .png, .jpg, pdf and .doc are allowed.',
		'decimal' => 'Decimal (at least one digit after the point)',
		'email' => 'Email',
		'money' => 'Monetary amount (with currency symbol)',
		'notEmpty' => 'Not empty',
		'numeric' => 'Numeric value only',
		'phone' => 'Phone number (North America)',
		'postal' => 'Postal/zip code (US, CA, UK, IT, DE, BE)',
		'time' => 'Time (HH:MM or [H]H:MM[a|p]m)',
		'url' => 'URL',
		'multiple' => 'At least one option selected', 
		
	);

/**
 * Displays an 'add new field' link which is designed to be handled by AJAX.
 *
 * A 'group' key in the $options array will be added to the URL as the optional grouping for the custom field.
 * The $options array can also contain a 'url' key which will override the default URL for adding a field.
 *
 * @param string alias of the custom field, including any parent aliases.
 * @param string model name, added to the url
 * @param options Optional HTML attributes
 * @return string
 */
	public function addNew($alias, $model, $options = array()) {
		$options = array_merge(
			array('class' => 'add-new-field'),
			$options
		);
		
		$options['class'] .= ' ' . $alias;
		
		$group = null;
		if (isset($options['group'])) {
			$group = $options['group'];
			unset($options['group']);
		}

		$url = array('plugin' => 'custom_fields', 'controller' => 'custom_fields', 'action' => 'new_field', $model, $group, 'admin' => true);
		if (isset($options['url'])) {
			$url = $options['url'];
			unset($options['url']);
		}
		
		return $this->Html->link('Add a field', $url, $options);
	}

/**
 * Outputs an admin field given data in $field, prepending the name with $alias and $count. Calls
 * on an element as defined in $this->_adminElement.
 *
 * @param array options
 * @throws CakeException
 * @return string
 */
	public function adminField($options = array()) {
		extract($options);
		
		if (!isset($alias) || !isset($field) || !isset($count) || !isset($model)) {
			throw new CakeException("CmsCustomFieldHelper::adminField: one of alias, field, count, or model missing in field options.");
		}
		//
		if (!isset($group)) {
			$group = null;
		}
		//
		$output = $this->_View->element($this->fieldElement, array(
			'alias' => $alias,
			'field' => $field,
			'count' => $count,
			'foreignModel' => $model,
			'group' => $group,
		));
		
		return $output;
	}

/**
 * Outputs multiple custom field inputs for an array of custom fields in $data. Each field's name
 * is prepended with $alias.
 * 
 * @param array data
 * @param string alias
 * @param string model Foreign model
 * @param string group Optional group
 * @return string
 */
	public function adminFields($data, $alias, $model, $group = null) {
		//
		$output = '';
		//
		foreach ($data as $key => $field) {
			$output .= $this->adminField(array(
				'alias' => $alias,
				'field' => $field,
				'count' => $key,
				'model' => $model,
				'group' => $group,
			));
		}
		return $output;
	}

/**
 * Returns an array of custom fields for $model and optionally $foreignKey and $group.
 *
 * @param string model
 * @param integer foreign key
 * @param string group
 * @return array
 */
	public function fieldList($model, $foreignKey = null, $group = null) {
		return ClassRegistry::init('CustomFields.CustomField')->findForModel($model, $foreignKey, $group);
	}

/**
 * Just a wrapper to inputField that converts WYSIWYG fields to regular text input fields.
 *
 * @see CmsCustomFieldHelper::inputField
 * @return string
 */
	public function searchField($field, $options = array()) {
		if (isset($field['type']) && $field['type'] == 'wysiwyg') {
			$field['type'] = 'text';
		}
		return $this->inputField($field, $options);
	}
	
/**
 * Outputs a form input field for a custom field, meant to populate the custom_field_values table.
 *
 * @param array the field
 * @param array optional HTML attributes
 * @return string
 */
	public function inputField($field, $options = array()) {
		if (!is_array($field)) {
			return null;
		}
		
		// Generate the label.
		$label = null;
		$legend = null;
		if (!$field['display_label']) {
			$label = false;
			$legend = false;
		} else if (!empty($field['label'])) {
			$label = $field['label'];
			$legend = $field['label']; //for radios, because they have legends instead of labels.
		} else {
			$label = Inflector::humanize($field['name']);
		}
		//allow image and document fields
		if (!empty($field['type']) && in_array($field['type'],array('image','document'))) {
			//determine the foreignkey for this customfieldvalue if there is one saved
			$fieldValue = ClassRegistry::init('CustomFields.CustomFieldValue')->find('first', array(
				'conditions' => array(
					'custom_field_id'=>$field['id']
					, 'foreign_key' => $field['foreign_key']
				)
			));
			$element = "";
			if (!empty($fieldValue['CustomFieldValue']['id'])) {					
				$model = $field['model'];
				if ($field['model']=='PrototypeInstance' && !empty($field['group'])) {
					$model = $field['group'];
				}
				$element = '<div class="input '.$field['type'].'"><label>' . $label . "</label>" . $this->_View->element(
					'Media.attachments'
					, array(
						'assocAlias'	=> $model.".".$field['name'].".Attachment"
						, 'model'	=> 'CustomFieldValue'
						, 'group'	=> ucfirst($field['type'])
						//foreign key points to the customfieldvalue
						//include in case we're in a prototypeitem
						, 'foreign_key'	=> $fieldValue['CustomFieldValue']['id']
						, 'modelId'	=> $fieldValue['CustomFieldValue']['id']
						, 'validateType' => ucfirst($field['type'])
						, 'attachmentType' => $field['type']
					)
				)."</div>";
				if ($field['type'] == 'image') {
					//if it's an image upload include a button to access the image versions
					$element .= '<div class="button-links">' . $this->Html->link('Image Versions', array('plugin'=>'media', 'controller'=>'attachment_versions','action'=>'edit', 'CustomFieldValue', $fieldValue['CustomFieldValue']['id'] )) . '</div>';				
				} 
			}	else {
				$element .= '<p>You must save this item before uploading files.</p>'
					//add a dummy value to the customfieldvalue so that it saves something to associate the uploads with
					.$this->Form->input($field['name'], array(
						'type' => 'hidden',
						'value' => $field['type']
					));
			}
			
			$element .= $this->Form->input($field['name'] . '_id', array(
				'type' => 'hidden',
				'value' => $field['id']
			));
			return $element;
		}
		//
		$labelOrg	= $label;		
		// Add the 'required' element if the label is not false and the field is required.
		if ($label !== false && $field['required']) {
			//
			$legend = $this->Form->addRequired($legend);
		} else {
			//
			$label 		.= ' <span class="optional">(Optional)</span>';
		}


		//
		$typeInfo		= $this->Form->fieldTypeInfo($field['type']);
		//
		$type			= $field['type'] == 'file' ? 'file' : $typeInfo['type'];
		//
		$fieldOptions = array(
			'label'		=> $label
			, 'type'	=> $type
			,
		);
		//
		if (strlen($field['default']) > 0) {
			//
			$fieldOptions['default']	= $field['default'];
		}
		// 
		if ($typeInfo['type'] == 'radio') {
			//
			$fieldOptions['legend']		= $label;
		}
		//
		$fieldOptions['div']	= array(
						'id'	=> 'form-' . strtolower(Inflector::slug($field['name'], '-'))
						,
					);
		//
		if (isset($field['div_css_name']) && strlen($field['div_css_name']) > 0) {
			//
			$fieldOptions['div']	= am(
							$fieldOptions['div']
							, array(
								'class'	=> $field['div_css_name']
								,
							)
						);
		}
		//
		if (isset($field['description']) && strlen($field['description']) > 0) {
			//
			$fieldOptions['description']	= $field['description'];
		}
		//
		if (isset($field['css_name']) && strlen($field['css_name']) > 0) {
			//
			$fieldOptions['class']		= $field['css_name'];
		}
		//
		if ($field['options']) {
			$tokenized = $this->Html->tokenize($field['options']);
			$fieldOptions['options'] = array_combine($tokenized, $tokenized);
		}
		//
		if (!empty($field['validate_message'])){
			$fieldOptions['data-invalid-message'] = $field['validate_message'];
		}
		//
		if ($field['type'] == 'checkbox' && $field['options']) {
			$fieldOptions['type'] = 'select';
			$fieldOptions['multiple'] = 'checkbox';
		}
		//
		if ($field['required']) {
			//
			$fieldOptions['required']	= 'required';
			//
			$fieldOptions['aria-required']	= 'true';
		}
		// Possible placeholder
		$fieldOptions['placeholder']		= !empty($field['placeholder'])
							? $field['placeholder']
							: ' ';
		//
		if ($field['type'] == 'date') {
			//
			$label				= $fieldOptions['label'];
			//
			unset($fieldOptions['label']);
			//
			$fieldOptions['placeholder']	= isset($fieldOptions['placeholder']) && strlen($fieldOptions['placeholder']) > 0
							? $fieldOptions['placeholder']
							: 'yyyy/mm/dd';
			//
			$thisId				= 'EmailFormSubmission' . Inflector::camelize($field['name']) . 'Id';
			//
			$fieldOptions['label']		= array(
								'text'	=> $label
								, 'for'	=> $thisId
								,
							);
			//
			$fieldOptions['id']		= $thisId;
			//
			$fieldOptions['type']		= 'text';
			// 
			$fieldOptions['onfocus']	= "(this.type='date')";
			// 
			$fieldOptions['onblur']		= "(this.type='text')";
		}
		//
		if (isset($typeInfo['options']) && is_array($typeInfo['options'])) {
			$fieldOptions = array_merge($fieldOptions, $typeInfo['options']);
		}
		// 
		if (isset($field['merge_content']) && strlen($field['merge_content']) > 0) {
			//
			$fieldOptions['div']	= false;
			//
			$fieldOptions['label']	= isset($field['display_label']) && $field['display_label']
						? $field['label']
						: false;
			//
			$input	= "\n" . $this->Form->input($field['name'], $fieldOptions, $options);
			//
			$input	= str_replace('[' . $field['name'] . ']', $input, $field['merge_content']);
		//
		} else {
			//
			$input	= "\n" . $this->Form->input($field['name'], $fieldOptions, $options);
		}
		//
		$input .= "\n" . $this->Form->input(
				$field['name'] . '_id'
				, array(
					'type'		=> 'hidden'
					, 'value'	=> $field['id']
					,
				)
			)
			. "\n";
		//
		return $input;
	}

/**
 * Returns a list of field keys from $fields and values from $data. Intended for use
 * in a public item view. If $fields is null then the function will look for a 
 * $customFields array in the view variables.
 *
 * @param array $data an array of data with keys matching custom field keys
 * @param array $fields an optional array of custom field model records
 * @param array $options optional HTML attributes to pass to the <ul>
 * @return string a formatted list
 */
	public function fieldValueList($data, $fields = null, $options = null) {
		if (!$fields) {
			$fields = $this->_View->viewVars['customFields'];
		}
		if (!$fields) {
			return null;
		}

		$items = array();
		foreach ($fields as $field) {
			$fieldName = $field['CustomField']['name'];

			if (!isset($data[$fieldName])) {
				continue;
			}

			$fieldValue = $data[$fieldName];
            
            if (is_array($fieldValue)) {
                // If it's an array, ensure each element is safely converted to a string
                // before imploding. This handles arrays of mixed types better.
                $stringifiedValues = array_map(function($value) {
                    // Use a simple (string) cast on each element.
                    // If an element is an object, this will rely on its __toString method.
                    return (string)$value;
                }, $fieldValue);
                
                // Join the stringified elements for display
                $fieldValue = implode(', ', $stringifiedValues);
                
            } elseif (is_object($fieldValue)) {
                // Handle objects that might not automatically cast well,
                // perhaps by checking for a __toString method or using a safe representation.
                // Fallback to a placeholder if it's a complex object without __toString
                if (method_exists($fieldValue, '__toString')) {
                    $fieldValue = (string)$fieldValue;
                } else {
                    $fieldValue = '[Complex Data]';
                }
            }

			$item = '<li class="' . Inflector::underscore(strtolower($fieldName)) . '"><span>';
			
			$item .= Inflector::humanize($fieldName) . ':</span> ' . $fieldValue . '</li>'; 
			$items[] = $item;
		}

		return $this->Html->tag(
			'ul',
			implode("\n", $items),
			$options
		);
	}

/**
 * Returns the array of $_validateTypes.
 *
 * @return array
 */
	public function validateTypes() {
		return $this->_validateTypes;
	}

}