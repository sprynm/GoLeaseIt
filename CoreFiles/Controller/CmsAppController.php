<?php
App::uses('Controller', 'Controller');
App::uses('CmsEventManager', 'Event');

/**
 * CMS core-level Controller
 *
 * This class provides the link between the app's AppController and the Cake core Controller.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAppController.html
 * @package		 Cms.Controller	 
 * @since		 Pyramid CMS v 1.0
 */
class CmsAppController extends Controller {

/**
 * Application-wide components.
 */
	public $components = array(
		'Notify',
		'Session',
		'AutoPaginate',
		'RequestHandler',
		'Mobile',
		'Cookie',
		'Paginator' => array('className' => 'AppPaginator')
	);

/** 
 * Application-wide helpers.
 */
	public $helpers = array(
		'Html' => array('className' => 'AppHtml'),
		'Form' => array('className' => 'AppForm'),
		'Paginator' => array('className' => 'AppPaginator'), 
		'Session',
		'Cms',
		'Copyright',
		'GoogleMap', 
		'SocialMedia',
		'ReCaptcha'
	);

/**
 * Constructor
 *
 * @see Controller::__construct
 */
	public function __construct($request = null, $response = null) {
		CmsEventManager::dispatchEvent('Controller.construct', $this);
		
		// Attach the DebugKit ToolbarComponent here so it catches everything set by other components.
		$this->components[] = 'DebugKit.Toolbar';
		
		parent::__construct($request, $response);
	}

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		//
		$this->viewClass = 'Cms';
		//
		parent::beforeFilter();
		//
		$this->_offlineCheck();
		//
		$this->_cacheCheck();
	}

/**
 * beforeRender callback
 *
 * @return void
 */
	public function beforeRender() {
		// If the "template" plugin setting is found, set it in the controller.
		if (!(isset($this->Admin) && $this->Admin->isAdminAction()) && $this->layout == 'default' && Configure::read('Plugins.' . $this->plugin . '.layout')) {
			if (!empty($this->viewVars['_page']['Page']['layout'])) {				
				$this->layout = $this->viewVars['_page']['Page']['layout'];
			} else {
				$this->layout = Configure::read('Plugins.' . $this->plugin . '.layout');
			}
		}

		// Body ID: $this->request->here made into slug form
		$url = trim($this->request->here, '/');
		if (!$url || $url == 'home') {
			$bodyId = 'home';
		} else {
			$bodyId = Inflector::slug($url, '-');
		}
		//$this->set('bodyId', 'body-' . $bodyId);

		// Body class: only if we're using a plugin: plugin-plugin-name (for example, plugin-featured-products)
		$bodyClass = '';
		if (!empty($this->plugin)) {
			$bodyClass = Inflector::slug('plugin-' . strtolower($this->plugin), '-');
		}
		//
		$settings	= Configure::read('Settings');
		//
		$siteSettings	= $settings['Site'];
		//
		$siteContact	= $siteSettings['Contact'];
		// Set to pass variables.
		$this->set(
			array(
				'bodyClass'
				, 'bodyId'
				, 'siteContact'
				, 'siteSettings'
				, 
			)
			, array(
				$bodyClass
				, 'body-' . $bodyId
				, $siteContact
				, $siteSettings
				,
			)
		);
		//
		parent::beforeRender();
	}
	public function beforeRender_z() {
		// If the "template" plugin setting is found, set it in the controller.
		if (!(isset($this->Admin) && $this->Admin->isAdminAction()) && $this->layout == 'default' && Configure::read('Plugins.' . $this->plugin . '.layout')) {
			if (!empty($this->viewVars['_page']['Page']['layout'])) {				
				$this->layout = $this->viewVars['_page']['Page']['layout'];
			} else {
				$this->layout = Configure::read('Plugins.' . $this->plugin . '.layout');
			}
		}

		// Body ID: $this->request->here made into slug form
		$url = trim($this->request->here, '/');
		if (!$url || $url == 'home') {
			$bodyId = 'home';
		} else {
			$bodyId = Inflector::slug($url, '-');
		}
		$this->set('bodyId', 'body-' . $bodyId);

		// Body class: only if we're using a plugin: plugin-plugin-name (for example, plugin-featured-products)
		$bodyClass = '';
		if (!empty($this->plugin)) {
			$bodyClass = Inflector::slug('plugin-' . strtolower($this->plugin), '-');
		}
		$this->set('bodyClass', $bodyClass);
		parent::beforeRender();
	}

/**
 * Default admin copy
 *
 * @param	integer $id OPTIONAL 
 * @return	void
 */
	public function admin_copy($id = null) {
		if (!$id) {
			$this->Notify->error('Invalid id for ' . $this->modelClass);
			$this->redirect($this->referer());
		}
		
		if ($this->{$this->modelClass}->copy($id)) {
			$this->Notify->success('Item has been successfully copied.');
			$this->redirect($this->referer());
		} else {
			$this->Notify->error('Could not copy ' . $this->modelClass);
			$this->redirect($this->referer());
		}
	}

