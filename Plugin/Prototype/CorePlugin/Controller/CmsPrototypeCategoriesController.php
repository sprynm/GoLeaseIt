<?php
/**
 * CmsPrototypeCategoriesController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeCategoriesController.html
 * @package      Cms.Plugin.Prototype.Controller 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeCategoriesController extends PrototypeAppController {
 
/** 
 * Attached components
 */
	public $components = array(
		'TreeSort.TreeSort'
	);

/**
 * Admin edit
 *
 * @param integer id - optional
 * @return void
 */
	public function admin_edit($id = null) {
		$categories = $this->PrototypeCategory->generateTreeList(array(
			'PrototypeCategory.prototype_instance_id' => $this->instance['PrototypeInstance']['id'],
			'PrototypeCategory.id !=' => $id
		));
		$this->set(compact('categories'));
		
		if (!empty($this->request->data)) {
			if (isset($this->PrototypeCategory->validateAdmin)) {
				$this->PrototypeCategory->setValidation('admin');
			}
			//
			if ($this->PrototypeCategory->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave(array(
					'continue' => array(
						$this->PrototypeCategory->id,
						'instance' => $this->instance['PrototypeInstance']['slug']
					), 
					'return' => array(
						'action' => 'index',
						'instance' => $this->instance['PrototypeInstance']['slug']
					)
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->{$this->modelClass}->find('edit', array(
				'conditions' => array($this->modelClass . '.id' => $id)
			));
		}
		//
		$pagesBannerImage	= Configure::read('Settings.Pages.PageOptions.banner_image');
		//
		$prototypeBannerImage	= $this->instance['PrototypeInstance']['use_page_banner_image_categories'];
		//
		$this->set('bannerImage', ($pagesBannerImage && $prototypeBannerImage ? true : false));
	}

/**
 * Admin index 
 *
 * @return	void
 */
	public function admin_index() {
		$prototypeCategories = $this->PrototypeCategory->find('all', array(
			'order' => 'PrototypeCategory.lft ASC',
			'conditions' => array('PrototypeCategory.prototype_instance_id' => $this->instance['PrototypeInstance']['id'])
		));

		$this->set(compact('prototypeCategories'));
	}

/**
 * Calls the TreeSortComponent's reorder function.
 *
 * @return void
 */
	public function admin_reorder() {
		return $this->TreeSort->reorder($this);
	}

/**
 * Basic view function for a category.
 *
 * @throws NotFoundException
 * @return void
 */
	public function view() {
		if (!$this->instance['PrototypeInstance']['allow_category_views']) {
			throw new NotFoundException("Item not found.");
		}

		$category = $this->PrototypeCategory->findForView($this->request->params);
		if (!$category) {
			throw new NotFoundException("Category not found."); 
		}

		// Load the items separately, with pagination if it's set in the instance.
		$query = $this->PrototypeCategory->PrototypeItem->summaryQuery($this->instance, $category);

		if (isset($this->instance['PrototypeInstance']['item_summary_pagination']) && $this->instance['PrototypeInstance']['item_summary_pagination'] == true) {
			$query['limit'] = isset($this->instance['PrototypeInstance']['item_summary_pagination_limit']) ? $this->instance['PrototypeInstance']['item_summary_pagination_limit'] : 10;
			$this->paginate = array('PrototypeItem' => $query);
			$items = $this->paginate('PrototypeItem');
			$paginatedItems = true;
		} else {
			$items = $this->PrototypeCategory->PrototypeItem->find('all', $query);
			$paginatedItems = false;
		}                

		$name = $category['PrototypeCategory']['head_title'] ? $category['PrototypeCategory']['head_title'] : $category['PrototypeCategory']['name'];
		$this->PageSettings->setTitle($name, $category['PrototypeCategory']['name']);

		$customFields = ClassRegistry::init('CustomFields.CustomField')->findForModel(
			'PrototypeInstance', 
			$this->instance['PrototypeInstance']['id'], 
			'PrototypeCategory'
		);

		$this->set(compact('category', 'customFields', 'items', 'paginatedItems'));
		$this->set('prototypeCategory', $category); // Needed for meta tags which requires the full model name.
		//
		$pagesBannerImage	= Configure::read('Settings.Pages.PageOptions.banner_image');
		//
		$prototypeBannerImage	= $this->instance['PrototypeInstance']['use_page_banner_image_categories'];
		
		//set the banner image for this category with fallback to instance banner if that feature is turned on	
		if ( $pagesBannerImage && $prototypeBannerImage ) {
			if (!empty($category['CategoryBannerImage'])) {
				$this->set('banner', array('Image' => $category['CategoryBannerImage']));
				//
				unset($category['CategoryBannerImage']);
			} else if (!empty($this->instance['PrototypeInstance']['fallback_to_instance_banner_image']) && !empty($this->instance['Image'])) {
				$this->set('banner', array('Image' => $this->instance['Image']));
			}
		}
	}

}