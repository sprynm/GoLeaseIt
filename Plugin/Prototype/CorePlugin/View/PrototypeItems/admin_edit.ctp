<?php $this->extend('Administration./Common/edit-page'); ?>
<?php
$this->set('header', 'Add/Edit ' . Inflector::singularize($_instance['PrototypeInstance']['name']));

$this->start('formStart');
echo $this->Form->create('PrototypeItem', array('class' => 'editor-form', 'type' => 'file', 'url' => $this->request->here));
$this->end('formStart');

// Sort JS
$this->Html->script('sort', array('inline' => false, 'once' => true));

$photos = ($_instance['PrototypeInstance']['item_image_type'] != 'none');
$documents = ($_instance['PrototypeInstance']['item_document_type'] != 'none');

$customFields = $this->CustomField->fieldList('PrototypeInstance', $_instance['PrototypeInstance']['id'], 'PrototypeItem');
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
    <?php
    echo $this->Form->input('PrototypeItem.id', array('type' => 'hidden'));
    echo $this->Form->input('PrototypeItem.prototype_instance_id', array('type' => 'hidden', 'value' => $_instance['PrototypeInstance']['id']));

    if (
    	$_instance['PrototypeInstance']['use_featured_items']
    	&& 
    	!$_instance['PrototypeInstance']['all_items_featured']
    ):
        echo $this->Form->input('featured');
    endif;
    
    $nameLabel = 'Name';
    if (isset($_instance['PrototypeInstance']['name_field_label']) && !empty($_instance['PrototypeInstance']['name_field_label'])) {
        $nameLabel = $_instance['PrototypeInstance']['name_field_label'];
    }
    echo $this->Form->input('PrototypeItem.name', array(
        'label' => $nameLabel
				, 'required' => true
    ));

    if ($_instance['PrototypeInstance']['use_categories']):
      //echo $this->Form->input('PrototypeCategory.PrototypeCategory', array('label' => 'Category'));
						
			//show category checkbox tree
			echo $this->Form->checkboxTree('PrototypeCategory.PrototypeCategory', array('div'=>array('class'=>'product-categories')));
    endif;

    /**
     * For the head_title, display it as a hidden field if this is a non-super admin because they shouldn't
     * be editing it.
     */
     if (AccessControl::inGroup('Super Administrator')):
        echo $this->Form->input('PrototypeItem.head_title', array(
            'label' => 'Head Title', 
            'description' => 'The title of the item as it appears in the browser window'
        ));
		
		echo $this->Form->input('PrototypeItem.slug', array(
            'label' => 'Slug', 
            'description' => 'The url slug - please make good choices!'
        ));
    else:
        echo $this->Form->input('PrototypeItem.head_title', array(
            'type' => 'hidden'
        ));
    endif;

    /**
     * Display the 'published at' link if this isn't a new item. Detail page if the instance allows, otherwise the summary.
     */
    if ($this->Publishing->isPublished($this->Form->value('PrototypeItem.id'), 'Prototype.PrototypeItem')):
        if ($_instance['PrototypeInstance']['allow_item_views']):
            $publishedUrl = $this->ModelLink->link('Prototype.PrototypeItem', $this->request->data['PrototypeItem']['id']);
        else:
            $publishedUrl = $this->ModelLink->link('Prototype.PrototypeInstance', $_instance['PrototypeInstance']['id']);
        endif;
        
        echo '<p>Published at: ' . $this->Html->link(Router::url($publishedUrl), $publishedUrl) . '</p>';
    endif;

    foreach ((array)$customFields as $field):
			if (!empty($this->request->data['PrototypeItem']['id'])):
				$field['CustomField']['foreign_key'] = $this->request->data['PrototypeItem']['id'];
			endif;
      echo $this->CustomField->inputField($field['CustomField']);
    endforeach;
    ?>
</div>

<?php if ($photos): ?>
<div id="tab-images">
    <?php
    echo $this->element('Media.attachments', array(
        'assocAlias' => 'Image', 
        'model' => 'PrototypeItem', 
        'foreignKey' => $this->Form->value('PrototypeItem.id'), 
        'single' => ($_instance['PrototypeInstance']['item_image_type'] == 'single')
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
			'assocAlias'	=> 'ItemBannerImage'
			, 'model'	=> 'PrototypeItem'
			, 'group'	=> 'Item Banner Image'
			, 'foreignKey'	=> $this->Form->value('PrototypeItem.id')
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
        'model' => 'PrototypeItem', 
        'foreignKey' => $this->Form->value('PrototypeItem.id'), 
        'single' => ($_instance['PrototypeInstance']['item_document_type'] == 'single'),
        'validateType' => 'Document' 
    ));
    ?>
</div>
<?php endif; ?>