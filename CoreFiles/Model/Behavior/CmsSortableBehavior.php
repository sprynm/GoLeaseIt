<?php
/**
 * Basics:
 * - Automatically add an order before a find operation
 * - Update the order of a new record after a save
 * - Correct the positions of all elements in a sequence upon deletion
 *
 * In a Model::find() operation, pass "sort" => false to disable automatic ordering.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsSortableBehavior.html
 * @package		 Cms.Model.Behavior
 * @since		 Pyramid CMS v 1.0
 */
class CmsSortableBehavior extends ModelBehavior {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 *
 * field - field used to record the order
 * group - optional field to group records by, for multiple sequences in one table
 * newRecords - whether to place new records at the "end"/"bottom" or "beginning"/"top" of a sequence
 */
	protected $_defaults = array(
		'field' => 'rank', 
		'group' => null, 
		'newRecords' => 'end', 
		'start' => 0
	);
	
/**
 * Deleted record(s).
 *
 * @var		array
 */
	protected $_deleted = array();

/**
 * Intializer
 *
 * @param Object $Model
 * @param array $config
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $config);
	}

/**
 * Returns the sorting field, usually rank, for use in an ORDER BY clause.
 *
 * @param	object	$Model
 * @return	string
 */
	public function sortField(Model $Model) {
		return $Model->alias . '.' . $this->settings[$Model->alias]['field'];
	}

/**
 * Returns the group field (if any) for use in a GROUP BY clause.
 *
 * @param	object	$Model
 * @return	string
 */
	public function groupField(Model $Model) {
		if (!$this->settings[$Model->alias]['group']) {
			return null;
		}
		
		return $Model->alias . '.' . $this->settings[$Model->alias]['group'];
	}

/**
 * Adds a sort by rank order condition, but only if:
 * - 'sort' is not set to false
 * - order isn't already set as a string
 *     - if order is an array, rank will be appended.
 *
 * @param	object	$Model
 * @param	array	$queryData
 * @return	mixed
 */
	public function beforeFind(Model $Model, $queryData) {
		if (isset($queryData['sort']) && $queryData['sort'] === false) {
			return $queryData;
		}

		if (!isset($queryData['order']) || empty($queryData['order'])) {
			$queryData['order'] = $this->sortField($Model);
		} else if (isset($queryData['order'])) {
			if (is_string($queryData['order'])) {
				return $queryData;
			}

			if (is_array($queryData['order']) && !in_array($this->settings[$Model->alias]['field'], $queryData['order']) 
				&& !in_array($this->sortField($Model), $queryData['order'])) {
				$queryData['order'] = array_merge($queryData['order'], array($this->sortField($Model)));
			}
		}

		return $queryData;
	}

/**
 * If the item being saved is new, it gets positioned in the tree.
 *
 * @param	object	$Model
 * @param	boolean $created
 * @return	boolean
 */
	public function afterSave(Model $Model, $created) {
		if (!$created) {
			return true;
		}
		
		return $this->_positionNewElement($Model);
	}

/**
 * Stores the information of a record about to be deleted for use in afterDelete
 *
 * @param	object	$Model
 * @param	boolean $cascade
 * @return	boolean
 */
	public function beforeDelete(Model $Model, $cascade = true) {
		$this->_deleted[$Model->alias] = $Model->findById($Model->id);
		return true;
	}

/**
 * Moves record $id to the bottom of the list.
 *
 * @param	object	$Model
 * @param	integer $id
 * @param	array	$list		OPTIONAL
 * @param	boolean $existing	OPTIONAL
 * @return	boolean
 */
	public function toBottom(Model $Model, $id, $list = null, $existing = false) {
		extract($this->settings[$Model->alias]);
		
		if (($record = $Model->findById($id)) == null) {
			return false;
		}
		
		if (!$list) {
			$list = $this->_getSequenceList($Model, $record, $existing);
		}

		// Record isn't in the list - easy. Just add it to the end and save.
		if (!$existing) {
			$position = end($list) + 1;
			$Model->id = $record[$Model->alias][$Model->primaryKey];
			return $Model->saveField($field, $position, array(
				'validate' => false, 
				'callbacks' => false
			));
		}
		
		// Record IS in the list, so we need to move it to the bottom, re-order the list, and save the sequence.
		$currentPosition = $list[$id];
		$newPosition = null;
		$saveData = array(
			$Model->alias => array(
			)
		);
		foreach ($list as $key => $val) {
			if ($val <= $currentPosition) {
				continue;
			}
			
			$saveData[$Model->alias][] = array(
				$Model->primaryKey => $key, 
				$field => ($val - 1)
			);
			
			$newPosition = $val;
		}
		
		$saveData[$Model->alias][] = array(
			$Model->alias . '.' . $Model->primaryKey => $id, 
			$this->sortField($Model) => $val
		);
		
		return $Model->saveAll($saveData, array(
			'validate' => false, 
			'callbacks' => false
		));
	}

/**
 * Moves record $id to the top of the list.
 *
 * @param	object	$Model
 * @param	integer $id
 * @param	array	$list		OPTIONAL
 * @param	boolean $existing	OPTIONAL
 * @return	boolean
 */
	public function toTop(Model $Model, $id, $list = null, $existing = false) {
		extract($this->settings[$Model->alias]);
		
		if (($record = $Model->findById($id)) == null) {
			return false;
		}
		
		if (!$list) {
			$list = $this->_getSequenceList($Model, $record, $existing);
		}
		
		/*
		 * The list's keys correspond to the model's primary keys and the values
		 * to the order field. Since we're saving the entire sequence, we don't
		 * need the order field values, so we convert the list to an array of
		 * primary key values. If the record exists in the list, we'll unset it 
		 * first so we can easily add it to the beginning.
		 */
		if ($existing && array_key_exists($id, $list)) {
			unset($list[$id]);
		}
		$list = array_merge(array(
			$id
		), array_keys($list));
		
		$saveData = array(
			$Model->alias => array(
			)
		);
		$position = $start;
		foreach ($list as $val) {
			$Model->id = $val;
			$Model->saveField($field, $position, array(
				'validate' => false, 
				'callbacks' => false
			));
			
			$position++;
		}
		
		return true;
	}

/**
 * Get a list of records in order. If $record is numeric then
 * we'll first get the matching record so we know what to use
 * for the group condition. If $record in as an array then we
 * assume we already have the relevant data.
 *
 * @param	object	$Model 
 * @param	mixed	$record			OPTIONAL
 * @param	boolean $excludeTarget	OPTIONAL
 */
	protected function _getSequenceList(Model $Model, $record = null, $includeTarget = true) {
		if (is_numeric($record)) {
			$record = $Model->findById($record);
		}
		
		if (!$record) {
			return null;
		}
		
		if ($includeTarget) {
			$conditions = array(
			);
		} else {
			$conditions = array(
				$Model->alias . '.' . $Model->primaryKey . ' !=' => $record[$Model->alias][$Model->primaryKey]
			);
		}
		
		$groupField = $this->groupField($Model);
		if ($groupField && isset($record[$Model->alias][$groupField])) {
			$conditions[$groupField] = $record[$Model->alias][$groupField];
		}
		
		// Find and return a list of items: primary key => order
		$sequence = $Model->find('list', 
				array(
					'conditions' => $conditions, 
					'fields' => array(
						$Model->alias . '.' . $Model->primaryKey, 
						$this->sortField($Model)
					), 
					'order' => $this->sortField($Model), 
					'callbacks' => false
				));
		
		return $sequence;
	}

/**
 * Called by afterSave - positions a new element at either the top or bottom, depending
 * on behavior settings.
 *
 * @param	object	$Model
 * @return	boolean
 */
	protected function _positionNewElement(Model $Model) {
		extract($this->settings[$Model->alias]);
		
		$oldData = $Model->data;
		$id = $Model->id;
		$return = false;
		if ($newRecords == 'end' || $newRecords == 'bottom') {
			$return = $this->toBottom($Model, $Model->id);
		} else {
			$return = $this->toTop($Model, $Model->id);
		}
		//update the model data with the new field value
		$newData = $Model->read($field, $id);
		$oldData[$Model->alias][$field] = $newData[$Model->alias][$field];
		$Model->data = $oldData;
		return $return;
	}

}