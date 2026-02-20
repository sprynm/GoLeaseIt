<?php $this->extend('Administration.Common/edit-page'); ?>
<?php // 
// 
$prototypes = $this->Prototype->listAliases();
//
$plugins = $this->App->listAliases();	
//
$unacceptableNames = strtolower(json_encode(array_merge($prototypes, $plugins))); 
//
$this->Html->script('Versioning.jquery.autosave', array('inline' => false));
//
$previewClass = 'page-preview';
//
if (!isset($this->request->data['Page']['id'])):
	$previewClass .= ' unchanged';
	$previewUrl = Router::url(array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'preview', 'admin' => true));
else:
	$previewUrl = Router::url(array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'preview', $this->request->data['Page']['id'], 'admin' => true));
endif;


$this->Html->scriptBlock('
	$(document).ready(function() {
		$("form.autosave").autosave({
			interval: 60000,
			idField: "#PageId",
			previewLink: ".page-preview",
			data: $("input,select,textarea").not(function (){ return $(this).closest(".autosave-ignore").length; })
		});
	});',
	array('inline' => false)
);

$this->start('formStart');
echo $this->Form->create('Page', array('class' => 'editor-form autosave', 'url' => $this->request->here, 'type' => 'file'));
$this->end('formStart');
?>

<script>
$(document).ready( function() { 
	$('#PagePageHeading').keyup(function() {
		
	  var unacceptableNames =  <?php echo $unacceptableNames; ?>;

		if( $.inArray($('#PagePageHeading').val().toLowerCase().trim(), unacceptableNames) >= 0 ) {
		  $('.admin-submit input[type="submit"]').attr('disabled','disabled');
		  $('.admin-submit input[type="submit"]').css('background-color', 'gray');
		  $('#remove').remove();
		  $('#PagePageHeading').after('<div id="remove" class="error-message">Please don\'t give a page the same name as a plugin or prototype instance.</div>');
		} 
		
		if($.inArray($('#PagePageHeading').val().toLowerCase().trim(), unacceptableNames) < 0 ) {
		  $('.admin-submit input[type="submit"]').removeAttr('disabled');
		  $('.admin-submit input[type="submit"]').removeAttr('style');
		  $('#remove').remove();
		}
	  
	}); 
});
</script>



<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<?php if ($this->Settings->show('Pages.PageOptions.banner_image')): ?>
<li><?php echo $this->Html->link('Banner Image', '#tab-images'); ?></li>
<?php endif; ?>
<?php if (AccessControl::inGroup('Super Administrator')): ?>
<li><?php echo $this->Html->link('Schema Code', '#tab-schema'); ?></li>
<li><?php echo $this->Html->link('Advanced', '#tab-advanced'); ?></li>
<li><?php echo $this->Html->link('Super Admin', '#tab-super'); ?></li>
<?php endif; ?>
<?php $this->end('tabs'); ?>

<p id="time-saved"></p>
<p><?php // 
//
if ($this->Form->value('Page.id')):
	//
	echo $this->Html->link('Preview Page', $previewUrl, array('class' => $previewClass, 'target' => '_blank'));
	//
	echo $this->Html->link('View Live Page', $this->ModelLink->link('Pages.Page', $this->request->data['Page']['id']), array('target' => '_blank'));
//
endif; 
?></p>
<div id="tab-basic">
	<h2>Basic Info</h2>
	<?php
	echo $this->Form->input('Page.id');

	echo $this->Form->input('Page.page_heading', array(
		'label' => 'Page Title',
		'description' => 'The title of the page'
	));

	if (!AccessControl::inGroup('Super Administrator')):
		echo $this->Form->input('Page.title', array(
			'type' => 'hidden'
		));
	endif;
	//
	$labelContent	= 'Content';
	//
	if (isset($emailFormMergeFields) && !empty($emailFormMergeFields)) :
		//
		$labelContent .= $this->Html->tag('p', 'Potential merge fields for use within the Content: ' . $this->Text->toList($emailFormMergeFields) . '. Be aware that these fields are dynamic and may change.');
	// (isset($emailFormMergeFields) && !empty($emailFormMergeFields))
	endif;
	//
	echo $this->Form->input('Page.content', array(
		'wysiwyg' => true
		, 'label'	=> $labelContent
		,
	));
  
  //print out the custom input fields for this page
  if (!empty($this->request->data['Page']['id'])){    
    $customFields = $this->CustomField->fieldList('Page', $this->request->data['Page']['id']);
		
		//set the foreign_key for each of the default fields to be the id of this page
		foreach ($customFields as $key => $field) {
			if (empty($field['CustomField']['foreign_key'])){				
				$customFields[$key]['CustomField']['foreign_key'] = $this->request->data['Page']['id'];
			}
		}
		
    if (!empty($customFields)) {      
      foreach ((array)$customFields as $field){
        echo $this->CustomField->inputField($field['CustomField']);
      }
    }
  }
	?>
</div>

