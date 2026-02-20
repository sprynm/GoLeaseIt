<?php
App::uses('PaginatorComponent', 'Controller/Component');
/**
 * CmsAppPaginatorComponent class
 *
 * An extension of the Cake Paginator component for better whitelist handling in validateSort().
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAppPaginatorComponent.html
 * @package		 Cms.Plugin.Controller.Component
 * @since		 Pyramid CMS v 1.0
 */
class CmsAppPaginatorComponent extends PaginatorComponent {

/**
 * Custom validateSort() method to ignore field validation if a whitelist has been
 * supplied.
 *
 * @see PaginatorComponent::validateSort()
 */
	public function validateSort(Model $object, array $options, array $whitelist = array()) {
		if (isset($options['sort'])) {
			$direction = null;
			if (isset($options['direction'])) {
				$direction = strtolower($options['direction']);
			}
			if (!in_array($direction, array('asc', 'desc'))) {
				$direction = 'asc';
			}
			$options['order'] = array($options['sort'] => $direction);
		}

		// This chunk is different: in the Cake core version, $options is only returned here
		// if the whitelist field isn ot present in the order. But we want the options to be
		// returned if there's a whitelist, period.
		if (!empty($whitelist) && isset($options['order']) && is_array($options['order'])) {
			$field = key($options['order']);
			if (!in_array($field, $whitelist)) {
				$options['order'] = null;
			}
			return $options;
		}

		if (!empty($options['order']) && is_array($options['order'])) {
			$order = array();
			foreach ($options['order'] as $key => $value) {
				$field = $key;
				$alias = $object->alias;
				if (strpos($key, '.') !== false) {
					list($alias, $field) = explode('.', $key);
				}
				$correctAlias = ($object->alias == $alias);
				if ($correctAlias && $object->hasField($field)) {
					$order[$object->alias . '.' . $field] = $value;
				} elseif ($correctAlias && $object->hasField($key, true)) {
					$order[$field] = $value;
				} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field, true)) {
					$order[$alias . '.' . $field] = $value;
				}
			}
			$options['order'] = $order;
		}

		return $options;
	}

}