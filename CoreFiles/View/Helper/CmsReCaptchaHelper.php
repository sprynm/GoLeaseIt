<?php
/**
 * CmsReCaptchaHelper Class File
 *
 * Outputs recaptcha security validation fields.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @author	 Shannon Graham
 * @link		 http://api.pyramidcms.com/docs/classCmsReCaptchaHelper.html
 * @package		 Cms.Plugin.ReCaptcha.View.Helper
 * @since		 Pyramid CMS v 1.0
 */
class CmsReCaptchaHelper extends AppHelper {
	/**
 * Helpers to load
 */
	public $helpers = array(
		'Form' => array('className' => 'appForm')
		, 'Html'
	);


/** 
 * Returns an input field.
 * calling for a captcha.
 * @return string
 * @param Model name
 */
	public function field($Model = null) {
		$this->Form->create($Model);
		
		if(!$sitekey = Configure::read('Settings.ReCaptcha.Google.sitekey')) {
			debug("Sitekey not set");	
			debug(Configure::read('Settings.ReCaptcha'));
			return;
		}
		
		$ret = array();
		if (Configure::read('Settings.ReCaptcha.invisible')) {
			$ret['submitParams'] = array(
				'class' => array('g-recaptcha')
 			);
			$ret['elements'] = $this->Form->input('remote_ip', array('type' => 'hidden', 'value' => $_SERVER['REMOTE_ADDR']));
		} else {
			$ret['elements'] = $this->Form->input('remote_ip', array('type' => 'hidden', 'value' => $_SERVER['REMOTE_ADDR']));
			$ret['elements'] .= '<div id="security_code" class="g-recaptcha"></div>';
		}
		
		//include the scripts
		$this->Html->script('ReCaptcha.recaptcha_callback', array('inline'=>false, 'once'=>true));
		$this->Html->script('https://www.google.com/recaptcha/api.js?onload=reCaptchaOnloadCallback&render=explicit', array('inline'=>false, 'once'=>true, 'async'=>1, 'defer'=>1));
		
		return $ret;
	
	}

}