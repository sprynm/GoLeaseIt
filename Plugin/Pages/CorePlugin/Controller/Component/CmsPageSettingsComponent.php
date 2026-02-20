<?php
/**
 * Page Settings component class
 *
 * Sets application-wide settings, mostly view variables such as google analytics code,
 * meta tags, the current pages table row, etc.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPageSettingsComponent.html
 * @package		 Cms.Plugin.Pages.Controller.Component
 * @since		 Pyramid CMS v 1.0
 */
class CmsPageSettingsComponent extends Component {
	
/**
 * page settings
 */
	public $pageSettings = array();
	
/**
 * component settings
 */
	public $settings = array();

/**
 * Controller object
 */
	public $controller = null;

/**
 * Default setting values
 * Options:
 * - autoSet: view variables that should be set automatically from page settings
 * the key corresponds to the pages record variable, while the value is the 
 * variable name that will be in the view.
 */
	protected $_defaults = array(
		'autoSet' => array(
			'page_heading' => 'pageHeading',
			'content' => 'pageIntro',
			'extra_header_code' => 'extraHeaderCode',
			'extra_footer_code' => 'extraFooterCode',
			'layout' => 'layout'
		)
	);
		
/** 
 * Configuration method.
 *
 * @param	object	$controller
 * @param	array	$settings
 * @return	void
 */
	public function initialize(Controller $controller, $settings = array()) {
		$this->settings = array_merge($this->_defaults, $settings);
		$this->controller = $controller;
		$this->pageSettings = $this->_configurePageSettings();
	}
	
/**
 * Executes after the controller action. All of the page setting functions are
 * called here.
 *
 * @param	object	$controller
 * @return	void
 */
	public function beforeRender(Controller $controller) {
		$this->pageSettings = $this->_configurePageSettings();
		$this->pageTitle();
		$this->_autoSet();
		$this->_verifyLayout();
	}
	
/**
 * Sets the page title based on the current action, the site settings, and
 * the $pageSettings array.
 *
 * @param	string	$overwrite	OPTIONAL
 * @return	void
 */
	public function pageTitle($overwrite = null) {
		
		if (!isset($this->controller->Admin)) {
			return false;
		}

		if (!Configure::read('Settings.Site')) {
			return false;
		}

		if (isset($this->pageSettings['override_title_format']) && $this->pageSettings['override_title_format']) {
			return $this->controller->set('titleTag', $this->pageSettings['title']);
		}

		if (isset($this->controller->viewVars['titleTag'])) {
			return false;
		}

		$commonString = Configure::read('Settings.Site.name');
		if (Configure::read('Settings.Site.common_head_title')) {
			$commonString .= ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.common_head_title');
		} 
		
		if ($this->controller->Admin->isAdminAction() && !$overwrite) {
			$title = null;
			if (isset($this->controller->params['plugin'])) {
				$title .= Configure::read('Plugins.' . $this->controller->params['plugin'] . '.alias');
				$controller = str_replace($this->controller->params['plugin'] . '_', '', $this->controller->params['controller']);
				$title .= ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Inflector::humanize($controller) . ' ' . Configure::read('Settings.Site.title_separator') . ' Administration';
			} else {
				$title = 'Administration';
			}
			$titleTag = $title . ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.name');
			$pageTitle = Configure::read('Plugins.' . $this->controller->params['plugin'] . '.alias') . ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.name') . ' Content Management';
			return $this->controller->set(compact('titleTag', 'pageTitle'));
		}


		//
		$title	= $overwrite
			? $overwrite
			: (
				!empty($this->pageSettings['title'])
				? $this->pageSettings['title']
				: null
			);
		//
		$title	.= ' ' . Configure::read('Settings.Site.title_separator') . ' ' . $commonString;
		//
		if (isset($this->controller->viewVars['instance']['PrototypeInstance']) && $this->controller->viewVars['instance']['PrototypeInstance']['override_title_format']) {
			//
			$title	= $this->controller->viewVars['instance']['PrototypeInstance']['head_title'];
		}
		//
		return $this->controller->set('titleTag', $title);
	}
	public function pageTitle_z($overwrite = null) {
		
		if (!isset($this->controller->Admin)) {
			return false;
		}

		if (!Configure::read('Settings.Site')) {
			return false;
		}

		if (isset($this->pageSettings['override_title_format']) && $this->pageSettings['override_title_format']) {
			return $this->controller->set('titleTag', $this->pageSettings['title']);
		}

		if (isset($this->controller->viewVars['titleTag'])) {
			return false;
		}

		$commonString = Configure::read('Settings.Site.name');
		if (Configure::read('Settings.Site.common_head_title')) {
			$commonString .= ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.common_head_title');
		} 
		
		if ($this->controller->Admin->isAdminAction() && !$overwrite) {
			$title = null;
			if (isset($this->controller->params['plugin'])) {
				$title .= Configure::read('Plugins.' . $this->controller->params['plugin'] . '.alias');
				$controller = str_replace($this->controller->params['plugin'] . '_', '', $this->controller->params['controller']);
				$title .= ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Inflector::humanize($controller) . ' ' . Configure::read('Settings.Site.title_separator') . ' Administration';
			} else {
				$title = 'Administration';
			}
			$titleTag = $title . ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.name');
			$pageTitle = Configure::read('Plugins.' . $this->controller->params['plugin'] . '.alias') . ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.name') . ' Content Management';
			return $this->controller->set(compact('titleTag', 'pageTitle'));
		}
		
		$title = $overwrite ? $overwrite : (!empty($this->pageSettings['title']) ? $this->pageSettings['title'] : null);
		
		return $this->controller->set('titleTag', $title . ' ' . Configure::read('Settings.Site.title_separator') . ' ' . $commonString);
	}


/**
 * Sets the <title> tag and the pageHeading variable at the same time, OR set both to different values.
 *
 * @param string $string
 * @param string $heading Optional - will set the pageHeading separately if not null
 * @return void
 */
	public function setTitle($string, $heading = null) {
		// If this value has been set Page.page_heading
		if (isset($this->_configurePageSettings()['page_heading']) && !empty($this->_configurePageSettings()['page_heading'])) {
			// 
			$heading	= $this->_configurePageSettings()['page_heading'];
			//
			$string		= $heading;
		} elseif (!$heading) {
			//
			$heading	= $string;
		}
		//
		$this->pageTitle($string);
		//
		$this->controller->set('pageHeading', $heading);
	}
	
/**
 * Automatically sets view variables as defined in the component settings.
 *
 * @return	void
 */
	protected function _autoSet() {
		if (!$this->settings) {
			return;
		}
		
		foreach ($this->settings['autoSet'] as $key => $val) {
			$this->_setFromPageSettings($key, $val);
		}
	}
	
/**
 * Loads the record from the pages table that matches the current request.
 *
 * @return	array
 */
	protected function _configurePageSettings() {
		if (!$this->controller) {
			return array();
		}

		if (isset($this->controller->params['admin']) && $this->controller->params['admin'] == 1) {
			return array();
		}
		
		if (isset($this->controller->viewVars['page']['Page'])) {
			return $this->controller->viewVars['page']['Page'];
		} else if (isset($this->controller->viewVars['_page']['Page'])) {
			return $this->controller->viewVars['_page']['Page'];
		} else {
			$conditions = array(
				'controller' => $this->controller->params['controller'], 
				'action' => $this->controller->params['action']
			);
			
			if (isset($this->controller->params['pass'][0]) || isset($this->controller->params['slug'])) {
				$conditions['extra'] = isset($this->controller->params['pass'][0]) ? $this->controller->params['pass'][0] : $this->controller->params['slug'];
			}
			
			$page = ClassRegistry::init('Pages.Page')->find('first', array(
				'conditions' => $conditions
				, 'contain' => array('Image')
			));

			if (empty($page) && isset($conditions['extra'])) {
				unset($conditions['extra']);
				$page = ClassRegistry::init('Pages.Page')->find('first', array(
					'conditions' => $conditions
					, 'contain' => array('Image')
				));
			}
			
			if (empty($page)) {
				return array();
			}
			
			if (!empty($page['Image'])){
				$banner = array('Image'=>$page['Image']);
				$this->controller->set('banner', $banner);
			}
			
			$this->controller->set('_page', $page);
			return $page['Page'];
		}
		
		return array();
	}
	
/**
 * Sets a variable $viewKey into the view by taking the value of $dbKey
 * from $this->pageSettings.
 *
 * @param	string	$dbKey
 * @param	string	$viewKey
 * @return	boolean
 */
	protected function _setFromPageSettings($dbKey, $viewKey) {
		if (isset($this->controller->viewVars[$viewKey])) {
			return false;
		}
		
		// If we're viewing a page from the pages_controller, we don't want the intro because it's the
		// same as the content.
		if (isset($this->controller->params['controller']) && $this->controller->params['controller'] == 'pages' && $dbKey == 'content') {
			return $this->controller->set($viewKey, '');
		}
		$value = isset($this->pageSettings[$dbKey]) ? $this->pageSettings[$dbKey] : '';
		
		return $this->controller->set($viewKey, $value);
	}

/**
 * Ensures that the controller's layout matches the layout found in the current page's settings.
 *
 * @return	boolean
 */
	protected function _verifyLayout() {
		if (
			!empty($this->controller->viewVars['layout'])
			&& $this->controller->viewVars['layout'] != 1
			&& (!isset($this->controller->layout) || $this->controller->layout == 'default')
			) {			   
			$this->controller->layout = $this->controller->viewVars['layout'];
		}
		return true;
	}

}
