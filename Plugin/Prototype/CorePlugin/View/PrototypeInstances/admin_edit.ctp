<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
//
$this->set('header', 'Add/Edit Prototype Instance');
//
$this->start('formStart');
echo $this->Form->create('PrototypeInstance', array('class' => 'editor-form', 'url' => $this->request->here));
$this->end('formStart');

echo $this->element('Prototype.js/ajax', array('plugin' => 'prototype'));
echo $this->element('Prototype.js/plugins', array('plugin' => 'prototype'));
echo $this->Html->script('Prototype.admin/edit_instance', array('inline'=>false, 'once'=>true));

//
$itemImages	= 'none';
//
$categoryImages	= 'none';
//
if (isset($this->request->data['PrototypeInstance'])) {
	//
	if (isset($this->request->data['PrototypeInstance']['item_image_type']) && $this->request->data['PrototypeInstance']['item_image_type'] != 'none') {
		//
		$itemImages = $this->request->data['PrototypeInstance']['item_image_type'];
	}
	//
	if (isset($this->request->data['PrototypeInstance']['category_image_type']) && $this->request->data['PrototypeInstance']['use_categories']) {
		//
		$categoryImages = $this->request->data['PrototypeInstance']['category_image_type'];
	}
}
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic Info', '#tab-basic'); ?></li>
<li><?php echo $this->Html->link('Categories', '#tab-categories'); ?></li>
<li><?php echo $this->Html->link('Items', '#tab-items'); ?></li>
<?php if (!empty($this->request->data['PrototypeInstance']['id']) && ($itemImages || $categoryImages)): ?>
	<li><?php echo $this->Html->link('Image Versions', '#tab-image-versions'); ?></li>
<?php endif; ?>
<?php $this->end('tabs'); ?>

<div id="tab-basic">
	<?php
	echo $this->Form->input('PrototypeInstance.id');
	echo $this->Form->input('PrototypeInstance.name', array( 
		'required' => true 
	));

	if ($this->Form->value('PrototypeInstance.id')):
		echo $this->Form->input('PrototypeInstance.slug');
	endif;

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
		'wysiwyg' => true
	));
	
	echo $this->Form->input('PrototypeInstance.layout', array(
		'type' => 'select',
		'options' => $layouts,
		'default' => 'default'
	));
	
	echo $this->Form->input('PrototypeInstance.allow_instance_view', array(
		'label' => 'Allow instance summary to be viewed.',
		'type' => 'checkbox',
		'default' => 'checked'
	));
	
	//
	if ($bannerImage):
		//show banner image options
		echo $this->Form->input('PrototypeInstance.use_page_banner_images', array('label' => 'Use Page Banner Images'));
		echo $this->Form->input('PrototypeInstance.fallback_to_instance_banner_image', array('label' => 'Use PrototypeInstance banner if categories or items do not have a banner image'));
	else:
		//if banner images are turned off for pages then tell the user they need to be turned on for this feature
		echo $this->Html->tag('h3', 'If you want to use banner images you will need to turn on Settings > Pages > "Use Page Banner Images".');
	endif;
	
	echo $this->Form->input('PrototypeInstance.category_changefreq', array('label' => 'Category change frequency (for sitemaps)'));
	echo $this->Form->input('PrototypeInstance.item_changefreq', array('label' => 'Item change frequency (for sitemaps)'));
?>
	<hr>
	<h3>Extra Fields</h3>
	<p class="emphasis">Remember that 'name', 'description' and 'slug' are enabled for every item.</p>
	<?php
	//allow the user to add custom fields for prototype items
	echo $this->element('CustomFields.admin/field_table', array(
		'fields' => isset($this->request->data['PrototypeInstanceField']) ? $this->request->data['PrototypeInstanceField'] : null,
		'alias' => 'PrototypeInstanceField',
		'model' => 'PrototypeInstance',
		'group' => 'PrototypeInstance'
	));
	?>

</div>
<div id="tab-categories">
	<?php 
	echo $this->Form->input('PrototypeInstance.use_categories');
	?>
	<div id="CategoryOptions">
		<?php
		echo $this->Form->input('allow_category_views', array('label' => 'Allow categories to be viewed individually'));
		
		echo $this->Form->input('PrototypeInstance.category_image_type', array(
			'label' => 'Image Type',
			'type' => 'select',
			'options' => $this->Prototype->imageTypes()
		));
		
		echo $this->Form->input('PrototypeInstance.category_document_type', array(
			'label' => 'Document Type',
			'type' => 'select',
			'options' => $this->Prototype->documentTypes()
		));
		
		//banner image options for categories
		if ($bannerImage) {
			//only if they are turned on for pages
			echo $this->Form->input('PrototypeInstance.use_page_banner_image_categories', array('label' => 'Use Page Banner Images'));
		} else {
			//otherwise tell them that they need them to be turned on for pages first
			echo $this->Html->tag('h3', 'If you want to use banner images you will need to turn on Settings > Pages > "Use Page Banner Images".');
		}
		?>
		<h3>Extra Fields</h3>
		<p class="emphasis">Remember that 'name' and 'slug' are enabled for every category.</p>
		<?php
		echo $this->element('CustomFields.admin/field_table', array(
			'fields' => isset($this->request->data['PrototypeCategoryField']) ? $this->request->data['PrototypeCategoryField'] : null,
			'alias' => 'PrototypeCategoryField',
			'model' => 'PrototypeInstance',
			'group' => 'PrototypeCategory'
		));
		?>
	</div>
