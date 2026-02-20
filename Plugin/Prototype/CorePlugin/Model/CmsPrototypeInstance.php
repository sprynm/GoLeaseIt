<?php
App::uses('Folder', 'Utility');
/**
 * CmsPrototypeInstance class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPrototypeInstance.html
 * @package		 Cms.Plugin.Prototype.Model 
 * @since		 Pyramid CMS v 1.0
 */
class CmsPrototypeInstance extends PrototypeAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Sluggable' => array(
			'label' => 'head_title'
		),
		'CustomFields.CustomField',
		'CustomFields.Expandable',
		'Publishing.Publishable',
		'Versioning.SoftDelete',
		'Versioning.Revision' => array('preview' => true),
		'Copyable',
		'Metas.MetaTag' => array( 'contentField' => 'description'),
		'Users.Lockable' => array( 
			'labelField' => 'name'
			, 'lockedChildren' => array(
				'Prototype.PrototypeItem' => array(
					'foreignKey' => 'prototype_instance_id' 
					, 'labelField' => 'name'
				)
				, 'Prototype.PrototypeCategory' => array(
					'foreignKey' => 'prototype_instance_id' 
					, 'labelField' => 'name'
				)
			)
		)
	);

/**
 * hasMany associations
 */
	public $hasMany = array(
		'PrototypeCategory' => array(
			'className' => 'Prototype.PrototypeCategory', 
			'foreignKey' => 'prototype_instance_id', 
			'order' => 'PrototypeCategory.lft ASC',
			'dependent' => true
		), 
		'PrototypeItem' => array(
			'className' => 'Prototype.PrototypeItem', 
			'foreignKey' => 'prototype_instance_id', 
			'dependent' => true
		),

		'PrototypeInstanceField' => array(
			'className' => 'CustomFields.CustomField',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'PrototypeInstanceField.model' => 'PrototypeInstance',
				'PrototypeInstanceField.group' => 'PrototypeInstance'
			),
			'dependent' => true,
			'order' => 'PrototypeInstanceField.rank ASC, PrototypeInstanceField.id ASC'
		),

		'PrototypeCategoryField' => array(
			'className' => 'CustomFields.CustomField',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'PrototypeCategoryField.model' => 'PrototypeInstance',
				'PrototypeCategoryField.group' => 'PrototypeCategory'
			),
			'dependent' => true,
			'order' => 'PrototypeCategoryField.rank ASC, PrototypeCategoryField.id ASC'
		),
		'PrototypeItemField' => array(
			'className' => 'CustomFields.CustomField',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'PrototypeItemField.model' => 'PrototypeInstance',
				'PrototypeItemField.group' => 'PrototypeItem'
			),
			'dependent' => true,
			'order' => 'PrototypeItemField.rank ASC, PrototypeItemField.id ASC'
		),
		'InstanceBannerImageVersion' => array(
			'className' => 'Media.AttachmentVersion',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'InstanceBannerImageVersion.model' => 'PrototypeInstance',
				'InstanceBannerImageVersion.group' => 'Instance Banner Image'
			),
			'dependent' => true
		),
		'ItemImageVersion' => array(
			'className' => 'Media.AttachmentVersion',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'ItemImageVersion.model' => 'PrototypeInstance',
				'ItemImageVersion.group' => 'Item Image'
			),
			'dependent' => true
		),
		'ItemBannerImageVersion' => array(
			'className' => 'Media.AttachmentVersion',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'ItemBannerImageVersion.model' => 'PrototypeInstance',
				'ItemBannerImageVersion.group' => 'Item Banner Image'
			),
			'dependent' => true
		),
		'CategoryImageVersion' => array(
			'className' => 'Media.AttachmentVersion',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'CategoryImageVersion.model' => 'PrototypeInstance',
				'CategoryImageVersion.group' => 'Category Image'
			),
			'dependent' => true
		),
		'CategoryBannerImageVersion' => array(
			'className' => 'Media.AttachmentVersion',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'CategoryBannerImageVersion.model' => 'PrototypeInstance',
				'CategoryBannerImageVersion.group' => 'Category Banner Image'
			),
			'dependent' => true
		),
		'Image' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Image.model' => 'PrototypeInstance', 
				'Image.group' => 'Instance Banner Image'
			), 
			'dependent' => true, 
			'order' => 'Image.rank ASC, Image.id ASC'
		),

	);

