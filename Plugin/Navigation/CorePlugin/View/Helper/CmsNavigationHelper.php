<?php
/**
 * CmsNavigationHelper class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsNavigationAppController.html
 * @package		 Cms.Plugin.Navigation.View.Helper 
 * @since		 Pyramid CMS v 1.0
 */
class CmsNavigationHelper extends AppHelper {
	
/**
 * Array of navigation items corresponding to the current page. There can be multiple
 * 'current pages' in the case of nested menus and such, so that the parents are also
 * marked as current. The last element in the array is the individual page.
 */
	public $currentItems = array();
	
/**
 * Helpers to load
 */
	public $helpers = array(
		'Html' => array('className' => 'appHtml'), 
		'Linking.ModelLink',
		'Prototype.Prototype',
		'Plugin'
	);

/**
 * Tree settings
 */
	protected $_treeSettings = array(
		'model' => 'NavigationMenuItem', 
		'alias' => 'name', 
		'left' => 'lft', 
		'right' => 'rght', 
		'primaryKey' => 'id'
	);

/**
 * Used for generating tab spacing between items in output
 */
	protected $_childCount = false;

/**
 * List nav item 'isCurrentPage' results where the array key is the item ID
 */
	protected $_navItemsIsCurrentPage = array();
/**
 * Returns a formatted string of HTML links for a 'breadcrumbs' display based on the 
 * contents of $this->currentItems, with $separator between each item.
 *
 * @param string $separator Optional
 * @param array $options Optional HTML options to pass to the containing p tag
 * @param array $itemOptions Optional HTML options to pass to each item
 * @return string
 */
	public function breadcrumbs($separator = '>', $options = array(), $itemOptions = array()) {
		$defaults = array('class' => 'breadcrumb-menu');
		$options = array_merge($defaults, $options);

		$items = array();

		if (!$this->currentItems || count($this->currentItems) < 2) {
			return null;
		}

		$end = end($this->currentItems);
		reset($this->currentItems);
		foreach ($this->currentItems as $item) {
			if ($end['NavigationMenuItem']['id'] == $item['NavigationMenuItem']['id']) {
				$items[] = $this->Html->tag(
					'span',
					$item['NavigationMenuItem']['name'],
					array('class' => 'active-breadcrumb')
				);
			} else {
				$items[] = $this->_generateLink($item['NavigationMenuItem'], $itemOptions);
			}
		}

		return $this->Html->tag(
			'p',
			implode($separator, $items),
			$options
		);

	}

/**
 * Returns a formatted string of HTML links for a 'breadcrumbs' display based on the 
 * contents of $this->currentItems, as a single list.
 *
 * @param array $ulOptions
 * @param array $liOptions
 * @return string
 */
	public function breadcrumbsList($ulOptions = array(), $liOptions = array()) {
	//
		$defaults = array('class' => 'breadcrumb-list');
	//
		$items = array();
	//
		if (!$this->currentItems || count($this->currentItems) < 2) {
			return null;
		}
	//
		$end = end($this->currentItems);
	//
		reset($this->currentItems);
	//
		foreach ($this->currentItems as $item) {
	//
			if ($end['NavigationMenuItem']['id'] == $item['NavigationMenuItem']['id']) {
				$item = $this->Html->tag(
					'span',
					$item['NavigationMenuItem']['name'],
					array('class' => 'active-breadcrumb')
				);
			} else {
				$item = $this->_generateLink($item['NavigationMenuItem'], $liOptions);
			}
	//
			$items[] = $this->Html->tag(
					'li'
					, $item
					, $liOptions
				);
		}
	//
		return $this->Html->tag(
				'ul'
				, implode('', $items)
				, $ulOptions
			);
	}

/**
 * Passes work to PrototypeHelper.
 * Returns true if $url is a prototype instance summary that matches the instance of the current request,
 * which must be a item or category detail view.
 *
 * @see CmsPrototypeHelper::isCurrentInstance
 * @param mixex $url array - must be an item array
 * @return boolean
 */
	public function isCurrentInstance($url) {
		return $this->Prototype->isCurrentInstance($url);
	}


/**
 * Returns true if $url matches the user's current page.
 *
 * @param	mixed $url array or string
 * @return	boolean
 */
	public function isCurrentPage($url) {

		$currentRoute = Router::currentRoute();
	
		if (is_array($url)) {
			
			if (isset($url[$this->_treeSettings['model']])) {
				$url = $url[$this->_treeSettings['model']];
			}
			
			if (isset($_navItemsIsCurrentPage[$url['id']])){
				return $_navItemsIsCurrentPage[$url['id']];
			}
			
			$url = $this->itemLink($url, false);
			$return = $this->isCurrentPage($url);
			
			if (isset($url['id'])) {
				$_navItemsIsCurrentPage[$url['id']] = $return;
			}
			
			return $return;
		} else {
			//
			$url	= trim($url);
		}
	
		if ($url == $this->request->here) {
			return true;
		}
	
		if ($url == Router::url( $this->request->here, true )) {
			return true;
		}
	
		if($url == substr($currentRoute->template, 0, strpos($this->request->here, '/', 1))) {
			return true;
		}
	
		if ($url == '/' . $this->request->url) {
			return true;
		}
		return false;
	}			

/**
 * Returns the URL to which an item will link, mostly for display purposes.
 *
 * @param	array	$item
 * @param	boolean $full	Whether Router should prepend the website domain and protocol to the URL.
 * @return	string
 */
	public function itemLink($item, $full = true) {
		//
		if (isset($item[$this->_treeSettings['model']])) {
			//
			$item = $item[$this->_treeSettings['model']];
		}
		//
		if ($item['url']) {
			//
			return $item['url'];
		//
		} elseif(is_null($item['foreign_key']) && ($item['foreign_plugin'] && $item['foreign_model'])) {
		//
			return $this->Html->url(
				array(
					'plugin'	=> strtolower($item['foreign_plugin'])
					, 'controller'	=> strtolower($item['foreign_model'])
					, 'action'	=> 'index'
					, 'full_base'	=> true
					, 'admin'	=> false
					,
				)
			);
		} elseif(!is_numeric($item['foreign_key']) && ($item['foreign_plugin'] && $item['foreign_model'])) {
			return $this->Html->url(
				array(
					'plugin'	=> strtolower($item['foreign_plugin'])
					, 'controller'	=> strtolower($item['foreign_model'])
					, 'action'	=> 'index'
					, 'slug'	=> $item['foreign_key']
					, 'full_base'	=> true
					, 'admin'	=> false
					,
				)
			);
		} else if (!$item['foreign_model'] || !$item['foreign_key']) {
			return null;
		} else if (!empty($item['foreign_model'])) {
			if (!empty($item['foreign_plugin'])){
				return Router::url($this->ModelLink->link($item['foreign_plugin'] . '.' . $item['foreign_model'], $item['foreign_key']), $full);
			} else {
				//use the plugin helper to determine the plugin that the model belongs to
				return Router::url( $this->ModelLink->link($this->Plugin->getModelsPlugin($item['foreign_model']) . '.' . $item['foreign_model'], $item['foreign_key']), $full);
			}
		} else {
			return false;
		}
	}

/**
 * Convenience method to only show the top level of a navigation menu.
 *
 * @param	mixed	$id
 * @param	array	$settings	OPTIONAL
 * @return	string
 */
	public function showTop($id, $settings = array()) {
		return $this->show($id, false, $settings);
	}

/**
 * Outputs a navigation menu consisting of children of a navigation menu item.
 *
 * @param	integer $navigationMenuItemId
 * @param	boolean $recursive	OPTIONAL
 * @param	array	$settings	OPTIONAL
 * @return	string
 */
	public function showChildren($navigationMenuItemId, $recursive = false, $settings = array()) {
		$settings = array_merge(array(
			'ulId' => null, 
			'ulClass' => null, 
			'liClass' => null, 
			'addLiCount' => false, 
			'addDepth' => false, 
			'markCurrent' => true
		), $settings);
		
		$topItem = ClassRegistry::init('Navigation.NavigationMenuItem')->find('first', array(
			'conditions' => array(
				'NavigationMenuItem.id' => $navigationMenuItemId
			)
		));
		
		if ($recursive) {
			$items = ClassRegistry::init('Navigation.NavigationMenuItem')->find('threaded', 
					array(
						'conditions' => array(
							'NavigationMenuItem.lft >' => $topItem['NavigationMenuItem']['lft'], 
							'NavigationMenuItem.rght <' => $topItem['NavigationMenuItem']['rght'],
							'NavigationMenuItem.navigation_menu_id' => $topItem['NavigationMenuItem']['navigation_menu_id'],
						),
						'order' => 'NavigationMenuItem.lft ASC',
						'published' => true
					));
			return $this->_generateTree($items, $settings);
		} else {
			if ( !isset( $topItem['NavigationMenuItem']['id'] ) || empty( $topItem['NavigationMenuItem']['id'] ) ) {
				return null;
			}
			$items = ClassRegistry::init('Navigation.NavigationMenuItem')->find('all', array(
				'conditions' => array(
					'NavigationMenuItem.parent_id' => $topItem['NavigationMenuItem']['id']
				),
				'order' => 'NavigationMenuItem.lft ASC',
				'published' => true
			));
			return $this->_generateFlatList($items, $settings);
		}
		
		return null;
	}

/**
 * Basic method to output a navigation menu, given menu id $id.
 *
 * @param	mixed	$id
 * @param	boolean $children	OPTIONAL
 * @param	array	$settings	OPTIONAL
 * @return	string
 */
	public function show($id, $children = true, $settings = array()) {
		if ($children) {
			$defaults = array(
				'ulId' => null, 
				'ulClass' => 'menu_level_', 
				'liClass' => 'menu_', 
				'addLiCount' => true, 
				'addDepth' => true, 
				'markCurrent' => true, 
				'showChildren' => 'all',
				'element' => false,
				'plugin' => false,
				'cache' => true,
				'publishing' => true
			);
		} else {
			$defaults = array(
				'ulId' => null, 
				'ulClass' => null, 
				'liClass' => null, 
				'addLiCount' => true, 
				'markCurrent' => true,
				'element' => false,
				'plugin' => false,
				'cache' => true,
				'publishing' => true
			);
		}
		//
		$settings = array_merge($defaults, $settings);
		//
		if ($settings['cache']) {
			$cacheName = 'navshow' . (is_array($id) ? md5(serialize($id)) : str_replace(' ', '_', $id)) . '_' . ($children ? 't' : 'f') . '_' . md5(serialize($settings)) . str_replace('/', '_', $this->request->url);
			$output = Cache::read($cacheName, 'navigation');
			$this->currentItems = Cache::read($cacheName . '_current_items', 'navigation');
		}
		//
		$output		= null;
		//
		if (!isset($output) || !$output) {
			if (is_array($id)) {
				$output = $this->_generateTree($id, $settings);
			} else if (is_numeric($id)) {
				$conditions = array(
					'NavigationMenuItem.navigation_menu_id' => $id
				);
			} else {
				$list = ClassRegistry::init('NavigationMenu')->find('first', array(
					'conditions' => array(
						'NavigationMenu.name' => $id
					), 
					'fields' => array(
						'NavigationMenu.id'
					), 
					'cache' => true
				));
				
				$conditions = array(
					'NavigationMenuItem.navigation_menu_id' => $list['NavigationMenu']['id']
				);
			}
			//
			$items = ClassRegistry::init('Navigation.NavigationMenuItem')->findForHelperShow($conditions);
			//
			if ($children) {
				$output = $this->_generateTree($items, $settings);
			} else {
				$output = $this->_generateFlatList($items, $settings);
			}
			
			if ($settings['cache']) {
				Cache::write($cacheName, $output, 'navigation');
				Cache::write($cacheName . '_current_items', $this->currentItems, 'navigation');
			}
		}
		
		return $output;
	}
	
/**
 * Returns the first in the $currentItems array, if any.
 *
 * @return	mixed array on success, null on failure
 */
	public function topCurrentItem() {
		if (!isset($this->currentItems[0]) || empty($this->currentItems[0])) {
			return null;
		}
		return $this->currentItems[0];
	}
	
/**
 * Generates a flat list from tree data
 *
 * @param	array	$data
 * @param	array	$settings	OPTIONAL
 * @return	string
 */
	protected function _generateFlatList($data, $settings = array()) {
		$settings = array_merge(array(
			'ulId' => null, 
			'ulClass' => null, 
			'liClass' => null, 
			'addLiCount' => false, 
			'markCurrent' => true, 
			'escape' => true
		), $settings);
		extract($settings);
		
		$return = '';
		$class = null;
		$id = null;
		if ($ulClass) {
			$class = " class=\"" . $ulClass . "\"";
		}
		if ($ulId) {
			$id = " id=\"" . $ulId . "\"";
		}
		
		$return = "<ul" . $class . $id . ">";
		
		foreach ($data as $i => $item) {
			$itemClass = null;
			if ($liClass) {
				$itemClass = array( );
				if ($addLiCount) {
					$itemClass[] = $liClass . ($i + 1) . (!empty($node['children']) ? ' has-sub' : '');
				} else {
					$itemClass[] = $liClass;
				}
				
				if ($i == 0) {
					$itemClass[] = 'first';
				}
				
				if (!isset($data[$i + 1])) {
					$itemClass[] = 'last';
				}
			}
			
			$nodeUrl = $this->_generateUrl($item, $this->_treeSettings['model']);
			
			if (!isset($item[$this->_treeSettings['model']]['url'])){
				$item[$this->_treeSettings['model']]['url'] = $nodeUrl;
			}
			
			if ($this->_hasCurrentChild($item) || $this->isCurrentPage($this->itemLink($item[$this->_treeSettings['model']], false)) || $this->isCurrentInstance($item)) {
				$this->currentItems[] = $item;
				$itemClass[] = 'current';
			}
			
			if ($itemClass) {
				$itemClass = ' class="' . implode(' ', $itemClass) . '"';
			}
		
			$itemReturn = "<li" . $itemClass . ">";
			$itemReturn .= $this->_generateLink($item[$this->_treeSettings['model']], $settings);	
			$itemReturn .= "</li>";
			$return .= $itemReturn;
		}
		
		$return .= "</ul>";
		return $return;
	}

/**
 * Generate nested list from tree data
 *
 * @param	array	$data
 * @param	array	$settings	OPTIONAL
 * @return	string
 */
	protected function _generateTree($data, $settings = array()) {
		$settings = array_merge(array(
			'ulId' => null, 
			'ulClass' => 'menu_level_', 
			'liClass' => 'menu_', 
			'addLiCount' => true, 
			'addDepth' => true, 
			'markCurrent' => false, 
			'showChildren' => 'all'
		), $settings);
		
		extract($this->_treeSettings);
		extract($settings);
		
		$this->_childCount = 1;
		$this->_depth = 0;
		$this->_current = null;
		
		return $this->_loopData($data, $settings);
	}

/**
 * Loops through $data to output a nested list
 *
 * @param	array	$data
 * @param	array	$settings	OPTIONAL
 * @return	string
 */
	protected function _loopData($data, $settings = array()) {
		//
		$settings = array_merge(
				array(
					'ulId' => null, 
					'ulClass' => 'menu_level_', 
					'liClass' => 'menu_', 
					'addLiCount' => true, 
					'addDepth' => true, 
					'markCurrent' => false, 
					'showChildren' => 'all', 
					'escape' => true,
					'publishing' => true
				), $settings);
		//
		extract($this->_treeSettings);
		//
		extract($settings);
		//
		if ($ulId && $this->_depth == 0) {
			//
			$id = " id=\"" . $ulId . "\" ";
		//
		} else {
			//
			$id = '';
		}
		//
		if ($ulClass) {
			//
			if ($addDepth) {
				//
				$topUl = $ulClass . $this->_depth;
			}
			//
			$return = '<ul class="' . $topUl . '"' . $id . '>';
		//
		} else {
			//
			$return = '<ul' . $id . '>';
		}
		//
		foreach ($data as $i => $node) {
			//
			$class	= array();
			//
			$first	= false;
			//
			if ($i == 0) {
				//
				$first		= true;
				//
				$class[]	= 'first';
			}
			//
			$last = false;
			//
			if (!isset($data[$i + 1])) {
				//
				$last		= true;
				//
				$class[]	= 'last';
			}
			// Is current?
			if ($this->_hasCurrentChild($node) || $this->isCurrentPage($this->itemLink($node[$model], true)) || $this->isCurrentInstance($node)) {
				//
				$this->currentItems[] = $node;
				//
				if ($markCurrent) {
					//
					$class[] = 'current';
				}
			}
			
			if ($liClass) {
				$liClass = str_replace('%depth%', $this->_depth, $liClass);
				if ($addLiCount) {
					$class[] = $liClass . ($i + 1) . (!empty($node['children']) ? ' has-sub' : '');
				} else {
					$class[] = $liClass;
				}
			}
			
			if (count($class) == 1) {
				$class = $class[0];
			} else {
				$class = implode(' ', $class);
			}
			
			if ($class) {
				$return .= '<li class="' . $class . '">';
			} else {
				$return .= '<li>';
			}
			
			$return .= $this->_generateLink($node, $settings, $model);
			
			if (!empty($node['children'])) {
				//
				if ($showChildren == 'all' || ($showChildren == 'current' && ($this->_hasCurrentChild($node) || array_key_exists($node[$model]['id'], (array)$this->_current)))) {
					//
					$this->_depth++;
					//
					$return .= $this->_loopData($node['children'], $settings);
					//
					$this->_depth--;
				}
			}
			
			$return .= "</li>";
		}
		
		$return .= '</ul>';
		return $return;
	}

/**
 * Returns true if a child of $node has a URL that matches the current page
 *
 * @param	array	$node
 * @return	boolean
 */
	protected function _hasCurrentChild($node) {
		if (!isset($node['children']) || empty($node['children'])) {
			return false;
		}

		foreach ($node['children'] as $child) {
			if ($this->isCurrentPage($child) || $this->_hasCurrentChild($child)) {
				return true;
			}
		}
		
		return false;
	}

/**
 * Returns the url for a navigationMenuItem with the optional $model specifier passed in
 */
	protected function _generateUrl($item, $model = null){
		if ($model) {
			$original = $item;
			$item = $item[$model];
		}
		
		if ($item['url']) {
			return $item['url'];
		} else if(!is_numeric($item['foreign_key']) && ($item['foreign_plugin'] && $item['foreign_model'])) {
			//there's a foreign key, model, and plugin
			return Router::url( array(
				'plugin'	=> strtolower($item['foreign_plugin'])
				,'controller'	=> strtolower($item['foreign_model'])
				, 'action'	=> 'index'
				, 'slug'	=> $item['foreign_key']
				, 'full_base'	=> true
				, 'admin'	=> false
			));
		} else if (is_null($item['foreign_key']) && ($item['foreign_plugin'] && $item['foreign_model'])) {
			//otherwise there's no foreign key but there is a model and plugin
			return Router::url(array(
				'plugin'	=> strtolower($item['foreign_plugin'])
				, 'controller'	=> strtolower($item['foreign_model'])
				, 'action'	=> 'index'
				, 'full_base'	=> true
				, 'admin'	=> false
			));
		} else if (!empty($item['foreign_model'])) {
			if (!empty($item['foreign_plugin'])){
				return Router::url($this->ModelLink->link($item['foreign_plugin'] . '.' . $item['foreign_model'], $item['foreign_key']));
			} else {
				//use the plugin helper to determine the plugin that the model belongs to
				return Router::url( $this->ModelLink->link($this->Plugin->getModelsPlugin($item['foreign_model']) . '.' . $item['foreign_model'], $item['foreign_key']));
			}
		} else {
			//unable to generate a URL for this item
			return false;
		}
	}

/**
 * Generates a link for a single node, either with a simple link or
 * via a view element.
 *
 * @param	array	$item
 * @param	array	$settings OPTIONAL
 * @param   string $model OPTIONAL - if $item contains multiple model keys, $model will be used as the nav item one.
 * @return	string
 */
	protected function _generateLink($item, $settings = array(), $model = null) {
		if ($model) {
			$original = $item;
			$item = $item[$model];
		}

		if (!array_key_exists('escape', $settings)) {
			$settings['escape'] = false;
		}
		
		if (isset($settings['element']) && $settings['element']) {
			$view = ClassRegistry::getObject('view');
			$elementData = array(
				'data' => $item,
				'plugin' => isset($settings['plugin']) ? $settings['plugin'] : null
			);
			
			return $view->element($settings['element'], $elementData);
		} else {		
			if ($item['url']) {
				$link = $item['url'];
			//
			} elseif(!is_numeric($item['foreign_key']) && ($item['foreign_plugin'] && $item['foreign_model'])) {
				//
				return $this->Html->link(
					$item['name']
					, array(
						'plugin'	=> strtolower($item['foreign_plugin'])
						,'controller'	=> strtolower($item['foreign_model'])
						, 'action'	=> 'index'
						, 'slug'	=> $item['foreign_key']
						, 'full_base'	=> true
						, 'admin'	=> false
						,
					)
				);
			} elseif(is_null($item['foreign_key']) && ($item['foreign_plugin'] && $item['foreign_model'])) {
				//
				return $this->Html->link(
					$item['name']
					, array(
						'plugin'	=> strtolower($item['foreign_plugin'])
						, 'controller'	=> strtolower($item['foreign_model'])
						, 'action'	=> 'index'
						, 'full_base'	=> true
						, 'admin'	=> false
						,
					)
				);
			} else {
				//
				$link = $this->ModelLink->link($item['foreign_plugin'] . '.' . $item['foreign_model'], $item['foreign_key']);
			}
			
			$options = array('escape' => $settings['escape']);
			if ($item['new_window'] == 1) {
				$options['target'] = '_blank';
			}
		//			
			return $this->Html->link($item['name'], $link, $options);
		}
	}

/**
 * Returns all NavigationMenus that are set to display in the sitemap
 * 
 * @return array
 */
	public function error404Sitemap() {
	//
		$navigationMenus = ClassRegistry::init('Navigation.NavigationMenu')->find(
			'all'
			, array(
				'conditions' => array(
					'NavigationMenu.sitemap' => 1
				)
			)
		);
	//
		return $navigationMenus;
	}

}
