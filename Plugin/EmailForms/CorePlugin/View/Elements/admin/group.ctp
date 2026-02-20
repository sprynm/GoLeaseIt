
<div class="form-group">
<h3>Group:
	<?php
	echo $this->Form->input('EmailFormGroup.' . $groupNum . '.id', array(
		'value' => isset($group['id']) ? $group['id'] : null,
		'type' => 'hidden'
	));
	echo $this->Form->input('EmailFormGroup.' . $groupNum . '.email_form_id', array(
		'value' => isset($group['email_form_id']) ? $group['email_form_id'] : null,
		'type' => 'hidden'
	));
	echo $this->Form->input('EmailFormGroup.' . $groupNum .'.name', array(
		'value' => $group['name'],
		'label' => false
	));
	?>
	</h3>
	<?php
	echo $this->element('CustomFields.admin/field_table', array(
		'fields' => isset($group['EmailFormField']) ? $group['EmailFormField'] : null,
		'alias' => 'EmailFormGroup.' . $groupNum . '.EmailFormField',
		'model' => 'EmailFormGroup',
		'group' => null
	));
	?>
<?php
if (isset($group['id']) && $group['id']):
	echo $this->Html->link(
		'Delete this group',
		array('plugin' => 'email_forms', 'controller' => 'email_form_groups', 'action' => 'delete', 'admin' => true, $group['id']),
		array('class' => 'delete-group'),
		__('Are you sure? All form field in this group will also be deleted.')
	);
endif;
?>
</div>