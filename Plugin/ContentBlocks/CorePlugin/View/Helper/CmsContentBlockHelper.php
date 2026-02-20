<?php
App::uses('BlockHelper', 'View/Helper');

/**
 * CmsContentBlockHelper class
 *
 * Replaces content block placeholders in view content with dynamic content.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsContentBlockHelper.html
 * @package		 Cms.Plugin.ContentBlocks.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsContentBlockHelper extends BlockHelper {

/**
 * Searches $content for content block placeholders and replaces with the proper data.
 *
 * @see Helper::afterRenderFile
 * @return string the modified content
 */
	public function afterRenderFile($viewFile, $content) {
		if ($this->adminCheck()) {
			return $content;
		}
		
		$matches = $this->_getMatches($content, 'ContentBlock');
		if (empty($matches)) {
			return $content;
		}
		
		foreach ($matches as $key => $val) {
			$cacheName = 'content_block_' . $val[0];
			$output = Cache::read($cacheName, 'block');

			if ($output === false) {
				$block = ClassRegistry::init('ContentBlocks.ContentBlock')->find('first', array(
					'conditions' => array('ContentBlock.id' => $val[0]),
					'published' => true
				));

				if (!$block) {
					$output = '';
				} else {
					$output = $block['ContentBlock']['content'];
				}
				Cache::write($cacheName, $output, 'block');
			}

			$content = str_replace($val[1], $output, $content);
		}
		
		return $content;
	}

}