<?php
/**
 * CmsEmailFormSubmission class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormSubmission.html
 * @package		 Cms.Plugin.EmailForms.Model  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormSubmission extends EmailFormsAppModel {

/**
 * Attached behaviors
 */
	public $actsAs = array(
		'Versioning.SoftDelete',
		'ReCaptcha'
	);

/**
 * belongsTo associations
 */
	public $belongsTo = array(
		'EmailForm' => array(
			'className' => 'EmailForms.EmailForm',
			'foreignKey' => 'email_form_id'
		)
	);
	
/**
 * hasMany associations
 */
 
	public $hasMany = array(
		'Image' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 

			'conditions' => array(
				'Image.model' => 'Product', 
				'Image.group' => 'Image'
			), 
			'dependent' => true, 
			'order' => 'Image.rank ASC, Image.id ASC'
		), 
		'Document' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Document.model' => 'Product', 
				'Document.group' => 'Document'
			), 
			'dependent' => true, 
			'order' => 'Document.rank ASC, Document.id ASC'
		),
		'File'=>array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'File.model' => 'Product', 
				'File.group' => 'File'
			), 
			'dependent' => true, 
			'order' => 'File.rank ASC, File.id ASC'
		),
	);
	

/**
 * Dynamic validation, populated in beforeValidate() when a form is submitted.
 */
	public $validateDynamic = array();

/**
 * Converts the JSON data to an array.
 *
 * @see Model::afterFind()
 */
	public function afterFind($results, $primary = false) {
		if (isset($results[0]) && isset($results[0][$this->alias])) {
			foreach ($results as $i => $result) {
				if (!isset($result[$this->alias]['data'])) {
					continue;
				}
				$data = json_decode($result[$this->alias]['data'], true);
				foreach ($data as $key => $val) {
					if (array_key_exists($key, $result[$this->alias])) {
						unset($data[$key]);
					} else if (substr($key, 0, 1) == '_') {
						unset($data[$key]);
					} else if ($key == 'security_code') {
						unset($data[$key]);
					}
				}
				$results[$i][$this->alias]['data'] = $data;
			}
		}

		return $results;
	}
 
/**
 * Generates dynamic validation rules. Always returns true, though throws a CakeException if it doesn't
 * get the fields that it expects.
 *
 * @throws CakeException
 * @return boolean
 */
	public function beforeValidate($options = array()) {
		if (!isset($this->data[$this->alias]['_dynamic']) || $this->data[$this->alias]['_dynamic'] === false) {
			return true;
		}

		if (!isset($this->data[$this->alias]['email_form_id']) || empty($this->data[$this->alias]['email_form_id'])) {
			throw new CakeException("CmsEmailFormSubmission::beforeValidate: missing " . $this->alias . " email_form_id or field for dynamic form.");
		}

		$form = $this->EmailForm->find('first', array(
			'conditions' => array('EmailForm.id' => $this->data[$this->alias]['email_form_id']),
			'contain' => array('EmailFormGroup' => array('EmailFormField'), 'EmailFormRecipient')
		));

		if (!$form) {
			throw new CakeException("CmsEmailFormSubmission::beforeValidate: EmailForm not found.");
		}

		$this->buildValidationRules($form);
		$this->setValidation('dynamic');

		return true;
	}

/**
 * Serializes the form data into a json object before saving, which will go into the 'data' column.
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		$this->data['EmailFormSubmission']['data'] = json_encode($this->data['EmailFormSubmission']);
		return true;
	}

/**
 * Constructs validation rules for the form based on the fields in $form.
 *
 * @param	array	$form
 * @return	array
 */
	public function buildValidationRules($form) {
		//
		$fields = $this->_buildFields($form);
		// set the form's recipient based on the data
		$selectedRecipient = $this->_selectedRecipient($this->data, $form);
		//
		if (!empty($selectedRecipient['displayed_fields']) && !in_array('All', $selectedRecipient['displayed_fields'])) {
			//
			$specificFields = $selectedRecipient['displayed_fields'];
		}
		//
		$validation = array();
		//
		foreach ($fields as $field) {

			if ($field['type'] == 'file') {
				//
				$validation[$field['name']] = array(
					'rule'		=> 'validateUpload'
					, 'required'	=> $field['required']
					, 'message'	=> $field['validate_message']
					,
				);
			// ($field['type'] == 'file')
			}

			//skip fields not required for the selected recipient and remove them from the data since
			//it would be misleading to submit a value that the user no longer sees on their screen
			if (!empty($specificFields) && !in_array($field['name'], $specificFields)) {
				unset($this->data[$this->alias][$field['name']]);
				continue;
			}
			//skip fields that don't need to be validated 
			if (!$field['required']) {
				//
				continue;
			}
			//
			$validation[$field['name']] = array(
					'rule' => $field['validate'], 
					'required' => $field['required'], 
					'message' => $field['validate_message'], 
					'allowEmpty' => $field['required'] == 1 ? false : true
				);
			//since the following field types can only be validated using 'multiple' set that as the method
			if ( in_array( $field['type'], array( 'datetime', 'date', 'checkbox', 'radio') ) ) {
				$validation[$field['name']]['rule'] = 'multiple';
				//don't need to validate optional fields of these types
				if (empty($field['required'])) {
					unset( $validation[$field['name']] );
				}
			}
		}
		// 
		/*if (!Configure::read('Settings.ReCaptcha.use_recaptcha')) {
			// No reCAPTCHA
			$this->validateDynamic = $validation;
		} else {
			// Uses merge because $validateDynamic may have been modified somewhere else
			$this->validateDynamic = Hash::merge($this->validateDynamic, $validation);
		}*/
		//
		$this->validateDynamic = $validation;
		//
		return $this->validateDynamic;
	}
	
