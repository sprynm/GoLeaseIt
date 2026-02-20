<?php

$this->Html->css('Media.uploadifive/uploadifive', null, array('inline' => false, 'once' => true));
$this->Html->script('Media.uploadifive/jquery.uploadifive.min', array('inline' => false, 'once' => true));


$inputId = 'inputId' . Inflector::camelize($model . $settingId . $group);
		
if (!isset($validateType)):
	$validateType = null;
endif;

if (!isset($fileType)):
	$fileType = null;
endif;


$errorMessages = $this->Media->validationErrors($validateType);

if (!isset($inputId)):
	$inputId = null;
endif;

if (!isset($uploaderUrl)):
	$uploaderUrl = array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'upload', 'admin' => false, $modelId);
endif;
$uploaderUrl = Router::url($uploaderUrl);


if (!isset($successUrl)):
	$successUrl = array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'preview', 'admin' => false, $modelId);
endif;
$successUrl = Router::url($successUrl);

if (!isset($checkscriptUrl)):
	$checkscriptUrl = array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'checkScript', 'admin' => false);
endif;
$checkscriptUrl = Router::url($checkscriptUrl);

?>
<div id='queue'></div>
<input multiple='false' type='file' name='<?php echo $inputId; ?>' id='<?php echo $inputId; ?>' />

<script type='text/javascript'>
$(function() {
	$('#<?php echo $inputId; ?>').uploadifive({
		'buttonText' : 'Upload',
		'fileType' : '<?php echo $fileType; ?>', 
		'uploadScript' : '<?php echo $uploaderUrl; ?>',
		'checkScript' : '<?php echo $checkscriptUrl; ?>',
		'fileSizeLimit' : 5000,
		'onFallback' : function() { 
							console.log('HTML5 not supported.'); 
							$('#<?php echo $inputId; ?>').replaceWith('<?php echo $altFileUploader; ?>');
						},
		'formData' : {
			'modelId' : '<?php echo $modelId; ?>', //actually foreignId
			'model' : '<?php echo $model; ?>',
			'group' : '<?php echo $group; ?>',
			'session_id' : '<?php echo $this->Session->id; ?>',
			'type' : '<?php echo $validateType; ?>',
		},
		'onError' : function(error, object) {
			errorCode = object.xhr.status;
	        var messageMap = <?php echo json_encode($errorMessages); ?>;
	        var friendlyMessage = messageMap[errorCode];
			$(object.queueItem[0]).html("Error " + errorCode + ": " + friendlyMessage);	
	    },
		'onUploadComplete' : function(file, data, response) {
			$.ajax({
				type: 'POST',
	            url: '<?php echo $successUrl; ?>/' + data,
	            data: {
	            	'assocAlias' : '<?php echo $assocAlias; ?>',
	            	'model' : '<?php echo $model; ?>',
	            	'attachmentType' : '<?php echo $attachmentType; ?>'
	            },
	            success: function(data)  {
	            	$('#<?php echo $model.$modelId.$group; ?>').html(data);
	            }
	        });
        }
    });
});
</script>