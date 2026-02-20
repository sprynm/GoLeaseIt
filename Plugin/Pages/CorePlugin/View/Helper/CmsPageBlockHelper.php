<?php // CmsPageBlockHelper.php
//
App::uses('BlockHelper', 'View/Helper');
/**
 * CmsEmailFormBlockHelper class
 *
 * Replaces content block placeholders in view content with dynamic content.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsEmailFormBlockHelper.html
 * @package		 Cms.Plugin.EmailForms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsPageBlockHelper extends BlockHelper {

/**
 * Searches $content for page link placeholders and replaces with an URL for the href.
 *
 * @see Helper::afterRenderFile
 * @throws CakeException
 * @return string the modified content
 */
	public function afterRenderFile($viewFile, $content) {
		//
		if ($this->adminCheck()) {
			//
			return	$content;
		}
		// 
		$matches	= $this->getMatches($content, '/dynamic_page_link_id/', '"');
		//
		if (empty($matches)) {
			//
			return	$content;
		}
		//
		$pageModel	= ClassRegistry::init('Pages.Page');
		//
		foreach ($matches AS $key => $val) {
			//
			$cacheName	= 'page_link_' . $val;
			//
			$form		= Cache::read($cacheName, 'block');
 			//
			if ($form === false) {
				//
				$form	= Router::url($pageModel->pageLink($val), true);
				//
				Cache::write($cacheName, $form, 'block');
			}
			//
			if (
				!$form
				||
				!$pageModel->parentsArePublished($val)
				||
				!$pageModel->isPublished($val)
			) {
				//
				$content	= str_replace(
							array(
								$val . '">'
								, $val . '" target="_blank">'
								,
							)
							, array(
								''
								, ''
								,
							)
							, $content
						);
				//
				continue;
			}
			// 
			$content	= str_replace(
						array(
							'/dynamic_page_link_id/'
							, $val . '">'
							, $val . '" target="_blank">'
							,
						)
						, array(
							''
							, $form . '">'
							, $form . '" target="_blank">'
							,
						)
						, $content
					);
		}
		//
		return $content;
	}

/**
 * 
 *
 * $content, '/dynamic_page_link_id/', '"'
 * 
 * 
 */
	private function getMatches($str, $startDelimiter, $endDelimiter) {
		//
		$contents		= array();
		//
		$startDelimiterLength	= strlen($startDelimiter);
		//
		$endDelimiterLength	= strlen($endDelimiter);
		//
		$startFrom		= $contentStart = $contentEnd = 0;
		//
		while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
			//
			$contentStart	+= $startDelimiterLength;
			//
			$contentEnd	= strpos($str, $endDelimiter, $contentStart);
			//
			if (false === $contentEnd) {
				//
				break;
			}
			//
			$contents[]	= substr($str, $contentStart, $contentEnd - $contentStart);
			//
			$startFrom	= $contentEnd + $endDelimiterLength;
		}
		//
		return $contents;
	}

}