<?php
/**
 * Attachment Model File
 *
 * Copyright (c) 2007-2010 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.3
 *
 * @package	   media
 * @subpackage media.models
 * @copyright  2007-2010 David Persson <davidpersson@gmx.de>
 * @license	   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	   http://github.com/davidpersson/media
 */

/**
 * Attachment Model Class
 *
 * A ready-to-use model combining multiple behaviors.
 *
 * @package	   media
 * @subpackage media.models
 */
class CmsAttachment extends MediaAppModel {

/**
 * actsAs property
 */
	public $actsAs = array(
		'Versioning.SoftDelete',
		'Media.Transfer' => array(
			'trustClient' => false,
			'transferDirectory' => MEDIA_TRANSFER,
			'createDirectory' => true,
			'alternativeFile' => 100
		),
		'Media.Generator' => array(
			'baseDirectory' => MEDIA_TRANSFER,
			'filterDirectory' => MEDIA_FILTER,
			'createDirectory' => true,
		),
		'Media.Polymorphic',
		'Media.Coupler' => array(
			'baseDirectory' => MEDIA_TRANSFER
		),
		'Media.Meta' => array(
			'level' => 1
		),
		'Sortable'
	);

/**
 * Validation rules for file and alternative fields
 *
 * For more information on the rules used here
 * see the source of TransferBehavior and MediaBehavior or
 * the test case for MediaValidation.
 *
 * If you experience problems with your model not validating,
 * try commenting the mimeType rule or providing less strict
 * settings for single rules.
 *
 * `checkExtension()` and `checkMimeType()` take both a blacklist and
 * a whitelist. If you are on windows make sure that you addtionally
 * specify the `'tmp'` extension in case you are using a whitelist.
 */
	public $validate = array(
		'file' => array(
			'resource'	 => array('rule' => 'checkResource'),
			'access'	 => array('rule' => 'checkAccess'),
			'permission' => array('rule' => array('checkPermission', '*')),
			'size'		 => array(
				'rule' => array('checkSize', 10000000),
				'message' => 'Please upload a file no larger than 10 megabytes.'
			),
			'extension'	 => array(
				'rule' => array('checkExtension', false, array('jpg', 'jpeg', 'png', 'tif', 'tiff', 'gif', 'tmp')),
				'message' => 'Please upload a valid image (jpg, png, tiff, gif).'
			),
			'mimeType'	 => array(
				'rule' => array('checkMimeType', false, array('image/jpeg', 'image/pjpeg', 'image/png', 'image/tiff', 'image/gif')),
				'message' => 'Please upload a valid image (jpg, png, tiff, gif).'
			)
		),
		'alternative' => array(
			'rule'		 => 'checkRepresent',
			'on'		 => 'create',
			'required'	 => false,
			'allowEmpty' => true,
		)
	);

/**
 * Document validating
 */
	public $validateDocument = array(
		'file' => array(
			'resource'	 => array(
				'rule' => 'checkResource',
				'allowEmpty' => true,
				'required' => false
			),
			'access'	 => array('rule' => 'checkAccess'),
			'permission' => array('rule' => array('checkPermission', '*')),
			'size'		 => array(
				'rule' => array('checkSize', 20000000),
				'message' => 'Please upload a file no larger than 20 megabytes.'
			),
			'extension'	 => array(
				'rule' => array('checkExtension', false, array('xls', 'xlsx', 'docx', 'doc', 'csv', 'pdf', 'rtf', 'txt')),
				'message' => 'Please upload a valid document (xls, xlsx, docx, doc, csv, pdf, rtf, txt).'
			),
		),
		'alternative' => array(
			'rule'		 => 'checkRepresent',
			'on'		 => 'create',
			'required'	 => false,
			'allowEmpty' => true,
		)
	);

/**
 * Holds a validate message => HTTP error array used to relay error messages to Uploadify. An
 * HTTP response code determined by the validation error message is sent back to Uploadify upon failed
 * upload. The onUploadError Uploady callback then translates the HTTP code back to the error by
 * looking at this same array. Due to the malleable nature of error messages, this array is actually
 * set in the model constructor.
 *
 * @see CmsAttachmentsController::upload
 * @see CmsAttachment::__construct
 */
	public $validationErrorCodes = array();

/**
 * Virtual fields
 */
	public $virtualFields = array(
		'path' => 'CONCAT_WS("/", dirname, basename)'
	);

/**
 * Sets up some stuff for relaying error messages to the Uploadify uploader.
 *
 * @see Model::__construct
 */
	public function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->setValidationErrors();
	}

