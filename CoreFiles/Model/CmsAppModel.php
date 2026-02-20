<?php
App::uses('Model', 'Model');

/**
 * CMS core-level model
 *
 * This class provides the link between the app's AppModel and the Cake core Model.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsModel.html
 * @package      Cms.Model  
 * @since        Pyramid CMS v 1.0
 */
class CmsAppModel extends Model {

/**
 * Application-wide behaviors
 */
	public $actsAs = array(
		'Containable',
		'DynamicValidation',
		'ValidateHabtm'
	);

/**
 * Default cache config
 */
	public $cacheName = 'query';

/**
 * Application-wide custom find methods
 */
	public $findMethods = array(
		'admin' => true, // For admin_index
		'edit' => true, // For admin_edit
		'random' => true
	);

/**
 * All models at -1 recursive.
 */
	public $recursive = -1;

/**
 * Whether to use query caching
 */
	public $useCache = true;

/**
 * Sorting validation array
 */
	public $validateChangeOrder = array(
		'rank' => array(
			'empty' => array(
				'rule' => 'notEmpty', 
				'message' => 'Must not be blank'
			), 
			'numeric' => array(
				'rule' => 'numeric', 
				'message' => 'Must be numeric'
			)
		)
	);

/**
 * Array of query options to pass to the _findAdmin() function, used by the admin_index
 * controller action. Query options defined here will be merged with the options passed
 * by the controller.
 */
	protected $_adminQuery = array();

/**
 * Array of query options to pass to the _findEdit() function, used by the admin_edit
 * controller action. Query options defined here will be merged with the options passed
 * by the controller.
 *
 * @var array
 */
	protected $_editQuery = array();

/**
 * Constructor
 *
 * @see Model::__construct
 */
	public function __construct($id = false, $table = null, $ds = null) {
		CmsEventManager::dispatchEvent('Model.construct', $this);
		parent::__construct($id, $table, $ds);
		
		// Move containable to the end of the behavior chain.
		if ($this->Behaviors->attached('Containable')) {
			$this->Behaviors->detach('Containable');
			$this->Behaviors->attach('Containable');
		}
	}

/**
 * Overrides Model::delete() to add support for returning if a record is "soft-deleted" via the
 * SoftDeleteBehavior in the Versioning plugin.
 *
 * @see Model::delete()
 */
	public function delete($id = null, $cascade = true) {
		$deleted = parent::delete($id, $cascade);
		return $deleted || ($this->hasField('deleted') && $this->findByIdAndDeleted($id, true));
	}

	
/**
 * Replaces all @ symbols in a string with obfuscation spans to confuse spambots. 
 * 
 * @author Shannon shannon@radarhill.com
 * @param &$text 
 */	

