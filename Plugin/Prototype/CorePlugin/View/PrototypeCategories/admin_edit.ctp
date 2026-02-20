<?php $this->extend('Administration./Common/edit-page'); ?>
<?php
$this->set('header', 'Add/Edit ' . Inflector::singularize($_instance['PrototypeInstance']['name']) . ' Category');

$this->start('formStart');
echo $this->Form->create('PrototypeCategory', array('class' => 'editor-form', 'type' => 'file', 'url' => $this->request->here));
$this->end('formStart');

// Sort JS
$this->Html->script('sort', array('inline' => false, 'once' => true));

$photos = ($_instance['PrototypeInstance']['category_image_type'] != 'none');
$documents = ($_instance['PrototypeInstance']['category_document_type'] != 'none');

$customFields = $this->CustomField->fieldList('PrototypeInstance', $_instance['PrototypeInstance']['id'], 'PrototypeCategory');
?>


<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic Info', '#tab-basic'); ?></li>

<?php if ($photos): ?>
    <li><?php echo $this->Html->link('Images', '#tab-images'); ?></li>
<?php endif; ?>
<?php if ($bannerImage): ?>
<li><?php echo $this->Html->link('Banner Image', '#tab-banner-image'); ?></li>
<?php endif; ?>
 <?php if ($documents): ?>
    <li><?php echo $this->Html->link('Document(s)', '#tab-documents'); ?></li>
<?php endif; ?>
<?php $this->end('tabs'); ?>


<div id="tab-basic">
    <h2>Basic Information</h2>
	<?php
    echo $this->Form->input('PrototypeCategory.id', array('type' => 'hidden'));
    echo $this->Form->input('PrototypeCategory.prototype_instance_id', array('type' => 'hidden', 'value' => $_instance['PrototypeInstance']['id']));
		echo $this->Form->input('PrototypeCategory.name', array(
			'required' => true
		));

    /**
     * For the head_title, display it as a hidden field if this is a non-super admin because they shouldn't
     * be editing it.
     */
    if (AccessControl::inGroup('Super Administrator')):
        echo $this->Form->input('PrototypeCategory.head_title', array(
            'label' => 'Head Title', 
            'description' => 'The title of the category as it appears in the browser window'
        ));
    else:
        echo $this->Form->input('PrototypeCategory.head_title', array(
            'type' => 'hidden'
        ));
    endif;

    echo $this->Form->input('PrototypeCategory.parent_id', array(
        'label' => 'Parent',
        'type' => 'select',
        'options' => $categories,
        'empty' => '- No Parent -'
    ));
		
		echo $this->Form->input('PrototypeCategory.slug');

    /**
     * Display the 'published at' link if this isn't a new item. Detail page if the instance allows, otherwise the summary.
     */
    if ($this->Publishing->isPublished($this->Form->value('PrototypeCategory.id'), 'Prototype.PrototypeCategory')):
        $publishedUrl = $this->ModelLink->link('Prototype.PrototypeCategory', $this->request->data['PrototypeCategory']['id']);
        echo '<p>Published at: ' . $this->Html->link(Router::url($publishedUrl), $publishedUrl, array('target'=>'_blank')) . '</p>';
    endif;

    foreach ((array)$customFields as $field): 
        echo $this->CustomField->inputField($field['CustomField']);
    endforeach;
	?>
</div>

<?php if ($photos): ?>
<div id="tab-images">
    <?php
    echo $this->element('Media.attachments', array(
        'assocAlias' => 'Image', 
        'model' => 'PrototypeCategory', 
        'foreignKey' => $this->Form->value('PrototypeCategory.id'), 
        'single' => ($_instance['PrototypeInstance']['category_image_type'] == 'single')
    ));
    ?>
</div>
<?php endif; ?>

<?php if ($bannerImage): ?>
<div id="tab-banner-image">
<?php
//	
	echo $this->element(
		'Media.single'
		, array(
			'assocAlias'	=> 'CategoryBannerImage'
			, 'model'	=> 'PrototypeCategory'
			, 'group'	=> 'Category Banner Image'
			, 'foreignKey'	=> $this->Form->value('PrototypeCategory.id')
			, 
		)
	);
?>
</div>
<?php endif; ?>

<?php if ($documents): ?>
<div id="tab-documents">
    <?php
    echo $this->element('Media.attachments', array(
        'assocAlias' => 'Document', 
        'model' => 'PrototypeCategory', 
        'foreignKey' => $this->Form->value('PrototypeCategory.id'), 
        'single' => ($_instance['PrototypeInstance']['category_document_type'] == 'single'), 'validateType' => 'document'
    ));
    ?>
</div>
<?php endif; ?>