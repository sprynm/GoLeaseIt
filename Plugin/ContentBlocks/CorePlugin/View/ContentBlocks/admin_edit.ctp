<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$this->start('formStart');
echo $this->Form->create('ContentBlock', array('class' => 'editor-form', 'url' => $this->request->here));
$this->end('formStart');
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<?php $this->end('tabs'); ?>

<div id="tab-basic">
	<h2>Basic Info</h2>
	<?php
	echo $this->Form->input('ContentBlock.id');
	echo $this->Form->input('ContentBlock.name');
	echo $this->Form->input('ContentBlock.content', array(
		'wysiwyg' => true
	));

	if (AccessControl::inGroup('Super Administrator')):
		echo $this->Form->input('ContentBlock.super_admin', array(
			'label' => 'Only visible to super administrators'
		));
	endif;
	?>
</div>
