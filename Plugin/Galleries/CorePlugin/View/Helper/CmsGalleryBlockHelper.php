<?php
App::uses('BlockHelper', 'View/Helper');

/**
 * CmsGalleryHelper class
 *
 * Replaces static block placeholders in view content with dynamic content.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsGalleryHelper.html
 * @package		 Cms.Plugin.Galleries.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsGalleryBlockHelper extends BlockHelper {

/**
 * Helpers
 */
	public $helpers = array(
		'Html' => array('className' => 'AppHtml'),
		//'Media.Media'
	);

/**
 * Searches $content for gallery placeholders and replaces with a gallery view element.
 *
 * @see Helper::afterRenderFile
 * @throws CakeException
 * @return string the modified content
 */
	public function afterRenderFile($viewFile, $content) {
		if ($this->adminCheck()) {
			return $content;
		}
		
		$matches = $this->_getMatches($content, 'Gallery');
		if (empty($matches)) {
			return $content;
		}
		//
		foreach ($matches as $key => $val) {
			//
			$cacheName = 'gallery_' . $val[0];
			//
			$output = Cache::read($cacheName, 'block');
			//
			if ($output === false) {
				$gallery = ClassRegistry::init('Galleries.Gallery')->findForDisplay($val[0]);
				if (!$gallery) {
					$content = str_replace($val[1], '', $content);
					continue;
				}
			
				$element = $this->_findElement($gallery);
				if (!$element) {
					throw new CakeException('CmsGalleryBlockHelper: element file not found for gallery "' . $gallery['Gallery']['id'] . '"');
				}

				$output = $this->_View->element($element, array('gallery' => $gallery['Image']));
				Cache::write($cacheName, $output, 'block');
			}
			
			$content = str_replace($val[1], $output, $content);
		}

		return $content;
	}
	
/**
 * Searches for an appropriate gallery view element in this order:
 * 
 * - APP/Plugin/Galleries/View/Elements/<id>.ctp
 * - APP/Plugin/Media/View/Elements/basic_gallery.ctp
 * - CMS/Plugin/Media/View/Elements/basic_gallery.ctp
 *
 * @param array the gallery
 * @return string the element path
 */
	protected function _findElement($gallery) {
		$paths = array(
			APP . 'Plugin' . DS . 'Galleries' . DS . 'View' . DS . 'Elements' . DS . $gallery['Gallery']['id'] . '.ctp' => 'Galleries',
			APP . 'Plugin' . DS . 'Galleries' . DS . 'View' . DS . 'Elements' . DS . $gallery['Gallery']['type'] . '.ctp' => 'Galleries',
			APP . 'Plugin' . DS . 'Media' . DS . 'View' . DS . 'Elements' . DS . 'basic_gallery.ctp' => 'Media',
			CMS . 'Plugin' . DS . 'Media' . DS . 'View' . DS . 'Elements' . DS . 'basic_gallery.ctp' => 'Media'
		);
		
		foreach ($paths as $path => $plugin) {
			if (file_exists($path)) {
				return $plugin . '.' . basename($path, '.ctp');
			}
		}
		
		return null;
	}

}