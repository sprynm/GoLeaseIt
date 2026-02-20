<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Email Form Submissions');
?>
<table class="admin-table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>View Submissions</th>

		</tr>
	</thead>
	<tbody>
	<?php foreach ($emailForms as $i => $emailForm): ?>
		<tr>
			<td><?php echo $emailForm['EmailForm']['id']; ?></td>
			<td><?php echo $emailForm['EmailForm']['name']; ?></td>
			<td><?php echo $this->Html->link('Submissions', array('controller' => 'email_form_submissions', 'action' => 'index', $emailForm['EmailForm']['id'])); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>