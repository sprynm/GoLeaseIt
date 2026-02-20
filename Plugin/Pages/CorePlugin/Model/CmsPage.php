<?php
/**
 * CmsPage class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPage.html
 * @package		 Cms.Plugin.Pages.Model
 * @since		 Pyramid CMS v 1.0
 */
class CmsPage extends PagesAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'AppTree' => array(
			'parent' => 'parent_id'
		),
		'Sluggable' => array(
			'label' => 'title'
		),
		'CustomFields.CustomField' => array(
			'searchFields' => array('name')
		),
		'CustomFields.Expandable',
		'TreeSort.TreeSort',
		'Metas.MetaTag' => array('contentField' => 'content'),
		'Versioning.Revision' => array('preview' => true),
		'Versioning.SoftDelete',
		'Users.Lockable' => array( 'labelField' => 'page_heading' )
	);

/**
 * hasMany
 */
	public $hasMany = array(
		'Image' => array(
			'className'	=> 'Media.Attachment'
			, 'foreignKey'	=> 'foreign_key'
			, 'conditions'	=> array(
					'Image.model'		=> 'Page'
					, 'Image.group'		=> 'Image'
					, 'Image.deleted'	=> false
					,
			)
			, 'order'	=> 'Image.id DESC'
			, 'limit'	=> 1
			, 'dependent'	=> true
			, 
		)
		,'PageField' => array(
			'className' => 'CustomFields.CustomField',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'PageField.model' => 'Page'
			),
			'dependent' => true,
			'order' => 'PageField.rank ASC, PageField.id ASC'
		)
	);

/**
 * Generic link array for Linkable behavior
 */
	public $linkFormat = array(
		'function' => array(
			'function' => 'pageLink',
			'arguments' => array('{alias}.id')
		)
	);

/**
 * Validation array
 */
	public $validate = array(
		'title' => array(
			'rule' => 'notEmpty', 
			'message' => 'This field is required.', 
			'required' => true
		),
		'page_heading' => array(
			'rule' => 'notEmpty', 
			'message' => 'This field is required.', 
			'required' => true
		)
	);

/**
 * Virtual fields
 */
	public $virtualFields = array(
		"action_map" => "CONCAT_WS('/', NULLIF(Page.plugin, ''), NULLIF(Page.controller, ''), NULLIF(Page.action, ''), NULLIF(Page.extra, ''))"
	);

/**
 * Constructor to modify the tree behavior used depending on CMS version.
 *
 * @see Model::__construct
 */
	public function __construct($id = null, $table = null, $ds = null) {
		if (Cms::minVersion('1.0.4')) {
			$this->actsAs['AppTree']['scope'] = array(
				"Page.plugin = ''"
			);
		}
		parent::__construct($id, $table, $ds);
	}

/**
 * 
 * 
 *
 * 
 */
	public function strReplace($data, $content, $options = array()) {
		//
		$return		= $content;
		//
		if (!$data || empty($data) || empty($return)) {
			//
			return	$return;
		}
		//
		$find		= array();
		//
		$replace	= array();
		//
		foreach ($data AS $dataKey => $datum) {
			//
			$find[]		= '[' . $dataKey . ']';
			//
			$replace[]	= $datum;
		}
		//
		return		str_replace($find, $replace, $content);
	}

/**
 * Updates path information after a save is finished, which has to be done here rather than afterSave()
 * for transactional purposes.
 *
 * @see Model::afterSave
 */
	public function saveAll($data = array(), $options = array()) {
		$saved = parent::saveAll($data, $options);
		if ($saved) {
			// Saving a single record
			if (isset($data['Page']) || isset($data['id'])) {
				$id = $this->id;
				$page = $this->findById($id);
				$this->id = null;
				$this->_updatePath($page);
				$this->_updateChildPaths($page);
				$this->id = $id;
			} else {
				// Saving multiple records, usually by AJAX reordering
				foreach ($data as $key => $val) {
					if (!isset($val['id']) || !$val['id']) {
						continue;
					}
					$page = $this->findById($val['id']);
					$this->_updatePath($page);
					$this->_updateChildPaths($page);
				}
			}
		}

		return $saved;
	}

/**
 * If title is empty and page_heading is not, set title to = page_heading.
 *
 * @return boolean
 */
	public function beforeValidate($options = array()) {
		if (!array_key_exists('page_heading', $this->data[$this->alias])) {
			return true;
		}

		if (!isset($this->data[$this->alias]['title'])) {
			$this->data[$this->alias]['title'] = null;
		}

		if (!empty($this->data[$this->alias]['title'])) {
			return true;
		}

		$this->data[$this->alias]['title'] = $this->data[$this->alias]['page_heading'];
		return true;
	}
	
/**
 * Obfuscate email addresses.
 *
 * @return $results
 */
	public function afterFind($results = array(), $primary = false) {
		return $results;
	}
		
/**
 * Finds pages with exclude_sitemap = 1 and returns an array of URLs for inclusion in robots.txt.
 * Called by CmsSitemapControler::robots().
 *
 * @return array
 */
	public function findDisallowedRobots() {
		$pages = $this->find('all', array(
			'conditions' => array('Page.exclude_sitemap' => true),
			'fields' => array('modified', 'plugin', 'controller', 'action', 'extra', 'exclude_sitemap', 'slug', 'path', 'id', 'title')
		));

		$urls = array();
		foreach ((array)$pages as $page) {
			$urls[] = Router::url($this->link($page));
		}

		return $urls;
	}
