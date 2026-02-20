<?php
if (!isset($name)):
	$name = true;
endif;

echo $this->Form->input('EmailForm.id');

if ($name):
	echo $this->Form->input('EmailForm.name');
endif;

echo $this->Form->input('EmailForm.submit_button_text');
echo $this->Form->input('EmailForm.recipient', array(
	'label' => 'Recipient Email Address - Separate Multiples With Commas',
	'description' => 'If empty, will use the email address defined in the site settings, currently <strong>' . Configure::read('Settings.Site.email') . '</strong>.'
));

echo $this->Form->input('EmailForm.use_recipient_list', array('label'=>'Variable Recipient'));
?>
<div class="email-form-recipients-container">
<?php 
echo $this->Form->input('EmailForm.recipient_list_label', array('label'=>'Label'));

$recipientListLftOptions = array('0'=>'Bottom of Form');
foreach ($this->request->data['EmailFormGroup'] as $group){
	$recipientListLftOptions[(-1)*$group['id']] = 'Start of Group "'.$group['name'] . '"';
	foreach ($group['EmailFormField'] as $field) {
		$recipientListLftOptions[$field['id']] = 'After Field "' . $field['name'] . '"';
	}
}
echo $this->Form->input('EmailForm.recipient_list_lft', array(
	'label'=>'Selectbox Location'
	, 'options' => $recipientListLftOptions
	, 'type'=>'select'
));
?>
	<div class="email-form-recipients ui-sortable">
	<?php
	if (!empty($this->data['EmailFormRecipient'])):
		foreach ($this->data['EmailFormRecipient'] as $key => $recipient):
			?>
			<div class="email-form-recipient">
				<div class="recipient-actions" style="text-align: right;"><a href="#" class="delete button"><img src="/img/icons/cross.png"></a></div>
				<?php
				echo $this->Form->input('EmailFormRecipient.'.$key.'.id', array('type'=>'hidden', 'class'=>'id'));
				echo $this->Html->tag('div', $this->Form->input('EmailFormRecipient.'.$key.'.rank', array('type'=>'hidden')), array('class'=>'rank'));
				echo $this->Form->input('EmailFormRecipient.'.$key.'.name', array('label'=>'Option Value'));
				echo $this->Form->input('EmailFormRecipient.'.$key.'.email_address');
				
				echo $this->Form->input('EmailFormRecipient.'.$key.'.redirect_page_id', array(
					'label' => 'Redirect page',
					'type' => 'select',
					'options' => Hash::merge(array(0 => 'Use Form\'s'), $pages)
				));
				
				$fields = array('All'=>'All');
				
				foreach ($this->data['EmailFormGroup'] as $group):
					$fields = Hash::merge($fields, Hash::combine($group, 'EmailFormField.{n}.name', 'EmailFormField.{n}.label'));
				endforeach;
				
				echo $this->Form->input('EmailFormRecipient.'.$key.'.displayed_fields', array(
					'label'=>'Displayed Fields'
					, 'type'=>'select'
					, 'multiple'=>'checkbox'
					, 'options' => $fields
				));
				
				?>
				<div class="custom-templates">
					<h3 class="toggle">Custom Templates</h3>
					<div>
						<?php
						echo $this->Form->input('EmailFormRecipient.'.$key.'.subject_template', array('label'=>'Subject Line Format'));
						echo $this->Form->input('EmailFormRecipient.'.$key.'.content_template', array('label'=>'Content Format'));
						echo $this->Form->input('EmailFormRecipient.'.$key.'.auto_response_subject_template', array('label'=>'Auto Response Subject Line Format'));
						echo $this->Form->input('EmailFormRecipient.'.$key.'.auto_response_content_template', array('label'=>'Auto Response Content Format'));
						?>
					</div>
				</div>
			</div>
			<?php
		endforeach;
	endif;
	?>
	</div>
	<?php
	echo $this->Html->link(
		'Add a recipient', 
		'#',
		array('class' => 'button add-new-recipient')
	);
	?>
</div>
<?php
$this->Html->script('EmailForms.admin/email_form_recipients', array('inline'=>false, 'once'=>true));

echo $this->Form->input('EmailForm.cc', array(
	'label' => 'CC (Carbon Copy) Emails - Separate Multiples With Commas'
));
echo $this->Form->input('EmailForm.bcc', array(
	'label' => 'BCC (Blind Carbon Copy) Emails - Separate Multiples With Commas'
));

if ($this->Form->value('EmailForm.id')):
	echo $this->Form->input('EmailForm.redirect_page_id', array(
		'label' => 'Redirect page',
		'description' => 'The page to which users should be redirected after successfully completing the form.',
		'type' => 'select',
		'options' => $pages
	));
endif;
?>