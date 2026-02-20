<?php
/**
 * CmsNavigationMenuItem class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsNavigationMenuItem.html
 * @package		 Cms.Plugin.Navigation.Model 
 * @since		 Pyramid CMS v 1.0
 */
class CmsNavigationMenuItem extends NavigationAppModel {

/**
 * Attached behaviors
 */
	public $actsAs = array(
		'MultiTree' => array(
			'root' => 'navigation_menu_id',
			'rootModel' => 'NavigationMenu',
			'level' => false
		),
		'TreeSort.TreeSort',
		'Publishing.Publishable',
		'Versioning.SoftDelete',
		'Copyable'
	);

/**
 * hasMany associations
 */
	public $hasMany = array(
		'ChildNavigationMenuItem' => array(
			'className' => 'Navigation.NavigationMenuItem', 
			'foreignKey' => 'parent_id', 
			'dependent' => false
		)
	);

/**
 * belongsTo associations
 */
	public $belongsTo = array(
		'NavigationMenu' => array(
			'className' => 'Navigation.NavigationMenu', 
			'foreignKey' => 'navigation_menu_id', 
			'dependent' => true
		)
	);

/**
 * Default order for queries
 */
	//public $order = 'NavigationMenuItem.lft ASC';

/**
 * Validate array
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'This field is required.'
		)
	);

/**
 * Admin edit query modifications
 *
 * @var array
 */
	protected $_editQuery = array(
		'contain' => array('NavigationMenu')
	);

/**
 * Finds and returns items for CmsNavigationHelper::show()
 *
 * @param array $conditions Conditions from the helper
 * @return array
 */
	public function findForHelperShow($conditions) {
		//
		$items	= $this->find(
				'threaded'
				, array(
					'order'		=> 'NavigationMenuItem.lft ASC'
					, 'conditions'	=> $conditions
					//, 'cache'	=> true
					, 'published'	=> true
					,
				)
			);
		//
		##CakeLog::write('findForHelperShow-93', 'File: ' . __FILE__ . "\n" . '$items: ' . print_r($items, true) . "\n-------\n");
		//
		$items	= $this->findForDynamic($items);
		//
		##CakeLog::write('findForHelperShow-97', 'File: ' . __FILE__ . "\n" . '$items: ' . print_r($items, true) . "\n-------\n");
		// 
		return	$items;
	}

/**
 * Finds and returns items for 
 *
 * @param array $data (items)
 * @return array
 */
	public function findChildrenFor($array, $parentId, $options = array()) {
		//
		$return		= array();
		// 
		$items		= $this->find(
					'all'
					, array(
						'conditions'	=> array(
							$this->alias . '.deleted'	=> false
							, $this->alias . '.navigation_menu_id'	=> $array['navigation_menu_id']
							, $this->alias . '.parent_id'	=> $parentId
							,
						)
						, 'order'	=> array(
							$this->alias . '.lft'
							,
						)
						, 'published'	=> true
						, 'callbacks'	=> false
						,
					)
				);
		// 
		if (!empty($items)) {
			//
			foreach ($items AS $key => $item) {
				//
				$children	= isset($item['children']) && empty($item['children'])
						? $this->runTheLine($item['children'])
						: array();
				//
				$return[]	= $this->runTheLine(
						array(
							'name'			=> $item[$this->alias][$this->displayField]
							, 'parent_id'		=> $parentId
							, 'lft'			=> null
							, 'rght'		=> null
							, 'foreign_model'	=> $item[$this->alias]['foreign_model']
							, 'foreign_key'		=> $item[$this->alias]['foreign_key']
							, 'foreign_plugin'	=> $item[$this->alias]['foreign_plugin']
							, 'id'			=> $item[$this->alias][$this->primaryKey]
							, 'url'			=> null
							, 'navigation_menu_id'	=> $array['navigation_menu_id']
							, 'new_window'		=> false
							, 'dynamic'		=> false
							, 'children'		=> $children
							, 
						));
			}
		}
		//
		/*if (isset($array['child_depth']) && !$array['child_depth']) {
			//
			return	$return;
		}*/
		// 
		$model		= ClassRegistry::init($array['foreign_plugin'] . '.' . $array['foreign_model']);
		//
		$conditions	= $array['foreign_plugin'] == 'Prototype'
				? array(
					$model->alias . '.prototype_instance_id'	=> $array['dynamic_foreign_key']
					,
				)
				: array();
		//
		$order		= !empty($model->schema('lft'))
				? $model->alias . '.lft'
				: (!empty($model->schema('rank'))
					? $model->alias . '.rank'
					: $model->primaryKey
				);
		// 
		$find		= $array['child_depth'] //isset($model->actsAs['TreeSort.TreeSort']) //&& $array['child_depth']
				? 'all'
				: 'threaded';
		//
		if ($array['child_depth']) {
			//
			$conditions[$model->alias . '.parent_id'] = NULL;
		}
		//
		$model->Behaviors->unload('Metas.MetaTag');
		$model->Behaviors->unload('CustomFields.CustomField');
		// 
		$items		= $model->find(
					$find
					, array(
						'conditions'	=> $conditions
						, 'order'	=> $order
						, 'published'	=> true
						, 'callbacks'	=> false
						,
					)
				);
		//
		if (!$items) {
			//
			return	$return;
		}
		//
		foreach ($items AS $key => $item) {
			// 
			$children	= isset($item['children'])
					? $item['children']
					: array();
			// 
			$return[]	= $this->runTheLine(
					array(
						'name'			=> $item[$model->alias][$model->displayField]
						, 'parent_id'		=> $parentId
						, 'lft'			=> null
						, 'rght'		=> null
						, 'foreign_model'	=> $array['foreign_model']
						, 'foreign_key'		=> $item[$model->alias][$model->primaryKey]
						, 'foreign_plugin'	=> $array['foreign_plugin']
						, 'id'			=> null
						, 'url'			=> null
						, 'navigation_menu_id'	=> $array['navigation_menu_id']
						, 'new_window'		=> false
						, 'dynamic'		=> false
						, 'children'		=> $children
						, 
					));
		}
		//
		return		$return;
	}


/**
 * 
 *
 * 
 * 
 */
	public function runTheLine($datum) {
		//
		if (empty($datum)) {
			//
			return		array();
		}
		//
		if(isset($datum['children']) && !empty($datum['children'])) {
			//
			foreach ($datum['children'] AS $key => $array) {
				//
				$arrayKeys	= array_keys($array);
				// 
				$arrayModel	= $arrayKeys[0];
				// 
				$arrayChildren	= $arrayKeys[1];
				// 
				$children[]	= $this->runTheLine(array(
							'name'			=> $array[$arrayModel]['name']
							, 'parent_id'		=> $datum['parent_id']
							, 'lft'			=> null
							, 'rght'		=> null
							, 'foreign_model'	=> $datum['foreign_model']
							, 'foreign_key'		=> $array[$arrayModel]['id']
							, 'foreign_plugin'	=> $datum['foreign_plugin']
							, 'id'			=> null
							, 'url'			=> null
							, 'navigation_menu_id'	=> $datum['navigation_menu_id']
							, 'new_window'		=> false
							, 'dynamic'		=> false
							, 'children'		=> $array['children']
							, 
						));
			}
		// 
		} else {
			//
			$children	= array();
		}
		//
		$return		= array(
					'NavigationMenuItem'	=> array(
						'name'			=> $datum['name']
						, 'parent_id'		=> $datum['parent_id']
						, 'lft'			=> $datum['lft']
						, 'rght'		=> $datum['rght']
						, 'foreign_model'	=> $datum['foreign_model']
						, 'foreign_key'		=> $datum['foreign_key']
						, 'foreign_plugin'	=> $datum['foreign_plugin']
						, 'id'			=> $datum['id']
						, 'url'			=> $datum['url']
						, 'navigation_menu_id'	=> $datum['navigation_menu_id']
						, 'new_window'		=> $datum['new_window']
						, 'dynamic'		=> $datum['dynamic']
						,
					)
					, 'children'	=> $children
					,
				);
		//
		return		$return;
	}


/**
 * Finds and returns items for CmsNavigationHelper::show()
 *
 * @param array $data (items)
 * @return array
 */
	public function findForDynamic($data = array(), $options = array()) {
		//
		$flatData	= Hash::flatten($data);
//
##CakeLog::write('findForDynamic-311', 'File: ' . __FILE__ . "\n" . 'flatData: ' . print_r($flatData, true) . "\n-------\n");
		//
		foreach ($flatData AS $key => $flatDatum) :
			//
			$array		= array();
			//
			if (substr($key, -8) == '.dynamic' && $flatDatum == 1) :
				//
				$tmpKey		= substr($key, 0, -8);
				//
				$array['id']			= $flatData[$tmpKey . '.id'];
				//
				$array['foreign_plugin']	= $flatData[$tmpKey . '.foreign_plugin'];
				//
				$array['foreign_model']		= $flatData[$tmpKey . '.foreign_model'];
				//
				$array['foreign_key']		= $flatData[$tmpKey . '.foreign_key'];
				//
				$array['dynamic_foreign_key']	= $flatData[$tmpKey . '.dynamic_foreign_key'];
				//
				$array['navigation_menu_id']	= $flatData[$tmpKey . '.navigation_menu_id'];
				// 
				$array['child_depth']		= $flatData[$tmpKey . '.child_depth'];
				// 
				$childrenKey			= str_replace('.NavigationMenuItem.dynamic', '.children', $key);
				//
				$flatData[$childrenKey]		= $this->findChildrenFor($array, $flatData[$tmpKey . '.id']);
			// (substr($key, -8) == '.dynamic' && $flatDatum == 1)
			endif;
		// ($flatData AS $key => $flatDatum)
		endforeach;
		//
		return		Hash::expand($flatData);
	}
	public function findForDynamic__z($data = array(), $options = array()) {
		//
		$flatData	= Hash::flatten($data);
		//
		$return		= array();
		//
		if (empty($data)) {
			//
			return	$return;
		}
		//
		$itemId	= 9999999;
		// 
		foreach ($flatData AS $dataKey => $datum) {
			// 
			if (substr($dataKey, -22) == '.NavigationMenuItem.id') {
				//
				$itemId		= $datum;
			}
			// 
			if (substr($dataKey, -38) == '.NavigationMenuItem.navigation_menu_id') {
				//
				$navigationMenuId		= $datum;
			}
			//
			if (substr($dataKey, -34) == '.NavigationMenuItem.foreign_plugin') {
				//
				$foreignPlugin	= $datum;
			}
			//
			if (substr($dataKey, -33) == '.NavigationMenuItem.foreign_model') {
				//
				$foreignModel	= $datum;
			}
			//
			if (substr($dataKey, -32) == '.NavigationMenuItem.foreign_key') {
				//
				$foreignkey	= $datum;
			}
			//
			if (substr($dataKey, -39) == '.NavigationMenuItem.dynamic_foreign_key') {
				//
				$dynamicForeignkey	= $datum;
			}
			//
			if (substr($dataKey, -29) == '.NavigationMenuItem.parent_id') {
				//
				$parentId	= $datum;
			}
			//
			if (substr($dataKey, -24) == '.NavigationMenuItem.name') {
				//
				$name		= $datum;
			}
			// 
			if (substr($dataKey, -27) == '.NavigationMenuItem.dynamic' && $datum == 1) {
				//
				$model			= ClassRegistry::init($foreignPlugin . '.' . $foreignModel);
				//
				$conditions		= $foreignPlugin == 'Prototype'
							? array(
								$model->alias . '.prototype_instance_id'	=> $dynamicForeignkey
								,
							)
							: array(
								$model->alias . '.' . $model->primaryKey . ' >'	=> 1
								,
							);
				//
				$order			= !empty($model->schema('lft'))
							? $model->alias . '.lft'
							: (!empty($model->schema('rank'))
								? $model->alias . '.rank'
								: $model->primaryKey
							);
				//
				$find			= strpos($model->alias, 'Category') !== false
							? 'threaded'
							: 'all';
//
/*CakeLog::write(
	'findForDynamic-186'
	, 'File: ' . __FILE__ . "\n"
	. '$find: ' . print_r($find, true) . "\n"
	. '$model->alias: ' . $model->alias . "\n"
	. '$model->actsAs[\'TreeSort.TreeSort\']: ' . (strpos($model->alias, 'Category') !== false ? 'true' : 'false') . "\n"
	. '$model->actsAs: ' . print_r($model->actsAs, true)
	. "\n-------\n"
);*/
				// 
				$kids			= $model->find(
								$find
								, array(
									'conditions'	=> $conditions
									, 'order'	=> $order
									, 'published'	=> true
									,
								)
							);
			}
			//
			if (!empty($kids) && substr($dataKey, -8) == 'children') {
				//
				$foreignkey	= isset($foreignkey) && strlen($foreignkey) > 0
						? $foreignkey
						: null;
				//
				foreach ($kids AS $kid) {
					//
					$data[$dataKey][]	= array(
						'NavigationMenuItem'	=> array(
							'name'			=> $kid[$model->alias][$model->displayField]
							, 'parent_id'		=> $itemId
							, 'lft'			=> 0
							, 'rght'		=> 0
							, 'foreign_model'	=> $foreignModel
							, 'foreign_key'		=> $kid[$model->alias][$model->primaryKey]
							, 'foreign_plugin'	=> $foreignPlugin
							, 'id'			=> $kid[$model->alias][$model->primaryKey]
							//, 'slug'		=> $kid[$model->alias]['slug']
							, 'url'			=> null
							, 'navigation_menu_id'	=> $navigationMenuId
							, 'new_window'		=> false
							, 'dynamic'		=> null
							,
						)
					);
				}
				//
				$kids		= array();
			}
		}
		//
		return		Hash::expand($data);
	}

/**
 * 
 *
 * 
 * 
 */
	public function runTheLine_z($item, $datum, $model, $parentId, $lft, $rght) {
		//
		$parentId	= isset($parentId)
				? $parentId
				: (isset($item[$model->alias]['parent_id'])
				? $item[$model->alias]['parent_id']
				: $datum['NavigationMenuItem']['parent_id']);
		//
		$return		= array(
					'NavigationMenuItem'	=> array(
						'name'			=> $item[$model->alias][$model->displayField]
						, 'parent_id'		=> $parentId
						, 'lft'			=> $lft
						, 'rght'		=> $rght
						, 'foreign_model'	=> $datum[$this->alias]['foreign_model']
						, 'foreign_key'		=> $item[$model->alias][$model->primaryKey]
						, 'foreign_plugin'	=> $datum[$this->alias]['foreign_plugin']
						, 'id'			=> null
						, 'url'			=> null
						, 'navigation_menu_id'	=> $datum[$this->alias]['navigation_menu_id']
						, 'new_window'		=> false
						, 'dynamic'		=> null
						,
					)
				);
		//
		return		$return;
	}





