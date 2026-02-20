<?php

class CmsControllerToolsComponent extends Component {

/**
 * Configuration method.
 *
 * @param	object	$model
 * @param	array	$settings
 * @return	void
 */
	public function initialize(Controller $controller, $settings = array()) {
		$this->settings = $settings;
		$this->controller = $controller;
	}

/**
 * Find AJAX sent conditions and order
 *
 * @param	$array request from $this->request->data
 * @param	$array fields we might get rid of this if we can use the key from the array sent by datatables.net
 * @param	$array options
 * @return	array
 */
	public function datatableConditionsAndOrder($Model, $options = array()) {
		//
		$return			= array();
		//
		$request		= $options['request'];
		//
		$default		= $options['default'];
		//
		$Fields			= $options['fields'];
		// conditions
		$return['conditions']	= array();
		// Input field
		$a = array();
		//
		foreach ($Fields AS $unsetKey => $field) {
			//
			if (isset($request['search']['value']) && !empty($request['search']['value']) && $field['searchable']) {
				//
				if (isset($field['searchable_fields'])) {
					//
					foreach ($field['searchable_fields'] AS $f) {
						//
						$a[$f . ' LIKE']	= '%' . $request['search']['value'] . '%';
					}
				} else {
					//
					$a[$field['name'] . ' LIKE']	= '%' . $request['search']['value'] . '%';
				}
			}
		}
		//
		if (count($a) > 0) {
			//
			$return['conditions']['OR']	= $a;
		} else {
			//
			$return['conditions']		= $a;
		}
		// 
		if (isset($request['extra_search']) && !empty($request['extra_search'])) {
			//
			foreach ($request['extra_search'] AS $field) {
				//
				if (isset($field['value']) && strlen($field['value']) > 0) {
				//if (!empty($field['value'])) {
					//
					if ($field['value'] == 'NOT') {
						//
						$return['conditions']['NOT'][$field['name']]	= '';
					} else {
						//
						$return['conditions'][$field['name']]	= $field['value'];
					}
				}
			}
		}
		//
		if (isset($default['conditions']) && !empty($default['conditions'])) {
			//
			$return['conditions'][]	= $default['conditions'];

		}
		// order
		if (isset($request['order']) && is_array($request['order']) && !empty($request['order'])) {
			//
			$orderBy	= array();
			//
			foreach ($request['order'] AS $o) {
				//
				if (isset($Fields[$o['column']]['varchar_order'])) {
					//
					if (is_array($Fields[$o['column']]['varchar_order'])) {
						//
						foreach ($Fields[$o['column']]['varchar_order'] AS $column) {
							//
							$orderBy[]	= $column . ' ' . strtoupper($o['dir']);
						}
					} else {
						//
						$orderBy[]		= 'LENGTH(' . $request['columns'][$o['column']]['name'] . ') ' . strtoupper($o['dir']) . ', ' . $request['columns'][$o['column']]['name'] . ' ' . strtoupper($o['dir']);
					}
				} else {
					//
					$orderBy[]			= $request['columns'][$o['column']]['name'] . ' ' . strtoupper($o['dir']);
				}
				//
				$return['order']			= $orderBy;
			}
		} else {
			//
				$return['order'][]	= $Fields[0]['order_field'] . ' ' . (isset($Fields[0]['orderdir']) && !$Fields[0]['orderdir'] ? 'DESC' : 'ASC');
		}
		//
		return $return;
	}
	
}