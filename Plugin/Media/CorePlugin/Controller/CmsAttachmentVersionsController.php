<?php
/**
 * Attachment Versions Controller class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPagesAppController.html
 * @package		 Cms.Plugin.Media.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsAttachmentVersionsController extends MediaAppController {

/**
 * Adds a new attachment version via AJAX.
 *
 * @return	void
 */
	public function admin_add_version() {
		if (!$this->request->is('ajax')) {
			exit();
		}
		
		extract($this->request->params['named']);
		if (!isset($count) || !isset($group) || !isset($model) || !isset($alias)) {
			exit();
		}
		
		if (isset($this->params['named']['foreign_key'])) {
			$foreignKey = $this->params['named']['foreign_key'];
		} else {
			$foreignKey = null;
		}
		
		$group = ucwords(str_replace('_', ' ', $group));
		
		$this->layout = null;
		$this->autoLayout = false;
		
		$this->helpers[] = 'Media.MediaVersion';
		$this->set(compact('count', 'group', 'model', 'foreignKey', 'alias'));
	}
	
/**
 * Admin edit
 *
 * @param string model
 * @param integer foreign key
 * @return void
 */
	public function admin_edit($model = null, $foreignKey = null) {
		if (!$model) {
			$this->redirect(array('plugin' => 'administration', 'controller' => 'dashboard', 'action' => 'index'));
		}
		
		if (!empty($this->request->data)) {
			if ($this->AttachmentVersion->saveAll($this->data['AttachmentVersion'])) {
				$this->Notify->handleSuccessfulSave(array(
					'return' => $this->request->here(),
					'continue' => '/' . $this->request->here()
				));
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		$conditions = array('AttachmentVersion.model' => $model);
		$conditions['AttachmentVersion.foreign_key'] = $foreignKey ? $foreignKey : null;

		$versions = $this->AttachmentVersion->find('all', array(
			'conditions' => $conditions
		));
		$versions = Set::combine($versions, '{n}.AttachmentVersion.id', '{n}.AttachmentVersion', '{n}.AttachmentVersion.group');
		$dataVersions = Set::combine($this->data, 'AttachmentVersion.{n}.id', 'AttachmentVersion.{n}', 'AttachmentVersion.{n}.group');
		$versions = Set::merge($versions, $dataVersions);
		
		//set default version for page banner images
		if ($model == 'Page' && empty($versions)) {
			$versions	= array(
				'Banner Image' => array(
					array(
						'model' => $model
						, 'group' => 'Image'
						, 'foreign_key' => NULL
						, 'name' => 'banner'
						, 'type' => 'fit'
						, 'convert' => 'image/jpeg'
						, 'width' => 1200
						, 'height' => 375
						, 'id' => NULL
					)
				)
			);
			$group		= 'Image';
		}
		
		//set default for customfield values
		if (in_array($model, array('CustomFieldValue', 'CustomField')) && empty($versions) ){
			$versions	= array(
				'Image' => array(
					array(
						'model' => $model
						, 'group' => 'Image'
						, 'foreign_key' => $foreignKey
						, 'name' => 'large'
						, 'type' => 'fit'
						, 'convert' => 'image/jpeg'
						, 'width' => 800
						, 'height' => 600
						, 'id' => NULL
					)
				)
			);
			$group		= 'Image';
		}
		
		$this->set(compact('versions'));

		$this->set(compact('model', 'foreignKey'));
	}

/**
 * Regenerates versions of images for any image versions found belonging to $model and optionally $foreignKey.
 * The CmsAttachment::regenerate() function will return either false (for a failure) or the number of images
 * regenerated.
 *
 * @param string model
 * @param integer foreign key
 * @param string optional group
 * @return void
 */
	public function admin_regenerate() {
		//use post if this is a post request
		$requestData = $this->request->named;
		if ($this->request->is('post')) {
			$requestData = $this->request->data;
		}
		
		//no model passed so flash error
		if (empty($requestData['model'])) {
			$this->Notify->error('Could not regenerate image version since no model was given.');
			$this->redirect($this->referer());
		}
		$model = $requestData['model'];
		
		
		//optional parameters
		$version = null;
		$foreignKey = null;
		$group = null;
		
		if (!empty($requestData['version'])) {
			$version = $requestData['version'];
		}
		
		if (!empty($requestData['foreign_key'])) {
			$foreignKey = $requestData['foreign_key'];
		}
		
		if (!empty($requestData['group'])) {
			$group = $requestData['group'];
		}
		
		//load the attachment model from the named or query variable
		if (!empty($this->request->query['attachmentModel'])) {
			$attachmentModel = $this->request->query['attachmentModel'];
		} else if (!empty($this->request->named['attachmentModel'])) {
			$attachmentModel = $this->request->named['attachmentModel'];
		} else {
			$attachmentModel = 'Media.Attachment';
		}
		
		$offset = isset($requestData['offset']) ? $requestData['offset'] : 0;
		$info = ClassRegistry::init($attachmentModel)->regenerate($model, $foreignKey, $group, $version, $offset);
		
		if (isset($info['error'])) {
			$keep_resizing = '';
			if(($info['number_of_images_resized'] + $info['starting_offset']) < $info['total_number_of_files']) {
				$keep_resizing = sprintf("<a id=\"regenerate_link\" href='%s'>Skip item and keep resizing from offset %d</a>"
					, Router::url(array('action' => 'regenerate'
										, 'model' => $model
										, 'version' => $version
										, 'group' => $group
										, 'foreign_key' => $foreignKey
										, 'attachmentModel' => $attachmentModel
										, 'offset' => ($info['number_of_images_resized'] + $info['starting_offset'] + 1)))
					, ($info['number_of_images_resized'] + $info['starting_offset'] + 1));
			}
			$this->Notify->error("Could not regenerate image versions. " . $info['error'] . ". " . $keep_resizing);
			
		} else {
			$keep_resizing = ' ';
	
			if(($info['number_of_images_resized'] + $info['starting_offset']) < $info['total_number_of_files']) {
				$keep_resizing = sprintf("<a id=\"regenerate_link\" href='%s'>Keep resizing from offset %d</a>"
					, Router::url(array('action' => 'regenerate'
										, 'model' => $model
										, 'version' => $version
										, 'group' => $group
										, 'foreign_key' => $foreignKey
										, 'attachmentModel' => $attachmentModel
										, 'offset' => ($info['number_of_images_resized'] + $info['starting_offset'])))
					, ($info['number_of_images_resized'] + $info['starting_offset']));
			}
		
			$this->Notify->success(sprintf( "Regenerated versions for %d of %d image%s. %s", 
				$info['number_of_images_resized'], 
				$info['total_number_of_files'], 
				($info['total_number_of_files'] > 1 || $info['total_number_of_files'] == 0) ? 's' : '', $keep_resizing));
		
		}

		$this->redirect($this->referer());
	}
	
}
