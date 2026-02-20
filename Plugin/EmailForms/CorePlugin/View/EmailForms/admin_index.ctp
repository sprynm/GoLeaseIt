<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Email Forms');
$canDelete = AccessControl::isAuthorized(array('plugin' => 'email_forms', 'controller' => 'email_forms', 'action' => 'delete'));
?>
<?php
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Email Form'), array('action'=>'edit'));
$this->end('actionLinks');
?>
<table class="admin-table">
	<?php echo $this->element('Administration.index/table_caption'); ?>
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th class="icon-column">Published</th>
			<th>View Submissions</th>
			<?php echo $this->element('Administration.index/actions_header', array('showDelete' => $canDelete)); ?>

		</tr>
	</thead>
	<tbody>
	<?php foreach ($emailForms as $i => $emailForm): ?>
		<tr>
			<td><?php echo $emailForm['EmailForm']['id']; ?></td>
			<td><?php echo $emailForm['EmailForm']['name']; ?></td>
			<td>
			<?php
			echo $this->Publishing->toggle(array('data' => $emailForm, 'model' => 'EmailForms.EmailForm'));
			?>
			</td>
			<td><?php echo $this->Html->link('Submissions', array('controller' => 'email_form_submissions', 'action' => 'index', $emailForm['EmailForm']['id'])); ?></td>
			<?php echo $this->element('Administration.index/actions_column', array('item' => $emailForm['EmailForm'], 'showDelete' => $canDelete)); ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>
