<?php
App::uses('PrototypeInstance', 'Prototype.Model');
App::uses('PrototypeItem', 'Prototype.Model');
/**
 * CmsPrototypeHelper class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeHelper.html
 * @package      Cms.Plugin.Prototype.View.Helper 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeHelper extends AppHelper {

/**
 * Helpers
 */
	public $helpers = array(
		'Html' => array('className' => 'AppHtml')
		, 'AdminLink'
	);

/**
 * Instance array - corresponds to current instance set in view.
 */
	protected $_instance = null;

/**
 * Constructor - sets the view variable $_instance in the helper if it's found.
 *
 * @see Helper::__construct
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		if (isset($view->viewVars['_instance'])) {
			$this->_instance = $view->viewVars['_instance'];
		}	
	}

/**
 * Returns a string of categories for $item intended for the admin item index view.
 *
 * @param array $item
 * @return string
 */
	public function adminCategoryList($item) {
		$cats = array();
		foreach ($item['PrototypeCategory'] as $category) {
			$string = $category['name'];
			$path = ClassRegistry::init('Prototype.PrototypeCategory')->getPath($category['id']);
			if (!empty($path) && count($path) > 1) {
				$string .= ' (' . $path[0]['PrototypeCategory']['name'] . ')';
			}
			$cats[] = $string;
		}

		return implode('; ', $cats);
	}

/**
 * Returns child categories for category $id. Optionally recursive.
 *
 * @param integer $id
 * @param boolean $recursive
 * @return array
 */
	public function categoryChildren($id, $recursive = false) {
		$Category = ClassRegistry::init('Prototype.PrototypeCategory');
		
		if ($recursive) {
			$cacheName = 'prototype_category_display_children_' . $id;
			$children = Cache::read($cacheName, 'query');
			if (!$children) {				
				$children = $Category->children($id);
				Cache::write($cacheName, $children, 'query');
			}
		} else {
			$children = $Category->find('all', array(
				'conditions' => array('PrototypeCategory.parent_id' => $id),
				'order' => 'PrototypeCategory.lft ASC',
				'contain' => array('Image', 'Document'),
				'cache' => true 
			));
		}

		return $children;
	}

/**
 * Returns all items for category $categoryId belonging to instance $instanceId.
 *
 * @param integer $instanceId
 * @param integer $categoryId
 * @param array $options Optional query parameters
 * @return array
 */
	public function categoryItems($instanceId, $categoryId, $options = array()) {
		$items = ClassRegistry::init('Prototype.PrototypeItem')->summaryQuery($instanceId, $categoryId, true, $options);
		return $items;		
	}

/**
 * Returns PrototypeInstances $documentTypes array.
 *
 * @return array
 */
	public function documentTypes() {
		return PrototypeInstance::$documentTypes;
	}

/**
 * Function to return a value from $this->_instance using dot notation.
 *
 * @param string path
 * @param array instance - optional prototype instance
 * @return mixed
 */
	public function fetch($path, $instance = null) {
		if (!$instance) {
			$instance = $this->_instance;
		}
		return Hash::get($instance, $path);
	}

/**
 * Returns true if $this->_instance supports categories.
 *
 * @param array instance - optional prototype instance
 * @return boolean
 */
	public function hasCategories($instance = null) {
		if (!$instance) {
			$instance = $this->_instance;
		}
		return $instance['PrototypeInstance']['use_categories'];
	}

/**
 * Returns PrototypeInstances $imageTypes array.
 *
 * @return array
 */
	public function imageTypes() {
		return PrototypeInstance::$imageTypes;
	}

/**
 * Returns an array of published categories for $instance, which uses the current instance if set and
 * otherwise accepts an id.
 *
 * @param integer $instance Optional instance ID or instance array
 * @return mixed array on success, false on failure
 */
	public function instanceCategories($instance = null, $includeItems = true) {
		ClassRegistry::init('Prototype.PrototypeCategory');

		if (!$instance && isset($this->_instance)) {
			$instance = $this->_instance;
		}

		return ClassRegistry::init('Prototype.PrototypeCategory')->findForSummary($instance, $includeItems);
	}

/**
 * Returns an array of published items for $instance, which uses the current instance if set and otherwise
 * accepts an id. 
 *
 * @param integer $instance Optional instance ID or instance array
 * @return mixed array on success, false on failure
 */
	public function instanceItems($instance = null, $options = array()) {
		$Object = ClassRegistry::init('Prototype.PrototypeInstance');

		if (!$instance && isset($this->_instance)) {
			$instance = $this->_instance;
		} else if (is_numeric($instance)) {
			$instance = $Object->findById($instance);
		}

		if (!isset($instance['PrototypeInstance'])) {
			return false;
		}

		$items = $Object->PrototypeItem->summaryQuery($instance, null, true, $options);
		return $items;
	}

/**
 * Loads an item with all associations. Uses CmsPrototypeItem::findForView(), which calls for
 * an array of params.
 *
 * @param integer $id
 * @return array
 */
	public function item($id) {
		return ClassRegistry::init('Prototype.PrototypeItem')->findForView(array('id' => $id));
	}

