<?php
/**
 * CmsPrototypeCategory class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeCategory.html
 * @package      Cms.Plugin.Prototype.Model 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeCategory extends PrototypeAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Sluggable' => array(
			'overwrite' => true,
			'label' => 'head_title'
		), 
		'CustomFields.CustomField',
		'CustomFields.Expandable',
		'Publishing.Publishable',
		'Sortable' => array(
			'group' => 'prototype_instance_id'
		),
		'MultiTree' => array(
			'root' => 'prototype_instance_id',
			'rootModel' => 'PrototypeInstance',
			'level' => false
		),
		'TreeSort.TreeSort',
		'Versioning.SoftDelete',
		'Versioning.Revision' => array('preview' => true),
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
 * hasAndBelongsToMany associations
 */
	public $hasAndBelongsToMany = array(
		'PrototypeItem' => array(
			'className' => 'Prototype.PrototypeItem',
			'joinTable' => 'prototype_categories_prototype_items', 
			'foreignKey' => 'prototype_category_id', 
			'associationForeignKey' => 'prototype_item_id', 
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
				'Image.model' => 'PrototypeCategory', 
				'Image.group' => 'Image'
			), 
			'dependent' => true, 
			'order' => 'Image.rank ASC, Image.id ASC'
		), 
		'Document' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Document.model' => 'PrototypeCategory', 
				'Document.group' => 'Document'
			), 
			'dependent' => true, 
			'order' => 'Document.rank ASC'
		),
		'PrototypeCategory' => array(
			'className' => 'Prototype.PrototypeCategory',
			'foreignKey' => 'parent_id',
			'dependent' => false
		),
		'CategoryBannerImage' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'CategoryBannerImage.model' => 'PrototypeCategory',
				'CategoryBannerImage.group' => 'Category Banner Image'
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
			'controller' => 'prototype_categories',
			'action' => 'view',
			'instance' => 'PrototypeInstance.slug',
			'category' => '{alias}.slug'
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
			'message' => 'A name is required.'
		)
	);

/**
 * Edit query conditions
 */
	protected $_editQuery = array(
		'contain' => array(
			'Image',
			'Document',
			'CategoryBannerImage',
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
			if (!isset($val['PrototypeCategory'])) {
				continue;
			}

			if (!isset($val['PrototypeCategory']['name'])) {
				continue;
			}

			if (empty($val['PrototypeCategory']['head_title'])) {
				$results[$key]['PrototypeCategory']['head_title'] = $val['PrototypeCategory']['name'];
			}
		}
		return $results;
	}

/**
 * Blocks deletion of a category if there are no other categories.
 *
 * @return	boolean
 */
	public function beforeDelete($cascade = true) {
		return $this->canBeDeleted($this->id);
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
 * Returns true only if there is another category in the system other than $id.
 *
 * @param	integer $id
 * @return	boolean
 */
	public function canBeDeleted($id) {
		$category = $this->findById($id);
		$count = $this->find('count', array(
			'conditions' => array(
				'PrototypeCategory.id !=' => $category['PrototypeCategory']['id'],
				'PrototypeCategory.prototype_instance_id' => $category['PrototypeCategory']['prototype_instance_id'],
			)
		));
		
		return $count > 0;
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
 * Returns prototype categories for the sitemap listener. Only returns categories for instances
 * that allow individual category views.
 *
 * @return array
 */
	public function findForSitemap() {
		$instances = $this->PrototypeInstance->find('list', array(
			'fields' => array('id', 'allow_category_views')
		));
		
		$changeFrequencies = $this->PrototypeInstance->find('list', array(
			'fields' => array('id', 'category_changefreq')
		));

		$categories = $this->find('all', array(
			'fields' => array('id', 'slug', 'modified', 'name', 'prototype_instance_id'),
			'published' => true
		));

		foreach ($categories as $i => $category) {
			if (!isset($instances[$category['PrototypeCategory']['prototype_instance_id']]) || $instances[$category['PrototypeCategory']['prototype_instance_id']] == false) {
				unset($categories[$i]);
				continue;
			}
			$categories[$i]['PrototypeCategory']['url'] = Router::url($this->link($category['PrototypeCategory']['id']), true);
			$categories[$i]['PrototypeCategory']['changefreq'] = $changeFrequencies[$categories[$i]['PrototypeCategory']['prototype_instance_id']];
		}
		
		return $categories;
	}

/**
 * Finds and returns an array of prototype categories for a summary, usually CmsPrototypeInstancesController::_categoryView.
 *
 * @param array a prototype instance array or an integer
 * @param boolean $includeItems If true, will also return items
 * @return array
 */
	public function findForSummary($instance, $includeItems = true) {
		if (is_numeric($instance)) {
			$instance = $this->PrototypeInstance->findById($instance);
		}

		if (!$instance) {
			return null;
		}

		$categories = $this->find('all', array(
			'conditions' => array(
				'PrototypeCategory.prototype_instance_id' => $instance['PrototypeInstance']['id']
			),
			'contain' => array(
				'Image',
				'Document',
				'PrototypeItem'
			),
			'published' => true,
			'cache' => true,
			'order' => 'PrototypeCategory.lft ASC'
		));

		if ($includeItems) {
			foreach ($categories as $key => $val) {
				$items = $this->PrototypeItem->summaryQuery($instance, $val, true);
				$categories[$key]['items'] = $items;
			}
		}

		return $categories;
	}

/**
 * Finds and returns a prototype category for CmsPrototypeCategories::view, using the param
 * array from the request object.
 *
 * @param array the params array from the current request
 * @return array
 */
	public function findForView($params) {
		$category = $this->find('first', array(
			'conditions' => array(
				'PrototypeCategory.slug'	=> $params['category']
				, 'PrototypeCategory.deleted'	=> false
				,
			),
			'contain' => array(
				'PrototypeCategory',
				'Image',
				'Document',
				'CategoryBannerImage',
			),
			'published' => true,
			'cache' => true
		));
		return $category;
	}

/**
 * BORROWED FROM THE MEDIA plugin
 * Finds and returns image versions for $Model based on AttachmentVersion records.
 *
 * @param object $Model
 * @return array
 */

	public function findImageVersions($data) {
		//
		App::uses('AttachmentVersion', 'Media.Model');
		//
		$Version = new AttachmentVersion();
		
		//if it's a banner image then grab the images versions for the Page banners
		if ( strpos($data['group'], 'Banner') !== false ) {
			$conditions = array(
				'AttachmentVersion.model'=>'Page'
				, 'AttachmentVersion.group' => 'Image'
				, 'AttachmentVersion.foreign_key' => NULL
			);
		} else {
			//
			$item = $this->findById($data['foreign_key']);
			
			//conditions are pretty static for categories, just the foreign key needs to be specified
			$conditions = array(
				'AttachmentVersion.model'=>'PrototypeInstance'
				, 'AttachmentVersion.group'=>'Category Image'
				, 'AttachmentVersion.foreign_key' => $item[$this->alias]['prototype_instance_id']
			);
		}
		//
		$versions = $Version->find('all', array('conditions' => $conditions));
		//
		if (empty($versions)) {
			//if this instance has no versions for categories yet then just grab the default versions
			$conditions['AttachmentVersion.foreign_key'] = null;
			//
			$versions = $Version->find('all', array('conditions' => $conditions));
		}
		//
		return $versions;
	}

}