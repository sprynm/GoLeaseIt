<?php
/**
 * CmsContentBlock class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsContentBlock.html
 * @package		 Cms.Plugin.ContentBlocks.Model
 * @since		 Pyramid CMS v 1.0
 */
class CmsContentBlock extends ContentBlocksAppModel {

/**
 * Helpers
 */
	public $actsAs = array(
		'Versioning.SoftDelete',
		'Copyable',
		'Publishing.Publishable'
	);

/**
 * Validation array
 */
	public $validate = array(
		'content' => array(
			'rule' => 'notEmpty', 
			'message' => 'This field cannot be blank.'
		),
		'name' => array(
			'rule' => 'notEmpty', 
			'message' => 'This field cannot be blank.'
		)
	);

/**
 * Finds for TinyMCE event listener
 *
 * @return array
 */
	public function findForTinyMce() {
		//
		$data	= Router::getRequest();
		//
		$id	= isset($data['data']['ContentBlock']['id']) && $data['data']['ContentBlock']['id'] > 0
			? $data['data']['ContentBlock']['id']
			: array();
		//
		$blocks = $this->find('all', array(
			'fields' => array('id', 'name'),
			'conditions'	=> array('NOT' => array($this->alias . '.id' => $id)),
			'published' => true,
			'cache' => true
		));
		//
		return $blocks;
	}

}