/**
 * Returns an array of pages for use in the sitemap plugin. Called by the sitemap
 * event listener in CmsPagesEventListener.
 *
 * @return void
 */
	public function findForSitemap() {
		$pages = $this->find('all', array(
			'conditions' => array('exclude_sitemap' => false),
			'fields' => array('modified', 'plugin', 'controller', 'action', 'extra', 'exclude_sitemap', 'slug', 'path', 'id', 'title'),
			'published' => true
		));
		foreach ($pages as $i => $page) {
			if (!$this->parentsArePublished($page['Page']['id'])) {
				unset($pages[$i]);
				continue;
			}
			$pages[$i]['Page']['url'] = Router::url($this->link($page), true);
		}
		return $pages;
	}

/**
 * Finds a page for viewing via CmsPagesController::view().
 *
 * @param mixed either a string path for looking up, or a numeric id.
 * @return array
 */
		public function findForView($path = null) {
		//
		$online		= Cms::online();
		//
		$signedIn	=  Authsome::get('Group');
		//
		if (!$online && !$signedIn) {
			//
			$page['Page']['content']	= Configure::read('Settings.Pages.Offline.page_content');
			//
			$page['Page']['password']	= '';
			//
			return $page;
		}
		//
		$online		= Cms::maintenance_mode();
		//
		if ($online && !$signedIn) {
			//
			$page['Page']['content']	= '';
			//
			$page['Page']['password']	= '';
			//
			return $page;
		}
		//
		$conditions = array(
			'Page.plugin'		=> ''
			, 'Page.controller'	=> ''
			, 'Page.published'	=> 1
			,
		);
		//
		if (is_numeric($path)) {
			//
			$conditions['Page.id'] = $path;
		} else {
			//
			$conditions['Page.path'] = $path;
		}
		//
		$page = $this->find(
				'first'
				, array(
					'conditions'	=> $conditions
					, 'contain'	=> array(
								'Image'
								,
					)
					, 'cache'	=> true
					,
				)
		);
		//
		if ($page) {
			//
			if (!$this->parentsArePublished($page['Page']['id'])) {
				//
				return false;
			}
		}
		//
		return $page;
	}

/**
 * Returns true if page $id has published = 1.
 *
 * @param integer $id
 * @return boolean
 */
	public function isPublished($id) {
		$count = $this->find('count', array(
			'conditions' => array(
				'Page.id' => $id,
				'Page.published' => 1
			)
		));
		return $count > 0;
	}

/**
 * Returns a link to a page based on $id. The link will either be the page path or
 * the action map, if applicable.
 *
 * @param integer id
 * @return array
 */
	public function pageLink($id) {
		$page = $this->findById($id);
		if (!$page) {
			return null;
		}
		
		// "Full" page
		if (!$page['Page']['action_map']) {
			return array(
				'plugin' => 'pages',
				'controller' => 'pages',
				'action' => 'view',
				'path' => $page['Page']['path']
			);
		}
		
		//  Pages linked to a controller action
		$link = array(
			'plugin' => $page['Page']['plugin'],
			'controller' => $page['Page']['controller'],
			'action' => $page['Page']['action'],
		);
		
		if ($page['Page']['extra']) {
			$link[] = $page['Page']['extra'];
		}
		
		return $link;
	}

/**
 * Returns true if all the parents of page with $id are published, false otherwise.
 *
 * @param integer id
 * @return boolean
 */
	public function parentsArePublished($id) {
		$cacheName = 'page_parents_' . $id;
		$parents = Cache::read($cacheName, 'query');
		if ($parents === false) {
			$parents = $this->getPath($id, array('id', 'published'));
			Cache::write($cacheName, $parents, 'query');
		}

		if ($parents === null) {
			// No path information - the id is invalid
			return false;
		} else if (count($parents) == 1) {
			// One entry = no parents, so nothing to check (this is a top level page)
			return true;
		} else {
			// If any unpublished parents are found, return false
			foreach ($parents as $parent) {
				if ($parent['Page']['published'] < 1) {
					return false;
				}
			}
		}

		return true;
	}

/**
 * Updates the paths for $page and its children. If $page is numeric then it's loaded first.
 *
 * @param mixed $page
 * @return boolean
 */
	public function updatePagePaths($page) {
		if (is_numeric($page)) {
			$page = $this->findById($page);
		}
		return $this->_updatePath($page) && $this->_updateChildPaths($page);
	}

/**
 * Updates the paths of all children of $page. Called mainly y afterSave.
 *
 * @param array the page record
 * @return boolean
 */
	protected function _updateChildPaths($page) {
		$children = $this->children($page['Page']['id']);
		if (empty($children)) {
			return false;
		}
		
		foreach ($children as $key => $val) {
			$this->_updatePath($val);
		}
		
		return true;
	}
/**
 * Updates the path of a page based on its slug and its parents' slugs. Called by afterSave.
 *
 * @param array the page record
 * @return boolean
 */
	protected function _updatePath($page) {
		if (!isset($page['Page'])) {
			return false;
		}

		if ($page['Page']['action_map']) {
			return false;
		}

		$path = array();
		
		$parents = $this->getPath(
			$page['Page']['id'],
			array('id', 'slug', 'title')
		);

		foreach ((array)$parents as $parent) {
			$slug = trim($parent['Page']['slug'], '/');
			$path[] = $slug;
		}

		if (empty($path)) {
			$path = '';
		} else if (count($path) > 1) {
			$path = implode('/', $path);
		} else {
			$path = $path[0];
		}

		$path = ltrim($path, '/');

		// Only save if the path has changed
		if ($path == $page['Page']['path']) {
			return true;
		}

		$data = array('Page' => array(
			'id' => $page['Page']['id'],
			'path' => $path
		));
		
		return $this->save($data, array('validate' => false, 'callbacks' => false));
	}

}
