<?php
/**
 * CmsNotifyComponent class
 *
 * Component for handling user-friendly messages. Basically shortcuts a bunch of Session->setFlash() calls.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsNotifyComponent.html
 * @package      Cms.Plugin.Controller.Component
 * @since        Pyramid CMS v 1.0
 */
class CmsNotifyComponent extends Component {

/**
 * Components to load
 */
	public $components = array('Session');

/**
 * Controller object
 */
	public $controller = null;

/**
 * Constants for referencing the type of message. Cleaner and less error-prone than strings.
 */
	const ATTENTION = 0;
	const ERROR = 1;
	const INFORMATION = 2;
	const SUCCESS = 3;

/**
 * Configuration method.
 *
 * @param	object	$model
 * @param	array	$settings
 * @return	void
 */
	public function initialize(Controller $controller, $settings = array()) {
		$this->settings = $settings;
		$this->controller = $controller;
	}

/**
 * Send an attention message to the user
 *
 * @var object
 * @var string
 * @return void
 */
	public function attention($message) {
		$this->_flash($message, NotifyComponent::ATTENTION);
	}

/**
 * Send an error message to the user
 *
 * @var object
 * @var string
 * @return void
 */
	public function error($message) {
		$this->_flash($message, NotifyComponent::ERROR);
	}

/**
 * Gives a user-friendly error message for a failed model-save form submission.
 * Can optionally pass an array of validation errors.
 *
 * @param array Optional errors
 * @return void
 */
	public function handleFailedSave($errors = null) {
		if (!$errors) {
			$errors = $this->controller->{$this->controller->modelClass}->validationErrors;
		}

		$flash = $this->errorString($errors);
		$this->error($flash);
	}

/**
 * Generates an error string from errors in $errors.
 *
 * @param array errors
 * @return string
 */
	public function errorString($errors) {
		$string = __('There were problems processing your form submission') . ': <ol>';
		foreach ($errors as $key => $val) {
			$string .= $this->_validationErrorLoop($key, $val);
		}
		$string .= '</ol>';
		return $string;
	}
/**
 * Gives a form save success message and figures out where to redirect the user afterwards.
 *
 * @param array Optional settings to override the defaults
 * @return void
 */
	public function handleSuccessfulSave($options = array()) {
		$options = array_merge(
				array(
					'continue' => array(
						$this->controller->{$this->controller->modelClass}->id
					), 
					'return' => array(
						'action' => 'index'
					), 
					'message' => 'Item saved.  To view changes on the live website, ensure you refresh  your browser window to view the latest changes.  There is a "Clear Cache" tool (in the header above) if you do not see your changes immediately.  Advanced formatting may also display unexpectedly, contact Radar Hill to address any display inconsistencies.'
				), $options);

		$this->success(__($options['message']));
		if (isset($this->controller->request->data['save_continue'])) {
			$this->controller->redirect($options['continue']);
		} else {
			$this->controller->redirect($options['return']);
		}
	}

/**
 * Send an information message to the user
 *
 * @var object
 * @var string
 * @return void
 */
	public function information($message) {
		$this->_flash($message, NotifyComponent::INFORMATION);
	}

/**
 * Send a success message to the user
 *
 * @var object
 * @var string
 * @return void
 */
	public function success($message) {
		$this->_flash($message, NotifyComponent::SUCCESS);
	}

/**
 * Base message functin
 * 
 * @var object
 * @var string
 * @var int
 * @return void
 */
	protected function _flash($message, $type = NotifyComponent::INFORMATION) {
		$class = 'information';
		
		switch ($type) {
			case NotifyComponent::SUCCESS:
				$class = 'success';
			break;
			
			case NotifyComponent::ERROR:
				$class = 'error';
			break;
		
			case NotifyComponent::ATTENTION:
				$class = 'attention';
			break;
		}
		
		$this->Session->setFlash(__($message), 'notification', array('class' => $class));
	}
	
/**
 * Formats validation errors in a friendly way.
 *
 * @param string The field failing validation
 * @param string The reason for the failure
 * @return string
 */
	protected function _validationErrorLoop($key, $val) {
		$flash = '';
		if (is_array($val) && count($val) > 1) {
			foreach ($val as $k => $v) {
				$flash .= '<li><ol>';
				$flash .= $this->_validationErrorLoop($k, $v);
				$flash .= '</ol></li>';
			}
		} else {
			$msg = current($val);			
			
			if(empty($val[0])) {
				$val[0] = $msg;
			}
			
			if(!empty($val[0]['file']) && is_array($val[0]['file'])) {
				$val[0] = $val[0]['file'][0];
			} 
			
			$flash .= '<li>' . Inflector::humanize($key);
			if (is_array($val[0])) {
				//hasMany relations on this model
				$flash .= ': ' . implode("<br>\r\n", $val[0]);
			} else {
				//hasOne relation on this model
				$flash .= ': ' . $val[0];
			}
			$flash .= '</li>';
		} 
		
		return $flash;
	}
	
}