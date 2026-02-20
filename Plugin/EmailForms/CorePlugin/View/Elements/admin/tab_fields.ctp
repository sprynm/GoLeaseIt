<?php
if (!isset($groups) || !is_array($groups)):
	$groups = array();
endif;

foreach ($groups as $groupNum => $group): 
	echo $this->element('EmailForms.admin/group', array(
		'group' => $group,
		'groupNum' => $groupNum,
	));
endforeach;
?>
<?php 
echo $this->Html->link(
	'Add a group', 
	array('plugin' => 'email_forms', 'controller' => 'email_forms', 'action' => 'new_group', 'admin' => true), 
	array('class' => 'add-new-group'));
?>