<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
//
$customFields = $this->CustomField->fieldList('PrototypeInstance', $_instance['PrototypeInstance']['id'], 'PrototypeInstance');
//
$this->set('header', $instance['PrototypeInstance']['name'] . ' Introduction');
//
$this->start('formStart');
echo $this->Form->create('PrototypeInstance', array('class' => 'editor-form', 'url' => $this->request->here, 'type' => 'file'));
$this->end('formStart');
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic Info', '#tab-basic'); ?></li>
<?php if ($bannerImage): ?>
<li><?php echo $this->Html->link('Banner Image', '#tab-banner-image'); ?></li>
<?php endif; ?>
<?php $this->end('tabs'); ?>

<div id="tab-basic">
	<?php
	echo $this->Form->input('PrototypeInstance.id');

    /**
     * For the head_title, display it as a hidden field if this is a non-super admin because they shouldn't
     * be editing it.
     */
    if (AccessControl::inGroup('Super Administrator')):
        echo $this->Form->input('PrototypeInstance.head_title', array(
            'label' => 'Head Title', 
            'description' => 'The title of the instance as it appears in the browser window'
        ));
		echo $this->Form->input('PrototypeInstance.override_title_format', array(
			'label' => 'Override Title Format',
			'description' => 'If checked, then the "Title Separator" and "Common Head Title" site settings will NOT be appended to the "Head Title" field.'
		));
    else:
        echo $this->Form->input('PrototypeInstance.head_title', array(
            'type' => 'hidden'
        ));
    endif;

	echo $this->Form->input('PrototypeInstance.description', array(
		'wysiwyg' => true,
		'description' => 'Displays at top of page.'
	));
		echo $this->Form->input('PrototypeInstance.footer_text', array(
		'wysiwyg' => true,
		'description' => 'Displays at bottom of page.'
	));

    foreach ((array)$customFields as $field): 
        echo $this->CustomField->inputField($field['CustomField']);
    endforeach;

	?>
</div>

<?php if ($bannerImage): ?>
<div id="tab-banner-image">
<?php
//	
	echo $this->element(
		'Media.single'
		, array(
			'assocAlias'	=> 'Image'
			, 'model'	=> 'PrototypeInstance'
			, 'group'	=> 'Instance Banner Image'
			, 'foreignKey'	=> $this->Form->value('PrototypeInstance.id')
			, 'version'	=> 'banner-lrg'
			, 
		)
	);
?>
</div>
<?php endif; ?>