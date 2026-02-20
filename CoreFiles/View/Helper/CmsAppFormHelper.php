<?php
App::uses('FormHelper', 'View/Helper');

/**
 * CMS core-level Form helper
 *
 * Extends the Cake core FormHelper.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAppFormHelper.html
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsAppFormHelper extends FormHelper {

/**
 * Field types, used by email forms and extra fields, among others. The 'options' array
 * can be used to pass options to the Form helper when outputting a custom field (see the
 * Email Forms plugin for an example).
 * 
 * retired [a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})
 * 
 */
	protected $_fieldTypes = array(
		'basic' => array(
			'text' => array(
				'name' => 'Text (string)'
				, 'type' => 'text'
			)
			, 'textarea' => array(
				'name' => 'Text (textarea)'
				, 'type' => 'textarea'
			)
			, 'wysiwyg' => array(
				'name' => 'Text (WYSIWYG)'
				, 'type' => 'textarea'
				, 'options' => array(
					'wysiwyg' => true
				)
			)
			, 'checkbox' => 'Checkbox'
			, 'radio' => 'Radio'
			, 'select' => 'Select'
			/** Checkboxes should be used instead of multi select boxes
			, 'select_multiple' => array(
				'name' => 'Select (multiple)'
				, 'type' => 'select'
				, 'options' => array(
					'multiple' => true
				)
			)
			*/
			, 'image' => 'Image'
			, 'document' => 'Document'
			, 'readonly'	=> 'Read Only'
			, 'email' => array(
				'name'=>'Email Address'
				, 'type'=>'email'
				, 'options' => array(
					'pattern' => "\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})"
				)
			)
			, 'tel' => array(
				'name' => 'Telephone'
				, 'type' => 'tel'
				, 'options' => array(
					//telephone validation, copied from Validation::phone, works for US and Canada numbers
					'pattern' => "(?:\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}"
				)
			)
			, 'url' => array(
				'name' => 'URL'
				, 'type'=>'url'
			)
			, 'date'	=> 'Date'
			,
		)
	);

/**
 * Helpers to load
 */
	public $helpers = array(
		'Html' => array('className' => 'appHtml'),
		'TinyMce.TinyMce'
	);

/** 
 * Initiates WYSIWYG stuff if requested by a form input.
 *
 * @param array
 * @return array
 */
	public function addWysiwyg($options) {
		if (CmsPlugin::isInstalled('CkEditor')) {
			$this->CkEditor->addEditor();
			$class = 'ckeditor';
		} else {
			$this->TinyMce->addEditor();
			$class = 'wysiwyg';
		}

		unset($options['wysiwyg']);
		if (isset($options['class'])) {
			$options['class'] .= ' ' . $class;
		} else {
			$options['class'] = $class;
		}
		
		return $options;
	}

/**
 * Overrides FormHelper::create() to remove HTML5 validation.
 *
 * @see FormHelper::create()
 */
	public function create($model = null, $options = array()) {
		if (!array_key_exists('novalidate', $options)) {
			$options['novalidate'] = true;
		}
		return parent::create($model, $options);
	}

/**
 * Initiates JQuery datepicker.
 *
 * @param array
 * @return array
 */
	public function addDatepicker($options) {
		$this->Html->script('datepicker', array('inline' => false, 'once' => true));
		$this->Html->css('datepicker', null, array('inline' => false, 'once' => true));
		
		unset($options['datepicker']);
		if (isset($options['class'])) {
			$options['class'] .= ' datepicker';
		} else {
			$options['class'] = 'datepicker';
		}
		
		return $options;
	}

/**
 * Adds the required field element to a $label string.
 *
 * @param string $label
 * @param string $name
 * @return string
 */
	public function addRequired($label) {
		is_array($label) ? $label['text'] : $label .= ' ' . $this->_View->element('required');
		return $label;
	}

/**
 * Initiates JQuery datetimepicker.
 *
 * @param array
 * @return array
 */
	public function addTimepicker($options) {
		$this->Html->script('jquery-ui-timepicker-addon', array('inline' => false, 'once' => true));
		$this->Html->script('timepicker', array('inline' => false, 'once' => true));
		$this->Html->css('datepicker', null, array('inline' => false, 'once' => true));

		unset($options['timepicker']);
		if (isset($options['class'])) {
			$options['class'] .= ' timepicker';
		} else {
			$options['class'] = 'timepicker';
		}
		
		return $options;
	}

