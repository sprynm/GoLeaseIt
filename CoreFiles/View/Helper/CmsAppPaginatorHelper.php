<?php
App::uses('PaginatorHelper', 'View/Helper');

/**
 * CMS core-level Paginator helper
 *
 * Extends the Cake core PaginatorHelper.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAppPaginatorHelper.Paginator
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 2.0
 */
class CmsAppPaginatorHelper extends PaginatorHelper {

/**
 * Returns the meta-links for a paginated result set.
 *
 * `echo $this->Paginator->meta();`
 *
 * Echos the links directly, will output nothing if there is neither a previous nor next page.
 *
 * `$this->Paginator->meta(array('block' => true));`
 *
 * Will append the output of the meta function to the named block - if true is passed the "meta"
 * block is used.
 *
 * ### Options:
 *
 * - `model` The model to use defaults to PaginatorHelper::defaultModel()
 * - `block` The block name to append the output to, or false/absent to return as a string
 *
 * @param array $options Array of options.
 * @return string|null Meta links.
 */
	public function meta($options = array()) {
		$params = Router::getRequest()->params;
		$model = isset($options['model']) ? $options['model'] : null;
		$params = $this->params($model);
		$urlOptions = isset($this->options['url']) ? $this->options['url'] : array();
		$links = array();
		
		if ($this->hasPrev()) {
			$links[] = $this->Html->meta(array(
				'rel' => 'prev',
				'link' => $this->url(am($urlOptions, array('page' => $params['page'] - 1)), true)
			));
		}
		
		if ($this->hasNext()) {
			$links[] = $this->Html->meta(array(
				'rel' => 'next',
				'link' => $this->url(am($urlOptions, array('page' => $params['page'] + 1)), true)
			));
		}
		
		$out = implode($links);
		
		if (empty($options['block'])) {
			return $out;
		}
		
		if ($options['block'] === true) {
			$options['block'] = __FUNCTION__;
		}
		
		$this->_View->append($options['block'], $out);
	}
	
/**
 * Overrides PaginatorHelper::url to make the default url load from the parsed url then clear out named parameters since they will already get merged in with $options
 *
 * Merges passed URL options with current pagination state to generate a pagination URL.
 * 
 * @param array $options Pagination/URL options array
 * @param boolean $asArray Return the url as an array, or a URI string
 * @param string $model Which model to paginate on
 * @return mixed By default, returns a full pagination URL string for use in non-standard contexts (i.e. JavaScript)
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/paginator.html#PaginatorHelper::url
 */
	public function url($options = array(), $asArray = false, $model = null) {
		$paging = $this->params($model);
		$url = am( Router::parse(Router::url()), $options );
		
		//strip named parameters from the url
		if (isset($url['named'])){
			unset($url['named']);
		}
		
		$url = array_merge(array_filter($paging['options']), $url);

		if (isset($url['order'])) {
			$sort = $direction = null;
			if (is_array($url['order'])) {
				list($sort, $direction) = array($this->sortKey($model, $url), current($url['order']));
			}
			unset($url['order']);
			$url = array_merge($url, compact('sort', 'direction'));
		}
		$url = $this->_convertUrlKeys($url, $paging['paramType']);

		if ($asArray) {
			return $url;
		}
		
		//call the url method from the PaginatorHelper's parent class: AppHelper
		return AppHelper::url($url);
	}
}