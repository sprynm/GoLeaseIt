<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Navigation Menus');
?>
<?php 
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Menu'), array('action' => 'edit'));
$this->end('actionLinks');
?>
<table class="admin-table">
	<?php echo $this->element('Administration.index/table_caption'); ?>
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
        	<th class="icon-column">Published</th>
			<?php // 
			//
			echo $this->element('Administration.index/actions_header'); ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($navigationMenus as $i => $navigationMenu): ?>
		<tr>
			<td><?php echo $navigationMenu['NavigationMenu']['name']; ?></td>
    		<td><?php echo $this->Time->nice($navigationMenu['NavigationMenu']['created']); ?></td>
    		<td><?php echo $this->Time->nice($navigationMenu['NavigationMenu']['modified']); ?></td>
			<td>
			<?php
			echo $this->Publishing->toggle(array('data' => $navigationMenu, 'model' => 'Navigation.NavigationMenu'));
			?>
			</td>
			<?php // 
			// 
			echo $this->element('Administration.index/actions_column', array('item' => $navigationMenu['NavigationMenu'])); ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>