	public function emailize(&$text) {
		if (!is_string($text)) {
			return;
		}
		
		$explosion = explode(' ', $text);
		
		foreach($explosion as $k => $v) {
			
			if(preg_match('/(?<!mailto:)\[[\w\.]+@([\w]+\.[\w]+)+\]/', $v)) {
				$v = str_replace('[', '', $v);
				$v = str_replace(']', '', $v);
				$explosion[$k] = str_replace('@', '<span class="obfuscate" style="display:none;">obfuscation for the confusion of spambots</span>@<span class="obfuscate" style="display:none;">null</span>', $v);
				
			}
		}
		$text = implode(" ", $explosion);
		return $text;
	}
	
/**
 * Override find function to use caching
 *
 * Caching can be done either by unique names,
 * or prefixes where a hashed value of $options array is appended to the name
 * 
 * @see Croogo
 * @param mixed $type 
 * @param array $options 
 * @return mixed
 */
	public function find($type = 'first', $options = array()) {
		if ($this->useCache) {
			// Allowing for a simple cache => true option without worrying about config or name.
			if (isset($options['cache']) && !is_array($options['cache'])) {
				if ($options['cache'] === true) {
					$name = $this->alias . '_' . md5(serialize($options));
				} else {
					$name = $options['cache'];
				}
				$options['cache'] = array(
					'config' => $this->cacheName,
					'name' => $name
				);
			}
			
			if (!isset($options['cache']['config'])) {
				$options['cache']['config'] = $this->cacheName;
			}

			$cachedResults = $this->_findCached($type, $options);
			if ($cachedResults) {
				// Dispatch an event for after cached find.
				$event = CmsEventManager::dispatchEvent('Model.afterCachedFind', $this, array($cachedResults));
				if (is_array($event->result) && !empty($event->result)) {
					$cachedResults = Hash::merge($cachedResults, $event->result);
				}
				return $cachedResults;
			}
		}

		$args = func_get_args();
		$results = call_user_func_array(array('parent', 'find'), $args);
		if ($this->useCache) {
			if (isset($options['cache']['name']) && isset($options['cache']['config'])) {
				$cacheName = $options['cache']['name'];
			} else if (isset($options['cache']['prefix']) && isset($options['cache']['config'])) {
				$cacheName = $options['cache']['prefix'] . md5(serialize($options));
			}

			if (isset($cacheName)) {
				$cacheName .= '_' . Configure::read('Config.language');
				Cache::write($cacheName, $results, $options['cache']['config']);
			}
		}
		return $results;
	}

/**
 * Check if find() was already cached
 *
 * @see Croogo
 * @param mixed $type
 * @param array $options
 * @return void
 */
	protected function _findCached($type, $options) {
		if (isset($options['cache']['name']) && isset($options['cache']['config'])) {
			$cacheName = $options['cache']['name'];
		} elseif (isset($options['cache']['prefix']) && isset($options['cache']['config'])) {
			$cacheName = $options['cache']['prefix'] . md5(serialize($options));
		} else {
			return false;
		}

		$cacheName .= '_' . Configure::read('Config.language');
		$results = Cache::read($cacheName, $options['cache']['config']);
		if ($results) {
			return $results;
		}
		return false;
	}

/**
 * Attempts to find a plugin associated with $model name.
 *
 * @param string model
 * @return string
 */
	public function findModelPlugin($model) {
		$found = false;
		
		$plugins = CakePlugin::loaded();
		foreach ($plugins as $plugin) {
			$pluginModels = App::objects($plugin . '.Model');
			if (in_array($model, $pluginModels)) {
				App::uses($plugin . 'AppModel', $plugin . '.Model');
				App::uses($model, $plugin . '.Model');
				$found = $plugin;
			}
		}
		
		return $found;
	}
	
/**
 * Hands everything off to find() but specifies the 'static' cache (i.e. 999 day expiration).
 *
 * @see CmsAppModel::find
 */
	public function findStatic($type, $options = array()) {
		$options['cacheConfig'] = 'static';
		if (!isset($options['cache'])) {
			$options['cache'] = $type . '_' . md5(serialize($options));
		}

		return $this->find($type, $options);
	}

/**
 * Validation rule for one or more emails in a string
 *
 * @param	array	$check
 * @param	boolean $deep	OPTIONAL
 * @param	string	$regex	OPTIONAL
 * @return	boolean
 */
	public function multipleEmails($check, $deep = false, $regex = null) {
		$value = array_values($check);
		$emails = explode(',', $value[0]);

		if (is_null($regex)) {
			$regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9][-a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,4}|museum|travel)$/i';
		}

		foreach ($emails as $email) {
			if (!preg_match($regex, $email)) {
				return false;
			}

			if ($deep) {
				if (!preg_match('/@(' . Validation::getInstance()->__pattern['hostname'] . ')$/i', $email, $regs)) {
					return false;
				}

				if (function_exists('getmxrr')) {
					return getmxrr($regs[1], $mxhosts);
				}
				if (function_exists('checkdnsrr')) {
					return checkdnsrr($regs[1], 'MX');
				}
				return is_array(gethostbynamel($regs[1]));
			}
		}

		return true;
	}

/**
 * Returns true if the string contains no forward slashes
 *
 * @param array $check
 * @return boolean
 */
	public function noSlashes($check) {
		$value = array_shift($check);
		return !strstr($value, '/');
	}

/**
 * Takes a method string from the 'method' field and parses it into a plugin, library path,
 * class, and method, following this convention:
 *
 * Plugin.Path.To.Class.ClassName.methodName
 *
 * If the class is found, the method is executed and the results are returned.
 *
 * @param string $method Method string
 * @return mixed Method results on success, false on failure
 */
	public function optionsFromMethod($method) {
		$method = explode('.', $method);
		$plugin = array_shift($method);
		$methodName = array_pop($method);
		$class = array_pop($method);
		$path = implode('/', $method);

		App::uses($plugin . 'AppModel', $plugin . '.Model');
		App::uses($class, $plugin . '.' . $path);
		if (!class_exists($class)) {
			return false;
		}

		if (!method_exists($class, $methodName)) {
			return false;
		}

		// Test if the method is static - if so, execute it as such. If not, instantiate
		// the object and execute it normally.
		$method = new ReflectionMethod($class . '::' . $methodName);
		if ($method->isStatic()) {
			$results = $method->invoke(null);
		} else {
			$Instance = new $class();
			$results = $Instance->{$methodName}();
		}

		return $results;
	}

