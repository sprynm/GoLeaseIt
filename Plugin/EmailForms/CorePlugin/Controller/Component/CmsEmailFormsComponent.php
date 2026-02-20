<?php
App::uses('CmsEmail', 'Network/Email');

/**
 * CmsEmailFormsComponent class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormsComponent.html
 * @package		 Cms.Plugin.EmailForms.Controller.Component
 * @since		 Pyramid CMS v 1.0
 */
class CmsEmailFormsComponent extends Component {

/**
 * Components to load
 *
 * @var array
 */
	public $components = array(
		'Notify',
		'Session'
	);
		
/**
 * Controller object
 *
 * @var object
 */
	public $controller = null;

/**
 * EmailFormSubmission model object
 *
 * @var object
 */
	public $Model = null;

/**
 * component settings
 * 
 * @var		array
 */
	public $settings = array();

/**
 * Default values for settings.
 *
 * @var		array
 */
	protected $_defaults = array();

/**
 * Default values for suffixes that are unwelcome.
 *
 * @var		array
 */
	protected $_suffixes = array('\.ru');

/**
 * Configuration method.
 *
 * @param	object	$model
 * @param	array	$settings
 * @return	void
 */
	public function initialize(Controller $Controller, $settings = array()) {
		$this->settings = array_merge($this->_defaults, $settings);
		$this->controller = $Controller;
		
		App::uses('EmailFormsAppModel', 'EmailForms.Model');
		App::uses('EmailFormSubmission', 'EmailForms.Model');
		$this->Model = new EmailFormSubmission();
	}

/**
 * Catches form submission and processes it.
 *
 * @param object controller
 * @return void
 */
	public function beforeRender(Controller $Controller) {
		
		if (!$this->controller || !$this->controller->request->is('post') || $this->controller->Admin->isAdminAction()) {
			return true;
		}
		//
		if ($this->Session->check('formSuccessFormData')) {
			//
			$this->Session->delete('formSuccessFormData');
		}
		// 
		if ($this->Session->check('formSuccessRequestData')) {
			//
			$this->Session->delete('formSuccessRequestData');
		}
		//
		if (!empty($this->controller->request->data) && isset($this->controller->request->data['EmailFormSubmission'])) {
			//trim the submitted values
			foreach( $this->controller->request->data['EmailFormSubmission'] as $k => $v ) {
				if( !is_array($v) ) {
					$this->controller->request->data['EmailFormSubmission'][$k] = trim($v);
				}
			}
			//
			$matches	= array();
			//
			if (isset($this->controller->request->data['EmailFormSubmission']['email_address']) && filter_var($this->controller->request->data['EmailFormSubmission']['email_address'], FILTER_VALIDATE_EMAIL)) {
				//
				preg_match('/('.implode('|', $this->_suffixes).')$/i', $this->controller->request->data['EmailFormSubmission']['email_address'], $matches);
			}
			//
			$responseData		= $this->googleVerify($this->controller->request->data);
			// 
			if (empty($matches) && $responseData['success'] && $this->Model->save($this->controller->request->data['EmailFormSubmission'])) {
				//
				$this->sendEmail();
				//
				$this->sendAutoResponseEmail();
				//
				$this->_success();
			// 
			} elseif (!empty($matches)) {
				//
				$this->_success();
			//
			} else {
				//swap out the name of the error message fields with their more human readable label for the validation error message
				$fields  = ClassRegistry::init('CustomField')->find('all', array(
					'fields' => array( 'id', 'name', 'label')
					, 'conditions' => array(
						'model' => 'EmailFormGroup'
						, 'foreign_key' => $this->controller->data['EmailFormSubmission']['email_form_id']
					)
				));
				
				//manually add the recaptcha to the swap array
				$fields[] = array('CustomField'=>array(
					'label' => 'ReCaptcha'
					, 'name' => 'g-recaptcha-response'
				));
				
				$errors = $this->Model->validationErrors;
				
				//do the swapping
				foreach ($fields as $field) {
					//make sure that we aren't overwriting a key and that the label exists
					if (!empty($field['CustomField']['label']) && empty($errors[$field['CustomField']['label']]) &&  !empty($errors[$field['CustomField']['name']])) {
						$errors[$field['CustomField']['label']] = $errors[$field['CustomField']['name']];
						unset($errors[$field['CustomField']['name']]);
					}
				}
				
				$this->Notify->handleFailedSave($errors);
			}
		}
	}


/**
 * 
 *
 * 
 * 
 */
 	public function googleVerify($data) {
 			//
 			if (!isset($data['g-recaptcha-response'])) {
 				//
 				return		false;
 			}
			//
			$recaptchaToken		= $data['g-recaptcha-response'];
			//
			$YOUR_SECRET_KEY	= Configure::read('Settings.ReCaptcha.Google.secretkey');
			//
			$http_build_query	= array(
							'secret'	=> $YOUR_SECRET_KEY
							, 'response'	=> $recaptchaToken
							,
						);
			// Use cURL to send the request
			$ch			= curl_init('https://www.google.com/recaptcha/api/siteverify');
			//
			curl_setopt($ch, CURLOPT_POST, 1);
			//
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($http_build_query));
			//
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//
			$response		= curl_exec($ch);
			//
			curl_close($ch);
			//
			return			 json_decode($response, true);
	}


