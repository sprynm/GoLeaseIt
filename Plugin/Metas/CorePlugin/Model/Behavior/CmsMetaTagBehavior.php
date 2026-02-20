<?php
/**
 * CmsMetaTagBehavior Class File
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsMetaTagBehavior.html
 * @package      Cms.Plugin.Metas.Model.Behavior
 * @since        Pyramid CMS v 1.0
 */
class CmsMetaTagBehavior extends ModelBehavior implements CakeEventListener {

/**
 * Behavior settings
 */
	public $settings = array();

/**
 * Default values for settings.
 */
	protected $_defaults = array(
		'defaults' => array()
	);

/**
 * Intializer
 *
 * @param   object $Model
 * @param   array $config
 * @return  void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $settings);
		$this->_addAssociation($Model);
		CmsEventManager::instance()->attach($this);
	}

/**
 * Formats MetaValue so that the numeric key corresponds to the meta_key_id.
 *
 * @param object model
 * @param array results
 * @param boolean primary
 */
	public function afterFind(Model $Model, $results, $primary) {
		if (isset($results[0]) && isset($results[0]['MetaValue'])) {
			if (!empty($results[0]['MetaValue'])) {
				foreach ($results as $i => $result) {
					$metas = Hash::combine($results[$i]['MetaValue'], '{n}.meta_key_id', '{n}');
					$results[$i]['MetaValue'] = $metas;
				}
			}

			if (!empty($this->settings[$Model->alias]['defaults'])) {
				foreach ($results as $i => $result) {
					$metas = array();
					foreach ($this->settings[$Model->alias]['defaults'] as $key => $val) {
						$metas[$key] = Cms::dynamicReplace($result[$Model->alias], $val);
					}
					$results[$i]['metaDefaults'] = $metas;
				}
			}
		}

		return $results;
	}

/**
 * Clears meta group after save.
 *
 * @param object $Model
 * @param boolean created
 * @return void
 */    
	public function afterSave(Model $Model, $created) {
		Cache::clearGroup('meta');
	}

/**
 * Adds meta conditions to the query. Metas are attached automatically unless a metas => false
 * option is passed in the query.
 *
 * @param	object	$Model
 * @param	array	$query
 * @return	mixed
 */
	public function beforeFind(Model $Model, $query) {
		if (isset($query['metas']) && $query['metas'] === false) {
			return $query;
		}
		
		if (!isset($query['contain'])) {
			$query['contain'] = array();
		}

		$query['contain'][] = 'MetaValue';

		return $query;
	}

/**
 * Implemented events
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Model.saveAll' => 'parseMetaData'
		);
	}

/**
 * Event listener to parse MetaValue data before a saveAll() so that it's saved properly, since you can't
 * edit such information in beforeSave().
 *
 * @param object event
 * @return void
 */
	public function parseMetaData($event) {
		$data = array();
		
		if (isset($event->data['data']['MetaValue'])){			
			$data = Hash::merge($event->data['data']['MetaValue'], $event->result['MetaValue']);
			foreach ($data as $key => $val) {
				$data[$key]['model'] = $event->data['Model']->name;
			}
			
			$event->data['data']['MetaValue'] = $data;
			$event->result['MetaValue'] = $data;
		}
		
	}

/**
 * Adds a hasMany MetaValue assocation to $Model.
 *
 * @param object Model
 * @return boolean
 */
	protected function _addAssociation(Model $Model) {
		if (!isset($Model->hasMany['MetaValue'])) {
			$Model->bindModel(
				array('hasMany' => array(
					'MetaValue' => array(
						'className' => 'Metas.MetaValue',
						'foreignKey' => 'foreign_key',
						'conditions' => array(
							'MetaValue.model' => $Model->name 
						), 
						'dependent' => true
					)
				)),
				false
			);
		}

		return isset($Model->hasMany['MetaValue']);
	}

}