<?php 
$this->set('header', 'Edit Default Page Fields');
$this->set('showAdminTabs', false);
$this->set('saveReturn', false);
$this->extend('Administration.Common/edit-page');
$this->start('formStart');
echo $this->Form->create('Page', array('class' => 'editor-form', 'url' => $this->request->here, 'type' => 'file'));
$this->end('formStart');

//default fields for pages
echo $this->element('CustomFields.admin/field_table', array(
	'fields' => isset($this->request->data['PageField']) ? $this->request->data['PageField'] : null,
	'alias' => 'PageField',
	'model' => 'Page',
	'group' => null
));
?>