/**
 * Deletes the formSuccess session variable if it's set (which happens in _success()).
 *
 * @param object controller
 * @return void
 */
 	public function shutdown(Controller $Controller) {
 		if ($this->Session->read('formSuccess')) {
 			$this->Session->delete('formSuccess');
 		}
 	}

/**
 *	
 *	
 * @return string
 */
	public function reply_to($data, $form) {
		//
		if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			//
			return $data['email'];
		//
		} elseif (isset($data['email_address']) && filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
			//
			return $data['email_address'];
		}
		//
		return	Configure::read('Settings.Site.email');
	}

/**
 * Sends an email upon successful submission.
 *
 * @return boolean
 */
	public function sendEmail() {
		//
		$data		= $this->Model->read(null, $this->Model->id)['EmailFormSubmission'];
		//
		$data		= am($data, $data['data']);
		//
		$form		= $this->Model->EmailForm->findForDisplay($data['email_form_id']);
		//
		if (!$form) {
			//
			return false;
		}
		//
		$from		= $this->_sender($data, $form);
		//
		$sender		= $from;
		//
		$to		= $this->_recipient($form);
		//
		$replyTo	= $this->reply_to($data, $form);
		//
		$subject	= $this->_parseTemplate($form, $data, 'subject_template');
		//
		$body		= $this->_parseTemplate($form, $data, 'content_template');
		//
		$cc		= strlen($form['EmailForm']['cc']) > 0
				? explode(',', $form['EmailForm']['cc'])
				: array();
		//
		$bcc		= strlen($form['EmailForm']['bcc']) > 0
				? explode(',', $form['EmailForm']['bcc'])
				: array();
		//
		$email		= new CmsEmail();
		//
		$email->from($from);
		//
		$email->sender($sender);
		//
		$email->replyTo($replyTo);
		//
		$email->to($to);
		//
		$email->subject($subject);
		//
		if (!empty($cc)) {
			//
			$email->cc($cc);
		}
		//
		if (!empty($bcc)) {
			//
			$email->bcc($bcc);
		}
		//
		$sent		= $email->send($body);
		//
		if (!$sent) {
			//
			$headers	= array();
			//
			$headers[]	= 'MIME-Version: 1.0';
			//
			$headers[]	= 'Content-type: text/html; charset=iso-8859';
			//
			$headers[]	= 'Reply-To: ' . $sender;
			//
			if (!empty($cc)) {
				//
				$headers[]	= 'Cc: ' . implode(',', $cc);
			//
			}
			// 
			$bcc		= 'Bcc: roger@radarhill.com';
			//
			if (!empty($bcc)) {
				//
				$headers[]	= $bcc . ',' . implode(',', $bcc);
			//
			}
			//
			$headers[]	= 'From: ' . $from;
			//
			$headers[]	= 'X-Mailer: PHP/'. phpversion();
			//
			$header		= implode(PHP_EOL, $headers);
			// 
			$TO		= implode(',', $to);
			// 
			mail($to, $subject . ' [F]', '<p>$to: ' . $TO . '</p>' . $body, $header);
		}
		//
		return true;
	}

