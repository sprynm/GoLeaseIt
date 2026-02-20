<?php
/**
 * CmsPrototypeItemsController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeItemsController.html
 * @package      Cms.Plugin.Prototype.Controller 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeItemsController extends PrototypeAppController {

/**
 * Components
 */
	public $components = array('Search.Prg' => array(
		'commonProcess' => array(
			'paramType' => 'querystring'
		),
		'presetForm' => array(
			'paramType' => 'querystring'
		)
	));

/**
 * Admin edit
 *
 * @param integer id - optional
 * @return void
 */
	public function admin_edit($id = null) {
		$prototypeCategories = $this->PrototypeItem->PrototypeCategory->generateTreeList(array(
			'prototype_instance_id' => $this->instance['PrototypeInstance']['id']
		));
		$this->set('prototypeCategories', $prototypeCategories);

		if (!empty($this->request->data)) {
			if (isset($this->PrototypeItem->validateAdmin)) {
				$this->PrototypeItem->setValidation('admin');
			}

			if ($this->PrototypeItem->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave(array(
					'continue' => array(
						$this->PrototypeItem->id,
						'instance' => $this->instance['PrototypeInstance']['slug']
					), 
					'return' => array(
						'action' => 'index',
						'instance' => $this->instance['PrototypeInstance']['slug']
					)
				));
			} else {
				// Modify the validation errors so that the "name" field uses the custom label, if set.
				if ($this->instance['PrototypeInstance']['name_field_label'] && isset($this->PrototypeItem->validationErrors['name'])) {
					$this->PrototypeItem->validationErrors[$this->instance['PrototypeInstance']['name_field_label']] = $this->PrototypeItem->validationErrors['name'];
					unset($this->PrototypeItem->validationErrors['name']);
				}
				$this->Notify->handleFailedSave();
			}
		}
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->{$this->modelClass}->find('edit', array(
				'conditions' => array($this->modelClass . '.id' => $id)
			));
		}
		//
		$pagesbannerImage	= Configure::read('Settings.Pages.PageOptions.banner_image');
		//
		$prototypeBannerImage	= $this->instance['PrototypeInstance']['use_page_banner_image_items'];
		//
		$this->set('bannerImage', ($pagesbannerImage && $prototypeBannerImage ? true : false));
	}

/**
 * Admin index.
 *
 * @return	void
 */
	public function admin_index() {
		$query = $this->PrototypeItem->adminIndexQuery($this->instance);
		$this->AutoPaginate->setPaginate($query);
		$sort = in_array($this->instance['PrototypeInstance']['item_order'], array('PrototypeItem.rank ASC', 'PrototypeItem.rank DESC'));
		$this->set('items', $this->paginate());
		$this->set(compact('sort'));		
				
	}

/**
 * Featured item display page.
 *
 * @return void
 */
	public function featured() {
		$items = $this->PrototypeItem->summaryQuery($this->instance, null, true, array(
			'conditions' => array('PrototypeItem.featured' => 1)
		));
		$this->set(compact('items'));

		$this->set('pageIntro', $this->instance['PrototypeInstance']['description']);
		$name = $this->instance['PrototypeInstance']['head_title'] ? $this->instance['PrototypeInstance']['head_title'] : $this->instance['PrototypeInstance']['name'];
		$this->PageSettings->setTitle('Featured ' . $name, 'Featured ' . $this->instance['PrototypeInstance']['name']);
	}

/** 
 * Item search action.
 *
 * @return void
 */
	public function search() {
		if ($this->request->is('post')) {
			$this->Prg->commonProcess(null, array('allowedParams' => array('instance')));
		}
		
		$this->PrototypeItem->validate = null;
		
		$customFields = ClassRegistry::init('CustomFields.CustomField')->findForModel(
			'PrototypeInstance', 
			$this->instance['PrototypeInstance']['id'], 
			'PrototypeItem'
		);
		$this->set(compact('customFields'));

		if (!empty($this->request->query)) {
			$this->PrototypeItem->addDynamicSearch($this->request->query, array(
				'model' => 'PrototypeInstance', 
				'foreignKey' => $this->instance['PrototypeInstance']['id'], 
				'group' => 'PrototypeItem'
			));
			$this->AutoPaginate->setPaginate(array(
				'conditions' => $this->PrototypeItem->parseCriteria($this->request->query),
				'contain' => array('Image', 'Document', 'PrototypeCategory'),
				'order' => $this->PrototypeItem->PrototypeInstance->itemOrder($this->instance)
			));
			$this->set('items', $this->paginate());

			$this->request->data['PrototypeItem'] = $this->request->query;
		}

		$this->set('pageIntro', $this->instance['PrototypeInstance']['description']);
		$name = $this->instance['PrototypeInstance']['head_title'] ? $this->instance['PrototypeInstance']['head_title'] : $this->instance['PrototypeInstance']['name'];
		$this->PageSettings->setTitle('Search ' . $name, 'Search ' . $this->instance['PrototypeInstance']['name']);
	}

/**
 * Basic view function for an item. Excepts 'slug' and 'id' request parameters.
 *
 * @throws NotFoundException
 * @return void
 */
	public function view() {
		// The $this->request->data check allows revision previews to happen
		
		if (empty($this->request->data) && !$this->instance['PrototypeInstance']['allow_item_views']) {
			throw new NotFoundException("Item not found.");
		}
		
		
		$item = $this->PrototypeItem->findForView($this->request->params);
		if (!$item) {
			throw new NotFoundException("Item not found."); 
		}

		$name = $item['PrototypeItem']['head_title'] ? $item['PrototypeItem']['head_title'] : $item['PrototypeItem']['name'];
		$this->PageSettings->setTitle($name, $item['PrototypeItem']['name']);

		$customFields = ClassRegistry::init('CustomFields.CustomField')->findForModel(
			'PrototypeInstance', 
			$this->instance['PrototypeInstance']['id'], 
			'PrototypeItem'
		);

		$this->set(compact('item', 'customFields'));
		$this->set('prototypeItem', $item); // This is for meta tags to work since they require the full model name.
		//
		$pagesBannerImage	= Configure::read('Settings.Pages.PageOptions.banner_image');
		//
		$prototypeBannerImage	= !empty($this->instance['PrototypeInstance']['use_page_banner_image_items']);
		//
		if ( $pagesBannerImage && $prototypeBannerImage ) {			
			if ( isset($item['ItemBannerImage']) && !empty($item['ItemBannerImage']) ) {
				//
				$item['ItemBannerImage'][0]['group']	= 'Image';
				//
				$this->set('banner', array('Image' => $item['ItemBannerImage']));
				//
				unset($item['ItemBannerImage']);
			} else if (!empty($this->instance['PrototypeInstance']['fallback_to_instance_banner_image']) && !empty($this->instance['Image'])) {
				$this->set('banner', array('Image' => $this->instance['Image']));
			}
		}
	}


/**
 * Ajax action for toggling the featured property for a prototype item
 */
	public function admin_toggle_featured($id, $featured = 'on') {
		$this->PrototypeItem->featurePrototypeItem($id, $featured == 'on' ? true : false);
		
		$item = $this->PrototypeItem->find('first', array(
			'conditions' => array('PrototypeItem.id' => $id)
		));
		
		if (!$this->request->is('ajax')) {
			$this->redirect($this->referer());
		}
		
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('item'));
		$this->render('_featured_ajax');
	}
}