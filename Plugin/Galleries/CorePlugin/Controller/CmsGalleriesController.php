<?php
/**
 * CmsGalleriesController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsGalleriesController.html
 * @package		 Cms.Plugin.Galleries.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsGalleriesController extends GalleriesAppController {

/**
 * Default admin edit
 *
 * @param	integer $id OPTIONAL
 * @return	void
 */
	public function admin_edit($id = null) {
		if (!empty($this->request->data)) {
			if ($this->Gallery->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave();
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->Gallery->findForEdit($id);
		}
	}
	
}