/**
 * Returns the value in $_fieldTypes for field type $key. Always return an array. For simple
 * fields (i.e. with only key => val strings defined), val is wrapped in an array with a 'name' key.
 *
 * @param string name
 * @return array
 */
	public function fieldTypeInfo($name) {
		$field = null;
		
		foreach ($this->_fieldTypes as $key => $val) {
			if (isset($val[$name])) {
				$field = $val[$name];
				break;
			}
		}
		
		if (!$field) {
			return null;
		}
		
		if (!is_array($field)) {
			$field = array(
				'name' => $field,
				'type' => $name
			);
		}
		
		return $field;
	}

/**
 * Returns the $_fieldTypes array for a select input. Since some fields are simple key => val and some contain
 * extra information, this function is necessary for proper formatting of the data.
 *
 * @return array
 */
	public function fieldTypes() {
		$return = array();
		
		foreach ($this->_fieldTypes as $group => $types) {
			$label = ucfirst($group);
			$return[$label] = array();
			foreach ($types as $key => $val) {
				if (!is_array($val)) {
					$return[$label][$key] = $val;
				} else {
					$return[$label][$key] = $val['name'];
				}
			}
		}
		
		return $return;
	}

/**
 * Extends the core FormHelper::input() function to allow for a custom 
 * "description" argument, as well as some other functionality.
 *
 * @param	string	$fieldName
 * @param	array	$options	OPTIONAL
 * @return	string
 */
	public function input($fieldName, $options = array()) {
		if (isset($options['description'])) {
			if (isset($options['after'])) {
				$after = $options['after'];
			} else {
				$after = '';
			}
			$after .= '<span class="input-desc">' . $options['description'] . '</span>';
			$options['after'] = $after;

			if (empty($options['label'])) {
				list($splitPlugin, $splitField) = pluginSplit($fieldName);
				$options['label'] = Inflector::humanize($splitField);
			}
			$options['label'] .= '<span class="tooltip rounded-corners">' . $options['description'] . '</span>';
			unset($options['description']);
		}
		

		// Handle a checkbox that should be checked by default
		if (isset($options['default']) && $options['default'] == 'checked') {
			$field = $this->_determineInputField($fieldName);
			if ($field) {
				if (!isset($this->request->data[$field['model']])) {
					$this->request->data[$field['model']] = array();
				}
				if (!isset($this->request->data[$field['model']][$field['type']])) {
					$this->request->data[$field['model']][$field['type']] = 1;
				}
			}
		}
		
		// WYSIWYG
		if (!empty($options['wysiwyg'])) {
			$options = $this->addWysiwyg($options);
		}
		
		// Datepicker
		if (!empty($options['datepicker'])) {
			$options = $this->addDatepicker($options);
		}
		
		// Date & time picker
		if (!empty($options['timepicker'])) {
			$options = $this->addTimepicker($options);
		}

		if (!empty($options['required'])) {
			if (!empty($options['legend'])){
				$options['legend'] = $this->addRequired($options['legend']);
				} else if (!empty($options['label'])) {
				$options['label'] = $this->addRequired($options['label']);
			} else {
				$splitName = pluginSplit($fieldName); // Just in case
				$splitName = Inflector::humanize($splitName[1]);
				$options['label'] = $this->addRequired($splitName);
			}
		}

		return parent::input($fieldName, $options);
	}
	
/**
 * Returns a formatted error message for given FORM field, NULL if no errors.
 * Altered to adjust the structure of the error messages
 * ### Options:
 *
 * - `escape`  bool  Whether or not to html escape the contents of the error.
 * - `wrap`  mixed  Whether or not the error message should be wrapped in a div. If a
 *   string, will be used as the HTML tag to use.
 * - `class` string  The classname for the error message
 *
 * @param string $field A field name, like "Modelname.fieldname"
 * @param string|array $text Error message as string or array of messages.
 * If array contains `attributes` key it will be used as options for error container
 * @param array $options Rendering options for <div /> wrapper tag
 * @return string If there are errors this method returns an error message, otherwise null.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::error
 */
	public function error($field, $text = null, $options = array()) {
		
		$defaults = array('wrap' => true, 'class' => 'error-message input-desc input-error', 'escape' => true);
		$options = array_merge($defaults, $options);
		
		return parent::error($field, $text, $options);
	}

