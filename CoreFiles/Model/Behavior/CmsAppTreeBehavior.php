<?php
App::uses('TreeBehavior', 'Model/Behavior');
/**
 * CmsAppTreeBehavior core class
 *
 * An extension of the Cake core TreeBehavior class that fixes the recover() function to
 * lalow for Pyramid's recursive = -1 setup.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsAppTreeBehavior.html
 * @package      Cms.Model.Behavior
 * @since        Pyramid CMS v 1.0
 */
class CmsAppTreeBehavior extends TreeBehavior {

/**
 * Recover a corrupted tree
 *
 * The only change here from the core is removing the recursive = 0 key from the $missingParents
 * find() and adding the VerifyParent contain.
 *
 * @see TreeBehavior::recover
 * @link http://book.cakephp.org/2.0/en/core-libraries/behaviors/tree.html#TreeBehavior::recover
 */
	public function recover(Model $Model, $mode = 'parent', $missingParentAction = null) {
		if (is_array($mode)) {
			extract(array_merge(array('mode' => 'parent'), $mode));
		}
		extract($this->settings[$Model->alias]);
		$Model->recursive = $recursive;
		if ($mode === 'parent') {
			$Model->bindModel(array('belongsTo' => array('VerifyParent' => array(
				'className' => $Model->name,
				'foreignKey' => $parent,
				'fields' => array($Model->primaryKey, $left, $right, $parent),
			))));
			$missingParents = $Model->find('list', array(
				'conditions' => array($scope, array(
					'NOT' => array($Model->escapeField($parent) => null), $Model->VerifyParent->escapeField() => null
				)),
				'contain' => array('VerifyParent')
			));
			$Model->unbindModel(array('belongsTo' => array('VerifyParent')));
			if ($missingParents) {
				if ($missingParentAction === 'return') {
					foreach ($missingParents as $id => $display) {
						$this->errors[] = 'cannot find the parent for ' . $Model->alias . ' with id ' . $id . '(' . $display . ')';
					}
					return false;
				} elseif ($missingParentAction === 'delete') {
					$Model->deleteAll(array($Model->escapeField($Model->primaryKey) => array_flip($missingParents)), false);
				} else {
					$Model->updateAll(array($Model->escapeField($parent) => $missingParentAction), array($Model->escapeField($Model->primaryKey) => array_flip($missingParents)));
				}
			}
			$count = 1;
			foreach ($Model->find('all', array('conditions' => $scope, 'fields' => array($Model->primaryKey), 'order' => $left)) as $array) {
				$lft = $count++;
				$rght = $count++;
				$Model->create(false);
				$Model->id = $array[$Model->alias][$Model->primaryKey];
				$Model->save(array($left => $lft, $right => $rght), array('callbacks' => false, 'validate' => false));
			}
			foreach ($Model->find('all', array('conditions' => $scope, 'fields' => array($Model->primaryKey, $parent), 'order' => $left)) as $array) {
				$Model->create(false);
				$Model->id = $array[$Model->alias][$Model->primaryKey];
				$this->_setParent($Model, $array[$Model->alias][$parent]);
			}
		} else {
			$db = ConnectionManager::getDataSource($Model->useDbConfig);
			foreach ($Model->find('all', array('conditions' => $scope, 'fields' => array($Model->primaryKey, $parent), 'order' => $left)) as $array) {
				$path = $this->getPath($Model, $array[$Model->alias][$Model->primaryKey]);
				$parentId = null;
				if (count($path) > 1) {
					$parentId = $path[count($path) - 2][$Model->alias][$Model->primaryKey];
				}
				$Model->updateAll(array($parent => $db->value($parentId, $parent)), array($Model->escapeField() => $array[$Model->alias][$Model->primaryKey]));
			}
		}
		return true;
	}

}