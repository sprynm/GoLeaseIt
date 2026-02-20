<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', $emailForm['EmailForm']['name'] . ': Submissions');
?>
<?php
$this->start('actionLinks');
echo $this->AdminLink->link(__('Export to CSV'), array('action' => 'export', $emailForm['EmailForm']['id']));
echo $this->AdminLink->link(__('Edit Form'), array('controller' => 'email_forms', 'action' => 'edit', $emailForm['EmailForm']['id']));
echo $this->AdminLink->link(__('Back to Index'), array('controller' => 'email_form_submissions', 'action' => 'index'));
$this->end('actionLinks');

$this->start('formStart');
echo $this->Form->Create('EmailFormSubmission');
$this->end('formStart');

$this->start('formEnd');
echo $this->Form->submit('Delete Selected', array('id' => 'deleteEmailFormSubmit'));
echo $this->Form->end();
$this->end('formEnd');
?>
<table class="admin-table">
<?php echo $this->element('Administration.index/table_caption'); ?>

<?php echo $this->Form->input('Select All', array('type' => 'checkbox', 'class' => 'selectall')); ?>
	
	<thead>
		<tr>
			<th>Delete</th>
			<th>Email Address</th>
			<th><?php echo $this->Paginator->sort('created', 'Submitted');?></th>
			<th class="actions icon-column"><?php echo __('View');?></th>
			<th class="actions icon-column"><?php echo __('Delete');?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($emailFormSubmissions as $i => $emailFormSubmission): ?>
		<tr>
			<td><?php echo $this->Form->input(
					'EmailFormSubmission.' . $i . '.delete'
					, array(
						'label'		=> false
						, 'value'	=> $emailFormSubmission['EmailFormSubmission']['id']
						, 'type'	=> 'checkbox'
						, 'name'	=> 'data[EmailFormSubmission][]'
						, 'class'	=> 'deleteEmailFormSubmission'
						,
					)
			); ?></td>
			
			<td>
				<?php 	
					if (!empty($emailFormSubmission['EmailFormSubmission']['data']['Email'])) {
						echo $emailFormSubmission['EmailFormSubmission']['data']['Email'] ;	
					} else if (!empty($emailFormSubmission['EmailFormSubmission']['data']['ordEmailAddress'])) {
						echo $emailFormSubmission['EmailFormSubmission']['data']['ordEmailAddress'] ;	
					} else if (!empty($emailFormSubmission['EmailFormSubmission']['data']['email_address'])) {
						echo $emailFormSubmission['EmailFormSubmission']['data']['email_address'] ;	
					} else if (!empty($emailFormSubmission['EmailFormSubmission']['data']['email'])) {
						echo $emailFormSubmission['EmailFormSubmission']['data']['email'] ;	
					} else if (!empty($emailFormSubmission['EmailFormSubmission']['data']['Email Address'])) {
						echo $emailFormSubmission['EmailFormSubmission']['data']['Email Address'] ;	
					} 
				?>
				
			</td>
			<td><?php echo $this->Time->nice($emailFormSubmission['EmailFormSubmission']['created']); ?></td>
			<td class="actions icon-column">
			    <?php echo $this->element('Administration.index/actions/edit', array('url' => array('action' => 'edit', $emailFormSubmission['EmailFormSubmission']['id']))); ?>
			</td>
			<td class="actions icon-column">
			    <?php echo $this->element('Administration.index/actions/delete', array('url' => array('action' => 'delete', $emailFormSubmission['EmailFormSubmission']['id']))); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>
<script>
$(document).ready(function(){

	$('.selectall').click( function() {
	  $('.deleteEmailFormSubmission').each( function() {
		$(this).prop('checked', $('.selectall').is(':checked'));
	  });
	});

	$('#deleteEmailFormSubmit').hide();

	$('.deleteEmailFormSubmission').click(function(){
		if($('.deleteEmailFormSubmission').is(':checked')) {
			$('#deleteEmailFormSubmit').show();
		} else {
			$('#deleteEmailFormSubmit').hide();
		}
	});

});
</script>