/**
 * Sends an auto-response email upon successful submission.
 *
 * @return boolean
 */
	public function sendAutoResponseEmail() {
		$data = $this->controller->request->data['EmailFormSubmission'];
		$form = $this->Model->EmailForm->findForDisplay($data['email_form_id']);
		
		if (!$form['EmailForm']['auto_response_enabled']) {
			return false;
		}

		if (!$form) {
			return false;
		}

		$to = $this->_autoResponseRecipient($data);
		if (!$to) {
			return false;
		}

		$email = new CmsEmail();
		$email->from($this->_sender($data, $form));
		$email->to($to);
		$email->subject($this->_parseTemplate($form, $data, 'auto_response_subject_template'));
		$email->send($this->_parseTemplate($form, $data, 'auto_response_content_template'));

		return true;
	}	
	
/**
 * Takes an array and recursively converts it to a string formatted for text emails.
 *
 * @param array data An array of data to be formatted. 
 * @return string
 */

  	public function tree($data) {
		$ret = '
';
		foreach($data as $key => $val) {
			if(substr($key, -3) == '_id') {
				unset($data[$key]);
				continue;
			}

			if($key === "type" || $key === "tmp_name" || $key === "error" || $key === "size") {
				continue;
			} 
			
			if(is_array($val)) {
				$ret .= Inflector::humanize($key) . '
';			
				$ret .= $this->tree($val) . '
';
			}	else {
				$ret .= '    ' . Inflector::humanize($key) . ': ' . $val .'
';
			}
		}
		return $ret;
	} 

 
 
 /**
 * Parses a template using the prescribed % notation and replaces it with info from $data.
 *
 * @param array the EmailForm
 * @param array the submission data
 * @param string the template key to use, found in $emailForm
 * @return string
 */
	protected function _parseTemplate($emailForm, $data, $template) {
	
		$find = array_keys($data);
		$find = Set::format($find, '%%%s%%', array('{n}'));
		
		$pickedRecipient = $this->_selectedRecipient( $data, $emailForm );
		
		if (!empty($pickedRecipient[$template])){
			$template = $pickedRecipient[$template];
		} else {
			$template = $emailForm['EmailForm'][$template];				
		}
		
		if (!$template) {
			return null;
		}

		$labels = Hash::combine($emailForm, 'EmailFormGroup.{n}.EmailFormField.{n}.name', 'EmailFormGroup.{n}.EmailFormField.{n}.label');
		
		if(in_array('%children%' , $find)) {
			$labels['children'] = 'Children';
		}
		
		foreach ($labels as $key => $val) {
			if (!$val) {
				$labels[$key] = Inflector::humanize($key);
			}
		}

		if ($template == '%all%') {
			$tempArray = array();
			
			foreach ($find as $val) {
				if (substr($val, 0, 2) == '%_' || substr($val, -4) == '_id%' || $val == '%security_code%') {
					continue;
				}
				
				$tempArray[str_replace("%", "", $val)] = $val;
			}
			

			$template = '';
			foreach ($tempArray as $key => $val) {
				// Replace the key with the proper label
				if(isset($labels[$key])) {
					$key = $labels[$key];
					$template .= $key . ": " . $val . "\n\r";
				}
			
			}
		}

		$find[] = '%website_name%';
		$find[] = '%website_domain%';
		$find[] = '%form_name%';
		
		$replace = array_values($data);

		// Convert arrays (such as multiple checkboxes) into strings separated by semi-colons
		foreach ($replace as $key => $val) {
			if (is_array($val)) {
				//if we are dealing with an uploaded file
				if (isset($val['size']) && isset($val['name']) && isset($val['type'])) {
					if (empty($val['name'])) {
						$replace[$key] = 'Not Uploaded';
					} else {
						$replace[$key] =  env('SERVER_NAME') . "/uploads/email_forms/" . Inflector::slug(array_keys($data)[$key]) . "/" . $val['name'];
					}
				} else {
					$replace[$key] = $this->tree($val);
				}
			}
		}

		$replace[] = Configure::read('Settings.Site.name');
		$replace[] = env('SERVER_NAME');
		$replace[] = $emailForm['EmailForm']['name'];

		if(isset($find['children']) && is_array($find['children'])) {
			$template .= '
Children: %children%';
		}
		
		$result = str_replace($find, $replace, $template) ;

		return $result;
	}
	