	public function findForDynamic_z($data = array()) {
		//
		$return		= array();
		//
		if (empty($data)) {
			//
			return	$return;
		}
##CakeLog::write('findForDynamic-89', 'File: ' . __FILE__ . "\n" . '$data: ' . print_r($data, true) . "\n-------\n");
		// 
		foreach ($data AS $keyData => $datum) {
			//
			if ($datum[$this->alias]['dynamic']) {
				//
				$lft		= $datum[$this->alias]['lft'];
				//
				$rght		= $datum[$this->alias]['rght'];
				//
				$model		= ClassRegistry::init($datum[$this->alias]['foreign_plugin'] . '.' . $datum[$this->alias]['foreign_model']);
				//
				$conditions	= $datum[$this->alias]['foreign_plugin'] == 'Prototype'
						? array(
							$model->alias . '.prototype_instance_id'	=> $datum[$this->alias]['dynamic_foreign_key']
							,
						)
						: array();
				//
				$order		= !empty($model->schema('lft'))
						? $model->alias . '.lft'
						: (!empty($model->schema('rank'))
							? $model->alias . '.rank'
							: $model->primaryKey
						);
				// 
				$find		= !isset($model->actsAs['TreeSort.TreeSort'])
						? 'all'
						: 'threaded';
				// These need to be added to the $data[$keyData] as the array('children') - example 'children' => array( array('NavigationMenuItem' => array()))
				$items		= $model->find(
							'threaded' //$find
							, array(
								'conditions'	=> $conditions
								, 'order'	=> $order
								, 'published'	=> true
								,
							)
						);
				//
				if (!empty($items)) {
					//
					if ($datum[$this->alias]['foreign_model'] == 'BlogPost') {
						//
						##CakeLog::write('findForDynamic-132', 'File: ' . __FILE__ . "\n" . '$items: ' . print_r($items, true) . "\n-------\n");
						//
						$data[$keyData]['children'][]	= $items['children'];
					// 
					} else {
						//
						foreach ($items AS $itemKey => $item) {
							//
							$data[$keyData]['children'][]	= $this->runTheLine($item, $datum, $model, $datum[$this->alias][$model->primaryKey], $lft, $rght);
						}
					}
				}
			}
		}
		//
		return		$data;
	}


}
