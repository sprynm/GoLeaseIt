<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Content Blocks');
$canDelete = AccessControl::isAuthorized(array('plugin' => 'content_blocks', 'controller' => 'content_blocks', 'action' => 'delete'));
?>
<?php 
	$this->start('actionLinks');
	echo $this->AdminLink->link(__('New Block'), array('action'=>'edit'));
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
	<?php foreach ($contentBlocks as $i => $contentBlock): ?>
		<tr>
			<td><?php echo $contentBlock['ContentBlock']['id']; ?></td>
			<td><?php echo $contentBlock['ContentBlock']['name']; ?></td>
			<td><?php echo $this->Publishing->start($contentBlock['PublishingInformation']['start']); ?></td>
			<td><?php echo $this->Publishing->end($contentBlock['PublishingInformation']['end']); ?></td>
			<td>
			<?php
			echo $this->Publishing->toggle(array('data' => $contentBlock, 'model' => 'ContentBlocks.ContentBlock'));
			?>
			</td>
			<?php echo $this->element('Administration.index/actions_column', array('item' => $contentBlock['ContentBlock'], 'showDelete' => $canDelete)); ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>