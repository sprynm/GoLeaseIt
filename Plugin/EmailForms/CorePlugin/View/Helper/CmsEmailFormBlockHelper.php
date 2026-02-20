<?php
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
class CmsEmailFormBlockHelper extends BlockHelper {

/**
 * Searches $content for email forms placeholders and replaces with a email form view element.
 *
 * @see Helper::afterRenderFile
 * @throws CakeException
 * @return string the modified content
 */
	public function afterRenderFile($viewFile, $content) {
		if ($this->adminCheck()) {
			return $content;
		}

		$matches = $this->_getMatches($content, 'EmailForm');
		if (empty($matches)) {
			return $content;
		}

		foreach ($matches as $key => $val) {
			$cacheName = 'email_form_' . $val[0];

			$form = Cache::read($cacheName, 'block');
 
			if ($form === false) {
				$form = ClassRegistry::init('EmailForms.EmailForm')->findForDisplay($val[0]);
				Cache::write($cacheName, $form, 'block');
			}
			
			if (!$form) {
				$content = str_replace($val[1], '', $content);
				continue;
			}

			$element = $this->_findElement($form);
			if (!$element) {
				throw new CakeException('CmsEmailFormBlockHelper: element file not found for form "' . $form['EmailForm']['id'] . '"');
			}

			$output = $this->_View->element($element, array('emailForm' => $form));
			

			$content = str_replace($val[1], $output, $content);
		}

		return $content;
	}

/**
 * Searches for an appropriate email form view element in this order:
 * 
 * - APP/Plugin/EmailForms/View/Elements/<id>.ctp
 * - APP/Plugin/EmailForms/View/Elements/email_form.ctp
 * - CMS/Plugin/EmailForms/View/Elements/email_form.ctp
 *
 * @param array the form
 * @return string the element path
 */
	protected function _findElement($emailForm) {
		$paths = array(
			APP . 'Plugin' . DS . 'EmailForms' . DS . 'View' . DS . 'Elements' . DS . $emailForm['EmailForm']['id'] . '.ctp',
			APP . 'Plugin' . DS . 'EmailForms' . DS . 'View' . DS . 'Elements' . DS . 'email_form.ctp',
			CMS . 'Plugin' . DS . 'EmailForms' . DS . 'View' . DS . 'Elements' . DS . 'email_form.ctp'
		);

		foreach ($paths as $path) {
			if (file_exists($path)) {
				return 'EmailForms.' . basename($path, '.ctp');
			}
		}

		return null;
	}
	
}