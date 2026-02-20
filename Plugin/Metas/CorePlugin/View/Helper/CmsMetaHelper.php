<?php
/**
 * CmsMetaHelper class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsMetaHelper.html
 * @package		 Cms.Plugin.Metas.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsMetaHelper extends AppHelper {

/**
 * Helpers
 */
	public $helpers = array(
		'Html' => array('className' => 'appHtml')
	);

/**
 * MetaKey model object
 */
	public $MetaKey = null;

/**
 * Constructor
 *
 * @see Helper::__construct
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		
		App::uses('MetaKey', 'Metas.Model');
		$this->MetaKey = new MetaKey();
	}
	
/**
 * Returns all MetaKey records.
 *
 * @return array
 */
	public function keys() {
		$metas = $this->MetaKey->find('all');
		$metas = Hash::combine($metas, '{n}.MetaKey.id', '{n}');
		return $metas;
	}

/**
 * Metas are added to the meta content block automatically before the view is rendered, but only
 * if that setting is enabled. 
 *
 * @param string viewFile
 * @return void
 */
	public function beforeRender($viewFile) {
		if (!Configure::read('Settings.Metas.automatic_output')) {
			return;
		}
		
		return $this->showAll(array('inline' => false));
	}

/**
 * Outputs a meta tag with name $key. If $val is set then that's used as the value; otherwise, the
 * metas view array is used.
 *
 * @param string key
 * @param string val
 * @param array options to pass to HtmlHelper::meta
 */
	public function show($key, $val = null, $options = array()) {
	//
		if (!$val && isset($this->_View->viewVars['metas'][$key]))
		{
		//
			$val = $this->_View->viewVars['metas'][$key];
		}
	//
		if (!$val || $key == 'author')
		{
		//
			return null;
		}
	//
		return $this->Html->meta(array((substr($key, 0, 3) == 'og:' ? 'property': 'name') => $key, 'content' => $val), null, $options);
	}

/**
 * Outputs all meta tags found in the $metas view variable. Basically a wrapper for show().
 *
 * @param array Options to pass along to HtmlHelper::meta
 * @return string
 */
	public function showAll($options = array()) {
		if (!isset($this->_View->viewVars['metas']) || empty($this->_View->viewVars['metas'])) {
			return null;
		}
		
		$output = '';
		foreach ($this->_View->viewVars['metas'] as $key => $val) {
			$output .= $this->show($key, $val, $options);
		}
		
		return $output;
	}
}