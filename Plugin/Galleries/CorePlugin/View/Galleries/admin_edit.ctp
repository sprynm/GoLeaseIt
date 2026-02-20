<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$this->start('formStart');
echo $this->Form->create('Gallery', array('class' => 'editor-form', 'type' => 'file', 'url' => $this->request->here));
$this->end('formStart');
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<li><?php echo $this->Html->link('Images', '#tab-images'); ?></li>
<?php $this->end('tabs'); ?>

<div id="tab-basic">
	<h2>Basic Info</h2>
	<?php
	echo $this->Form->input('Gallery.id');
	echo $this->Form->input('Gallery.name');
	if( AccessControl::inGroup( 'Super Administrators' ) ) { echo $this->Form->input('Gallery.type'); }
	?>
</div>
<div id="tab-images">
	<h2>Images</h2>
	<?php
	echo $this->element('Media.attachments', array('assocAlias' => 'Image', 'model' => 'Gallery', 'foreignKey' => $this->Form->value('Gallery.id')));
	?>
</div>