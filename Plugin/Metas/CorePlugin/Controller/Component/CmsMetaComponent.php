<?php
App::uses('String', 'Utility');
/**
 * CmsMetaComponent class
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsMetaComponent.html
 * @package      Cms.Plugin.Metas.Controller.Component 
 * @since        Pyramid CMS v 1.0
 */
class CmsMetaComponent extends Component {

/**
 * beforeRender - calls _setMetas().
 *
 * @param object Controller
 */
	public function beforeRender(Controller $Controller) {
		$this->_setMetas($Controller);
	}

/**
 * If a MetaValue array is found on the main model, it's parsed and set as a direct view variable as 'metas'.
 * Also sets the meta names of each value.
 *
 * If metas aren't found for the main model, the function will also look for a '_page' variable, set by the 
 * PageSettings component, variable to see if there are any page-level metas.
 *
 * @param object Controller
 */
	protected function _setMetas(Controller $Controller) {
		$foreignKey = null;
		$cacheName = null;

		// First look for the main model's metas, and then, if not found, the page's metas.
		$model = Inflector::variable(Inflector::underscore($Controller->modelClass));
		if (isset($Controller->viewVars[$model]) && (isset($Controller->viewVars[$model]['MetaValue']) || isset($Controller->viewVars[$model]['metaDefaults']))) {
			$foreignKey = $Controller->viewVars[$model][$Controller->modelClass]['id'];
			$cacheName = $Controller->name . '_' . $model . '_' . $foreignKey;
		} else if (isset($Controller->viewVars['_page']) && isset($Controller->viewVars['_page']['MetaValue'])) {
			$model = '_page';
			$foreignKey = $Controller->viewVars[$model]['Page']['id'];
			$cacheName = 'PageSettings_' . $model . '_' . $foreignKey;
		}

		if (!$foreignKey || !$cacheName) {
			return;
		}

		$modelAlias = $Controller->{$Controller->modelClass}->alias;
		$metas = Cache::read($cacheName, 'meta');

		if ($metas === false) {
			$values = $Controller->viewVars[$model]['MetaValue'];

			$keys = ClassRegistry::init('Metas.MetaKey')->find('all', array(
				'fields' => array('id', 'name', 'default', 'allow_default')
			));
			$keys = Hash::combine($keys, '{n}.MetaKey.id', '{n}.MetaKey');
			$metas = array();

			foreach ($values as $key => $val) {
				if (empty($val['val']) && !Configure::read('Settings.Metas.use_defaults')) {
					continue;
				}

				if (!empty($val['val'])) {
					$value = $val['val'];
				} else if (empty($val['val']) && isset($keys[$val['meta_key_id']]) && $keys[$val['meta_key_id']]['allow_default']) {
					$value = $keys[$val['meta_key_id']]['default'];
				} else {
					continue;
				}

				if (isset($keys[$val['meta_key_id']])) {
					$metas[$keys[$val['meta_key_id']]['name']] = str_replace(' ,', ',', $value);	
				}
			}
			
			//if there's no meta description set then try to grab one from the model data set
			if (!isset($metas['description']) && !empty($Controller->viewVars[$model][$modelAlias]['description'])) {
				$metas['description'] = html_entity_decode(strip_tags($Controller->viewVars[$model][$modelAlias]['description']));
				if (strlen($metas['description'])>156){
					$metas['description'] = String::truncate($metas['description'], 156);
				}
			}
			
			if (isset($Controller->viewVars[$model]['metaDefaults']) && !empty($Controller->viewVars[$model]['metaDefaults'])) {
				foreach ($Controller->viewVars[$model]['metaDefaults'] as $key => $val) {
					if (!isset($metas[$key])) {
						// 156 character limit for description
						if ($key == 'description' && strlen($val) > 156) {
							$val = String::truncate($val, 156);
						}
						$metas[$key] = str_replace(' ,', ',', $val);
					}
				}
			}

			Cache::write($cacheName, $metas, 'meta');
		}

		$Controller->set('metas', $metas);
	}
	
}
