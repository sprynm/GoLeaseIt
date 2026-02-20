<?php
/* CmsAttachmentsControllerTrait class file
 *
 * Contains virtually all functionality needed for CmsAttachmentsController, including all actions.
 * This trait exists so that other plugins can use the same actions if they need to override the default
 * attachment controller behavior for some reason.
 *
 * @copyright	 Copyright 2010-2013, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAttachmentsControllerTrait.html
 * @package		 Cms.Plugin.Media.Trait 
 * @since		 Pyramid CMS v 1.0
 */
trait CmsAttachmentsControllerTrait {


	public function admin_delete($id = null) {
				
		if (!$id) {
			$this->Notify->error('Invalid id for ' . $this->modelClass);
			$this->redirect($this->referer());
			return;
		}

		$this->{$this->modelClass}->id = $id;
		$itemName = "Item";
		
		if($this->{$this->modelClass}->hasField('basename')) {
			$itemName = $this->{$this->modelClass}->field('basename');
		} else if($this->{$this->modelClass}->hasField('name')) {
			$itemName = $this->{$this->modelClass}->field('name');
		} else if($this->{$this->modelClass}->hasField('group')) {
			$itemName = $this->{$this->modelClass}->field('group');
		}
		
		if ($this->{$this->modelClass}->delete($id)) {
			$this->set(array('message' => '<strong>' . $itemName . "</strong> deleted successfully."
			, 'class' => 'success'));
			//
			$this->Notify->success('<strong>' . $itemName . '</strong> deleted successfully.');
		} else {
			if (!$this->{$this->modelClass}->Behaviors->loaded("SoftDelete")) {
				$this->set(array('message' => '<strong>' . $itemName . "</strong> was not deleted due to an error." 
				, 'class' => 'error'));
				//
				$this->Notify->error('<strong>' . $itemName . '</strong> was not deleted due to an error.');
			} else {
				//successful soft delete so show the regular undo button
				$this->Notify->success(
					'<strong>' . $itemName . "</strong> deleted successfully. <a href='" 
					. Router::url( array(	'controller' => $this->request['controller'], 
											'plugin' => $this->request['plugin'], 
											'action' => 'undo',
											$id )
								) 
					. "'>Undo</a>"
				);
			}
		}
		//
		$this->redirect($this->referer());
	}
	
/**
 * Soft Delete the attachment via an ajax request and send back a notification on success or fail to append to the DOM
 */
	public function admin_ajax_delete() {
		$ids = array();
		
		if (empty($this->request->data['id'])) {
			return;
		}
		
		$ids = $this->request->data['id'];
		
		if (!is_array($ids)){
			$ids = array($ids);
		}
		
		foreach ($ids as $id) {
			$this->{$this->modelClass}->delete($id);
		}
		
		$undoUrl = Router::url(array('action'=>'ajax_undo_delete', 'admin'=>false));
		//write a session value to track the last deleted attachments for an easy undo delete action
		$this->Session->write("Media.Attachments.last_deleted", $ids);
		
		if (count($ids) > 1) {
			$this->Notify->success(count($ids)." attachments have been deleted. <a href=\"".$undoUrl."\" class=\"undo-attachment-delete\" data-id=\"" . implode(",",$ids) . "\">Undo Delete</a>");
		} else {
			$this->Notify->success("The attachment has been deleted. <a href=\"".$undoUrl."\" class=\"undo-attachment-delete\" data-id=\"" . implode(",",$ids) . "\">Undo Delete</a>");
		}
		
		$this->set('ids', $ids);
	}

/**
 * Undoes the last ajax attachment delete operation the user performed (in this session)
 * Since we can't check if the user has access to the id in the normal way (an ID isn't passed as a parameter)
 * this needs to be a non-admin action
 */
	public function ajax_undo_delete() {
		$ids = array();
		
		if (empty($this->request->data['id'])) {
			$ids = $this->Session->read("Media.Attachments.last_deleted");
		} else {
			//if the user specified an id then verify that the user can modify that attachment
			$request = $this->mergeRequest($this->currentRequest, array('admin'=>true));
			if (!$this->AccessControl->isAuthorized($request)) {
				$this->AccessControl->_redirect();
			}
		}
		
		//if there are no ids then quit
		if (empty($ids)) {
			return;
		}
		
		if (!is_array($ids)){
			$ids = array($ids);
		}
		
		foreach ($ids as $id) {
			//undo the delete for this item without the redirect and notification
			$this->admin_undo($id, false);
		}
		
		if (count($ids) > 1) {
			$this->Notify->success(count($ids)." deleted attachments have been restored.");
		} else {
			$this->Notify->success("The deleted attachment has been restored.");
		}
		
		if (!$this->request->is('post')) {
			$this->redirect( Router::url( $this->referer(), true ) );
		}
		
		$this->set('ids', $ids);
	}
	
/**
 * Restores a soft deleted attachment if the SoftDelete behavior has been loaded
 * 
 * @param int $id 	the id for the attachment to be restored
 */
	
