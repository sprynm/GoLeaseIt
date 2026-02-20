<?php 
$recipientFields = array();
if (!empty($emailForm['EmailFormRecipient'])):
	//set js global for which fields to show for which recipients if any are customized
	foreach ($emailForm['EmailFormRecipient'] as $recipient):
		if (!empty($recipient['displayed_fields']) && !in_array('All',$recipient['displayed_fields'])):
			$recipientFields[$recipient['id']] = $recipient['displayed_fields'];
		endif;
	endforeach;
endif;

echo $this->Html->scriptBlock('EMAIL_FORM_' . $emailForm['EmailForm']['id'] . '_RECIPIENT_FIELDS = ' . json_encode($recipientFields) . ';', array('inline'=>true));

?>
<div role="form" class="email-form">
<?php // 
	//
	echo $this->EmailForm->open($emailForm);
	//
	echo $this->EmailForm->fieldsets();
	// the form needs a unique id in case there are multiple on the page
	$closeOptions = array( 
		'id' => $this->EmailForm->uniqueFieldId('SubmitForm')
	);
	
	$recaptchaData = $this->ReCaptcha->field('EmailFormSubmission');
	
	if (!empty($recaptchaData['submitParams'])){
		$closeOptions = $closeOptions + (array)$recaptchaData['submitParams'];
	}
	
	if (isset($recaptchaData['elements'])){
		echo $recaptchaData['elements'];
	}
?>
	<p class="form_tip"><?php echo $this->element('required'); ?> denotes a required field.</p>
<?php
//
	echo $this->EmailForm->close($closeOptions);
?>
</div>