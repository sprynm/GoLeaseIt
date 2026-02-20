<?php
App::uses('AttachmentsControllerTrait', 'Media.Trait');
/**
 * Attachments Controller class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPagesAppController.html
 * @package		 Cms.Plugin.Media.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsAttachmentsController extends MediaAppController {

/**
 * All functionality moved to a trait so that it can be shared if needed.
 */
	use AttachmentsControllerTrait;

	
	
	
}