</div>
<div id="tab-items">
	<?php
	echo $this->Form->input('PrototypeInstance.allow_item_views', array(
		'label' => 'Allow items to be viewed individually',
		'default' => 'checked'
	));
	
	echo $this->Form->input('PrototypeInstance.item_order', array(
		'label' => 'Item order',
		'options' => $this->Prototype->orderOptions()
	));

	echo $this->Form->input('PrototypeInstance.item_summary_pagination', array(
		'label' => 'Paginate item summary',
		'type' => 'checkbox'
	));
	
	echo $this->Form->input('PrototypeInstance.item_summary_pagination_limit', array(
		'label' => 'Items per page (if using pagination)',
		'type' => 'text',
		'div'=>array('id'=>'ItemsPerPageSetting')
	));

	echo $this->Form->input('PrototypeInstance.item_image_type', array(
		'label' => 'Image Type',
		'type' => 'select',
		'options' => $this->Prototype->imageTypes()
	));
	
	echo $this->Form->input('PrototypeInstance.item_document_type', array(
		'label' => 'Document Type',
		'type' => 'select',
		'options' => $this->Prototype->documentTypes()
	));
	//banner image options for prototype items
	if ($bannerImage) {
		//only show them if banner images are enabled for pages
		echo $this->Form->input('PrototypeInstance.use_page_banner_image_items', array('label' => 'Use Page Banner Images'));
	} else {
		//otherwise tell the user that they need to be enabled for pages first
		echo $this->Html->tag('h3', 'If you want to use banner images you will need to turn on Settings > Pages > "Use Page Banner Images".');
	}

	echo $this->Form->input('PrototypeInstance.name_field_label', array(
		'label' => 'Label for "name" field on item edit page',
		'default' => 'Name'
	));
	?>
	<hr>
	<h3>Feature Settings</h3>
	<?php
	echo $this->Form->input('PrototypeInstance.use_featured_items', array('type' => 'checkbox'));
	?>
	<div id="FeatureSettings">
	<?php
	echo $this->Form->input('PrototypeInstance.all_items_featured', array(
		'label' => 'Load all items as featured',
		'type' => 'checkbox'
	));
	echo $this->Form->input('PrototypeInstance.number_of_featured_items', array(
		'label' => 'Number of featured items - only used if "Load all items as featured" is not selected.',
		'type' => 'text', 
		'default' => 1
	));
	echo $this->Form->input('PrototypeInstance.autoload_featured_items_in_layouts', array('type' => 'text', 'default' => 'home'));
	?>
	</div>
	<hr>
	<h3>Extra Fields</h3>
	<p class="emphasis">Remember that 'name' and 'slug' are enabled for every item.</p>
	<?php
	//allow the user to add custom fields for prototype items
	echo $this->element('CustomFields.admin/field_table', array(
		'fields' => isset($this->request->data['PrototypeItemField']) ? $this->request->data['PrototypeItemField'] : null,
		'alias' => 'PrototypeItemField',
		'model' => 'PrototypeInstance',
		'group' => 'PrototypeItem'
	));
	?>
</div>
<?php 
if (!empty($this->request->data['PrototypeInstance']['id'])):
?>
<div id="tab-image-versions">
	<?php if ($itemImages && $this->request->data['PrototypeInstance']['item_image_type'] != 'none') : ?>
	<h3>Items</h3>
	<?php
	echo $this->Html->link('Regenerate images', array(
	 	'plugin' => 'prototype',
	 	'controller' => 'prototype_instances',
	 	'action' => 'regenerate', 
	 	'foreign_key' => $this->Form->value('PrototypeInstance.id'),
		'model' => 'PrototypeInstance',
		'group' => 'Item Image'
	));	
	
	echo $this->element('Media.admin/image_version_table', array(
		'name' => false,
		'alias' => 'ItemImageVersion',
		'group' => 'Item Image',
		'model' => 'PrototypeInstance',
		'foreign_key' => $this->request->data['PrototypeInstance']['id']
	));
	?>
	<?php endif; ?>

	<?php
	//
	if ($categoryImages && $this->request->data['PrototypeInstance']['use_categories']) :
	?>
	<h3>Categories</h3>
	<?php
	echo $this->Html->link('Regenerate images', array(
	 	'plugin' => 'prototype',
	 	'controller' => 'prototype_instances',
	 	'action' => 'regenerate', 
	 	'foreign_key' => $this->Form->value('PrototypeInstance.id'),
		'model' => 'PrototypeInstance',
		'group' => 'Category Image'
	));
	 
	echo $this->element('Media.admin/image_version_table', array(
		'name' => false,
		'alias' => 'CategoryImageVersion',
		'group' => 'Category Image',
		'model' => 'PrototypeInstance',
		'foreign_key' => $this->request->data['PrototypeInstance']['id']
	));
	?>
	<?php
	// ($categoryImages && $this->request->data['PrototypeInstance']['use_categories'])
	endif; ?>
</div>
<?php 
endif;
?>