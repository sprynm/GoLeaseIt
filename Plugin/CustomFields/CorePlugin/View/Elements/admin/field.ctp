<?php
$fieldTypes = $this->Form->fieldTypes(); 
if($alias != 'PrototypeItemField') {
	$fieldTypes['Basic']['file'] = 'File';
}


// Add the JS to the header
echo $this->Html->script('CustomFields.ajax', array('once' => true, 'inline' => false));

$divClass = 'custom-field-form';
if ($count % 2):
	$divClass .= ' odd';
else:
	$divClass .= ' even';
endif;
?>
<div class="<?php echo $divClass; ?>">
	<?php
		echo $this->Form->input($alias . '.' . $count . '.id', array(
			'type' => 'hidden', 
			'value' => isset($field['id']) ? $field['id'] : null
		));
		echo $this->Form->input($alias . '.' . $count . '.model', array(
			'type' => 'hidden', 
			'value' => $foreignModel
		));
		echo $this->Form->input($alias . '.' . $count . '.group', array(
			'type' => 'hidden', 
			'value' => $group
		));
		echo $this->Form->input($alias . '.' . $count . '.foreign_key', array(
			'type' => 'hidden', 
			'value' => isset($field['foreign_key']) ? $field['foreign_key'] : null
		));
	    echo $this->Form->input($alias . '.' . $count . '.name', array(
			'type' => 'text', 
			'value' => isset($field['name']) ? $field['name'] : null,
			'div' => 'input text'
		));

		echo $this->Form->input($alias . '.' . $count . '.label', array(
			'type' => 'text', 
			'value' => isset($field['label']) ? $field['label'] : null
		));
		// 
		echo $this->Form->input($alias . '.' . $count . '.placeholder', array(
			'type' => 'text', 
			'value' => isset($field['placeholder']) ? $field['placeholder'] : null,
			'label' => 'Placeholder <span class="optional">(Optional)</span>',
			'div' => 'input text'
		));
	?>
	<div class="mini-options">
		<div class="mini-options-tab">
			<div class="checkboxes">
				<?php
				if (!empty($this->request->data['EmailForm']) && isset($field['name']) && $field['name'] == 'email_address') {
					echo "<span><em>An email address is always required.</em></span>";
					echo $this->Form->input($alias . '.' . $count . '.required', array(
						'type' => 'hidden', 
						//'label' => false, 
						'disabled' => 'disabled',
						'value' => 1
					));
				} else {
					echo $this->Form->input($alias . '.' . $count . '.required', array(
						'type' => 'checkbox', 
						'checked' => isset($field['required']) && $field['required'] ? true : false
					));
				}
				
				echo $this->Form->input($alias . '.' . $count . '.display_label', array(
					'type' => 'checkbox', 
					'checked' => isset($field['display_label']) && $field['display_label'] ? true : false
				));
				?>
			</div>
			<div class="delete">
				<?php
				//don't show the delete button for an email_address field on email forms
				if (isset($field['id']) && !(!empty($this->request->data['EmailForm']) && isset($field['name']) && $field['name'] == 'email_address')):
					echo $this->element('Administration.index/actions/delete', array('url' => array('plugin' => 'custom_fields', 'controller' => 'custom_fields', 'action' => 'delete', 'admin' => true, $field['id'])));
				else:
					echo '&nbsp;';
				endif;
				?>
			</div>
			<div class="sort">
				<div class="rank-input">
					<?php
					echo $this->Form->input($alias . '.' . $count . '.rank', array('type' => 'hidden', 'value' => isset($field['rank']) ? $field['rank'] : null));
					?>
				</div>
				<span>Sort</span>
				<?php
				echo $this->Html->image('icons/sort.png', array('alt' => 'Drag and drop to sort', 'title' => 'Drag and drop to sort', 'class' => 'sort-icon'));
				?>
			</div>
		</div>
	</div>
	<?php


	echo $this->Form->input($alias . '.' . $count . '.type', array(
		'type' => 'select', 
		'options' => $fieldTypes,
		'value' => isset($field['type']) ? $field['type'] : null,
	));

	echo $this->Form->input($alias . '.' . $count . '.default', array(
		'type' => 'text', 
		'value' => isset($field['default']) ? $field['default'] : null
	));

	echo $this->Form->input($alias . '.' . $count . '.options', array(
		'type' => 'text', 
		'value' => isset($field['options']) ? $field['options'] : null
	));

	echo $this->Form->input($alias . '.' . $count . '.validate', array(
		'type' => 'select', 
		'options' => $this->CustomField->validateTypes(),
		'value' => isset($field['validate']) ? $field['validate'] : null,
	));

	echo $this->Form->input($alias . '.' . $count . '.validate_message', array(
		'type' => 'text', 
		'value' => isset($field['validate_message']) ? $field['validate_message'] : 'This field is required.',
		'label' => 'Validation Message'
	));
	//
	echo $this->Form->input($alias . '.' . $count . '.description', array(
		'type' => 'text',
		'label' => 'Description <span class="optional">(Optional)</span>',
		'value' => isset($field['description']) ? $field['description'] : null,
		'after' => '<span class="input-desc">Displays below field</span>'
	));
	// 
	echo $this->Form->input($alias . '.' . $count . '.autocomplete', array(
		'type' => 'text',
		//'label' => 'Optional Description (displayed underneath field)',
		'value' => isset($field['autocomplete']) ? $field['autocomplete'] : null
	));
	//
	if ($this->params->plugin == 'email_forms') :
		//
		echo $this->Form->input($alias . '.' . $count . '.css_name', array(
			'type' => 'text', 
			'value' => isset($field['css_name']) ? $field['css_name'] : null,
			'label' => 'Input CSS Name <span class="optional">(Optional)</span>',
			'div' => 'input text'
		));
		//
		echo $this->Form->input($alias . '.' . $count . '.div_css_name', array(
			'type' => 'text', 
			'value' => isset($field['div_css_name']) ? $field['div_css_name'] : null,
			'label' => 'Div CSS name <span class="optional">(Optional)</span>',
			'div' => 'input text'
		));
		// 
		echo $this->Form->input($alias . '.' . $count . '.group_with', array(
			'type' => 'text', 
			'value' => isset($field['group_with']) ? $field['group_with'] : null,
			'label' => 'Group with',
			'div' => 'input text'
		));
		// 
		echo $this->Form->input($alias . '.' . $count . '.merge_content', array(
			'type' => 'textarea', 
			'value' => isset($field['merge_content']) ? $field['merge_content'] : null,
			'label' => 'Merge Content',
			'div' => 'input textarea',
			'after' => '<span class="input-desc">Use field Name value to merge input fields into content [email_address].</span>'
		));
	// ($this->plugin == 'email_forms')
	endif;
	?>
</div>