<?php
App::uses('CmsEventListener', 'Event');
/**
 *
 * CmsEmailFormsEventListener class
 *
 * Event Listener for the EmailForms plugin.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormsEventListener.html
 * @package		 Cms.Core.Plugin.EmailForms.Event
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormsEventListener extends CmsEventListener {

/**
 * Components
 */
	protected $_components = array(
		'EmailForms.EmailForms'
	);

/**
 * Helpers
 */
	protected $_helpers = array(
		'EmailForms.EmailForm',
		'EmailForms.EmailFormBlock'
	);

/**
 * Implemented events - merged with core events in CmsEventListener
 */
	public $implementedEvents = array(
		'Component.AdminNav.pluginNav' => array(
			'callable' => 'onAdminNavPluginNav'
		),
		'Helper.TinyMce.blockLists' => array(
			'callable' => 'onTinyMceBlockLists'
		)
	);

/**
 * Admin nav listener
 *
 * @var array
 */
	public function onAdminNavPluginNav($event) {
		$items = array(
			Configure::read('Plugins.EmailForms.alias') => array(
				'link' => '#'
				, 'children' => array(
					'Manage Email Forms' => array(
						'link' => array(
							'plugin' => 'email_forms',
							'controller' => 'email_forms',
							'action' => 'index'
						)
					),
					'Email Form Submissions' => array(
						'link' => array(
							'plugin' => 'email_forms',
							'controller' => 'email_form_submissions',
							'action' => 'index'
						)
					)
				)
			)
		);
		
		$event->result = Set::merge($event->result, $items);
	}

/**
 * Listener for TinyMCE content blocks select list plugin
 *
 * @param object $event
 * @return void
 */
	public function onTinyMceBlockLists($event) {
		$emailForms = ClassRegistry::init('EmailForms.EmailForm')->findForTinyMce();
		$emailForms = Set::combine($emailForms, '{n}.EmailForm.id', '{n}.EmailForm.name');

		$items = array(
			Configure::read('Plugins.EmailForms.alias') => array(
				'blockType' => 'EmailForm',
				'options' => $emailForms
			)
		);
		
		$event->result = Set::merge($event->result, $items);
	}

}