/**
 * Generic link array for Linkable behavior
 */
	public $linkFormat = array(
		'link' => array(
			'plugin' => 'prototype',
			'controller' => 'prototype_instances',
			'action' => 'view',
			'instance' => '{alias}.slug'
		)
	);

/**
 * Used by before/afterSave to keep track of slugs for renaming view folders if necessary.
 */
	public $slugMap = array();

/**
 * Validate array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty', 
				'required' => true, 
				'message' => 'A name is required.',
				'on' => 'create'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'required' => true,
				'message' => 'That name is already in use.',
				'on' => 'create'
			),
			'notAPlugin' => array(
				'rule' => 'notAPlugin',
				'required' => true,
				'message' => 'A plugin by that name already exists.',
				'on' => 'create'
			)
		)
	);

/**
 * Item/category image type options
 */
	public static $imageTypes = array(
		'none' => 'None', 
		'single' => 'Single', 
		'multiple' => 'Multiple'
	);

/**
 * Item/category document type options
 */	
	public static $documentTypes = array(
		'none' => 'None', 
		'single' => 'Single', 
		'multiple' => 'Multiple'
	);

/**
 * Ordering options for items
 */
	public static $orderOptions = array(
		'PrototypeItem.rank ASC' => 'Manually Set Order', 
		//'PrototypeItem.rank DESC' => 'Manually Set order - descending',

		'PublishingInformation.start ASC' => 'Start publishing - ascending', 
		'PublishingInformation.start DESC' => 'Start publishing - descending',

		'PublishingInformation.end ASC' => 'End publishing - ascending', 
		'PublishingInformation.end DESC' => 'End publishing - descending',

		'PrototypeItem.name ASC' => 'Item name - ascending',
		'PrototypeItem.name DESC' => 'Item name - descending',

		'PrototypeItem.id ASC' => 'Item ID - ascending',
		'PrototypeItem.id DESC' => 'Item ID - descending', 
	);
	
/**
 * Edit query info
 */
	protected $_editQuery = array(
		'contain' => array(
			'PrototypeCategoryField',
			'PrototypeInstanceField',
			'PrototypeItemField',
			'ItemImageVersion',
			'CategoryImageVersion',
			'InstanceBannerImageVersion',
			'CategoryBannerImageVersion',
			'ItemBannerImageVersion'
			
		)
	);

/**
 * Adds some extra information to prototype instances designed to aid in determining the name
 * of the currently loaded instance and its associated item and category controllers.
 *
 * @see Model::afterFind
 */
	public function afterFind($results, $primary = false) {
		if (!$primary) {
			return $results;
		}
		if (isset($results[0]) && isset($results[0][$this->alias])) {
			foreach ($results as $i => $result) {
				if (!isset($result['PrototypeInstance']['name'])) {
					continue;
				}
				$name = $result['PrototypeInstance']['name'];
				$results[$i]['PrototypeInstance']['controllers'] = array(
					'category' => Inflector::underscore($name . '_categories'),
					'item' => $name . '_items'
				);
				$results[$i]['PrototypeInstance']['models'] = array(
					'category' => $name . 'Category',
					'item' => $name . 'Item'
				);

				if (!isset($result['PrototypeInstance']['head_title']) || !$result['PrototypeInstance']['head_title']) {
					$results[$i]['PrototypeInstance']['head_title'] = $results[$i]['PrototypeInstance']['name'];
				}
			
			}
		}
		return $results;
	}

