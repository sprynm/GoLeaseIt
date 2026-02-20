<?php
/**
 * CmsEmailFormGroup class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormGroup.html
 * @package		 Cms.Plugin.EmailForms.Model  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormGroup extends EmailFormsAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Sortable',
		'Copyable'
	);

/**
 * Default order
 */
	public $order = 'EmailFormGroup.rank ASC';

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
		'EmailFormField' => array(
			'className' => 'CustomFields.CustomField',
			'foreignKey' => 'foreign_key',
			'conditions' => array('EmailFormField.model' => 'EmailFormGroup'),
			'dependent' => true,
			'order' => 'EmailFormField.rank ASC, EmailFormField.id ASC'
		)
	);

/**
 * Returns a default form field group, for use with a new form.
 *
 * @param boolean $emailField If true, will also add an email address field to the group
 * @return array
 */
	public function addDefault($emailField = false) {
		$default = $this->create();
		$default[$this->alias]['name'] = 'New Group';

		if ($emailField) {
			$default[$this->alias]['EmailFormField'][] = array(
				'type' => 'email',
				'options' => null,
				'name' => 'email_address',
				'label' => 'Email Address',
				'default' => null,
				'required' => 1,
				'display_label' => 1,
				'validate' => 'email',
				'validate_message' => 'Please enter a valid email address.',
				'model' => 'EmailFormGroup',
				'rank' => 0
			);
		}
		
		return $default[$this->alias];
	}

/**
 * Blocks deletion of a group if there are no others.
 *
 * @return	boolean
 */
	public function beforeDelete($cascade = true) {
		return $this->canBeDeleted($this->id);
	}

/**
 * Returns true only if there is another group in the system other than $id.
 *
 * @param	integer $id
 * @return	boolean
 */
	public function canBeDeleted($id) {
		$found = $this->findById($id);
		$count = $this->find('count', array(
			'conditions' => array(
				'id !=' => $found['EmailFormGroup']['id']
			)
		));
	
		return $count > 0;
	}

}
