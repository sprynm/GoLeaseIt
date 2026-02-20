<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Custom Admin Nav Items');
?>
<?php 
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Custom Admin Nav Item'), array('action' => 'edit'));
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
			<?php echo $this->element('Administration.index/actions_header'); ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($adminNavigationItems as $i => $adminNavigationItem): ?>
		<tr>
			<td><?php echo $adminNavigationItem['AdminNavigationItem']['name']; ?></td>
          	<td><?php echo $this->Time->nice($adminNavigationItem['AdminNavigationItem']['created']); ?></td>
          	<td><?php echo $this->Time->nice($adminNavigationItem['AdminNavigationItem']['modified']); ?></td>
			<td>
			<?php
			echo $this->Publishing->toggle(array('data' => $adminNavigationItem, 'model' => 'Navigation.AdminNavigationItem'));
			?>
			</td>
			<?php echo $this->element('Administration.index/actions_column', array('item' => $adminNavigationItem['AdminNavigationItem'])); ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>