	public function admin_undo($id = null, $redirect = true) {
		if (!$id) {
			$this->Notify->error('Invalid id for ' . $this->modelClass);
			$this->redirect($this->referer());
			return;
		}
		
		if (!$this->{$this->modelClass}->Behaviors->loaded("SoftDelete")) {
			$this->Notify->error( $this->modelClass. ' does not allow undoing file deletion.');
			$this->redirect($this->referer());
			return;
		}
		
		$this->{$this->modelClass}->id = $id;
		$this->{$this->modelClass}->saveField('deleted', 0);
		$this->{$this->modelClass}->saveField('deleted_date', null, false);
				
		$itemName = $this->{$this->modelClass}->alias;
		
		if ($this->{$this->modelClass}->hasField('basename') && $this->{$this->modelClass}->field('basename')) {
			$itemName = $this->{$this->modelClass}->field('basename');
		}
		
		//remove the deleted_record(s) that map to this id and model
		$DeletedRecord = ClassRegistry::init('Versioning.DeletedRecord');
		$DeletedRecord->deleteAll(array('model'=> $this->{$this->modelClass}->alias, 'model_id' => $id ));
		
		if ($redirect) {
			//let the user know it worked
			$this->Notify->success( '<strong>' . $itemName . "</strong> has been restored.");
			//send them back to where they came from
			$this->redirect($this->referer());
		}
	}

/**
 * Adds a new file upload/caption field set via AJAX.
 *
 * @return	void
 */
	public function admin_add_file() {
		if (!$this->request->is('ajax')) {
			exit();
		}

		extract($this->request->params['named']);
		if (!isset($count) || !isset($model) || !isset($assocAlias) || !isset($group)) {
			exit();
		}
		
		$group = ucwords(str_replace('_', ' ', $group));

		$this->layout = null;
		$this->autoLayout = false;
		
		$this->set(compact('count', 'assocAlias', 'model', 'group'));
	}

/**
 * Displays a row in the attachments table; called upon Uploadify success.
 *
 * @param integer $id - Attachment ID
 * @return void
 */
	public function insert_row($id = null) {
		if (!$this->request->is('ajax')) {
			die();
		}

		$item = $this->{$this->modelClass}->findById($id);
		
		if (!$item) {
			die();
		}

		$item = $item[$this->modelClass];

		$count = '%TEMP%';

		extract($this->request->data);
		//
		$uploadify	= isset($uploadify)
				? $uploadify
				: null;
		//
		$single		= isset($single)
				? $single
				: null;
		//
		$this->set(compact('item', 'count', 'assocAlias', 'model', 'attachmentType', 'plug', 'troller', 'foreign_key', 'single', 'uploadify'));

		$this->layout = 'ajax';
		$this->autoLayout = false;
	}

/**
 * Displays a row in the attachments table; called upon Uploadify success.
 *
 * @param integer $id - Attachment ID
 * @return void
 */
	public function admin_insert_row($id = null) {

		if (!$this->request->is('ajax')) {
			die();
		}

		$item = $this->{$this->modelClass}->findById($id);
		if (!$item) {
			die();
		}

		$item = $item[$this->modelClass];

		$count = '%TEMP%';

		extract($this->request->data);

		$this->set(compact('item', 'count', 'assocAlias', 'model', 'plug', 'troller', 'foreign_key'));

		$this->layout = 'ajax';
		$this->autoLayout = false;
	}

	public function admin_index(){
		if (method_exists($this->{$this->modelClass}, 'containedModels')) {
			$contain = $this->{$this->modelClass}->containedModels();
		} else {
			$contain = null;
		}
		
		$this->paginate = array(
			'Attachment' => array(
				'limit' => 42
				, 'contain'=>$contain
			)
		);
		
		$attachments = $this->Paginator->paginate('Attachment');
		$this->set(compact('attachments'));
	}


