<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$prototypes = $this->Prototype->listAliases();
$plugins = $this->App->listAliases();	

$unacceptableNames = strtolower(json_encode(array_merge($prototypes, $plugins))); 

$this->start('formStart');
echo $this->Form->create('Page', array('class' => 'editor-form', 'url' => $this->request->here));
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
<?php if (AccessControl::inGroup('Super Administrator')): ?>
<li><?php echo $this->Html->link('Advanced', '#tab-advanced'); ?></li>
<li><?php echo $this->Html->link('Super Admin', '#tab-super'); ?></li>
<?php endif; ?>
<?php $this->end('tabs'); ?>

<div id="tab-basic">
	<h2>Basic Info</h2>
	<?php
	echo $this->Form->input('Page.id');

	echo $this->Form->input('Page.page_heading', array(
		'label' => 'Page Title',
		'description' => 'The title of the page'
	));
	
	echo $this->Form->input('Page.content', array(
		'wysiwyg' => true
	));
	?>
</div>

<?php if (AccessControl::inGroup('Super Administrator')): ?>
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

		echo $this->Form->input('Page.parent_id', array(
			'label' => 'Parent Page',
		 	'type' => 'select',
		 	'options' => $pages,
		 	'empty' => '- No Parent Page -'
		));

		echo $this->Form->input('Page.layout', array(
		   'label' => 'Layout Template',
		   'type' => 'select',
		   'option' => $layouts,
		   'default' => 'default'
		));

		echo $this->Form->input('Page.extra_header_code', array(
		 'description' => 'Code inserted just before the closing /head tag'
		));
		echo $this->Form->input('Page.extra_footer_code', array(
		 'description' => 'Code inserted just before the closing /body tag)'
		));
		?>
	</div>

	<div id="tab-super">
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
		'label' => 'Exclude from Sitemap XML Page'
	));
	?>
	</div>
<?php endif; ?>