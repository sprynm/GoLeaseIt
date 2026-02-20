<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Page Contents');
$this->Html->css('TreeSort.sort', null, array('inline' => false));
/**
 * Only super administrators can use the drag and drop sort.
 */
$canSort = false;
if (AccessControl::inGroup('Super Administrator')):
	$canSort = true;
	$this->Html->script('/tree_sort/js/jquery.ui.nestedSortable', array('inline' => false));
	echo $this->element('TreeSort.js/sort', array(
		'url' => Router::url(array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'reorder', 'admin' => true))
	));
endif;
?>
<?php $this->start('actionLinks'); ?>
<?php echo $this->AdminLink->link(__('New Page'), array('action' => 'add')); ?>
<?php $this->end('actionLinks'); ?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('User Pages', '#basic-pages'); ?></li>
<?php if (AccessControl::inGroup('Super Administrator')): ?>
	<li><?php echo $this->Html->link('Super Admin Page', '#tab-super'); ?></li>
<?php endif; ?>
<?php $this->end('tabs'); ?>

<div id="basic-pages">
   <?php 
   echo $this->Tree->generate($pages, array('plugin' => 'pages', 'element' => 'admin/page_tree', 'model' => 'Page', 'type' => 'ol', 'ulClass' => 'nested-sortable-', 'liClass' => null, 'liId' => 'list_')); 
   if ($canSort):
	   echo $this->element('TreeSort.ajax_save');
   endif;
   ?>
</div>

<?php if (AccessControl::inGroup('Super Administrator')): ?>
   <div id="tab-super">
      <?php
      if (Cms::minVersion('1.0.4')):
      ?>
      <ol class="nested-sortable-0 no-sort">
      <?php
         foreach ($superPages as $page):
            echo '<li>' . $this->element('Pages.admin/page_tree', array('data' => $page)) . '</li>';
         endforeach;
      ?>
      </ol>
      <?php
      else:
      echo $this->Tree->generate(
         $pages, 
         array('element' => 'Pages.admin/page_tree', 'model' => 'Page', 'type' => 'ol', 'ulClass' => 'nested-sortable-', 'liClass' => null, 'liId' => 'super_list_', 'extraData' => array('superAdmin' => true)
      ));
      endif;
      ?>
   </div>
<?php endif; ?>