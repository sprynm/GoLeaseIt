<?php $this->extend('Administration.Common/index-page'); ?>
<?php // 
// 
##echo $this->Html->tag('h2', 'Hi mom');
// 
echo $this->element('Administration.js/datatables');
//
$this->set('header', $this->Prototype->fetch('PrototypeInstance.name'));
//
$canSort	= false;
// 
if (strpos($instance['PrototypeInstance']['item_order'], 'PrototypeItem.rank') !== false) :
//if (!$instance['PrototypeInstance']['all_items_featured']) :
	//
	$canSort	= AccessControl::isAuthorized(array('plugin' => 'prototype', 'controller' => 'prototype_items', 'action' => 'reorder', 'admin' => true));
// (!$instance['PrototypeInstance']['all_items_featured'])
endif;
//
$this->start('actionLinks');
//
if ($sort && $canSort):
	echo $this->Html->link(__('Change Item Order'), '#', array('class' => 'change-order'));
endif;
// 
if ($instance['PrototypeInstance']['allow_instance_view']) :
	//
	echo $this->AdminLink->link(__('Edit ' . $instance['PrototypeInstance']['name'] . ' Details'), array('controller' => 'prototype_instances', 'action' => 'summary_edit', 'instance' => $this->Prototype->fetch('PrototypeInstance.slug')));
// 
endif;
// 
echo $this->AdminLink->link(__('New Item'), array('action' => 'edit', 'instance' => $this->Prototype->fetch('PrototypeInstance.slug')));
if ($this->Prototype->hasCategories()):
	echo $this->AdminLink->link(__('Categories'), array('controller' => 'prototype_categories', 'instance' => $this->Prototype->fetch('PrototypeInstance.slug')));
endif;
$this->end('actionLinks');

$featuredVariableName = 'featured' . Inflector::slug( Inflector::camelize($instance['PrototypeInstance']['name']), '');
$autoLoadFeaturedLayouts = Cms::commaExplode($instance['PrototypeInstance']['autoload_featured_items_in_layouts']);
foreach ($autoLoadFeaturedLayouts as $key => $layout) {
	$autoLoadFeaturedLayouts[$key] = "\"$layout\"";
}
?>
<p>
<?php
if (!empty($instance['PrototypeInstance']['use_featured_items'])):
?>
Featured items are available with the $<?php echo $featuredVariableName; ?> variable in the following template<?php echo (count($autoLoadFeaturedLayouts)>1)?'s':''; ?>: <?php echo implode(", ", $autoLoadFeaturedLayouts); ?> &nbsp; 
<?php
endif;
?>
<?php echo $this->Html->link('Edit Prototype Settings', array('controller'=>'prototype_instances', 'action'=>'edit', $instance['PrototypeInstance']['id'], '#'=>'tab-items'));?>
</p>

<?php

if ($sort):
	$this->start('formStart');
	echo $this->Form->Create('PrototypeItem', array('url' => array('action' => 'sort', 'admin' => true, 'PrototypeItem', 'Prototype', 'instance' => $instance['PrototypeInstance']['slug'])));
	$this->end('formStart');
	
	$this->start('formEnd');
  
  if ($canSort) {    
    echo $this->Form->submit('Save Order');
  }
  
  echo $this->Form->end();
	$this->end('formEnd');
endif;

echo $this->element('Administration.js/datatables_ajax');
?>
<table class="admin-table data-table sortable">
	<?php echo $this->element('Administration.index/table_caption', array('paginate' => false)); ?>
	<thead>
		<tr>
			<th>Name</th>
			<th>Date</th>
			<?php if ($instance['PrototypeInstance']['use_featured_items'] && !$instance['PrototypeInstance']['all_items_featured']): ?>
			<th data-ssortdatatype='dom-toggle'>Featured</th>
			<?php endif; ?>
			<?php if ($this->Prototype->hasCategories()): ?>
			<th class="filter-select">Category</th>
			<?php endif; ?>
			<?php if ($sort && $canSort): ?>
			<th class="icon-column sort-column"><?php echo __('Sort');?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($items as $i => $item): ?>
		<tr>
			<td>
			<?php echo $item['PrototypeItem']['name'] . '<div class="admin-cntrls">'; 

if (!isset($extra)):
    $extra = array();
endif;

if (!isset($itemId)):
    $itemId = $item['PrototypeItem']['id'];
endif;

if (!isset($model)):
    $model = $this->Form->model();
endif;

if (!isset($count)):
    $count = 0;
endif;

if (!isset($value)):
    $value = 0;
endif;

if (!isset($edit)):
	$edit = array_merge(array('action' => 'edit', $itemId), $extra);
endif;

if (!isset($copy)):
	$copy = array_merge(array('action' => 'copy', $itemId), $extra);
endif;

if (!isset($delete)):
	$delete = array_merge(array('action' => 'delete', $itemId), $extra);
endif;

if (!isset($showEdit)):
    $showEdit = true;
endif;

if (!isset($showDelete)):
    $showDelete = true;
endif;

if (!isset($showCopy)):
    $showCopy = true;
endif;

if (!isset($sort)):
    $sort = false;
endif;

if (!isset($deleteMessage)):
    $deleteMessage = null;
endif;
//
	if ($showEdit):
		echo $this->element(
			'Administration.index/actions/edit'
			, array(
				'url' => array(
					'action'	=> 'edit'
					, 'instance'	=> $this->Prototype->fetch('PrototypeInstance.slug')
					, $item['PrototypeItem']['id']
				)
				, 'item' => $item['PrototypeItem']
				,
			)
		);
	endif;
//
	if ($showDelete):
		echo $this->element(
			'Administration.index/actions/delete'
			, array(
				'url' => array(
					'action'	=> 'delete'
					, 'instance'	=> $this->Prototype->fetch('PrototypeInstance.slug')
					, $item['PrototypeItem']['id']
				)
				, 'item' => $item['PrototypeItem']
				, 'deleteMessage' => $deleteMessage
				,
			)
		);
	endif;
//
	if ($showCopy):
		echo $this->element(
			'Administration.index/actions/copy'
			, array(
				'url' => array(
					'action'	=> 'copy'
					, 'instance'	=> $this->Prototype->fetch('PrototypeInstance.slug')
					, $item['PrototypeItem']['id']
				)
				, 'item' => $item['PrototypeItem']
				,
			)
		);
	endif;
?></div>
</td>

<?php $date = (!empty($item['PublishingInformation']['start'])) ? date('Y/m/d', strtotime($item['PublishingInformation']['start'])) : '-'; ?>
<td><?php echo $date . '<br>' . $this->Publishing->toggle(array('data' => $item, 'model' => 'Prototype.PrototypeItem')); ?></td>
<?php if ($instance['PrototypeInstance']['use_featured_items'] && !$instance['PrototypeInstance']['all_items_featured']): ?>
<td><?php echo $this->Prototype->toggleFeatured($item, true); ?></td>
<?php endif; ?>
<?php if ($this->Prototype->hasCategories()): ?>
	<td><?php echo $this->Prototype->adminCategoryList($item); ?></td>
<?php endif; ?>

<?php if ($sort && $canSort): ?>
<td class="icon-column sorting">
<?php
    echo $this->element('Administration.index/actions/sort', array('model' => 'PrototypeItem', 'count' => $i, 'item' => $item['PrototypeItem']));
?>
</td>
<?php endif; ?>

		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer', array('paginate' => false)); ?>