<?php if ($this->Settings->show('Pages.PageOptions.banner_image')): ?>
<div id="tab-images">
<?php
// 
$imageItem	= isset($this->request->data['Image'][0])
		? $this->request->data['Image']
		: array();
// 
echo $this->element(
	'Media.single_uploadify'
	, array(
		'assocAlias'	=> 'Image'
		, 'model'	=> 'Page'
		, 'group'	=> 'Image'
		, 'foreignKey'	=> $this->Form->value('Page.id')
		, 'imageItem'	=> array('Image' => $imageItem)
		, 
	)
);
?>
</div>
<?php endif; ?>

<?php if (AccessControl::inGroup('Super Administrator')): ?>
	<div id="tab-schema">
	<?php
	// 
	$schema_code	= isset($this->request->data['Page']['schema_code']) && !empty($this->request->data['Page']['schema_code'])
			? $this->request->data['Page']['schema_code']
			: '
<script type="application/ld+json">
{
  "@context" : "http://schema.org",
  "@type" : "LocalBusiness",
  "name" : ' . json_encode($this->Settings->show('Site.Contact.name')) . '
}
</script>';
	//
	echo $this->Form->input('Page.schema_code', array('value' => $schema_code, 'after' => '<span class="input-desc">Paste your schema code here.</span>'))
		. "\n"
		. $this->Html->tag(
			'p'
			, $this->Html->link(
				'Schema Markup Generator'
				, 'https://technicalseo.com/tools/schema-markup-generator/'
				, array(
					'target'	=> '_blank'
					,
				)
			)
		);
	?>
	</div>
	<div id="tab-advanced">
		<h2>Advanced Info</h2>
		<?php
		echo $this->Form->input('Page.published', array(
			'label' => 'Published? (visible on the website)',
			'default' => '1'
		));
		echo $this->Form->input('Page.title', array(
			'label' => 'Head Title', 
			'description' => 'The title of the page as it appears in the browser window'
		));
		echo $this->Form->input('Page.override_title_format', array(
			'label' => 'Override Title Format',
			'description' => 'If checked, then the "Title Separator" and "Common Head Title" site settings will NOT be appended to the "Head Title" field.'
		));

		echo $this->Form->input('Page.path', array('type' => 'hidden'));

		if (!$this->Form->value('Page.id') || !$this->request->data['Page']['action_map']):
			echo $this->Form->input('Page.parent_id', array(
				'label' => 'Parent Page',
			 	'type' => 'select',
			 	'options' => $pages,
			 	'empty' => '- No Parent Page -'
			));
		endif;
		
		$slug = (!empty($this->request->data['Page']['slug'])) ? $this->request->data['Page']['slug'] : ' ';
		
		if (!empty($this->request->data) && (!isset($this->request->data['Page']['action_map']) || !$this->request->data['Page']['action_map'])):
			echo $this->Form->input('Page.slug', array(
				'label' => 'Published at: ' . $path,
				'value' => $slug,
				'autocomplete' => 'off'
			));

		elseif (!empty($this->request->data) && $this->request->data['Page']['action_map']):
			echo $this->Form->input('Page.path', array(
				'label' => 'Published at: ',
				'disabled' => true,
				'value' => Router::url($this->ModelLink->link('Pages.Page', $this->request->data['Page']['id']))
			));
		endif;

		echo $this->Form->input('Page.layout', array(
		   'label' => 'Layout Template',
		   'type' => 'select',
		   'option' => $layouts,
		   'default' => 'default'
		));
		
		echo $this->Form->input('Group.Group', array(
			'label' => 'Groups allowed to view this page',
			'type' => 'select',
			'multiple' => 'checkbox',
			'options' => $groups
    ));
		
		echo $this->Form->input('Page.extra_header_code', array(
		 'description' => 'Code inserted just before the closing /head tag'
		));
		echo $this->Form->input('Page.extra_footer_code', array(
		 'description' => 'Code inserted just before the closing /body tag)'
		));
		?>
	</div>

	<div id="tab-super" class="autosave-ignore">
	<h2>Super Admin Settings</h2>
	<?php
	echo $this->Form->input('Page.protected', array(
		'label' => 'Protect from deletion'
	));
	echo $this->Form->input('Page.plugin');
	echo $this->Form->input('Page.controller');
	echo $this->Form->input('Page.action');
	echo $this->Form->input('Page.extra');
	echo $this->Form->input('Page.internal_name', array(
		'label' => 'Internal Name', 
		'description' => 'If used, displayed on the page contents admin index page'
	));
	echo $this->Form->input('Page.exclude_sitemap', array(
		'label' => 'Exclude from Sitemap XML Page and robots.txt'
	));
  
  //custom fields for pages
  ?>
  <h3>Custom Fields</h3>
  <?php
	echo $this->element('CustomFields.admin/field_table', array(
		'fields' => isset($this->request->data['PageField']) ? $this->request->data['PageField'] : null,
		'alias' => 'PageField',
		'model' => 'Page',
		'group' => null
	));
	
	?>
	</div>
<?php endif; ?>