/**
 * builds an array of fields from the email form groups.
 *
 * @param	array	$form
 * @return	array
 */
	protected function _buildFields($form) {
		$fields = array();
		foreach ($form['EmailFormGroup'] as $group) {
			$fields = array_merge($fields, $group['EmailFormField']);
		}

		return $fields;
	}
	
/**
 * Returns the EmailFormRecipient value that matches the EmailFormSubmission's recipient_id
 */
	protected function _selectedRecipient($data, $form) {
		// try the selected recipients redirect page first if there's one set
		if (!empty($form['EmailForm']['use_recipient_list']) && !empty($form['EmailFormRecipient'])) {			
			//if the $recipient is out of array bounds then select the first one
			$recipientMap = Hash::combine($form['EmailFormRecipient'], '{n}.id', '{n}');
			if ( empty($data['EmailFormSubmission']['recipient']) || empty($recipientMap[$data['EmailFormSubmission']['recipient']]) ){
				$recipient = $form['EmailFormRecipient'][0]['id'];
			} else {
				$recipient = $data['EmailFormSubmission']['recipient'];
			}
			
			return $recipientMap[$recipient];
		}
		
		return false;
	}
	
/***
 * Checks validity of file uploads against a variety of criteria. 
 *  @param array $check =
 *	array(
 *		'file_upload' => array(
 *			'name' => 'sweetride.jpg',
 *			'type' => 'image/jpeg',
 *			'tmp_name' => '/tmp/phpJ69zMr',
 *			'error' => (int) 0,
 *			'size' => (int) 48408
 *		)
 *	)
 * @return bool
 */
	public function validateUpload ($check) {
		//
		$formFieldName		= key($check);
		//
		$file			= end($check);
		// 
		$allowedMimeTypes	= array('image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/acrobat', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// if file is not required and wasn't uploaded
		if( $file['error'] == 4 && $this->validate[key($check)]['required'] == false ) {
			return true;
		}
		// if file is required but wasn't uploaded
		if( $file['error'] == 4 && $this->validate[key($check)]['required'] == true ) {
			return false;
		}
		// if file uploaded with errors
		if($file['error'] != 0 ) {
			return false;
		}
		// if filetype is not allowed
		if( in_array($file['type'], $allowedMimeTypes) == false ) {
			return false;
		}
		//
		$mimeType = exec('file -b --mime-type ' . escapeshellarg($file['tmp_name']), $foo, $returnCode);
		// if mimetype is not in allowedMimeTypes array - very suspenders and belt, very redundant, whatever.
		if( in_array($mimeType, $allowedMimeTypes) == false ) {
			return false;
		}
		// if filetype has been spoofed
		if( $returnCode != 0 )  {
			return false; //there was an error on the command line
		}
		// [1]
		if($mimeType != 'application/msword' && $mimeType != $file['type']) {
			return false;
		}
		// if file is too big.
		if( $file['size'] > 8388608 ) { // 8 megabytes
			return false;
		}
		// get final home of file.
		$path = WWW_ROOT . 'uploads' . DS . 'email_forms' . DS . Inflector::slug($formFieldName); 
		// make sure there's a dir to save it in.
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
		}
		// replace spaces in filename with underscores
		$file['name'] = preg_replace("/\s+/", "_", $file['name']);
		// prevent filename conflicts.
		if(file_exists($path . DS . $file['name'])) {
			$file['name'] = date('His') . '_' . $file['name'];
		}
		// update the filename that gets emailed out
		$this->data[get_class($this)][$formFieldName]['name'] = $file['name'];
		// save file.
		if(!move_uploaded_file($file['tmp_name'], $path . DS . $file['name'])) {
			return false;
		}
		//
		return true;
	}
}