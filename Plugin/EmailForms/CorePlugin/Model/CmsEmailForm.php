<?php
/**
 * CmsEmailForm class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailForm.html
 * @package		 Cms.Plugin.EmailForms.Model  
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailForm extends EmailFormsAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Sluggable' => array(
			'overwrite' => true
		), 
		'Publishing.Publishable',
		'Copyable',
		'Versioning.SoftDelete'
	);
	
/**
 * hasMany associations
 */
	public $hasMany = array(
		'EmailFormGroup' => array(
			'className' => 'EmailForms.EmailFormGroup', 
			'foreignKey' => 'email_form_id', 
			'dependent' => true, 
			'order' => 'EmailFormGroup.rank ASC'
		),
		'EmailFormSubmission' => array(
			'className' => 'EmailForms.EmailFormSubmission',
			'foreignKey' => 'email_form_id',
			'dependent' => true
		),
		'EmailFormRecipient' => array(
			'className' => 'EmailForms.EmailFormRecipient',
			'foreignKey' => 'email_form_id',
			'dependent' => true, 
			'order' => 'EmailFormRecipient.rank ASC'
		)
	);

/**
 * Validate array
 */
	public $validate = array();

/**
 * Validate array for admin form
 */
	public $validateAdmin = array(
		'name' => array(
			'rule' => 'notEmpty', 
			'required' => true, 
			'message' => 'A name is required.'
		), 
		'recipient' => array(
			'rule' => array(
				'multipleEmails', 
				false, 
				null
			), 
			'required' => false,
			'allowEmpty' => true, 
			'message' => 'Please enter one or more valid email addresses.'
		),
		'subject_template' => array(
			'rule' => 'notEmpty', 
			'required' => true, 
			'message' => 'A subject template is required.'
		), 
		'content_template' => array(
			'rule' => 'notEmpty', 
			'required' => true, 
			'message' => 'A content template is required.'
		), 
		'submit_button_text' => array(
			'rule' => 'notEmpty', 
			'required' => true, 
			'message' => 'Submit button text is required.'
		), 
		'cc' => array(
			'rule' => array(
				'multipleEmails', 
				false, 
				null
			), 
			'required' => false, 
			'allowEmpty' => true, 
			'message' => 'Please enter one or more valid email addresses.'
		), 
		'bcc' => array(
			'rule' => array(
				'multipleEmails', 
				false, 
				null
			), 
			'required' => false, 
			'allowEmpty' => true, 
			'message' => 'Please enter one or more valid email addresses.'
		)
	);

/**
 * Admin edit query options
 */
	protected $_editQuery = array(
		'contain' => array('EmailFormGroup' => array('EmailFormField'), 'EmailFormRecipient')
	);
	
	public function beforeValidate() {
		//remove empty recipients from the data
		if (!empty($this->data['EmailFormRecipient'])) {
			foreach ( $this->data['EmailFormRecipient'] as $key => $recipient ) {
				//if all of the values in the recipient are empty omit it
				//still ignoring if the rank and redirect_page_id fields are not empty
				if (!array_filter($recipient) || !array_diff(array_keys(array_filter($recipient)), array('rank', 'redirect_page_id')) ) {
					unset($this->data['EmailFormRecipient'][$key]);
				}
			}
		}
		
		return true;
	}

/**
 * Creates a new Page record for the 'thanks' page.
 *
 * @see Model::afterSave
 */
	public function afterSave($created) {
		$form = $this->findById($this->id);

		// This catches forms that have been soft-deleted.
		if (!$form) {
			return;
		}

		if ($created) {
			ClassRegistry::init('Pages.Page')->create();
			//
			$saveArray	= array(
						'title'		=> $form['EmailForm']['name'] . ': Thanks',
						'page_heading'	=> 'Thanks',
						'internal_name'	=> $form['EmailForm']['name'] . ': Redirect Page',
						'parent_id'	=> null,
						'layout'	=> 'default',
						'published'	=> 1,
						'content'	=> '<p>Thanks for your email.</p>',
						'plugin'	=> '',
					);
			//
			if ($form['EmailForm']['id'] == 1) {
				//
				//$saveArray['path']		= '/contact/thank-you';
				//
				$saveArray['parent_id']		= 2;
				//
				//$saveArray['slug']		= 'contact/thank-you';
			}
			//
			ClassRegistry::init('Pages.Page')->save(
				array('Page' => $saveArray),
				array('validate' => false)
			);

			$this->id = $form['EmailForm']['id'];
			$this->saveField('redirect_page_id', ClassRegistry::init('Pages.Page')->id);

			ClassRegistry::init('Pages.Page')->updatePagePaths(ClassRegistry::init('Pages.Page')->id);
		}
	}

/**
 * Finds an EmailForm with id $id and returns for usage in the EmailFormBlockHelper.
 *
 * @param integer id
 * @return array
 */
	public function findForDisplay($id) {
		$emailForm = $this->find('first', array(
			'conditions' => array('EmailForm.id' => $id),
			'contain' => array(
				'EmailFormGroup' => array('EmailFormField'), 
				'EmailFormRecipient'
			),
			'published' => true
		));

		return $emailForm;
	}

/**
 * Finds for TinyMCE event listener
 *
 * @return array
 */
	public function findForTinyMce() {
		$emailForms = $this->find('all', array(
			'conditions' => array('EmailForm.super_admin' => 0),
			'fields' => array('id', 'name'),
			'order' => 'PublishingInformation.start DESC',
			'published' => true,
			'cache' => true
		));
		
		return $emailForms;
	}

}
