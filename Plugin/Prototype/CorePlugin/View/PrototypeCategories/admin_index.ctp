<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', $this->Prototype->fetch('PrototypeInstance.name') . ' Categories');
$this->Html->css('TreeSort.sort', null, array('inline' => false));

$canSort = AccessControl::isAuthorized(array('plugin' => 'prototype', 'controller' => 'prototype_categories', 'action' => 'reorder', 'admin' => true));
if ($canSort):
	$this->Html->script('/tree_sort/js/jquery.ui.nestedSortable', array('inline' => false));
	echo $this->element('TreeSort.js/sort', array(
		'url' => Router::url(array('plugin' => 'prototype', 'controller' => 'prototype_categories', 'action' => 'reorder', 'admin' => true))
	));
endif;
?>
<?php 
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Category'), array('action' => 'edit', 'instance' => $this->Prototype->fetch('PrototypeInstance.slug')));
$this->end('actionLinks');
?>

<div id="basic-categories">
	<?php 
	echo $this->Tree->generate($prototypeCategories, array('plugin' => 'prototype', 'element' => 'admin/category/tree', 'model' => 'PrototypeCategory', 'type' => 'ol', 'ulClass' => 'nested-sortable-', 'liClass' => null, 'liId' => 'list_'));

	if ($canSort):
		echo $this->element('TreeSort.ajax_save');
	endif;
	?>
</div>