/**
 * Default admin delete
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Notify->error('Invalid id for ' . $this->modelClass);
			$this->redirect($this->referer());
			return;
		}

		$this->{$this->modelClass}->id = $id;
		$itemName = "Item";
		
		if($this->{$this->modelClass}->hasField('name')) {
			$itemName = $this->{$this->modelClass}->field('name');
		} else if($this->{$this->modelClass}->hasField('title')) {
			$itemName = $this->{$this->modelClass}->field('title');
		}
		
		if ($this->{$this->modelClass}->delete($id)) {
			$this->Notify->success(
				'<strong>' . $itemName . "</strong> deleted successfully. <a href='" 
				. Router::url( array(	'controller' => $this->request['controller'], 
										'plugin' => $this->request['plugin'], 
										'action' => 'undo',
										$id )
							) 
				. "'>Undo</a>"
			);
		}
		$this->redirect($this->referer());
	}
	
/**
 * Default admin undo delete
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	
	public function admin_undo($id = null) {
		if (!$id) {
			$this->Notify->error('Invalid id for ' . $this->modelClass);
			$this->redirect($this->referer());
			return;
		}
		
		$this->{$this->modelClass}->id = $id;
		$this->{$this->modelClass}->saveField('deleted', 0);
		$this->{$this->modelClass}->saveField('deleted_date', null, false);
				
		$itemName = "Item";
		
		if($this->{$this->modelClass}->hasField('name')) {
			$itemName = $this->{$this->modelClass}->field('name');
		} else if($this->{$this->modelClass}->hasField('title')) {
			$itemName = $this->{$this->modelClass}->field('title');
		}
				
		$this->Notify->success( '<strong>' . $itemName . "</strong> restored. <a href='" 
				. Router::url( array(	'controller' => $this->request['controller'], 
										'plugin' => $this->request['plugin'], 
										'action' => 'edit',
										$id )
							) 
				. "'>Edit</a>");
		
		$this->redirect($this->referer());
	}

/**
 * Default admin add
 *
 * @return void
 */
	public function admin_add() {
		if (!empty($this->request->data)) {
			if (isset($this->{$this->modelClass}->validateAdmin)) {
				$this->{$this->modelClass}->setValidation('admin');
			}
			
			if ($this->{$this->modelClass}->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave(array(
					'continue' => array(
						'action' => 'edit',
						$this->{$this->modelClass}->id
					)
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}
	}

/**
 * Default admin edit
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	public function admin_edit($id = null) {
		if (!empty($this->request->data)) {
			if (isset($this->{$this->modelClass}->validateAdmin)) {
				$this->{$this->modelClass}->setValidation('admin');
			}

			if ($this->{$this->modelClass}->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave();
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->{$this->modelClass}->find('edit', array(
				'conditions' => array($this->modelClass . '.id' => $id)
			));
		}
	}

/**
 * Admin index
 *
 * @return	void
 */
	public function admin_index() {
		$name = lcfirst($this->name);
		if (method_exists($this->{$this->modelClass}, 'containedModels')) {
			$contain = $this->{$this->modelClass}->containedModels();
		} else {
			$contain = null;
		}
		$this->AutoPaginate->setPaginate(array(
			'contain' => $contain
		));
		${$name} = $this->paginate();
		$this->set(compact($name));
	}

/**
 * Generic admin function for handling item sorting.
 *
 * @return	void
 */
	public function admin_sort($model, $plugin = null) {
		//
		if ($this->request->is('post') && $model) {
			//
			if ($plugin) {
				//
				$class = $plugin . '.' . $model;
			// 
			} else {
				//
				$class = $model;
			}
			//
			App::import('Model', $class);
			//
			$Item = new $model();
			//
			if ($Item->saveAll($this->request->data[$model], array('validate' => false, 'callbacks' => false))) {
				//
				$this->Notify->success('The order has been updated successfully.');
			// 
			} else {
				//
				$this->Notify->error('There was a problem updating the order.');
			}
		}
		//
		$this->redirect($this->referer());
	}

/**
 * Displays offline layout if:
 * (1) site is in offline mode;
 * (2) user cannot access the admin dashboard; and
 * (3) the request is for something other than the login page, the admin area or the password retrieval process.
 *
 * @return void
 */
	protected function _offlineCheck() {
		//
		if (Cms::online() && !Cms::maintenance_mode()) {
			return;
		}
		//
		if (!isset($this->AccessControl)) {
			return;
		}
		
		if ($this->AccessControl->isAuthorized(array('plugin' => 'administration', 'controller' => 'dashboard', 'action' => 'index', 'admin' => true))) {
			return;
		}
		
		if ($this->AccessControl->isAuthorized(array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'preview', 'admin' => true))) {
			return;
		}

		if (isset($this->request->prefix) && $this->request->prefix == 'admin') {
			return;
		}
 
		if (
			($this->request->plugin == 'users' && $this->request->controller = 'users' && $this->request->action == 'login')
			||
			($this->request->plugin == 'users' && $this->request->controller = 'users' && $this->request->action == 'retrieve')
			||
			($this->request->plugin == 'users' && $this->request->controller = 'users' && $this->request->action == 'verify')
		) {
			return;
		}
		//
		if (Cms::maintenance_mode()) {
			//
			$this->layout = false;
			//
			$this->render('/Layouts/maintenance-mode');
		} else {
			//
			$this->layout = 'offline';
			//
			if (!$this->Session->read('formSuccess')) {
				$this->PageSettings->setTitle(Configure::read('Settings.Pages.Offline.page_title') ? Configure::read('Settings.Pages.Offline.page_title') : 'Coming soon');
			}
			//
			$this->render('/Elements/offline');
		}
		//
		if ($this->request->is('post') && isset($this->request->data['EmailFormSubmission']) && !empty($this->request->data['EmailFormSubmission'])) {
			return;
		}
		//
		if ($this->Session->read('formSuccess')) {
			return;
		}
	}

/**
 * Check if we do not allow the client to cache.
 *
 * @return void
 */
	protected function _cacheCheck() {
		// 
		if (AccessControl::isAuthorized(array(
			'plugin' => 'caching',
			'controller' => 'cache',
			'action' => 'clear',
			'admin' => true
		))) {
			// HTTP 1.1.
			header("Cache-Control: no-cache, no-store, must-revalidate");
			// HTTP 1.0.
			header("Pragma: no-cache");
			// Proxies.
			header("Expires: 0");
		// 
		}
	}

}