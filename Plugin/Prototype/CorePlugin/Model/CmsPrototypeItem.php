<?php
/**
 * CmsPrototypeItem class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeItem.html
 * @package      Cms.Plugin.Prototype.Model 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeItem extends PrototypeAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Sluggable' => array(
			'overwrite' => true,
			'label' => 'head_title'
		), 
		'CustomFields.CustomField' => array(
			'searchFields' => array('name')
		),
		'CustomFields.Expandable',
		'Publishing.Publishable',
		'Sortable' => array(
			'group' => 'prototype_instance_id'
		),
		'Versioning.SoftDelete',
		'Versioning.Revision' => array('preview' => true),
		'Search.Searchable',
		'Copyable',
		'Metas.MetaTag' => array('contentField' => 'name'),
		'Users.LockableChild' => array(
			'labelField' => 'name'
			, 'lockedParent' => 'Prototype.PrototypeInstance'
			, 'foreignKey' => 'prototype_instance_id'
		)
	);
	


/**
 * belongsTo associations
 */
	public $belongsTo = array(
		'PrototypeInstance' => array(
			'className' => 'Prototype.PrototypeInstance', 
			'foreignKey' => 'prototype_instance_id'
		)
	);

/**
 * Search variables for the Search plugin
 */
	public $filterArgs = array(
		'name' => array('type' => 'like'),
		'search' => array('type' => 'query', 'method' => 'simpleSearch')
	);

/**
 * hasAndBelongsToMany associations
 */
	public $hasAndBelongsToMany = array(
		'PrototypeCategory' => array(
			'className' => 'Prototype.PrototypeCategory',
			'joinTable' => 'prototype_categories_prototype_items', 
			'foreignKey' => 'prototype_item_id', 
			'associationForeignKey' => 'prototype_category_id', 
			'unique' => true
		)
	);
	
/**
 * hasMany associations
 */
	public $hasMany = array(
		'Image' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Image.model' => 'PrototypeItem', 
				'Image.group' => 'Image'
			), 
			'dependent' => true, 
			'order' => 'Image.rank ASC, Image.id ASC'
		), 
		'Document' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Document.model' => 'PrototypeItem', 
				'Document.group' => 'Document'
			), 
			'dependent' => true, 
			'order' => 'Document.rank ASC'
		),
		'ItemBannerImage' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'ItemBannerImage.model' => 'PrototypeItem',
				'ItemBannerImage.group' => 'Item Banner Image'
			),
			'dependent' => true
		),
	);

/**
 * Generic link array for Linkable behavior
 */
	public $linkFormat = array(
		'link' => array(
			'plugin' => 'prototype',
			'controller' => 'prototype_items',
			'action' => 'view',
			'slug' => '{alias}.slug',
			'id' => '{alias}.id',
			'instance' => 'PrototypeInstance.slug'
		),
		'contain' => array(
			'PrototypeInstance'
		)
	);

/**
 * Validate array
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true, 
			'message' => 'This field is required.'
		)
	);

/**
 * Edit query conditions
 */
	protected $_editQuery = array(
		'contain' => array(
			'Image',
			'Document',
			'PrototypeCategory',
			'PrototypeInstance',
			'ItemBannerImage'
		)
	);

/**
 * afterFind callback
 *
 * @param array $results
 * @param boolean $primary
 * @return array
 */
	public function afterFind($results, $primary = false) {
		
		foreach ($results as $key => $val) {		
				
			if(Router::getRequest()->params['action'] != 'admin_edit') {
				if(isset($val['PrototypeItem'])) {
					foreach($val['PrototypeItem'] as $k => $v) {
						if( ($k != 'modified')  && ($k != 'slug') ) { 
							$this->emailize($val['PrototypeItem'][$k]);
							$results[$key]['PrototypeItem'][$k] = $val['PrototypeItem'][$k];
						}
						//
						if ($k == 'slug') {
							//
							$results[$key]['PrototypeItem'][$k]	= strtolower($results[$key]['PrototypeItem'][$k]);
						}
					}
				}
			}
			
			if (!isset($val['PrototypeItem']) || !isset($val['PrototypeCategory'])) {
				continue;
			}

			if (!isset($val['PrototypeItem']['name'])) {
				continue;
			}
			if (!$val['PrototypeItem']['head_title']) {
				$results[$key]['PrototypeItem']['head_title'] = $val['PrototypeItem']['name'];
			}
		}
		return $results;
	}


	
