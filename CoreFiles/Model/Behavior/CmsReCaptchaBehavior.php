<?php

/**
 * CmsRecaptchaBehavior class file
 * 
 * @copyright Copyright 2010-2015, Radar Hill Technology Inc.
 * @author Shannon Graham
 * (http://radarhill.com)
 * @link http://api.pyramidcms.com/docs/classCmsRecaptchaBehavior.html
 * @package Cms.Plugin.ReCaptcha.Model.Behavior
 * @since Pyramid CMS v 1.0.4
 */
class CmsReCaptchaBehavior extends ModelBehavior
{


/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 */
	protected $_defaults = array();

/**
 * Intializer
 *
 * @param   object $Model
 * @param   array $config
 * @return  void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $settings);
	}

/**
 * Adds a validation rule to the model for a recaptcha if the field is present in the data array. Since this
 * method simply adds to the validation array and does not validate anything itself, it always returns true.
 *
 * @param object Model
 * @return boolean
 */
	public function beforeValidate(Model $Model) {
		$Model->data[$Model->name]['g-recaptcha-response'] = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
		
		$rule = array(
			'rule' => array('captchaPassed'),
			'message' => 'Captcha is incorrect.'
		);  

		if (isset($Model->validateDynamic)) {
			$Model->validateDynamic['g-recaptcha-response'] = $rule;
		} else {
			$Model->validate['g-recaptcha-response'] = $rule;
		}
 
		return true;
	}
	
	function captchaPassed($check) {
	
		$key = "6LdppP8SAAAAAEcXf0VyK2zXT5DAWNlEKT5fk1tT";
		
		$keysetting = Configure::read('Settings.ReCaptcha.Google.secretkey');
		if(!empty($keysetting)) {
			$key = $keysetting;
		}
		
		if($check->data[$check->name]['g-recaptcha-response']) {
			$json = file_get_contents(
				"https://www.google.com/recaptcha/api/siteverify?secret=" 
				. $key // my secret key
				. "&response=" 
				. $check->data[$check->name]['g-recaptcha-response'] 
				. "&remoteip="
				. $check->data[$check->name]['remote_ip']
			);

			$response = json_decode($json);
			
			if($response->success) {
				return true;
			}
		}
		return false;
	}
}

