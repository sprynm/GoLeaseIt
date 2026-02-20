<?php
/**
 * CmsEmailForm class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailForm.html
 * @package		 Cms.Plugin.EmailForms.Model  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormRecipient extends EmailFormsAppModel {
	
	public $actsAs = array(
		'Sortable'
	);

/**
 * Default order
 */
	public $order = 'EmailFormRecipient.rank ASC';
	
/**
 * Validate array
 */
	public $validate = array(
		'email_address' => array(
			'rule' => array('multipleEmails', false, null),
			'required' => true,
			'message' => 'Please enter one or more emails for this entry'
		)
	);
	
/**
 * Remove spaces from comma separated email addresses
 */
	public function beforeValidate(){
		if (!empty($this->data[$this->alias]['email_address'])){
			$this->data[$this->alias]['email_address'] = str_replace(" ", "", $this->data[$this->alias]['email_address']);
		}
		
		return true;
	}
	
/**
 * Convert the custom recipient displayed fields array into a json string if it's set
 */
	public function beforeSave() {
		if (isset($this->data[$this->alias]['displayed_fields'])){
			if (empty($this->data[$this->alias]['displayed_fields']) || !is_array($this->data[$this->alias]['displayed_fields']) || in_array('All', $this->data[$this->alias]['displayed_fields'])) {
				$this->data[$this->alias]['displayed_fields'] = '';
			} else {
				$this->data[$this->alias]['displayed_fields'] = json_encode($this->data[$this->alias]['displayed_fields']);
			}
		}
		
		return parent::beforeSave();
	}
/**
 * Convert the displayed_fields back into an array
 *
 */
	public function afterFind($results, $primary = false) {
		$results = parent::afterFind($results, $primary);
		foreach ($results as $key => $result) {			
			if (!empty($result[$this->alias]['displayed_fields'])){
				$results[$key][$this->alias]['displayed_fields'] = json_decode($result[$this->alias]['displayed_fields'], true);
			}
			//if the displayed fields are still empty than set as an array with All being the only value
			if (empty($results[$key][$this->alias]['displayed_fields'])){
				$results[$key][$this->alias]['displayed_fields'] = array('All');
			}
		}
		
		return $results;
	}
}
