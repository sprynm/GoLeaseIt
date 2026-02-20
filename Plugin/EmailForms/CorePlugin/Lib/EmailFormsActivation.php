<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * EmailFormsActivation class
 *
 * Performs tasks related to EmailForms plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classEmailFormsActivation.html
 * @package      Cms.Plugin.EmailForms.Lib  
 * @since        Pyramid CMS v 1.0
 */
class EmailFormsActivation extends PluginActivation {

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		'Pages.Page' => array(
			array(
				'title' => 'Contact', 
				'page_heading' => 'Contact', 
				'content' => '<p>Please use this form to contact us.</p><div class="block EmailForm"><span>{{block type="EmailForm" id="1"}}</span></div>', 
				'layout' => 'default', 
				'path'	=> 'contact',
				'protected' => true
			)
		)
		, 'Navigation.AdminNavigationItem' => array(
			'AdminNavigationItem' => array(
				'name' => 'Email Form Submissions',
				'link' => array(
					'plugin' => 'email_forms',
					'controller' => 'email_form_submissions',
					'action' => 'index'
				)
			)
		)
		, 'Navigation.AdminNavigationItemsGroup' => array(
			'AdminNavigationItemsGroup' => array(
				'group_id' => 2,
				'admin_navigation_item_id' => 1
			)
		)
		, 'EmailForms.EmailForm' => array(
			'EmailForm' => array(
				'name' => 'Contact',
				'content_template' => '%all%',
				'success_text' => 'Thank-you for your inquiry.',
				'success_title' => 'Thank You',
				'submit_button_text' => 'Send'
			),
			'EmailFormGroup' => array(
				array(
					'name' => 'Default',
					'EmailFormField' => array(
						array(
							'name' => 'name',
							'label' => 'Name',
							'type' => 'text',
							'validate' => 'notEmpty',
							'model' => 'EmailFormGroup',
							'autocomplete' => 'name'
						),
						array(
							'name' => 'email_address',
							'label' => 'Email Address',
							'type' => 'text',
							'validate' => 'email',
							'model' => 'EmailFormGroup',
							'autocomplete' => 'email'
						),
						array(
							'name' => 'comments',
							'label' => 'Comments',
							'type' => 'textarea',
							'validate' => 'notEmpty',
							'model' => 'EmailFormGroup'
						)
					)
				)
			)
		)
	);

/**
 * Plugin permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'email_forms', 'controller' => 'email_forms', 'action' => 'admin_edit', 'description' => 'Add and edit email forms')
		),
		array(
			'Permission' => array('plugin' => 'email_forms', 'controller' => 'email_forms', 'action' => 'admin_delete', 'description' => 'Delete email forms')
		),
		array(
			'Permission' => array('plugin' => 'email_forms', 'controller' => 'email_forms', 'action' => 'admin_new_group', 'description' => 'Add new form groups')
		),
		array(
			'Permission' => array('plugin' => 'email_forms', 'controller' => 'email_forms', 'action' => 'admin_reorder', 'description' => 'Reorder email form groups and fields')
		),
		array(
			'Permission' => array('plugin' => 'email_forms', 'controller' => 'email_form_submissions', 'action' => '*', 'description' => 'Email form submission management'),
			'Group' => array('Group' => array(2))
		)
	);

/**
 * after schema update callback
 *
 * @see CmsPluginActivation::afterSchemaUpdate
 */
	public function afterSchemaUpdate($schemaVersion) {
		$db = ConnectionManager::getDataSource('default');
		$db->cacheSources = false;
		switch ($schemaVersion) {
			// Convert the 'success' page stuff to be actual Page rows.
			// This code looks ridiculous (and it is) due to the way CakePHP caches model tables
			// and also holds off saving records until the very end of a series of transactions.
			// So, until that changes, behold some craziness.
			case '3':
				$emailForms = ClassRegistry::init('EmailForms.EmailForm')->find('all');
				foreach ($emailForms as $i => $emailForm) {
					$page = array('Page' => array(
						'title' => $emailForm['EmailForm']['name'] . ': ' . $emailForm['EmailForm']['success_title'],
						'page_heading' => $emailForm['EmailForm']['success_title'],
						'internal_name' => $emailForm['EmailForm']['name'] . ': Redirect Page',
						'parent_id' => null,
						'layout' => 'default',
						'published' => 1,
						'content' => $emailForm['EmailForm']['success_text'],
						'plugin' => '',
						'exclude_sitemap' => true,
					));

					App::import('Model', 'Model', false);
					$Page = new Model(array(
						'name' => 'Page',
						'table' => 'pages',
						'ds' => 'default'
					));
					$Page->Behaviors->load('AppTree', array(
						'parent' => 'parent_id'
					));
					$Page->Behaviors->load('Sluggable', array(
						'label' => 'title'
					));
					$Page->Behaviors->load('Path', array(
						'excludeField' => 'action_map'
					));

					$Page->create();
					$Page->save($page);
					AppCache::clear();
					$formData = array('EmailForm' => array(
						'id' => $emailForm['EmailForm']['id'],
						'redirect_page_id' => $Page->id
					));
					App::import('Model', 'Model', false);
					$EmailForm = new Model(array(
						'name' => 'EmailForm',
						'table' => 'email_forms',
						'ds' => 'default'
					));
					$EmailForm->save($formData);
				}
				break;
		}
	}

}
