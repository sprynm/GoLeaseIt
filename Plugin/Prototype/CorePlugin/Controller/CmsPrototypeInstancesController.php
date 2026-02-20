<?php
/**
 * CmsPrototypeInstancesController class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsPrototypeInstancesController.html
 * @package      Cms.Plugin.Prototype.Controller 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeInstancesController extends PrototypeAppController {


/**
 * Admin delete function. Finds and deletes permissions associated with the prototype, then calls parent.
 *
 * @param	integer $id 
 * @return	void
 */

	public function admin_delete($id) {
		$prototype = $this->PrototypeInstance->find('first', 
					array(	'conditions' => array('PrototypeInstance.id' => $id),
							'fields' => array('slug'),
					));
		ClassRegistry::init('Users.Permission')->deleteAll(array('Permission.controller' => $prototype['PrototypeInstance']['slug']));
		parent::admin_delete($id);
	}


/**
 * Admin edit function
 *
 * @param	integer $id Optional
 * @return	void
 */	
	public function admin_edit($id = null) {
	
		$this->set('layouts', $this->Admin->getLayoutFiles(AccessControl::inGroup('Super Administrator')));

		// Add a prototype admin permission if one does not exist.
		if ($id) {
			$this->PrototypeInstance->verifyAdminPermission($id);
		}
		$plugins = ClassRegistry::init('PluginTools.InstalledPlugin')->find('list', 
								array('fields' => array(
									'InstalledPlugin.name'
									, 'InstalledPlugin.alias'
								)
							));
							
		$plugins = array_merge(array_values($plugins), array_keys($plugins));
		$plugins = array_unique($plugins);
		
		
		//set groups for group checkboxes
		$this->set('groups', $this->PrototypeInstance->getGroupsForInputs());
		
		$this->set('plugins', $plugins);
		//
		$this->set('bannerImage', Configure::read('Settings.Pages.PageOptions.banner_image'));
		//
		parent::admin_edit($id); 
		
	}

/**
 * Admin index - extended from CmsAppController to load, and allow for install of, preconfigured instances.
 *
 * @return void
 */
	public function admin_index() {
		$preconfigured = $this->PrototypeInstance->loadPreconfigured();
		$keys = array_keys($preconfigured);
		$this->set('preconfigured', array_combine($keys, $keys));

		if (!empty($this->request->data)) {
			$name = $this->request->data['PrototypeInstance']['preconfigured'];
			if ($this->PrototypeInstance->installPreconfigured($name, $preconfigured[$name])) {
				$this->Notify->success($name . ' has been successfully installed.');
			} else {
				$this->Notify->error('Could not install ' . $name . ' - please try again or contact a system administrator.');
			}
		}

		parent::admin_index();
	}

/**
 * Regenerates versions of images for prototype items or categories of $instance. Passes off the work to
 * the PrototypeInstance model.
 *
 * @param integer $id instance ID
 * @param string $type either 'item' or 'category'
 * @return void
 */
	public function admin_regenerate() {
		
		//use post if this is a post request
		$requestData = $this->request->named;
		if ($this->request->is('post')) {
			$requestData = $this->request->data;
		}
		
		//optional parameters
		$id = null;
		$version = null;
		$type = 'item';
		
		if (!empty($requestData['foreign_key'])) {
			$id = $requestData['foreign_key'];
		}
		
		if (!empty($requestData['version'])) {
			$version = $requestData['version'];
		}
		
		if (!empty($requestData['group']) && $requestData['group'] == "Category Image") {
			$type = 'category';
		}
		
		if (!empty($requestData['model'])) {
			$model = $requestData['model'];
		} else {
			$model = "PrototypeInstance";
		}
		if (!empty($requestData['foreign_key'])) {
			$foreignKey = $requestData['foreign_key'];
		} else {
			$foreignKey = 0;
		}
		
		
		if (!empty($requestData['offset'])) {
			$offset = $requestData['offset'];
		} else {
			$offset = 0;
		}
		
		if (!empty($requestData['group']) && $requestData['group'] == "Category Image") {
			$type = 'category';
		}
		
		$result = $this->PrototypeInstance->regenerateImages($id, $type, $version, $offset);
		$message = 'Images regenerated for ' . $version . ' versions. <ul>';
		
		$regeneratedImages = 0;
		
		if(is_array($result)) {
			foreach($result as $key => $val) {
				$regeneratedImages += $val['number_of_images_resized'];
				$info = $val;
				
				if (isset($info['error'])) {
					if(($info['number_of_images_resized'] + $info['starting_offset']) < $info['total_number_of_files']) {
						$keep_resizing = sprintf("<a id=\"regenerate_link\" href='%s'>Skip item and keep resizing from offset %d</a>"
							, Router::url(array(
								'action' => 'regenerate'
								, 'model' => $model
								, 'version' => $version
								, 'group' => $type
								, 'foreign_key' => $foreignKey
								, 'offset' => ($regeneratedImages + $offset + 1)))
							, ($regeneratedImages + $offset + 1));
					}
					$this->Notify->error("Could not regenerate image versions. " . $info['error'] . ". " . $keep_resizing);
					
				} else {
					$keep_resizing = ' ';
					
					if(($info['number_of_images_resized'] + $info['starting_offset']) < $info['total_number_of_files']) {
						$keep_resizing = "<li>".sprintf("<a id=\"regenerate_link\" href='%s'>Keep resizing from offset %d</a>"
							, Router::url(array('action' => 'regenerate'
												, 'model' => $model
												, 'version' => $version
												, 'group' => $type
												, 'foreign_key' => $foreignKey
												, 'offset' => ($regeneratedImages + $offset)))
							, ( $regeneratedImages + $offset ));
						$breakAfter = true;
					}
				}
				
				$message .= "<li>".sprintf( 
					"Regenerated versions for %d of %d image%s for %s. %s", 
					$info['number_of_images_resized'], 
					$info['total_number_of_files'], 
					($info['total_number_of_files'] > 1 || $info['total_number_of_files'] == 0) ? 's' : '', 
					$key,
					$keep_resizing
				)."</li>";
					
				if (!empty($breakAfter)){
					break;
				}
			}
		}
		$message .= '</ul>';
		
		$this->Notify->success($message);
			
		$this->redirect($this->referer());
	}