/**
 * Attempts to determine which model and field is being used for the input function call.
 *
 * @param string $fieldName
 * @return string
 */
	protected function _determineInputField($fieldName) {
		$returnField = null;

		if (strstr($fieldName, '.')) {
			$field = explode('.', $fieldName);
			$returnField['model'] = $field[0];
			$returnField['type'] = $field[1];
		} else {
			$returnField['model'] = $this->params['models'][0];
			$returnField['type'] = $fieldName;
		}

		return $returnField;
	}
	

/**
 * Extends core radio() function and adds a class to the Legend so that designers can force it to appear even if legends are turned off for the * form.
 *
 * @return string
 */
	public function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);

		$showEmpty = $this->_extractOption('empty', $attributes);
		if ($showEmpty) {
			$showEmpty = ($showEmpty === true) ? __d('cake', 'empty') : $showEmpty;
			$options = array('' => $showEmpty) + $options;
		}
		unset($attributes['empty']);

		$legend = false;
		if (isset($attributes['legend'])) {
			$legend = $attributes['legend'];
			unset($attributes['legend']);
		} elseif (count($options) > 1) {
			$legend = __(Inflector::humanize($this->field()));
		}

		$label = true;
		if (isset($attributes['label'])) {
			$label = $attributes['label'];
			unset($attributes['label']);
		}

		$separator = null;
		if (isset($attributes['separator'])) {
			$separator = $attributes['separator'];
			unset($attributes['separator']);
		}

		$between = null;
		if (isset($attributes['between'])) {
			$between = $attributes['between'];
			unset($attributes['between']);
		}

		$value = null;
		if (isset($attributes['value'])) {
			$value = $attributes['value'];
		} else {
			$value = $this->value($fieldName);
		}

		$disabled = array();
		if (isset($attributes['disabled'])) {
			$disabled = $attributes['disabled'];
		}

		$out = array();

		$hiddenField = isset($attributes['hiddenField']) ? $attributes['hiddenField'] : true;
		unset($attributes['hiddenField']);

		if (isset($value) && is_bool($value)) {
			$value = $value ? 1 : 0;
		}

		foreach ($options as $optValue => $optTitle) {
			$optionsHere = array('value' => $optValue);

			if (isset($value) && strval($optValue) === strval($value)) {
				$optionsHere['checked'] = 'checked';
			}
			$isNumeric = is_numeric($optValue);
			if ($disabled && (!is_array($disabled) || in_array((string)$optValue, $disabled, !$isNumeric))) {
				$optionsHere['disabled'] = true;
			}
			$tagName = Inflector::camelize(
				$attributes['id'] . '_' . Inflector::slug($optValue)
			);

			if ($label) {
				$optTitle = $this->Html->useTag('label', $tagName, '', $optTitle);
			}
			if (is_array($between)) {
				$optTitle .= array_shift($between);
			}
			$allOptions = array_merge($attributes, $optionsHere);
			$out[] = '<div class="radio">' . $this->Html->useTag('radio', $attributes['name'], $tagName,
				array_diff_key($allOptions, array('name' => null, 'type' => null, 'id' => null)),
				$optTitle
			) . '</div>';
		}
		$hidden = null;

		if ($hiddenField) {
			if (!isset($value) || $value === '') {
				$hidden = $this->hidden($fieldName, array(
					'id' => $attributes['id'] . '_', 'value' => '', 'name' => $attributes['name']
				));
			}
		}
		$out = $hidden . implode($separator, $out);
		
		if (is_array($between)) {
			$between = '';
		}
		
		if ($legend) {
			$out = $this->Html->useTag('fieldset', '', $this->Html->useTag('legend', $legend, array('class' => 'radiolegend')) . $between . $out);
		}
		return $out;
	}