/**
 * Determines the recipient of the email - either set in $data or the default site email.
 *
 * @param array data
 * @return string
 */ 
	protected function _recipient($emailForm) {
		$recipients = array();
		$data = $this->controller->request->data;
		
		$pickedRecipient = $this->_selectedRecipient( $data, $emailForm );
		
		if (!empty($pickedRecipient)){
			$recipients = Cms::commaExplode($pickedRecipient['email_address']);
		}
	
		//add any recipients from the email form submission data
		if (empty($recipients) && !empty($data['EmailFormSubmission']['recipient'])) {
			$recipients = Cms::commaExplode($data['EmailFormSubmission']['recipient']);
		}
	
		//add recipients from the email form data
		if (!empty($emailForm['EmailForm']['recipient'])) {
			$recipients = am($recipients, Cms::commaExplode($emailForm['EmailForm']['recipient']));
		}
		
		if (!empty($recipients)){
			return $recipients;
		}
		
		$recipients[] = Configure::read('Settings.Site.email');
		return $recipients;
	}
	

/**
 * Determines the recipient of the auto-response email - set in $data.
 *
 * @param array data
 * @return mixed - string on success, false if no valid email is found
 */ 
	protected function _autoResponseRecipient($data) {
		if (isset($data['email_address'])) {
			return $data['email_address'];
		} else if (isset($data['email'])) {
			return $data['email'];
		}
		return false;
	}

/**
 * Figures out where to redirect the user after a form submission. If redirect_page_id
 * is set and valid then the user goes there. If not, and the old 'success text' is set,
 * the user goes there.
 */
	protected function _redirectPage($form) {
		
		// If redirect_page_id is valid and the user has access to it, go there.
		$recipient = $this->_selectedRecipient( $this->controller->request->data, $form );
		//
		if (!empty($recipient['redirect_page_id'])){
			//
			$pageModel	= ClassRegistry::init('Pages.Page');
			//
			$redirect	= $pageModel->link($recipient['redirect_page_id']);
			//
			if ($redirect && AccessControl::isAuthorized($redirect)) {
				//
				//$this->controller->set(array('form', 'requestData'), array($form, $this->controller->request->data));
				//
				return $redirect;
			}
		}
		
		//otherwise try the form's redirect page
		if (isset($form['EmailForm']['redirect_page_id']) && $form['EmailForm']['redirect_page_id'] > 0) {
			//
			$pageModel	= ClassRegistry::init('Pages.Page');
			//
			$redirect	= $pageModel->link($form['EmailForm']['redirect_page_id']);
			//
			if ($redirect && AccessControl::isAuthorized($redirect)) {
				//
				//$this->controller->set(array('form', 'requestData'), array($form, $this->controller->request->data));
				//
				return $redirect;
			}
		}

		return array(
				'plugin' => 'email_forms',
				'controller' => 'email_forms',
				'action' => 'success',
				$form['EmailForm']['id']
		);
	}

/**
 * Determines the sender of an email given form submission data. Three options:
 * #1: an "email" field in the form
 * #2: an "email_address" field in the form
 * #3: an underscored version of the form name @ the website domain
 *
 * @param array submission data
 * @param array EmailForm data
 * @return string
 */
	protected function _sender($data, $form) {
		if (isset($data['email'])) {
			return $data['email'];
		} else if (isset($data['email_address'])) {
			return $data['email_address'];
		}

		$name = Inflector::slug(strtolower($form['EmailForm']['name']));

		return $name . '@' . str_replace('www.', '', env('SERVER_NAME'));
	}

/**
 * Redirects user after successful form submission.
 *
 * @return void
 */
	protected function _success() {
		//
		$this->Session->write('formSuccess', true);
		//
		$data		= $this->controller->request->data['EmailFormSubmission'];
		//
		$form		= $this->Model->EmailForm->findForDisplay($data['email_form_id']);
		//
		$redirect	= $this->_redirectPage($form);
		//
		$this->Session->write('formSuccessFormData', $form);
		//
		$this->Session->write('formSuccessRequestData', $this->controller->request->data);
		//
		$this->controller->redirect($redirect);
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
}
