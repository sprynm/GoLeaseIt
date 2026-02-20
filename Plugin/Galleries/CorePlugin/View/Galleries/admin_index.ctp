<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Photo Galleries');
$canDelete = AccessControl::isAuthorized(array('plugin' => 'galleries', 'controller' => 'galleries', 'action' => 'delete'));
?>
<?php 
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Gallery'), array('action'=>'edit'));
$this->end('actionLinks');
?>
<table class="admin-table">
	<?php echo $this->element('Administration.index/table_caption'); ?>
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('PublishingInformation.start', 'Start Publishing');?></th>
			<th><?php echo $this->Paginator->sort('PublishingInformation.end', 'End Publishing');?></th>
        	<th class="icon-column">Published</th>
			<?php echo $this->element('Administration.index/actions_header', array('showDelete' => $canDelete)); ?>
			
		</tr>
	</thead>
	<tbody>
	<?php foreach ($galleries as $i => $gallery): ?>
		<tr>
			<td><?php echo $gallery['Gallery']['id']; ?></td>
			<td><?php echo $gallery['Gallery']['name']; ?></td>
			<td><?php echo $this->Publishing->start($gallery['PublishingInformation']['start']); ?></td>
			<td><?php echo $this->Publishing->end($gallery['PublishingInformation']['end']); ?></td>
			<td>
			<?php
			echo $this->Publishing->toggle(array('data' => $gallery, 'model' => 'Galleries.Gallery'));
			?>
			</td>
			<?php echo $this->element('Administration.index/actions_column', array('item' => $gallery['Gallery'], 'showDelete' => $canDelete)); ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>