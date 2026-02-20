<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$this->set('header', 'Settings');

//include js for handling setting key display
$this->append("script");
echo $this->Html->script("Settings.settings");
$this->end();

$this->start('actionLinks');
echo $this->AdminLink->link('Add/Edit Settings', array('controller' => 'key_index'));
$this->end('actionLinks');

$this->start('formStart');
echo $this->Form->create('Setting', array('class' => 'editor-form', 'type' => 'file', 'url' => $this->request->here));
$this->end('formStart');
?>

<?php 
$this->start('tabs'); 
echo $this->Settings->adminTabs($settings);
$this->end('tabs');
?>

<?php
echo $this->Settings->adminForm($settings);
?>

<?php $this->start('formEnd'); ?>
<div class="admin-submit">
<?php
echo $this->Form->submit('Save Settings', array('div' => false));
?>
</div>
<?php $this->end('formEnd'); ?>