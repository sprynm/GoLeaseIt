<?php
/**
 * CmsAutoPaginateComponent class
 *
 * A simple extension for paginating that helps with persisting user-defined pagination limits.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAutoPaginateComponent.html
 * @package		 Cms.Plugin.Controller.Component
 * @since		 Pyramid CMS v 1.0
 */
class CmsAutoPaginateComponent extends Component {

/**
 * Other components needed by this component
 */
	public $components = array('Session');

/**
 * component settings
 */
	public $settings = array();

/**
 * Default values for settings.
 * - options: the results-per-page options to present to the user.
 */
	protected $_defaults = array(
		'options' => array(
			24, 
			48, 
			96
		)
	);

/**
 * Optional hard limit for pagination.
 */
	protected $_hardLimit = null;

/**
 * Configuration method.
 *
 * @param	object	$model
 * @param	array	$settings
 * @return	void
 */
	public function initialize(Controller $controller, $settings = array()) {
		$this->settings = array_merge($this->_defaults, $settings);
		$this->controller = $controller;
		
		$this->checkPaginateForm();
	}

/**
 * beforeRender()
 *
 * Set the variables needed by the controller.
 *
 * @param	object	$controller
 * @return	void
 */
	public function beforeRender(Controller $controller) {
		if (isset($this->settings['options'])) {
			$controller->set('paginationOptions', $this->settings['options']);
		}
		$controller->set('paginationLimit', $this->paginationLimit());
	}
	
/**
 * Looks for, and handles, submission of a "results per page" form.
 *
 * @return	mixed
 */
	public function checkPaginateForm() {
		if (!isset($this->controller->request->data['results-per-page-submit']) || !isset($this->controller->request->data['paginate'])) {
			return false;
		}
		
		$this->writeLimit($this->controller->request->data['paginate']);
		$this->controller->redirect($this->controller->referer());
	}
	
/**
 *
 * Set the controller's $paginate variable.
 *
 * @param	array	$options	OPTIONAL
 * @return	void
 */
	public function setPaginate($options = array(), $model = null) {
		$defaults = array(
			'limit' => $this->paginationLimit()
		);

		if ($model) {
			$this->controller->paginate = array($model => array_merge($defaults, $options));
		} else {
			$this->controller->paginate = array_merge($defaults, $options);
		}
	}

/**
 * Set the pagination limit based on user input and session variables.
 *
 * @return	integer
 */
	public function paginationLimit() {
		if (isset($this->controller->params['named']['Paginate'])) {
			$this->writeLimit($this->controller->params['named']['Paginate']);
		}
		
		$limit = $this->Session->check('Pagination.limit') ? $this->Session->read('Pagination.limit') : Configure::read('Settings.Site.default_pagination_limit');
		
		if ($this->_hardLimit && $limit > $this->_hardLimit) {
			$limit = $this->_hardLimit;
		}
		
		return $limit;
	}

/**
 * Set a hard limit for pagination, in case there are certain pages that should
 * not exceed a maximum page limit.
 *
 * @param	integer $limit
 * @return	void
 */
	public function setHardLimit($limit) {
		$this->_hardLimit = (int)$limit;
	}
	
/**
 * A wrapper to write the pagination limit to the session.
 *
 * @param	integer $limit
 * @return	boolean
 */
	public function writeLimit($limit) {
		return $this->Session->write('Pagination.limit', $limit);
	}

}