/**
 * Given a dynamic option string such as Users.Group, returns an array of list values
 * corresponding to the correct model (e.g. Group from the Users plugin).
 *
 * Supports possible find conditions
 * 
 * @link https://radarhill.lighthouseapp.com/projects/44179/site-and-plugin-settings
 * @param string
 * @throws CakeException
 * @return array
 */
	public function parseDynamicOptions($string) {
		if (strstr($string, '.')) {
			$class = explode('.', $string);
			$library = $class[0] . '.Model';
			$loadName = $class[0] . '.' . $class[1];
			$class = $class[1];
		} else {
			$class = $string;
			$library = 'Model';
			$loadName = $class;
		}

		// Look for possible find conditions
		$conditions = array();
		if (preg_match_all('/\[[a-zA-Z0-9]+[!=\>\<]+[a-zA-Z0-9"\']+\]/', $class, $matches)) {
			$class = str_replace(implode('', $matches[0]), '', $class);
			$conditions = $matches[0];
			foreach ($conditions as $key => $val) {
				$conditions[$key] = str_replace(array('[', ']'), '', $val);
			}
		}

		if (!in_array($class, App::objects($library))) {
			throw new CakeException("Invalid class " . $class . " passed to CmsAppModel::parseDynamicOptions().");
		}

		return ClassRegistry::init($loadName)->find('list', array('conditions' => $conditions));
	}

/**
 * Postal code validation that validates multiple countries.
 *
 * @param	array	$check
 * @param	string	$regex		OPTIONAL
 * @param	array	$country	OPTIONAL
 * @return	boolean
 */
	public function postal($check, $regex = null, $country = null) {
		// List of regular expressions to use, if a custom one isn't specified.
		$countryRegs = array(
			'uk' => '/\\A\\b[A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2}\\b\\z/i', 
			'ca' => '/\\A\\b[ABCEGHJKLMNPRSTVXY][0-9][A-Z][ ]?[0-9][A-Z][0-9]\\b\\z/i', 
			'it' => '/^[0-9]{5}$/i', 
			'de' => '/^[0-9]{5}$/i', 
			'be' => '/^[1-9]{1}[0-9]{3}$/i', 
			'us' => '/\\A\\b[0-9]{5}(?:-[0-9]{4})?\\b\\z/i', 
			'default' => '/\\A\\b[0-9]{5}(?:-[0-9]{4})?\\b\\z/i' // Same as US.
		);

		$value = array_values($check);
		$value = $value[0];
		if ($regex) {
			return preg_match($regex, $value);
		} else if (!is_array($country)) {
			return preg_match($countryRegs[$country], $value);
		}

		foreach ($country as $check) {
			if (!isset($countryRegs[$check]) && preg_match($countryRegs['default'], $value)) {
				return true;
			} else if (preg_match($countryRegs[$check], $value)) {
				return true;
			}
		}

		return false;
	}

/**
 * Extension of saveAll() to broadcast an event that allows the data in associated models to be
 * modified before the save, since beforeSave() for associated data doesn't work with saveAll.
 * The event does NOT fire when the 'callbacks' option is set to false.
 *
 * @see Model::saveAll
 */
	public function saveAll($data = array(), $options = array()) {
		if (!isset($options['callbacks']) || $options['callbacks'] == true) {
			$event = CmsEventManager::dispatchEvent('Model.saveAll', $this, array('data' => $data, 'Model' => $this));
			if (is_array($event->result) && !empty($event->result)) {
				foreach ($data as $key => $val) {
					if (!isset($event->result[$key])) {
						unset($data[$key]);
					}
				}
				$data = $event->result;
			}
		}

		return parent::saveAll((array)$data, $options);
	}
	
/**
 * Find function used by the default admin_index action. This function will merge the query options
 * with an $_adminQuery array if found as a model class variable. The array should be in standard
 * query condition format.
 *
 * @see Model::find
 */
	protected function _findAdmin($state, $query, $results = array()) {
		if ($state == 'before') {
			if (!empty($this->_adminQuery)) {
				$query = Hash::merge($query, $this->_adminQuery);
			}
			return $query;
		}

		return $results;
	}

/**
 * Find function used by the default admin_edit action. This function will merge the query options
 * with an $_editQuery array if found as a model class variable. The array should be in standard
 * query condition format.
 *
 * @see Model::find
 */
	protected function _findEdit($state, $query, $results = array()) {
		if ($state == 'before') {
			$query['limit'] = 1;
			if (!empty($this->_editQuery)) {
				$query = Hash::merge($query, $this->_editQuery);
			}
			return $query;
		}
		
		if ($state == 'after' && !empty($results)) {
			$results = $results[0];
		}
		
		return $results;
	}

}