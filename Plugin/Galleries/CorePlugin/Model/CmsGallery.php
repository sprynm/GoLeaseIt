<?php
/**
 * CmsGallery class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsGallery.html
 * @package		 Cms.Plugin.Galleries.Model
 * @since		 Pyramid CMS v 1.0
 */
class CmsGallery extends GalleriesAppModel {

/**
 * Behaviors
 */
	public $actsAs = array(
		'Publishing.Publishable',
		'Versioning.SoftDelete',
		'Copyable'
	);

/**
 * hasMany assocations
 */
	public $hasMany = array(
		'Image' => array(
			'className' => 'Media.Attachment', 
			'foreignKey' => 'foreign_key', 
			'conditions' => array(
				'Image.model' => 'Gallery', 
				'Image.group' => 'Image'
			), 
			'dependent' => true, 
			'order' => 'Image.rank ASC, Image.id ASC'
		)
	);

/**
 * Validation array
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty', 
			'message' => 'This field cannot be blank.'
		)
	);

/**
 * Finds a gallery with id $id and returns for usage in the GalleryBlockHelper.
 *
 * @param integer id
 * @return array
 */
	public function findForDisplay($id) {
	//
		$gallery = $this->find(
			'first'
			, array(
				'conditions'	=> array(
							'Gallery.id' => $id
							,
				)
				, 'contain'	=> array(
							'Image'
							,
				)
				, 'published'	=> true
				,
			)
		);
	//
		return $gallery;
	}

/**
 * Finds a gallery for edit
 *
 * @param integer id
 * @return array
 */
	public function findForEdit($id) {
		$gallery = $this->find('first', array(
			'conditions' => array('Gallery.id' => $id),
			'contain' => array(
				'Image'
			)
		));
		
		return $gallery;
	}

/**
 * Finds for TinyMCE event listener
 *
 * @return array
 */
	public function findForTinyMce() {
		$galleries = $this->find('all', array(
			'fields' => array('id', 'name'),
			'order' => 'PublishingInformation.start DESC',
			'published' => true,
			'cache' => true
		));
		
		return $galleries;
	}

}