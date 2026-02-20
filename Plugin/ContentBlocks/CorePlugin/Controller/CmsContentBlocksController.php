<?php
/**
 * CmsContentBlocksController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsContentBlocksController.html
 * @package		 Cms.Plugin.ContentBlocks.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsContentBlocksController extends ContentBlocksAppController {

/**
 * Admin index function
 *
 * @return void
 */
	public function admin_index() {
		$conditions = array();
		if (!AccessControl::inGroup('Super Administrator')) {
			$conditions['ContentBlock.super_admin'] = 0;
		}
		$this->AutoPaginate->setPaginate(array(
			'conditions' => $conditions
		));
		$contentBlocks = $this->paginate();
		$this->set(compact('contentBlocks'));
	}

}