/**
 * If head_title is empty and name is not, set head_title to = name.
 *
 * @return boolean
 */
	public function beforeValidate($options = array()) {
		if (!array_key_exists('name', $this->data[$this->alias])) {
			return true;
		}

		if (!isset($this->data[$this->alias]['head_title'])) {
			$this->data[$this->alias]['head_title'] = null;
		}

		if (!empty($this->data[$this->alias]['head_title'])) {
			return true;
		}

		$this->data[$this->alias]['head_title'] = $this->data[$this->alias]['name'];
		return true;
	}

/**
 * Admin index query, used in pagination
 *
 * @param array prottoype instance
 * @return array
 */
	public function adminIndexQuery($instance) {
		return array(
			'conditions' => array(
				'PrototypeItem.prototype_instance_id' => $instance['PrototypeInstance']['id']
			),
			'contain' => array('PrototypeCategory'),
			'limit' => 10000
		);
	}

/**
 * Finds custom fields for the instance - overrides the function in CmsCustomFieldBehavior.
 *
 * @return array
 */
	public function findCustomFields() {
		$foreignKey = null;
		if (isset($this->data[$this->alias]['prototype_instance_id'])) {
			$foreignKey = $this->data[$this->alias]['prototype_instance_id'];
		}
		return ClassRegistry::init('CustomFields.CustomField')->findForModel('PrototypeInstance', $foreignKey, $this->alias);
	}

/**
 * Finds 'featured' prototype items for $instance, cutting down the list as necessary based on $limit.
 * If $limit is null, 'all', or < 1, then all featured items will be returned.
 *
 * @param array $instance PrototypeInstance array
 * @param integer $limit
 * @return array
 */
	public function findFeatured($instance, $limit = 1) {
		$query = array(
			'conditions' => array(
				'PrototypeItem.prototype_instance_id' => $instance['PrototypeInstance']['id'],
				'PrototypeItem.featured' => 1
			),
			'contain' => array('Image', 'Document'),
			'order' => 'RAND()',
			'published' => true
		);

		if (is_numeric($limit) && $limit > 0) {
			$query['limit'] = $limit;
		}

		return $this->find('all', $query);	
	}

/**
 * Gets items for the admin index. Puts all category names (if applicable) into a column for display
 * in the admin.
 *
 * @return array
 */
	public function findForAdminIndex($instance) {
		$items = $this->find('all', array(
			'conditions' => array(
				'PrototypeItem.prototype_instance_id' => $instance['PrototypeInstance']['id']
			),
			'contain' => array('PrototypeCategory')
		));
		return $items;
	}

/**
 * Returns prototype items for the sitemap listener. Only returns items for instances
 * that allow individual item views.
 *
 * @return array
 */
	public function findForSitemap() {
		//
		$instances = $this->PrototypeInstance->find('list', array('fields' => array('id', 'allow_item_views'), 'conditions' => array('deleted' => false)));
		//
		$changeFrequencies = $this->PrototypeInstance->find('list', array('fields' => array('id', 'item_changefreq'), 'conditions' => array('deleted' => false)));
		//
		$items = $this->find('all', array('fields' => array('id', 'slug', 'modified', 'name', 'prototype_instance_id'), 'published' => true));

		foreach ($items as $i => $item) {
			if (empty($instances[$item['PrototypeItem']['prototype_instance_id']])) {
				unset($items[$i]);
				continue;
			}
			//
			$items[$i]['PrototypeItem']['url'] = Router::url($this->link($item['PrototypeItem']['id']), true);
			//
			$items[$i]['PrototypeItem']['changefreq'] = isset($changeFrequencies[$items[$i]['PrototypeItem']['prototype_instance_id']]) && !empty($changeFrequencies[$items[$i]['PrototypeItem']['prototype_instance_id']])
							? $changeFrequencies[$items[$i]['PrototypeItem']['prototype_instance_id']]
							: 'weekly';

		}

		return $items;
	}