/**
 * Executes beforeSave to grab the name of the instance in case it's changed, in which case
 * the view folder is renamed in afterSave.
 *
 * @param array options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (!isset($this->data['PrototypeInstance']['id']) || empty($this->data['PrototypeInstance']['id'])) {
			return true;
		}

		if (!isset($this->data['PrototypeInstance']['slug'])) {
			return true;
		}
		
		//make sure that the slug is well formed so that we know we have a valid slug before it gets corrected to something else
		$this->data['PrototypeInstance']['slug'] = Cms::slug($this->data['PrototypeInstance']['slug'], '-', 100);
	
		//don't save blank slug values
		if (empty($this->data['PrototypeInstance']['slug'])) {
			//try to set the slug based on the instance name
			$this->data['PrototypeInstance']['slug'] = Cms::slug($this->data['PrototypeInstance']['name'], '-', 100);			
			//otherwise keep it as it was
			if (empty($this->data['PrototypeInstance']['slug'])) {
				unset($this->data['PrototypeInstance']['slug']);
				return true;
			}
		}
		
		$oldData = $this->find('first', array(
			'conditions' => array('PrototypeInstance.id' => $this->data['PrototypeInstance']['id']),
			'fields' => array('PrototypeInstance.slug')
		));

		if (!$oldData) {
			return true;
		}

		if ($oldData['PrototypeInstance']['slug'] == $this->data['PrototypeInstance']['slug']) {
			return true;
		}

		$this->slugMap[$this->data['PrototypeInstance']['id']] = array(
			'new' => $this->data['PrototypeInstance']['slug'],
			'old' => $oldData['PrototypeInstance']['slug']
		);

		return true;
	}

/**
 * If head_title is empty and name is not, set head_title to = name.
 *
 * @return boolean
 */
	public function beforeValidate($options = array()) {
		
	   /**
		* If a prototype named the same as a plugin get beyond this point, it will pass validation 
		* but also crash the application so severely that someone has to manually change the prototype
		* name in the database before the site will work again. So there's an extra validation check 
		* here to make sure that doesn't happen. 
		*/
		/* 
		if(!$this->notAPlugin(array('name' => $this->data[$this->alias]['name']))) {
			$this->validationErrors['PrototypeInstance']['name'][] = 'A plugin by that name already exists.';
			return false;
		} */
		
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
 * Renames the view folder for instance $id if its slug changed.
 *
 * @param integer id
 * @return boolean
 */
	public function updateViewFolder($id) {
		if (!isset($this->slugMap[$id])) {
			return;
		}

		//avoid recursively copying the parent directory contents into the new directory
		if (empty($this->slugMap[$id]['old']) || empty($this->slugMap[$id]['new'])) {
			$this->log("Error while moving PrototypeInstance View Folder \n".print_r(array('Old Path'=>$this->slugMap[$id]['old'], 'New Path'=>$this->slugMap[$id]['new']),true), 'error');
			
			//copy the default views for this instance
			if (!empty($this->slugMap[$id]['new'])) {
				$instance = $this->findById($this->id);
				$this->copyDefaultViews($instance);
			}
			
			return false;
		}
		
		$folder = new Folder();
		$success = $folder->move(array(
			'from' => APP . 'Plugin' . DS . 'Prototype' . DS . 'View' . DS . $this->slugMap[$id]['old'],
			'to' => APP . 'Plugin' . DS . 'Prototype' . DS . 'View' . DS . $this->slugMap[$id]['new']		
		));
		
		//update the permission with the new slug
		$Permission = ClassRegistry::init('Users.Permission');
		$oldPerm = $Permission->find('first', array(
			'conditions' => array(
				'plugin' => 'prototype',
				'controller' => $this->slugMap[$id]['old'],
				'action' => 'admin'
			)
		));
		
		if (!empty($oldPerm)) {
			$oldPerm[$Permission->alias]['controller'] = $this->slugMap[$id]['new'];
			$oldPerm[$Permission->alias]['description'] = $this->data['PrototypeInstance']['name'] . ' management';
			$Permission->save($oldPerm);
		}
		
		return $success;
	}

/**
 * Executes after a record is saved. If the instance is new then default views are copied from the 
 * core to the instance's view element directory. Also deletes cached RSS feed.
 *
 * For existing invoices, if the name is changed then the view folder is changed as well.
 *
 * @param	boolean $created
 * @return	boolean
 */
	public function afterSave($created) {
		if (!$created) {
			$this->updateViewFolder($this->id);
			return true;
		}
		
		$instance = $this->findById($this->id);

		// Copy default views from core to app
		$this->copyDefaultViews($instance);

		// Make 'uncategorized' category
		$data = array(
			'PrototypeCategory' => array(
				'prototype_instance_id' => $this->id,
				'name' => 'Uncategorized'
			)
		);
		$this->PrototypeCategory->create();
		$this->PrototypeCategory->save($data);
		
		// Add new premission
		$this->addPermission($instance);
		
		// Initialize image versions
		$this->saveDefaultImageVersions($instance);
		
		// Clear the cache.
		Cache::delete('url_map', '_cake_core_');
		Cache::delete('admin_prototype_instance_list', '_cake_core_');
		AppCache::clear();
		
		return true;
	}

/**
 * Adds an admin permission
 *
 * @param array instance
 * @return void
 */
	public function addPermission($instance) {
		ClassRegistry::init('Users.Permission')->saveAll(array(
			array(
				'Permission' => array(
					'plugin' => 'prototype',
					'controller' => $instance['PrototypeInstance']['slug'],
					'action' => 'admin',
					'description' => $instance['PrototypeInstance']['name'] . ' management'
				),
				'Group' => array('Group' => array(2))
			)
		));
	}

/**
 * Copies default views from CMS to APP for $instance, usually when a new instance
 * is created. First will look here:
 * CMS/Plugin/Prototype/View/<instance_name_underscored>
 *
 * And if that doesn't exist, will then take from the default views in the Prototype* folders.
 *
 * @param array instance Prototype Instance array
 * @return boolean
 */
	public function copyDefaultViews($instance) {
		if (!isset($instance['PrototypeInstance'])) {
			return false;
		}

		$slug = $instance['PrototypeInstance']['slug'];
		$cmsPath = CMS . 'Plugin' . DS . 'Prototype' . DS . 'View';
		$appPath = APP . 'Plugin' . DS . 'Prototype' . DS . 'View';

		// There's a default view set for this instance - copy that
		if (is_dir($cmsPath . DS . $slug)) {
			$folder = new Folder($cmsPath . DS . $slug);
			$folder->copy($appPath . DS . $slug);
		} else {
			// No default view set - copy the base files
			$toCopy = array(
				'PrototypeCategories' => 'view.ctp',
				'PrototypeItems' => array('view.ctp', 'search.ctp', 'featured.ctp'),
				'PrototypeInstances' => 'view.ctp'
			);

			foreach ($toCopy as $dir => $name) {
				if (!is_array($name)) {
					$name = array($name);
				}
				foreach ($name as $val) {
					$folder = new Folder($appPath . DS . $slug . DS . $dir, true);
					$file = new File($cmsPath . DS . $dir . DS . $val);
					$file->copy($appPath . DS . $slug . DS . $dir . DS . $val);
				}
			}
		}

		shell_exec('chmod -R 776 ' . $appPath . DS . $slug);
		return true;
	}

/**
 * Finds all installed and published instances.
 *
 * @return array
 */
	public function findInstalled() {
		return $this->find('all', array(
			'published' => true,
			'cache' => true
		));
	}

/**
 * Returns prototype instances for the sitemap XML/HTML listener.
 *
 * @var string $type either 'xml' or 'html'
 * @return array
 */
	public function findForSitemap($type = 'xml') {
		$instances = $this->find('all', array(
			'fields' => array('id', 'slug', 'modified', 'name'),
			'published' => true,
			'cache' => true
		));

		foreach ($instances as $i => $instance) {
			$instances[$i]['PrototypeInstance']['url'] = Router::url($this->link($instance['PrototypeInstance']['id']), true);
		}

		return $instances;
	}

/**
 * Installs a preconfigured instance $name from data in the $data array.
 *
 * @param array name
 * @param array data
 * @return boolean
 */
	public function installPreconfigured($name, $data) {
		$data['PrototypeInstance']['name'] = $name;
		if (isset($data['PrototypeItemField'])) {
			foreach ($data['PrototypeItemField'] as $key => $val) {
				$data['PrototypeItemField'][$key]['model'] = 'PrototypeInstance';
				$data['PrototypeItemField'][$key]['group'] = 'PrototypeItem';
			}
		}
		if (isset($data['PrototypeCategoryField'])) {
			foreach ($data['PrototypeCategoryField'] as $key => $val) {
				$data['PrototypeCategoryField'][$key]['model'] = 'PrototypeInstance';
				$data['PrototypeCategoryField'][$key]['group'] = 'PrototypeCategory';
			}
		}
		return $this->saveAll($data, array('deep' => true));
	}

/**
 * Loads and returns preconfigured plugins array from Config/preconfigured.php. The file should
 * have a $preconfigured array.
 *
 * @throws CakeException
 * @return array
 */
	public function loadPreconfigured() {
		$file = CMS . 'Plugin' . DS . 'Prototype' . DS . 'Config' . DS . 'preconfigured.php';
		if (!file_exists($file)) {
			throw new CakeException("Prototype preconfigured instance file (" . $file . ") not found.");
		}

		include $file;
		
		if (!isset($preconfigured)) {
			throw new CakeException("Prototype preconfigured instance file (" . $file . ") missing preconfigured array.");
		}

		ksort($preconfigured);
		return $preconfigured;
	}

/**
 * Ensures that the name of a prototype instance does not already match the name of a plugin,
 * either a local plugin or a core plugin, installed or not.
 *
 * @var array
 * @return boolean
 */
	public function notAPlugin($check) {
		$value = array_values($check);
		$value = $value[0];

		if (CmsPlugin::isLocalPlugin($value) || CmsPlugin::inCore($value)) {
			return false;
		}

		return true;
	}

/**
 * Returns the item order field for $instance.
 *
 * @param array $instance PrototypeInstance 
 * @return mixed - string or false
 */
	public function itemOrder($instance) {
		if (!isset($instance['PrototypeInstance']) || !isset($instance['PrototypeInstance']['item_order'])) {
			return false;
		}

		return $instance['PrototypeInstance']['item_order'];
	}

/** 
 * Regenerates versions of images for prototype items or categories of $instance. The items are loaded and
 * then a loop regenerates images foreach each one. The CmsAttachment::regenerate() function will return 
 * an array specifying what was regenerated. 
 *
 * @param integer $id instance ID
 * @param string $type either 'item' or 'category'
 * @return mixed Number of images regenerated on success, false on failure
 */
	public function regenerateImages($id, $type = 'item', $versionName = null, $offset = 0) {
		$instance = $this->findById($id);
		if (!$instance) {
			return false;
		}

		$model = null;
		$group = 'Image';
		$versionGroup = null;

		//model is not passed in; it gets set here.
		if ($type == 'item') {
			$model = 'PrototypeItem';
			$versionGroup = 'Item Image';
		} else if ($type == 'category') {
			$model = 'PrototypeCategory';
			$versionGroup = 'Category Image';
		} else {
			return false;
		}

		// Load the image versions from the prototype instance, or else the function will try to use
		// versions for the items themselves, which will fail.
		$versions = ClassRegistry::init('Media.AttachmentVersion')->findForRegen($this->alias, $id, $versionGroup, $versionName);
		
		if (!$versions) {
			return false;
		}

		$items = $this->{$model}->find('list', array(
			'conditions' => array('prototype_instance_id' => $id)
		));
		
		$start_time = microtime(true);
		
		$res = array();
		foreach ((array)$items as $key => $val) {
			$info = ClassRegistry::init('Media.Attachment')->regenerate($model, $key, 'Image', $versions, $offset, $start_time);
			$res[$val] = $info;
			
			if ( $info['total_number_of_files'] > $info['number_of_images_resized'] + $offset ) {
				break;
			}
			
			if ($info['total_number_of_files'] - $offset < 0) {
				unset($res[$val]);
			}
			
			//adjust the offset, if the number of images in the version are less than the offset than none of the images in that version will be generated
			if (!empty($info['total_number_of_files'])) {
				$offset -= $info['total_number_of_files'];
				if ($offset < 0) {
					$offset = 0;
				}
			}
		}

		return $res;
	}
	
/**
 * Loads default prototype image versions and saves them to $instance.
 *
 * @param	array	$instance
 * @return	boolean
 */
	public function saveDefaultImageVersions($instance) {
		$itemDefaults = $this->ItemImageVersion->find('all', array(
			'conditions' => array(
				'ItemImageVersion.model' => 'PrototypeInstance',
				'ItemImageVersion.foreign_key' => null,
				'ItemImageVersion.group' => 'Item Image'
			) 
		));
		
		foreach ($itemDefaults as $key => $val) {
			$itemDefaults[$key]['ItemImageVersion']['foreign_key'] = $instance['PrototypeInstance']['id'];
			unset($itemDefaults[$key]['ItemImageVersion']['id']);
		}
		
		$itemDefaults = Set::extract($itemDefaults, '{n}.ItemImageVersion');
		$this->ItemImageVersion->saveAll($itemDefaults, array('validate' => false));

		$categoryDefaults = $this->CategoryImageVersion->find('all', array(
			'conditions' => array(
				'CategoryImageVersion.model' => 'PrototypeInstance',
				'CategoryImageVersion.foreign_key' => null,
				'CategoryImageVersion.group' => 'Category Image'
			) 
		));
		
		foreach ($categoryDefaults as $key => $val) {
			$categoryDefaults[$key]['CategoryImageVersion']['foreign_key'] = $instance['PrototypeInstance']['id'];
			unset($categoryDefaults[$key]['CategoryImageVersion']['id']);
		}
		
		$categoryDefaults = Set::extract($categoryDefaults, '{n}.CategoryImageVersion');
		$this->CategoryImageVersion->saveAll($categoryDefaults, array('validate' => false));
		return true;
	}

/** 
 * Checks to ensure that an admin permission exists for instance $id and adds one if it's not there.
 *
 * @param integer $id
 * @return boolean
 */
	public function verifyAdminPermission($id) {
		$instance = $this->findById($id);
		if (!$instance) {
			return false;
		}

		$permission = ClassRegistry::init('Users.Permission')->find('first', array(
			'conditions' => array(
				'plugin' => 'prototype',
				'controller' => $instance['PrototypeInstance']['slug'],
				'action' => 'admin'
			)
		));

		if ($permission) {
			return true;
		}

		$this->addPermission($instance);
		return true;
	}

/**
 * Finds and returns image versions for $Model based on AttachmentVersion records. Only used
 * if $Model does not have its own findImageVersions function.
 *
 * @param object $Model
 * @return array
 */
	public function findImageVersions() {
		App::uses('AttachmentVersion', 'Media.Model');
		$Version = new AttachmentVersion();

		$conditions = array(
			'AttachmentVersion.model' => 'Page',
			'AttachmentVersion.group' => 'Image',
			'AttachmentVersion.foreign_key' => NULL
		);

		$versions = $Version->find('all', array('conditions' => $conditions));

		if (empty($versions)) {
			$conditions['AttachmentVersion.foreign_key'] = null;
			$versions = $Version->find('all', array('conditions' => $conditions));
			return $versions;
		} else {
			return $versions;
		}
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

}