/**
 * Returns the item model name for the prototype instance if set in $this->_instance.
 *
 * @param array instance - optional prototype instance
 * @return string
 */
	public function itemModel($instance = null) {
		if (!$instance) {
			$instance = $this->_instance;
		}
		if (!isset($instance['PrototypeInstance']['models'])) {
			return null;
		}
		return $instance['PrototypeInstance']['models']['item'];
	}

/**
 * Adds CSS files for an instance to the CSS block if they're found in either:
 * - APP/Plugin/Prototype/webroot/css/<instance-slug>.css
 * - CMS/Plugin/Prototype/webroot/css/<instance-slug>.css
 *
 * @return boolean
 */
	public function instanceCss() {
		return $this->_addAsset('css');
	}

/**
 * Adds JS files for an instance to the script block if they're found in either:
 * - APP/Plugin/Prototype/webroot/js/<instance-slug>.js
 * - CMS/Plugin/Prototype/webroot/js/<instance-slug>.js
 *
 * @return boolean
 */
	public function instanceJs() {
		return $this->_addAsset('js');
	}

/**
 * Returns true if $url is a prototype instance summary that matches the instance of the current request,
 * which must be an item or category detail view.
 *
 * @param mixex $url array - must be an item array
 * @return boolean
 */
	public function isCurrentInstance($url) {
		if (!$this->_instance) {
			return false;
		}

		if (!is_array($url) || !isset($url['NavigationMenuItem'])) {
			return false;
		}

		$url = $url['NavigationMenuItem'];
	//  Added this to aid in identifying a PrototypeItem - without this the current class was not being added to the navigation menu.
		
		if ($url['foreign_plugin'] == 'Prototype' && $this->request->params['controller'] == 'prototype_items' && $this->request->params['action'] == 'view') {
			return false;
		//	return true;
		}

		if ($url['foreign_model'] != 'PrototypeInstance') {
			return false;
		}

	// Only item or category detail views.
		if ($this->request->params['controller'] == 'prototype_instances') {
			return false;
		}

		if ($this->request->params['action'] != 'view') {
			return false;
		}

		if ($this->_instance['PrototypeInstance']['name'] == $url['foreign_plugin']) {
			return true;
		}

		return false;
	}

/**
 * Returns PrototypeInstance $orderOptions array.
 *
 * @return array
 */
	public function orderOptions() {
		return PrototypeInstance::$orderOptions;
	}

/**
 * Called by instanceCss and instanceJs to add CSS or JS files to the proper blocks.
 *
 * @param string asset type
 * @param string optional name if not the name of the instance
 * @return boolean
 */
	protected function _addAsset($type, $name = null) {
		if (!$name) {
			$name = $this->_instance['PrototypeInstance']['name'];
		}

		if ($name == $this->_instance['PrototypeInstance']['name']) {
			$slug = $this->_instance['PrototypeInstance']['slug'];
		} else {
			$instance = ClassRegistry::init('Prototype.PrototypeInstance')->findByName($name);
			if (!$instance) {
				return false;
			}
			$slug = $instance['PrototypeInstance']['slug'];
		}

		switch ($type) {
			case 'css':
				$path = 'Plugin' . DS . 'Prototype' . DS . 'webroot' . DS . 'css' . DS . $slug . '.css';
				if (file_exists(APP . $path) || file_exists(CMS . $path)) {
					return $this->Html->css('Prototype.' . $slug, null, array('inline' => false, 'once' => true));
				} 
			break;
			case 'js':
				$path = 'Plugin' . DS . 'Prototype' . DS . 'webroot' . DS . 'js' . DS . $slug . '.js';
				if (file_exists(APP . $path) || file_exists(CMS . $path)) {
					return $this->Html->script('Prototype.' . $slug, array('inline' => false, 'once' => true));
				} 
			break;
		}

		return false;
	}

		public function listAliases() {
	
		$query = 'select `name` as "name" from `prototype_instances` where `deleted` = 0';
		$instance = new PrototypeInstance();
		$list = $instance->query($query);
		$names = array();
		
		foreach($list as $item) {
			$names[] = $item['prototype_instances']['name'];
		}

		return $names;
	}
	
/**
 * Shows a flag icon for allowing the user to toggle the featured state of a prototype item
 */
	public function toggleFeatured($data, $ajax = false) {
		// Add the AJAX script
		if ($ajax) {
			$this->Html->script('Prototype.ajax', array('inline' => false, 'once' => true));
		}

		$featured = ClassRegistry::init('Prototype.PrototypeItem')->isFeatured($data['PrototypeItem']['id']);

		return $this->Html->link(
			$this->AdminLink->toggleImage($featured),
			array(
				'plugin' => 'prototype',
				'controller' => 'prototype_items',
				'action' => 'toggle_featured',
				'admin' => true,
				$data['PrototypeItem']['id'],
				$featured ? 'off' : 'on'
			),
			array(
				'class' => 'toggle-featured',
				'title' => 'Click to toggle featured',
				'escape' => false
			)
		);
	}
}