/**
 * Switches validate array depending on the type of file being uploaded.
 *
 * @return boolean
 */
	public function beforeValidate($options = array()) {
		if (isset($this->data[$this->alias]['validateType']) && !empty($this->data[$this->alias]['validateType'])) {
			$varName = 'validate' . ucfirst($this->data[$this->alias]['validateType']);
			if (isset($this->{$varName})) {
				$this->validate = $this->{$varName};
			}
		}

		return true;
	}
	
/**
 * Checks to see if an attachment being uploaded is empty (i.e. a new record that has no uploaded file).
 *
 * @param array data
 * @return boolean
 */
	public function isEmptyFile($data) {
		if (isset($data[$this->alias])) {
			$data[$this->alias] = $data;
		}

		if (isset($data['id']) && !empty($data['id'])) {
			return false;
		}

		if (!empty($data['file']) && is_string($data['file'])) {
			return false;
		} 

		if (isset($data['file']['size']) && $data['file']['size'] > 0) {
			return false;
		}

		return true;
	}

/**
 * Regenerates image versions for attachments belonging to $model and optionally $foreignKey,
 * after finding suitable versions.
 *
 * @param string model
 * @param integer foreign key
 * @param string group
 * @param array Optional array of versions to pass
 * @return mixed false on failure, integer on success for number of images processed
 */
	public function regenerate($model, $foreignKey = null, $group = null, $version = null, $offset = 0, $start_time = 0) {
		//
		$done	= 0;
		//
		$conditions	= array(
					$this->alias . '.model' => ($model == 'Page' ? array('PrototypeCategory', 'Page') : $model)
					, 'NOT'			=> array(
									$this->alias . '.basename' => ''
									,
					)
					,
				);
		//
		if ($foreignKey) {
			$conditions[$this->alias . '.foreign_key'] = $foreignKey;
		}
		//
		if ($group) {
			$conditions[$this->alias . '.group'] = ($model == 'Page' ? array('Category Banner Image', 'Image') : $group);
		} else {
			$conditions[$this->alias . '.group'] = ($model == 'Page' ? array('Category Banner Image', 'Image') : 'Image');
		}
		//
		$files = $this->find('list', array(
			'fields' => array('id', 'path'),
			'conditions' => $conditions
		));
		//
		if (!$files) {
			return $done;
		}
		//
		$this->Behaviors->load('Media.Generator', array('overwrite' => true));
		//
		if(!is_array($version)) {
			$versions = ClassRegistry::init('Media.AttachmentVersion')->findforRegen($model, $foreignKey, $group, $version);
		} else {
			$versions = $version;
		}
		//
		if ($start_time==0) {			
			$start_time = microtime(true);
		}
		//
		$info = array(
			'number_of_images_resized' => 0,
			'starting_offset' => $offset,
			'total_number_of_files' => count($files),
			'start_time'=>$start_time
		);
		//
		$files = array_slice($files, $offset);
		// track the longest time a set of versions has taken to regenerate
		$last_time = microtime(true);
		//
		$max_time = 5;
		//
		foreach ($files as $key => $val) {
			if (!$this->make($val, $versions)) {
				CakeLog::write( 'error', print_r($info, true));
				CakeLog::write( 'error', $key . ": " . print_r($val, true));
				$info['error'] = 'Failed at ' . ($key + $info['starting_offset']) . ': ' . $val;
				$info['number_of_images_resized'] = $done;
				return $info;
			}
			
			$done++;
			
			$time_now = microtime(true);
			
			if ($time_now - $start_time > 25 || $time_now - $start_time > 30 - 2 * $max_time ) {
				$info['number_of_images_resized'] = $done;
				$info['time_taken'] = microtime(true) - $start_time;
				return $info;
			}
			
			//update the tracker on the longest time taken to regenerate these versions
			if ($time_now - $last_time > $max_time) {
				$max_time = $time_now - $last_time;
			}
			
			$last_time = $time_now;
		}
		$info['number_of_images_resized'] = $done;
		$info['time_taken'] = microtime(true) - $start_time;
		return $info;
	}	
	

/**
 * Sets the $this->validationErrorCodes array based on the current validate array.
 *
 * @return void
 */
	public function setValidationErrors() {
		$this->validationErrorCodes = array(
			'405' => 'Upload failed',
			'408' => $this->validate['file']['size']['message'],
			'409' => $this->validate['file']['extension']['message'],
		);
	}
	
	//filter out soft deleted attachments
	public function afterFind( $results = array(), $primary = false ) {
		foreach($results as $x=>$items) {
        foreach($items as $model=>$item) {
            if( isset($item['deleted']) && !empty($item['deleted']) ) {
                unset($results[$x]);
            }
        }
    }

    return $results;
	}
}