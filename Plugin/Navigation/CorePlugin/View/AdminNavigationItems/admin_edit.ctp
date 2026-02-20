<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$this->start('formStart');
echo $this->Form->create('AdminNavigationItem', array('class' => 'editor_form', 'url' => $this->request->here));
$this->end('formStart');
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<?php $this->end('tabs'); ?>
 
<div id="tab-basic">
	<h2>Basic Information</h2>
	<?php
	echo $this->Form->input('id');
	echo $this->Form->input('name');
	echo $this->Form->input('link');
	echo $this->Form->input('Group.Group', array('default' => array(1,2)));
	?>
</div>