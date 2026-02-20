<?php 
$this->extend('Administration.Common/edit-page'); 

if (isset($this->request->data['NavigationMenu']['id']) && $this->request->data['NavigationMenu']['id']):
	$new = false;
	$this->set('header', $this->request->data['NavigationMenu']['name']);
else:
	$new = true;
	$this->set('header', 'Add/Edit Navigation Menu');
endif;
//
$this->Html->css('/tree_sort/css/sort.css', null, array('inline' => false));
//
$canSort = false;
if (AccessControl::inGroup(array('Super Administrator', 'Administrator'))):
	$canSort = true;
	$this->Html->script('/tree_sort/js/jquery.ui.nestedSortable', array('inline' => false));
	echo $this->element('TreeSort.js/sort', array(
		'url' => Router::url(array('plugin' => 'navigation', 'controller' => 'navigation_menu_items', 'action' => 'reorder', 'admin' => true))
	));
endif;
?>

<?php
$this->start('formStart');
echo $this->Form->create('NavigationMenu', array('class' => 'editor_form', 'url' => $this->request->here));
$this->end('formStart');
?>

<?php $this->start('tabs'); ?>



<?php if (!$new): ?>

<li><?php echo $this->Html->link('Menu Items', '#tab-items'); ?></li>

<?php else: ?>

<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>

<?php endif; ?>

<?php $this->end('tabs'); ?>


<?php if (!$new): ?>
<div id="tab-items">

<?php
	echo $this->Form->input('NavigationMenu.id');
	echo $this->Form->input('NavigationMenu.name');
	
	echo $this->Form->input('NavigationMenu.sitemap', array('label' => 'Include in sitemap'));
	echo $this->Form->input('NavigationMenu.sitemap_display_label', array('label' => 'Include menu name in sitemap'));
?>
	
	<p><strong><?php echo $this->Html->link('Add a New Menu Item', array('controller' => 'navigation_menu_items', 'action' => 'edit', $this->request->data['NavigationMenu']['id'])); ?></strong></p>
	<?php 
	echo $this->Tree->generate($navigationMenuItems, array('plugin' => 'navigation', 'element' => 'admin/menu_items_index', 'model' => 'NavigationMenuItem', 'type' => 'ol', 'ulClass' => 'nested-sortable-', 'liClass' => null, 'liId' => 'list_'));
	if ($canSort):
		echo $this->element('TreeSort.ajax_save');
	endif;
	?>
</div>
<?php else: ?>

<div id="tab-basic">
	<?php
	echo $this->Form->input('NavigationMenu.id');
	echo $this->Form->input('NavigationMenu.name');
	echo $this->Form->input('NavigationMenu.sitemap', array('label' => 'Include in sitemap'));
	?>
</div>


<?php endif; ?>