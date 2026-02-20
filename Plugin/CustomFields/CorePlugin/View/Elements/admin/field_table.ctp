<?php
if (!isset($group)):
	$group = null;
endif;

$this->Html->script('CustomFields.ajax', array('once' => true, 'inline' => false)); 
?>
<div class="custom-fields-wrapper">
<?php
if (!empty($fields)):
	echo $this->CustomField->adminFields($fields, $alias, $model, $group);
endif;
?>
</div>
<?php
echo $this->CustomField->addNew($alias, $model, array('group' => $group));
?>
