<?php
App::uses('CakeSchema', 'Model');

/**
 * CmsSchema model file
 *
 * Extension of CakeSchema that adds some extra functionality and cuts down
 * on duplicated code. 
 *
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsModel.html
 * @package      Cms.Model  
 * @since        Pyramid CMS v 1.0
 */
class CmsSchema extends CakeSchema {
		
/**
 * Builds schema object properties
 *
 * @param array $data loaded object properties
 * @return void
 */
	public function build($data) {
		$file = null;
		foreach ($data as $key => $val) {
			if (!empty($val)) {
				if (!in_array($key, array('plugin', 'name', 'path', 'file', 'connection', 'tables', '_log'))) {
					if ($key[0] === '_') {
						continue;
					}
					$this->tables[$key] = $val;
					unset($this->{$key});
				} elseif ($key !== 'tables') {
					if ($key === 'name' && $val !== $this->name && !isset($data['file'])) {
						$file = Inflector::underscore($val) . '.php';
					}
					$this->{$key} = $val;
				}
			}
		}

		if (file_exists($this->path . DS . $file) && is_file($this->path . DS . $file)) {
			$this->file = $file;
		} elseif (!empty($this->plugin)) {
			$this->path = CakePlugin::path($this->plugin) . 'Config' . DS . 'Schema';
			if (!file_exists($this->path . DS . $file)) {
				$this->path = CMS . 'Plugin' . DS . $this->plugin . DS . 'Config' . DS . 'Schema';
			}
		}
	}

/**
 * Receives all requests to insert, drop, or alter schemas and sends the requests to the
 * proper functions.
 *
 * @param object
 * @param type Either "insert", "drop", or "alter"
 * @return boolean
 */
	public function importSchema($db, $type = 'insert') {
		switch ($type) {
			case 'insert':
				return $this->_insert($db);
			break;
			
			case 'update':
				return $this->_update($db);
			break;
			
			case 'drop':
				return $this->_drop($db);
			break;
		}
		
		return false;
	}

/**
 * Compares schema file with current database and returns true if there are no differences
 * between the two.
 */
	public function dbChanges() {
		$old = $this->read(array('models' => false));
		$compare = $this->compare($old);
		return $compare;
	}

/**
 * Alters schema based on schema file.
 *
 * @return boolean
 */
	protected function _update($db) {
		$compare = $this->dbChanges();

		foreach ($compare as $table => $changes) {
			if (isset($changes['drop'])) {
				unset($compare[$table]['drop']);
				if (empty($compare[$table])) {
					unset($compare[$table]);
				}
			}
		}

		// Schema is up to date
		if (empty($compare)) {
			return true;
		}
		
		$contents = array();
		
		// Get the existing tables - if the table to be changed isn't in the list then it's a CREATE, not ALTER, statement.
		$sources = $db->listSources();
		foreach ($compare as $table => $changes) {
			if (in_array($table, $sources)) {
				$contents[$table] = $db->alterSchema(array($table => $changes), $table);
			} else {
				$contents[$table] = $db->createSchema($this, $table);
			}
		}

		foreach ($contents as $table => $query) {
			$event = 'update';
			
			if (!$this->before(array($event => $table))) {
				return false;
			}

			try {
				$db->execute($query);
			} catch (PDOException $e) {
				$this->lastError = __("Could not update table '" . $table . '": %s', $e->getMessage());
				$this->after(array($event => $table, 'errors' => $this->lastError));
				continue;
			}

			$this->after(array($event => $table, 'errors' => null));
		}

		return true;
	}

/**
 * Drops all tables in the schema file.
 *
 * @return boolean
 */
	protected function _drop($db) {
		$existing = $db->listSources();

		foreach ($this->tables as $table => $fields) {
			$event = 'drop';

			if (!$this->before(array($event => $table))) {
				return false;
			}

			if (!in_array($table, $existing)) {
				continue;
			}

			$drop = $db->dropSchema($this, $table);

			try {
				$db->execute($drop);
			} catch (PDOException $e) {
				$this->lastError = __("Could not drop table '" . $table . '": %s', $e->getMessage());
				$this->after(array($event => $table, 'errors' => $this->lastError));
				return false;
			}
		}
	}
	
/**
 * Imports the schema file specified during object construction. Will skip tables if they exist.
 *
 * @return boolean
 */
	protected function _insert($db) {
		$existing = $db->listSources();
		
		foreach ($this->tables as $table => $fields) {
			$event = 'create';
			
			if (!$this->before(array($event => $table))) {
				return false;
			}
			
			if (in_array($table, $existing)) {
				continue;
			}
			
			$create = $db->createSchema($this, $table);
			
			try {
				$db->execute($create);
			} catch (PDOException $e) {
				$this->lastError = __("Could not create table '" . $table . '": %s', $e->getMessage());
				$this->after(array($event => $table, 'errors' => $this->lastError));
				return false;
			}
			
			$this->after(array($event => $table, 'errors' => null));
		}
		
		return true;
	}

/**
 * Inserts data into a table using the base Model class, to avoid problems with the model
 * cache and dependencies.
 *
 * @param string
 * @param array
 * @return boolean
 */
	public function insertData($table, $data) {
		Cache::clear();
		clearCache();
		App::import('Model', 'Model', false);
		$db = ConnectionManager::getDataSource('default');
		$db->cacheSources = false;
		$modelObject = new Model(array(
			'name' => Inflector::classify($table),
			'table' => $table,
			'ds' => 'default'
		));
		$modelObject->cacheSources = false;
		return $modelObject->saveAll($data);
	}

}