/**
 * Edit page for the instance description, basically just for client admins to edit the intro
 * to an instance. The instance ($this->instance) is set in CmsPrototypeAppController.
 *
 * @return void
 */
	public function admin_summary_edit() {
		if (!empty($this->request->data)) {
			$this->PrototypeInstance->setValidation('none');
			if ($this->PrototypeInstance->saveAll($this->data, array('fieldlist' => array('description', 'footer_text')))) {
				$this->Notify->handleSuccessfulSave(array(
					'return' => array(
						'controller' => 'prototype_items', 
						'action' => 'index', 
						'admin' => true, 
						'instance' => $this->instance['PrototypeInstance']['slug']
					), 
					'continue' => $this->request->here
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->instance;
		}
		//
		$pagesbannerImage	= Configure::read('Settings.Pages.PageOptions.banner_image');
		//
		$prototypeBannerImage	= $this->instance['PrototypeInstance']['use_page_banner_images'];
		//
		$this->set('bannerImage', ($pagesbannerImage && $prototypeBannerImage ? true : false));
		//
		$this->PageSettings->setTitle($this->instance['PrototypeInstance']['name']);
	}

/**
 * Basic view function - displays either a list of categories or a list of items, depending
 * on whether the instance has categories. The instance has already been loaded as $this->instance
 * in CmsPrototypeAppController, so we just need to load the categories/items.
 *
 * @return void
 */
	public function view() {
		if ($this->instance['PrototypeInstance']['use_categories']) {
			$this->_categorySummary();
		} else {
			$this->_itemSummary();
		}
		//
		$customFields = ClassRegistry::init('CustomFields.CustomField')->findForModel(
			'PrototypeInstance', 
			$this->instance['PrototypeInstance']['id'], 
			'PrototypeInstance'
		);
		//
		$pagesBannerImage	= Configure::read('Settings.Pages.PageOptions.banner_image');
		//
		$prototypeBannerImage	= !empty($this->instance['PrototypeInstance']['use_page_banner_images']);
		//if there's a banner and banner images are turned on then set the banner
		
		if ( !empty($this->instance['Image']) && $pagesBannerImage && $prototypeBannerImage ) {
			$this->set('banner', array('Image' => $this->instance['Image']));
			
		}

		$this->set(array('pageIntro', 'customFields'), array($this->instance['PrototypeInstance']['description'], $customFields));
		$name = $this->instance['PrototypeInstance']['head_title'] ? $this->instance['PrototypeInstance']['head_title'] : $this->instance['PrototypeInstance']['name'];
		$this->PageSettings->setTitle($name, $this->instance['PrototypeInstance']['name']);
		$this->set('prototypeInstance', $this->instance); // Required for meta tags which needs the full model name.
	}

/**
 * Summary of categories - called by view() when the instance loaded uses categories. Also loads the items
 * for each category.
 *
 * @return void
 */
	protected function _categorySummary() {
		$categories = $this->PrototypeInstance->PrototypeCategory->findForSummary($this->instance);
		$this->set(compact('categories'));
	}

/**
 * Summary of items - called by view() when the instance loaded does not use categories. Paginates
 * if necessary.
 *
 * @return void 
 */
	protected function _itemSummary() {
		// Load the items separately, with pagination if it's set in the instance.
		$query = $this->PrototypeInstance->PrototypeItem->summaryQuery($this->instance);

		if (isset($this->instance['PrototypeInstance']['item_summary_pagination']) && $this->instance['PrototypeInstance']['item_summary_pagination'] == true) {
			$query['limit'] = isset($this->instance['PrototypeInstance']['item_summary_pagination_limit']) ? $this->instance['PrototypeInstance']['item_summary_pagination_limit'] : 10;
			$this->paginate = array('PrototypeItem' => $query);
			$items = $this->paginate('PrototypeItem');
			$paginatedItems = true;
		} else {
			$items = $this->PrototypeInstance->PrototypeItem->find('all', $query);
			$paginatedItems = false;
		} 

		$this->set(compact('items', 'paginatedItems'));
	}
}
