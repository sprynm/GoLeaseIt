<?php $this->extend('Administration.Common/index-page'); ?>
<?php $this->set('header', 'Add/Edit Custom Fields: ' . Inflector::humanize($model)); ?>
<?php
$this->start('formStart');
	echo $this->Form->create('CustomField', array('class' => 'editor-form', 'url' => $this->request->here));
$this->end('formStart');

$this->start('formEnd');
	echo $this->Form->end('Save');
$this->end('formEnd');
?>

<?php
echo $this->element('CustomFields.admin/field_table', array(
	'fields' => $customFields,
	'alias' => $alias
));
?>

