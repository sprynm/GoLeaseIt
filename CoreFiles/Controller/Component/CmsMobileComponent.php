<?php
/**
 * Mobile Component class
 *
 * Automatically switches the controller to a 'mobile' theme if a mobile user agent
 * is detected.
 *
 * @author      Jamie Nay <jamie@radarhill.com>
 * @package     cms
 * @subpackage  cms.cms.controllers
 * @since       Version 2.6
 */
class CmsMobileComponent extends Component {

/**
 * Other components needed by this component
 *
 * @access public
 * @var array
 */
	public $components = array('RequestHandler');

/**
 * Controller object
 *
 * @access  public
 * @var     object
 */
	public $controller = null;

/**
 * component settings
 * 
 * @access public
 * @var array
 */
	public $settings = array();

/**
 * Default values for settings.
 *
 * @access private
 * @var array
 */
	protected $_defaults = array();

/**
 * Configuration method.
 *
 * @param   object  $model
 * @param   array   $settings
 * @access  public
 */
	public function initialize(Controller $controller, $settings = array()) {
		$this->settings = array_merge($this->_defaults, $settings);
		$this->controller = $controller;
	}

/**
 * Detects whether the current visitor is using a mobile device.
 *
 * @access  public
 * @return  boolean
 */
	public function isMobile() {
		if ($this->RequestHandler->isMobile()) {
			return true;
		}

		if (Configure::read('forceMobile')) {
			return true;
		}

		return false;
	}

/**
 * Runs after the controller's beforeFilter() method.
 *
 * @param   $controller
 * @access  public
 * @return  void
 */
	public function startup(Controller $controller) {
		$this->_detect();
	}

/**
 * Detects a mobile device, and, if found, sets up the mobile theme.
 *
 * @access  private
 * @return  boolean
 */
	protected function _detect() {
		if (!$this->isMobile()) {
			return false;
		}
		$this->controller->theme = 'Mobile';
		return true;
	}

}