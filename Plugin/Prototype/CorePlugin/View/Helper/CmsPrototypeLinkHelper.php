<?php
/**
 * PrototypeLinkHelper class
 *
 * Used primarily for navigation within the prototype helper, such as going to next/previous
 * items, and outputting breadcrumbs.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classPrototypeLinkHelper.html
 * @package      Cms.Plugin.Prototype.View.Helper 
 * @since        Pyramid CMS v 1.0
 */
class CmsPrototypeLinkHelper extends AppHelper {
	
/**
 * Helpers
 */
	public $helpers = array(
		'Html' => array('className' => 'AppHtml'),
		'Linking.ModelLink'
	);
	
/**
 * Neighbours of prototype items
 */
	public $neighbors = array();

/**
 * Returns an unordered list of breadcrumbs from the view's $breadcrumbs array, which is set
 * automatically by the PrototypeBreadcrumbs component.
 *
 * @param array $ulOptions Optional HTML attributes to pass to the UL
 * @param array $liOptions Optional HTML attributes to pass to each LI
 * @return string
 */
	public function breadcrumbs($ulOptions = array(), $liOptions = array()) {
		if (!isset($this->_View->viewVars['breadcrumbs']) || empty($this->_View->viewVars['breadcrumbs'])) {
			return null;
		}

		$items = array();
		$end = end($this->_View->viewVars['breadcrumbs']);
		reset($this->_View->viewVars['breadcrumbs']);
		foreach ($this->_View->viewVars['breadcrumbs'] as $key => $val) {
			$contents = $val == $end ? $key : $this->Html->link($key, $val);

			$items[] = $this->Html->tag(
				'li',
				$contents,
				$liOptions
			);
		}

		return $this->Html->tag(
			'ul',
			implode('', $items),
			$ulOptions
		);
	}

/**
 * Links to the next item as determined by the order of the items.
 *
 * @param   integer $id
 * @return  array
 */
	public function linkNext($id) {
		$item = $this->next($id);
		if (!$item) {
			return null;
		}
		
		return $this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id']);
	}

/**
 * Links to the previous item as determined by the order of the items.
 *
 * @param   integer $id
 * @return  array
 */
	public function linkPrev($id) {
		$item = $this->prev($id);
		if (!$item) {
			return null;
		}
		
		return $this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id']);
	}

/**
 * Returns the next prototype item in the order.
 *
 * @param   integer $id
 * @return  array
 */
	public function next($id) {
		return $this->_neighbor($id, 'next');
	}

/**
 * Returns the previous prototype item in the order.
 *
 * @param   integer $id
 * @return  array
 */
	public function prev($id) {
		return $this->_neighbor($id, 'prev');
	}

/**
 * Finds the neighbour of $id as indiciated by position $position
 *
 * @param   integer $id
 * @param   string  $position - either 'next' or 'prev'
 * @return  array
 */
	protected function _neighbor($id, $position) {
		if (!$this->_findNeighbors($id)) {
			return null;
		}
		
		if (!isset($this->neighbors[$id]) || !isset($this->neighbors[$id][$position])) {
			return null;
		}
		
		return $this->neighbors[$id][$position];
	}

/**
 * Finds and stores neighbour items of prototype item $id.
 *
 * @param   integer $id
 * @return  boolean
 */
	protected function _findNeighbors($id) {
	// We arleady cached?
		if (isset($this->neighbors[$id])) {
			return true;
		}
	// init
		$Item = ClassRegistry::init('Prototype.PrototypeItem');
	// Check it to see if this is the item being viewed.
		$rootItem = $Item->findById($id);
	// Well, is it?  If it is then it cannot be a neighbout.  Next!
		if (!$rootItem) {
			return false;
		}
	// We need the PrototypeInstance details if they're not already set.
		if (!isset($this->_View->viewVars['_instance'])) {
			$instance = $Item->PrototypeInstance->findById($rootItem['PrototypeItem']['prototype_instance_id']);
		} else {
			$instance = $this->_View->viewVars['_instance'];
		}
	// Create the appropriate conditions.
		$conditions = array(
			'PrototypeItem.prototype_instance_id' => $rootItem['PrototypeItem']['prototype_instance_id']
		);
	// Need the PrototypeCategory details
		if (isset($rootItem['PrototypeItem']['prototype_category_id']) && !empty($rootItem['PrototypeItem']['prototype_category_id'])) {
			$conditions['PrototypeItem.prototype_category_id'] = $rootItem['PrototypeItem']['prototype_category_id'];
		}
	// The PrototypeInstance order preference.
		$theTableAndField = explode('.', $instance['PrototypeInstance']['item_order']);
	// Trim.
		$theTable = trim($theTableAndField[0]);
	// The field.
		$theField = trim(strstr($theTableAndField[1], ' ', true));
	// The direction.
		$theDirec = trim(strstr($theTableAndField[1], ' '));
	// Alrighty then...  Who's your neighbours?
		$neighbors = $Item->find(
				'neighbors'
				, array(
					'field'		=> $theTable . '.' . $theField
					, 'value'	=> $rootItem[$theTable][$theField]
					, 'conditions'	=> $conditions
					, 'published'	=> true
					, 'sort'	=> false
					, 'cache'	=> 'neighbors_item_' . $id
					,
				)
		);
	// This became necessary because of the inability to add an order the $neighbours find.
		if( $theDirec == 'DESC' ) {
			$this->neighbors[$id]['next'] = $neighbors['prev'];
			$this->neighbors[$id]['prev'] = $neighbors['next'];
			$orderFirst = $theTable . '.' . $theField . ' DESC';
			$orderLast = $theTable . '.' . $theField;
		} else {
			$this->neighbors[$id]['prev'] = $neighbors['prev'];
			$this->neighbors[$id]['next'] = $neighbors['next'];
			$orderFirst = $theTable . '.' . $theField;
			$orderLast = $theTable . '.' . $theField . ' DESC';
		}
	// The final set up.
		if(!$this->neighbors[$id]['next'] || !$this->neighbors[$id]['prev']) {
		// First
			if(!$this->neighbors[$id]['next']) {
				$this->neighbors[$id]['next']	= $Item->find(
								'first'
								, array(
									'order'		=> $orderFirst
									, 'limit'	=> 1
									,
								)
				);
			}
		// Last
			if(!$this->neighbors[$id]['prev']) {
				$this->neighbors[$id]['prev']	= $Item->find(
								'first'
								, array(
									'order'		=> $orderLast
									, 'limit'	=> 1
									,
								)
				);
			}
		}
	// All done!
		return true;
	}

}