/**
 * Constructs a checkbox tree from a flat list generated by TreeBehaviour
 * Useful for category or brand selection for an admin edit page
 */
	public function checkboxTree($fieldName, $options = array(), $attributes = array(), $separator = '_' , $indentation = array()) {
		$this->setEntity($fieldName);
		
		$options = $this->_parseOptions($options);
		
		$divOptions = $this->_divOptions($options);
		unset($options['div']);
		
		if ($options['type'] === 'radio' && isset($options['options'])) {
			$radioOptions = (array)$options['options'];
			unset($options['options']);
		}
		
		$label = $this->_getLabel($fieldName, $options);
		if ($options['type'] !== 'radio') {
				unset($options['label']);
		}
		
		$error = $this->_extractOption('error', $options, null);
		unset($options['error']);
		$errorMessage = $this->_extractOption('errorMessage', $options, true);
		unset($options['errorMessage']);
		$selected = $this->_extractOption('selected', $options, null);
		unset($options['selected']);
		
		
		$indentation = Hash::merge(
			array('chunk'=>"  ", 'start'=>3)
			, $indentation
		);
		
		$startIndent = str_repeat($indentation['chunk'], $indentation['start']);
		
		$output = '';
		
		$values = $this->value()['value'];
		//get to the actual tree rendering
		$flattenedTree = array();
		
		//construct a more usable list of categories to convert it to a tree of lists
		foreach (array_keys($options['options']) as $key => $id ) {
			$matches = array();
			preg_match("/^((?:".$separator.")*)(.*)/", $options['options'][$id], $matches);
				
			$label = $matches[2];
			$depth = strlen($matches[1]);
			
			$flattenedTree[] = array('id'=>$id, 'label'=>$label, 'depth'=>$depth);
		}
		
		
		if (empty($flattenedTree)) {
			return "";
		}
		
		$openLists = 0;
		//print them out in a nested list now
		foreach ($flattenedTree as $key => $item) {
			$offsetNext = 0;
			if (isset($flattenedTree[$key + 1])) {
				$offsetNext = $flattenedTree[$key + 1]['depth'] - $item['depth'];
			}
			
			$offsetPrev = -1;
			if (isset($flattenedTree[$key - 1])){
				$offsetPrev = $flattenedTree[$key - 1]['depth'] - $item['depth'];
			}
			
			$fieldId = $fieldName . "." .  $item['id'];
			
			$output .= str_replace( "\n", "\n" . str_repeat( $indentation['chunk'], $item['depth'] * 2 ), ($offsetPrev >= 0?"\n</li>":'') . "\n<li>\n" . $indentation['chunk'] . '<div class="input checkbox">' .  $this->checkbox(
				$fieldId
				, array(
					'value' => $item['id']
					, 'checked' => (!empty($values[$item['id']]))
				)
			) . $this->label($fieldId, $item['label']) ) . "</div>";
			
			$openListsStart = $openLists;
			//make a list inside this one
			if ($offsetNext > 0) {
				$openLists++;
				$output .= str_replace( "\n", "\n" . str_repeat( $indentation['chunk'], $item['depth'] * 2 + 1), "\n<ul>");
				//otherwise if we are out of this list close the right number of lists
			} else if ($offsetNext < 0 && isset($flattenedTree[$key + 1])) {
				for ($i = 0; $i <= -$offsetNext - 1 && $openLists > 0; $i++) {
					$output .= str_replace( "\n", "\n" . str_repeat( $indentation['chunk'], ($item['depth'] - ($openListsStart - $openLists)) * 2 - 1 ), "\n" . $indentation['chunk'] . "</li>\n</ul>");
					$openLists--;
				}
			} else if (!isset($flattenedTree[$key + 1])) {
				while ($openLists > 0) {
					$output .= str_replace( "\n", "\n" . str_repeat( $indentation['chunk'], ( $item['depth'] - ($openListsStart - $openLists) ) * 2 - 1), "\n" . $indentation['chunk'] . "</li>\n</ul>");					
					$openLists--;
					if ($openLists == 0) {
						//close the final list item
						$output .= "\n</li>";
						$output = str_replace( "\n", "\n" . $indentation['chunk'], $output) . "\n";
					}
				}
			}
		}
		
		//wrap it in a ul
		$output = "\n" . $indentation['chunk'] . str_replace( "\n", "\n" . $indentation['chunk'], $this->Html->tag('ul', $output, array('class'=>array('checkbox-tree'), 'id'=>$this->domId($fieldName))) ) . "\n";
		//wrap it in a div and add the label
		unset($divOptions['tag']);
		
		$output = str_replace( "\n", "\n".$startIndent, "\n" . $this->Html->tag('div', $this->label($fieldName) . "\n" . $output, $divOptions) );
		
		return $output;
	}
	
}