	public function admin_index_ajax($offset){
		if (method_exists($this->{$this->modelClass}, 'containedModels')) {
			$contain = $this->{$this->modelClass}->containedModels();
		} else {
			$contain = null;
		}
		
		$this->paginate = array(
			'Attachment' => array(
				'limit' => 42
				, 'contain'=>$contain
				, 'offset' => $offset
			)
		);
		
		$this->layout = 'ajax';
		$this->autoLayout = false;
		$attachments = $this->Paginator->paginate('Attachment');
		$this->set(compact('attachments'));
	}

/**
 * Displays a thumbnail in Image Settingsl called on Uploadify success.
 *
 * @param integer $id - Attachment ID
 * @return void
 */
	
	public function preview($id = null) {
		if (!$this->request->is('ajax')) {
			die();
		}

		$item = $this->{$this->modelClass}->findByForeignKey($id);
		
		if (!$item) {
			die();
		}

		extract($this->request->data);
		$item = $item[$this->modelClass];
		
		$this->set(compact('item', 'assocAlias'));

		$this->layout = 'ajax';
		$this->autoLayout = false;
	}

/**
 * Sets the session ID if it has been transmitted by Uploadify.
 *
 * @see Controller::startupProcess
 * @return void
 */
	public function startupProcess() {
		parent::startupProcess();
		if ($this->request->is('flash') && isset($_REQUEST["session_id"])) {
			$this->controller->Session->id($_REQUEST['session_id']);
		}
	}

/**
 * Upload interface for Uploadify - adds an attachment via the SWF uploader.
 *
 * @return void
 */
	
	public function upload() {
		$this->layout = 'ajax';
		$this->autoLayout = false;
		
		if (!isset($this->request->data['modelId']) || !isset($this->request->data['group']) || !isset($this->request->data['model'])) {
			die();
		}
		

		if (!$_FILES || !isset($_FILES['Filedata']) || !$_FILES['Filedata']) {
			die();
		}

		//
		$data = array($this->modelClass => array(
			'model' => $this->request->data['model'],
			'foreign_key' => $this->request->data['modelId'],
			'group' => ucwords($this->request->data['group']),
			'file' => $_FILES['Filedata']
		));
		
		
		// Set validation based on possible document type
		if (isset($this->request->data['type']) && !empty($this->request->data['type'])) {
			$validate = 'validate' . ucfirst($this->request->data['type']);
			if (isset($this->{$this->modelClass}->{$validate})) {
				$this->{$this->modelClass}->setValidation($this->request->data['type']);
				$this->{$this->modelClass}->setValidationErrors();
			}
		}
		
		if($data[$this->modelClass]['model'] == 'Setting') {
			$setting = $this->{$this->modelClass}->findByForeignKey($data[$this->modelClass]['foreign_key']);
			$this->{$this->modelClass}->id = $setting[$this->modelClass]['id'];
		}

		$saved = $this->{$this->modelClass}->save($data);
		if ($saved) {
			echo $this->{$this->modelClass}->id;
		} else {
			if (isset($this->{$this->modelClass}->validationErrors['file'][0]) && in_array($this->{$this->modelClass}->validationErrors['file'][0], 
			$this->{$this->modelClass}->validationErrorCodes)) {
				$error = array_search($this->{$this->modelClass}->validationErrors['file'][0], $this->{$this->modelClass}->validationErrorCodes);
			} else {
				$error = 405;
			}
			
			http_response_code(intval($error));
		}
		die();
	}

	/**
	 * Checks for presence of file in webroot.
	 *
	 * @return void
	 */
	public function checkScript($type = 'img') {
		$this->autoRender = false;
		
		$filename = strtolower(Inflector::slug($_POST['filename']));
		
		if (file_exists(MEDIA_TRANSFER . $type . '/' . $filename)) {
			echo 1;
		}/* else {
			echo 0;
		}*/

	}
	
/**
 * Action for viewing transfer files in app/transfer.
 *
 * @param string $path
 * @return void
 */
	public function view($dir, $name, $ext) {
		$file = $dir . DS . $name . '.' . $ext;
		$this->response->file(MEDIA_TRANSFER . $file);
		return $this->response;
	}
/**
 * Crop an image.
 *
 * @param integer $id - Attachment ID
 * @return void
 */
	
