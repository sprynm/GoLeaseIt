<?php
/**
 * CmsEmailFormHelper class
 *
 * Does most of the work in constructing and displaying an email form. After parsing special
 * email form requirements, passes off most of the heavy lifting to the Form helper.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormHelper.html
 * @package		 Cms.Plugin.EmailForms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormHelper extends AppHelper {

/**
 * Helpers to load
 */

	public $helpers = array(
		'CustomField.CustomField',
		'Form' => array('className' => 'AppForm'),
		'Html' => array('className' => 'AppHtml'),
		'Text',
		'ReCaptcha'
	);

/** 
 * The currently loaded email form, set in open().
 */
	protected $_emailForm = null;
	
/** 
 * The currently loaded email form's options parameter, set in open().
 */
	protected $_formOptions = null;
	
/**
 * Closes a form.
 *
 * @param An array of options to pass to the Form helper
 * @return string
 */
	public function close($options = array()) {
		$this->_verifyForm();
		
		$options = array_merge(
			array('label' => $this->_emailForm['EmailForm']['submit_button_text'], 'class' => array('submit-button')),
			$options
		);

		if (isset($this->_emailForm['EmailForm']['submit_button_onclick']) && !empty($this->_emailForm['EmailForm']['submit_button_onclick'])) {
			$options['onclick'] = $this->_emailForm['EmailForm']['submit_button_onclick'];
		}
		
		return $this->Form->end($options);
	}

/**
 * Outputs a form input field. Can either take an array of form data or a field name. Hands off most of the 
 * work to the CustomField helper.
 *
 * @param mixed field - either an array or a string (name)
 * @param options an optional array of HTML attributes/options
 * @throws CakeException
 * @return string
 */
	public function inputField($field, $options = array()) {
		if (!is_array($field)) {
			$field = $this->_findField($field);
		}

		if (!$field['name']) {
			throw new CakeException('CmsEmailFormHelper::field: invalid field passed.');
		}

		if($field['type'] == 'checkbox' && $field['required']) {
			$options['hiddenField']	= 'false';
		}

		return $this->CustomField->inputField($field, $options);
	}
	
/**
 * Outputs a complete fieldset from an EmailFormGroup, with contained input fields.
 *
 * @param array the form group
 * @return string
 */
	public function fieldset($group, $options = array()) {
		$this->_verifyForm();
		
		if (isset($group['EmailFormGroup'])) {
			if (isset($group['EmailFormField'])) {
				$fields = $group['EmailFormField'];
			}
			$group = $group['EmailFormGroup'];
		} else {
			$fields = $group['EmailFormField'];
		}
		
		$options = array_merge(
			array('id' => Inflector::slug(strtolower($group['name']))),
			$options
		);
		
		$fieldOutput = '';
		
		//if the recipient list lft is negative then put it at the start of the group with that inverted id
		if (!empty($this->_emailForm['EmailForm']['recipient_list_lft']) && $this->_emailForm['EmailForm']['recipient_list_lft'] == (-1) * $group['id'] ) {
			$fieldOutput .= $this->recipients();
		}
		
		
		foreach ($fields as $i => $field) {
			$fieldOutput .= $this->inputField($field['name']);
			
			//if the recipient list lft is equal to this field's id then insert it after the input
			if (!empty($this->_emailForm['EmailForm']['recipient_list_lft']) && $this->_emailForm['EmailForm']['recipient_list_lft'] == $field['id'] ) {
				$fieldOutput .= $this->recipients();
			}
		}
		
		$legend = $this->Html->tag('legend', $group['name']);
		
		$output = $this->Html->tag('fieldset', $legend . $fieldOutput, $options);
		
		return $output;
	}

/**
 * A wrapper to display all fieldsets in a form already open.
 *
 * @return string
 */
	public function fieldsets() {
		$this->_verifyForm();
		
		$output = '';
		
		
		foreach ($this->_emailForm['EmailFormGroup'] as $group) {
			$output .= $this->fieldset($group);
		}
		
		//if the recipient list lft is empty or 0 then just put it after the other form elements
		if (empty($this->_emailForm['EmailForm']['recipient_list_lft'])){			
			$output .= $this->recipients();
		}
		
		return $output;
	}
	
