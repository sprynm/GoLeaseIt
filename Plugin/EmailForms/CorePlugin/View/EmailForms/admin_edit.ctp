<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
echo $this->Html->css('EmailForms.email_forms', null, array('inline' => false));
$this->Html->css('TreeSort.sort', null, array('inline' => false));

$this->start('formStart');
echo $this->Form->create('EmailForm', array('class' => 'editor-form', 'url' => $this->request->here));
$this->end('formStart');

$this->Html->script('/tree_sort/js/jquery.ui.nestedSortable', array('inline' => false));
echo $this->Html->script('EmailForms.ajax', array('inline' => false)); 
echo $this->Html->css('EmailForms.admin/email_forms'); 
?>
<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<li><?php echo $this->Html->link('Form Fields', '#tab-fields'); ?></li>
<li><?php echo $this->Html->link('Group & Field Order', '#tab-order'); ?></li>
<li><?php echo $this->Html->link('Email Format', '#tab-format'); ?></li>
<li><?php echo $this->Html->link('Auto Response', '#tab-auto-response'); ?></li>
<?php $this->end('tabs'); ?>

<div id="tab-basic">
	<?php
	echo $this->element('EmailForms.admin/basic_fields');
	?>
</div>
<div id="tab-fields">
	<?php echo $this->element('EmailForms.admin/tab_fields', array('groups' => $this->request->data['EmailFormGroup'])); ?>
</div>
<div id="tab-order">
	<p>Drag the form groups and fields into any order you'd like, then hit "Save Order" at the bottom to save.</p>
	<p><strong>Hitting the "Save Order" button will save only the order information. Any unsaved changes in other tabs will be lost.</strong></p>
	<div class="group-order">
		<?php 
		echo $this->Tree->generate(
			$this->request->data['EmailFormGroup'], 
			array('plugin' => 'email_forms', 'element' => 'admin/group_tree', 'model' => 'EmailFormGroup', 'type' => 'ol', 'ulClass' => 'nested-sortable-', 'liClass' => null, 'liId' => 'list_')
		);
		?>
	</div>
	<?php echo $this->element('TreeSort.ajax_save'); ?>
</div>
<div id="tab-format">
	<?php
	echo $this->Form->input('EmailForm.subject_template', array('default' => '%website_name% %form_name% Inquiry'));
	echo $this->Form->input('EmailForm.content_template', array('default' => '%all%'));
	?>
</div>
<div id="tab-auto-response">
	<?php
	echo $this->Form->input('EmailForm.auto_response_enabled', array('type' => 'checkbox'));
	echo $this->Form->input('EmailForm.auto_response_subject_template', array('default' => '%website_name% confirmation for %form_name%'));
	echo $this->Form->input('EmailForm.auto_response_content_template');
	?>
</div>