<?php
/**
 * CmsPrototypeBreadcrumbsComponent class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsPrototypeBreadcrumbsComponent.html
 * @package		 Cms.Plugin.Prototype.Controller.Component
 * @since		 Pyramid CMS v 1.0
 */
class CmsPrototypeBreadcrumbsComponent extends Component {

/**
 * Initializes and sets breadcrumbs as needed. Attempts to detect whether the current request
 * is an instance summary, a category view, or an item view, based on the variables set in the
 * view. The general breakdown is this:
 *
 * - If $category is set: category view
 * - If $item is set: item view
 * - If neither $category nor $item are set: instance summary
 *
 * For category and item views, if the category is a child category (i.e not top level), then its
 * path is fetched.
 *
 * @param object $Controller
 * @return void
 */
	public function beforeRender(Controller $Controller) {
		if ($Controller->Admin->isAdminAction()) {
			return;
		}

		$this->Instance = ClassRegistry::init('Prototype.PrototypeInstance');

		$breadcrumbs = $this->_item($Controller->viewVars['instance'], 'PrototypeInstance');

		// Category view
		if (isset($Controller->viewVars['category'])) {
			$breadcrumbs = array_merge(
				$breadcrumbs, 
				$this->_categoryPath($Controller->viewVars['category'])
			);
		} else if (isset($Controller->viewVars['item']) && $Controller->viewVars['_instance']['PrototypeInstance']['use_categories']) {
			// Item view with categories
			$breadcrumbs = array_merge(
				$breadcrumbs, 
				$this->_categoryPath($Controller->viewVars['item']['PrototypeCategory'][0]),
				$this->_item($Controller->viewVars['item'], 'PrototypeItem')
			);
		} else if (isset($Controller->viewVars['item'])) {
			// Item view without
			$breadcrumbs = array_merge(
				$breadcrumbs,
				$this->_item($Controller->viewVars['item'], 'PrototypeItem')
			);
		}

		$Controller->set('breadcrumbs', $breadcrumbs);
	}

/** 
 * Returns name => link array for $category and all of its possible parents.
 *
 * @param mixed $category either a category array or an ID
 * @return array
 */
	protected function _categoryPath($category) {
		if (is_numeric($category)) {
			$category = ClassRegistry::init('Prototype.PrototypeCategory')->findById($category);
		}

		if (!isset($category['PrototypeCategory'])) {
			$category = array('PrototypeCategory' => $category);
		}

		$breadcrumbs = array();

		// Only get the path if the category has a parent. getPath can be used even on root items,
		// in which case only that item would be returned, but that would result in an unnecessary
		// query in this case.
		if ($category['PrototypeCategory']['parent_id']) {
			$path = ClassRegistry::init('Prototype.PrototypeCategory')->getPath(
				$category['PrototypeCategory']['id'],
				array('PrototypeCategory.id', 'PrototypeCategory.name')
			);
			foreach ($path as $cat) {
				$breadcrumbs = array_merge($breadcrumbs, $this->_item($cat, 'PrototypeCategory'));
			}
		} else {
			$breadcrumbs = array_merge($breadcrumbs, $this->_item($category, 'PrototypeCategory'));
		}

		return $breadcrumbs;
	}

/**
 * Returns a breadcrumb name => link array element for $data which should belong to model $model.
 *
 * @param array $data Array of data with model key
 * @param array $model Model name, e.g. PrototypeInstance
 * @return array
 */
	protected function _item($data, $model) {
		return array(
			$data[$model]['name'] => ClassRegistry::init('Prototype.' . $model)->link($data[$model]['id'])
		);
	}

}