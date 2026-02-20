<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$this->set('header', 'Add/Edit Setting');

$this->start('formStart');
echo $this->Form->create('Setting', array('class' => 'editor-form', 'url' => $this->request->here, 'type' => 'file'));
$this->end('formStart');
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<?php $this->end('tabs'); ?>


<div id="tab-basic">
	<?php
	echo $this->Form->input('Setting.id');
	echo $this->Form->input('Setting.key');
//
	echo $this->Form->input('Setting.value', array(
		'label' => 'Current value'
	));

//
	$hideOrNot = (isset($this->request->data) && @$this->request->data['Setting']['type'] == 'image') ? '': ' style="display: none"';
//
	echo '<div id="mediaImage"' . $hideOrNot . '>' . $this->element('Media.attachments', array(
		'assocAlias' => 'Image', 
		'model' => 'Setting', 
		'foreignKey' => $this->Form->value('Setting.id'), 
		'single' => true
	)) . '</div>';
//
	$hideOrNot = (isset($this->request->data) && @$this->request->data['Setting']['type'] == 'document') ? '': ' style="display: none"';
//
	$docLink	= (isset($this->request->data) && !empty($this->request->data['Document'][0])) ? $this->Html->link( $this->request->data['Document'][0]['basename'], $this->Media->transferUrl($this->request->data['Document'][0]), array( 'target' => '_blank' ) ): '';
//
	echo '<div id="mediaDocument"' . $hideOrNot . '>' . $docLink . $this->element('Media.attachments', array(
		'assocAlias' => 'Document', 
		'model' => 'Setting', 
		'foreignKey' => $this->Form->value('Setting.id'), 
		'single' => true,
		'validateType' => 'document', 
		'attachmentType' => 'document'
	)) . '</div>';

	echo $this->Form->input('Setting.title', array(
		'label' => 'Optional title'
	));

	echo $this->Form->input('Setting.description');

	echo $this->Form->input('Setting.type', array(
		'type' => 'select',
		'options' => $this->Form->fieldTypes(),
		'default' => 'text'
	));

	echo $this->Form->input('Setting.editable', array(
		'description' => 'Setting cannot be edited if left unchecked'
	));
	echo $this->Form->input('Setting.super_admin', array(
		'description' => 'If checked, setting can only be edited by super administrators'
	));
	echo $this->Form->input('Setting.options', array(
		'description' => 'Options for select and radio types, in this format: (Option),(Option),(An Option)'
	));
	?>
</div>
<script>
//
$(document).ready(function() {
//
	$(document).on('change', '#SettingType', function() {
	//
		if($(this).val() == 'image')
		{
		//
			$('#mediaImage').show();
		//
			return false;
		}
	//
		if($(this).val() == 'document')
		{
		//
			$('#mediaDocument').show();
		//
			return false;
		}
	//
		$('#mediaImage, #mediaDocument').hide();
	});

});
</script>