/** 
 * Return the selectbox for the recipient list as a string
 */
	public function recipients(){
		$output = '';
		//if there are EmailFormRecipients associated with this form then show the select box for those
		if (!empty($this->_emailForm['EmailForm']['use_recipient_list']) && !empty($this->_emailForm['EmailFormRecipient'])){
			$label = null;
			if (!empty($this->_emailForm['EmailForm']['recipient_list_label'])){
				$label = $this->_emailForm['EmailForm']['recipient_list_label'];
			}
			//don't include the actual email address in the form output, only grab that server side
			$options = Hash::combine($this->_emailForm['EmailFormRecipient'], "{n}.id", "{n}.name");
			
			$output .= $this->Form->input('recipient', array(
				'label' => $label
				, 'type' => 'select'
				, 'options' => $options
			));
		}
		
		return $output;
	}
	
	public function reCaptcha() {
		return $this->ReCaptcha->field('EmailFormSubmission');
	}

/**
 * Opens up a new form. Also inserts a hidden field with the form ID and a dynamic marker, used
 * form generating the form validation when submitted.
 *
 * @param Email form array
 * @param An array of options to pass to the Form helper
 * @return string
 */
//this function fires when you view a page that contains a form, in the public side.
	public function open($emailForm, $options = array()) {
		$this->_emailForm = $emailForm;
		
		$options = array_merge(array('type' => 'file'), $options);
		
		$options = array_merge(
			array('url' => $this->request->here),
			$options
		);
		
		if (empty($options['id'])){
			$options['id'] = 'EmailFormSubmission';
		}
		
		//check for duplicate ids and make sure that they are unique, assumes that the form ID does not end with a number
		$formIds = Configure::read('EmailFormHelper.usedFormIds');
		
		if (empty($formIds[$options['id']])){
			$formIds[$options['id']] = 1;
		} else {
			$formIds[$options['id']]++;
			$options['id'] .= $formIds[$options['id']];
		}
		
		Configure::write('EmailFormHelper.usedFormIds', $formIds);
		
		$this->_formOptions = $options;
		//slightly different options for the form creation where the form id needs to have ViewForm appended to it
		$createOptions			= $options;
		//
		$createOptions['id']		.= "ViewForm"; 
		// 
		$createOptions['aria-label']	= $emailForm['EmailForm']['name'];
		//
		$output = $this->Form->create('EmailFormSubmission', $createOptions);
		//
		$output .= $this->Form->input('email_form_id', array(
			'value' => $this->_emailForm['EmailForm']['id'],
			'type' => 'hidden',
			'id' => $options['id'].'EmailFormId'
		));
		
		$output .= $this->Form->input('_dynamic', array(
			'value' => true,
			'type' => 'hidden',
			'id' => $options['id'].'Dynamic'
		));
		
		return $output;
	}

/**
 * Looks for an EmailFormField array in the $_emailForm with a name matching $name.
 *
 * @param string name
 * @return array
 */
	protected function _findField($name) {
		$field = Hash::extract($this->_emailForm['EmailFormGroup'], '{n}.EmailFormField.{n}[name=' . $name . ']');
		if ($field) {
			return $field[0];
		}		
		return null;
	}

/**
 * Verifies that an email form has been opened with open() and set as $_emailForm.
 * If not, throws an exception.
 *
 * @throws CakeException
 * @return boolean
 */
	protected function _verifyForm() {
		if (!$this->_emailForm) {
			throw new CakeException("CmsEmailFormHelper::_verifyForm: a helper method has been called without proper email form initialization.");
		}
		
		return true;
	}
	
	
/**
 * Creates a unique field id based off of a field name prepending the current email form id if there is one
 *
 * @param string fieldName The name of the field to give a unique id for
 * @return string
 */	
	public function uniqueFieldId($fieldName) {
		
		//make sure the the input names are unique across this page prefixed with the form ID if there is one
		if (!empty($this->_formOptions['id']) ){			
			$formId = $this->_formOptions['id'];
		} else {
			$formId = '';
		}
		
		//set the field name
		$fieldId =  $formId . Inflector::camelize($fieldName);
		
		$usedFieldIds = Configure::read("EmailFormHelper.formFieldIds");
		
		if (empty($usedFieldIds[$fieldId])) {
			$usedFieldIds[$fieldId] = 0;
			$fieldOptions['id'] = $fieldId;
		} else {
			//separate the duplicate count with an underscore
			//since the names are getting converted to camel case for the id (before verifiying) there will be no conflicts
			$fieldOptions['id'] = $fieldId . '_' . ($usedFieldIds[$fieldId] + 1);
		}
		
		$usedFieldIds[$fieldId]++;
		
		//store the used fields for the next field to check
		Configure::write("EmailFormHelper.formFieldIds", $usedFieldIds);
		
		return $fieldId;
	}
	
}