	public function admin_crop($id) {
		$AttachmentModel = $this->{$this->modelClass};
		//find the attachment that we will be cropping
		$image	= $AttachmentModel->findById($id);
		
		if (!$image) {
			return;
		}
		//
		$this->set('image', $image[$AttachmentModel->alias]);
		
		$AttachmentVersion = ClassRegistry::init('Media.AttachmentVersion');
		
		$modelName = $image[$AttachmentModel->alias]['model'];
		//both prototypes and prototype instances use the PrototypeInstance model name
		if ($image[$AttachmentModel->alias]['model'] == 'PrototypeItem' || $image[$AttachmentModel->alias]['model'] == 'PrototypeCategory'){
			$modelName = 'PrototypeInstance';
		}
	
		if (
			($modelName == 'PrototypeInstance' && $image[$AttachmentModel->alias]['model'] == 'PrototypeInstance')
			||
			(stripos($image[$AttachmentModel->alias]['group'], 'banner') !== false)
		) {
			//
			$group = 'Image';
			//
			$modelName = 'Page';
		//
		} else if ($modelName == 'PrototypeInstance' && $image[$AttachmentModel->alias]['model'] == 'PrototypeItem') {
			$group = 'Item Image';
		} else if ($modelName == 'PrototypeInstance' && $image[$AttachmentModel->alias]['model'] == 'PrototypeCategory') {
			$group = 'Category Image';
		} else if ($modelName == 'Page') {
			$group = 'Image';
		} else {
			$group = $image[$AttachmentModel->alias]['group'];
		}
		
		$foreignKey = null;
		
		if ($modelName == 'PrototypeInstance' || $modelName == 'CustomFieldValue') {
			$foreignKey = $this->params->named['foreign_key'];
		}
		//
		/*if ($image[$AttachmentModel->alias]['model'] == 'PrototypeInstance') {
			//
			$versionName	= 'banner-lrg';
			//
			$version = $AttachmentVersion->findForRegen($modelName, $foreignKey, $group, $versionName, false);

			if (!empty($version)){
				$this->set('version', $version[0][$AttachmentVersion->alias]);
			}
		//if a version was specified then select it
		} else*/ if ( !empty($this->params->named['version']) ) {
			$versionName	= $this->params->named['version'];
			
			$version = $AttachmentVersion->findForRegen($modelName, $foreignKey, $group, $versionName, false);

			if (!empty($version)){
				$this->set('version', $version[0][$AttachmentVersion->alias]);
			}
		}
		//
		$versions = $AttachmentVersion->findForRegen($modelName, $foreignKey, $group, null, false);
		//
// 
##echo '<p>$modelName: ' . $modelName . ' $foreignKey: ' . $foreignKey . ' $group: ' . $group . ' $versions:<pre>' . print_r($versions, true) . '</pre></p>';
//
##die();
		//
		$this->set('versions', $versions);
		
		$this->render("Media.Attachments/admin_crop");
	}

/**
 * Using the image data on canvas create an image and then save it over the existing.
 *
 * @param integer $id - Attachment ID
 * @return void
 */
	public function admin_ajax_save_canvas($id, $attachment_version_id) {
		if (!$this->request->is('ajax')) {
			return false;
		}
		
		if (empty($id) || empty($attachment_version_id)) {
			return false;
		}
		
		//since it's an ajax request it doesn't need to render the page
		$this->autoRender	= false;
		
		//grab the image data
		$data	= $this->request->data['photo'];
		
		//separate the file type from the encoded image
		list($type, $data) = explode(';', $data);
		//remove the "base64," at the start of the data
		list(, $data) = explode(',', $data);
		
		//got the raw image data
		$data = base64_decode($data);
		
		$attachment = $this->{$this->modelClass}->findById($id);
		$AttachmentVersion = ClassRegistry::init("Media.AttachmentVersion");
		$attachmentVersion = $AttachmentVersion->findById($attachment_version_id);
		
		//determine where to save the file
		if (empty($attachment) || empty($attachmentVersion)) {
			return false;
		}
		
		//make sure that the models have the same group
		$group = "";
		if ($attachmentVersion[$AttachmentVersion->alias]['model'] == 'PrototypeInstance' && $attachment[$this->{$this->modelClass}->alias]['model'] == 'PrototypeItem') {
			$group	= 'Item Image';
		} else if ($attachmentVersion[$AttachmentVersion->alias]['model'] == 'PrototypeInstance' && $attachment[$this->{$this->modelClass}->alias]['model'] == 'PrototypeCategory') {
			$group	= 'Category Image';
		} else if ($attachmentVersion[$AttachmentVersion->alias]['model'] == 'Page') {
			$group	= 'Image';
		} else {
			$group	= $attachment[$this->{$this->modelClass}->alias]['group'];
		}
		
		if (!empty($group) && strtolower($attachmentVersion[$AttachmentVersion->alias]['group']) != strtolower($group)) {
			return false;
		}
		if (!empty($attachmentVersion[$AttachmentVersion->alias]['foreign_key'])) {
			if ($group != 'Item Image' && $group != 'Category Image') {
				//if the attachment and version aren't connected to the same thing the there's a problem
				//if there's a foreign key set for the version and the version's foreign key isn't the same as the attachment's foreign key
				if ( $attachmentVersion[$AttachmentVersion->alias]['foreign_key'] != $attachment[$this->{$this->modelClass}->alias]['foreign_key'] ){
					return false;
				}
			} else {
				//the attachment will be linked to either the category or item whereas the version will be linked to the image
				//so find the id of the prototypecategory or prototypeitem and compare that to the attachmentVersion foreign_key
				$PrototypeModel = ClassRegistry::init("Prototype.".$attachment[$this->{$this->modelClass}->alias]['model']);
				$prototypeThing = $PrototypeModel->findById($attachment[$this->{$this->modelClass}->alias]['foreign_key']);
				
				if ($prototypeThing[$PrototypeModel->alias]['prototype_instance_id'] != $attachmentVersion[$AttachmentVersion->alias]['foreign_key']) {
					return false;
				}
			}	
		}
		
		//now that we've verified that this attachment version is valid for this attachment we can save the version
		$file_directory = $attachmentVersion[$AttachmentVersion->alias]['name'] . DS . $attachment[$this->{$this->modelClass}->alias]['dirname'] . DS;
		$file_name = $attachment[$this->{$this->modelClass}->alias]['basename'];
		
		//remove the extension from the file name
		$file_name = explode(".", $file_name);
		array_pop($file_name);
		$file_name = implode(".", $file_name);
		
		//add the new file extension
		//Mime_Type is included in Attachment so we don't need to include it again
		$file_name .= "." . Mime_Type::guessExtension($attachmentVersion[$AttachmentVersion->alias]['convert']);
		
		//add the media filter directory to the beginning of the path
		$file_path = MEDIA_FILTER . $file_directory . $file_name;
		
		//check if the containing dir exists and if not create it
		$containingDir = MEDIA_FILTER . $file_directory;
		if (!is_dir($containingDir)) {
			mkdir($containingDir, 0777, true);
		}
		
		//finally overwrite the target file
		file_put_contents($file_path, $data);
		
		$this->_resizeImage(array('convert' => $attachmentVersion[$AttachmentVersion->alias]['convert'], 'mime_type' => $this->request->data['mime_type'], 'file_name' => $file_path, 'width' => $this->request->data['width'], 'height' => $this->request->data['height']));
	}
	
/**
 * Saves a specific version for an image attachment
 *
 */
	public function admin_upload_version(){
		$this->autoRender = false;
		
		$file_name = $this->request->data['file_name'];
		print_r($_FILES);
		if (empty($_FILES['file']['name'])) {
			//error no file provided
			return false;
		}
		
		move_uploaded_file($_FILES['file']['tmp_name'], $file_name);
		
		$this->_resizeImage(array('convert' => $this->request->data['convert'], 'mime_type' => $_FILES['file']['type'], 'file_name' => $file_name, 'width' => $this->request->data['width'], 'height' => $this->request->data['height']));
		
	}
	
	public function _resizeImage($array) {
	//
		list($org_width, $org_height) = getimagesize($array['file_name']);
	//
	        switch ($array['mime_type'])
	        {
	        //
	            case 'image/gif':
		//
			$src		= imagecreatefromgif($array['file_name']);
	                break;
	            case 'image/png':
		//
			$src		= imagecreatefrompng($array['file_name']);
	                break;
	            default:
		//
			$src		= imagecreatefromjpeg($array['file_name']);
	        }
        
	//
		$tmp		= imagecreatetruecolor($array['width'], $array['height']);
	// preserve transparency
		if($array['convert'] == 'image/png' || $array['convert'] == 'image/gif'){
			imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
			imagealphablending($tmp, false);
			imagesavealpha($tmp, true);
		}
	//
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $array['width'], $array['height'], $org_width, $org_height); 
	//
		switch($array['convert'])
		{
		//
			case 'image/gif': imagegif($tmp, $array['file_name']); break;
		//
			case 'image/png': imagepng($tmp, $array['file_name']); break;
		//
			default: imagejpeg($tmp, $array['file_name'], 100);
		}
	}
	
	
}