/**
 * Finds and returns a prototype item for CmsPrototypeItems::view, using the param
 * array from the request object, or as otherwise passed to the function.
 *
 * @param array the params array from the current request
 * @return array
 */
	public function findForView($params) {
		$conditions = array(
			'PrototypeItem.id' => $params['id']
		);

		if (isset($params['slug']) && !empty($params['slug'])) {
			$conditions['PrototypeItem.slug'] = $params['slug'];
		}

		$item = $this->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'PrototypeCategory',
				'Image',
				'Document',
				'ItemBannerImage'
			),
			'published' => true,
			'cache' => true
		));
		return $item;
	}

/**
 * Constructs query condition for a summary of prototype items depending on the settings
 * in $category and $instance. If $execute is true then an actual find will be executed. 
 * Otherwise, the query array is returned.
 *
 * @param array instance - Prototype instance
 * @param array category - the prototype category - optional
 * @param array options - optional query parameters to pass
 * @return array
 */
	public function summaryQuery($instance, $category = null, $execute = false, $options = array()) {
		if (is_numeric($instance)) {
			$instance = $this->PrototypeInstance->findById($instance);
		}

	//
		$options['limit']	= (isset($options['limit']) && !empty($options['limit'])) ? $options['limit'] : 1000;
    
	//
		$options['order']	= (isset($options['order']) && !empty($options['order'])) ? $options['order'] : $this->PrototypeInstance->itemOrder($instance);

    if (!isset($options['cache'])) {
      $options['cache'] = true;
    }
    
		$query = array(
			'conditions' => array(
				'PrototypeItem.prototype_instance_id' => $instance['PrototypeInstance']['id']
			),
			'contain' => array(
				'Image',
				'Document'
			),
			'published' => true
		);
		if ($category) {
			if (is_numeric($category)) {
				$category = $this->PrototypeCategory->findById($category);
			}
			
			$query['joins'] = array(
				array(
					'table' => 'prototype_categories_prototype_items',
					'alias' => 'PrototypeCategory',
					'type' => 'INNER',
					'conditions' => array(
						'PrototypeCategory.prototype_item_id = PrototypeItem.id',
						'PrototypeCategory.prototype_category_id' => $category['PrototypeCategory']['id']
					)
				)
			);
		}

		$query = Hash::merge( $options, $query );

		if ($execute) {
			return $this->find('all', $query);
		} else {
			return $query;
		}
	}

/**
 * BORROWED FROM THE MEDIA plugin
 * Finds and returns image versions for $Model based on AttachmentVersion records. Only used
 * if $Model does not have its own findImageVersions function.
 *
 * @param object $Model
 * @return array
 */
	public function findImageVersions($data) {
		//
		App::uses('AttachmentVersion', 'Media.Model');
		//
		$Version	= new AttachmentVersion();
		//
		$item		= $this->findById($data['foreign_key']);
		//
		$model		= strpos($data['group'], 'Banner') !== false
				? 'Page'
				: 'PrototypeInstance';
		//
		$group		= strpos($data['group'], 'Banner') !== false
				? 'Image'
				: ($data['group'] == 'FB Item Image' ? 'FB Item Image' : 'Item Image');
		//
		$foreign_key	= strpos($data['group'], 'Banner') !== false
				? NULL
				: $item[$this->alias]['prototype_instance_id'];
		//
		$conditions = array(
			'AttachmentVersion.model'	=> $model
			, 'AttachmentVersion.group'	=> $group
			, 'AttachmentVersion.foreign_key' => $foreign_key
			,
		);
		//
		$versions = $Version->find('all', array('conditions' => $conditions));
		//
		if (empty($versions)) {
			//
			$conditions['AttachmentVersion.foreign_key'] = null;
			//
			$versions = $Version->find('all', array('conditions' => $conditions));
		}
		//
		return $versions;
	}



/**
* Returns true if prototype item is featured.
* @params id	item id
* @return 	boolean
**/
	 public function isFeatured($id = null) {
		
		if (!$id) {
			return false;
		}

		$info = $this->find('list', array('conditions' => array('PrototypeItem.id' => $id), 'fields' => array('PrototypeItem.featured')));
	
		// No publishing info - return false.
		if (!$info) {
			return false;
		}

		if($info[$id] != true) {
			return false;
		}
		
		return true;
	}
	
	
/**
* Sets product with given id to featured or unfeatured based on $featured
* @return void
*/
	public function featurePrototypeItem($id, $featured) {
		$this->id = $id;
		$this->saveField('featured', $featured);
	}
}