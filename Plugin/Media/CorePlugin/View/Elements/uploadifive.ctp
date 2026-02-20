<?php
//
	$this->Html->css('Media.uploadifive/uploadifive', null, array('inline' => false, 'once' => true));
//
	$this->Html->script('Media.uploadifive/jquery.uploadifive.min', array('inline' => false, 'once' => true));
//
	$inputId = 'inputId' . Inflector::camelize($model . $group . $modelId);
//
	$errorMessages = $this->Media->validationErrors($validateType);
//
	if (!isset($uploaderUrl)):
		$uploaderUrl = array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'upload', 'admin' => false);
	endif;
//
	$uploaderUrl = Router::url($uploaderUrl);
//
	if (!isset($successUrl)):
		$successUrl = array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'insert_row', 'admin' => false);
	endif;
//
	$successUrl = Router::url($successUrl);
//
	if (!isset($checkscriptUrl)):
		$checkscriptUrl = array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'checkScript', 'admin' => false);
	endif;
//
	$checkscriptUrl = Router::url($checkscriptUrl);
//
	print '<div id="queue_' . $inputId . '"></div>
	';
//
	print '<input type="file" name="' . $inputId . '" id="' . $inputId . '">
	';
?>
<script type="text/javascript">
$(function() {
	$('#<?php echo $inputId; ?>').uploadifive({
		'buttonText'		: 'Upload'
		, 'queueID'		: 'queue_<?php echo $inputId; ?>'
		, 'simUploadLimit' 	: 1
		, 'uploadScript' 	: '<?php echo $uploaderUrl; ?>'
		, 'checkScript'		: '<?php echo $checkscriptUrl; ?>'
		, 'fileSizeLimit'	: <?php echo (1000 * (int)preg_replace('/\D/', '', ini_get('upload_max_filesize'))); ?>
		<?php if (isset($altFileUploader) && !empty($altFileUploader)) : ?>
		, 'onFallback'		: function() {
			$('#<?php echo $inputId; ?>').replaceWith(<?php echo JSON_encode($altFileUploader); ?>);
		}
		<?php endif; ?>
		, 'formData'		: {
			'modelId'	: '<?php echo $modelId; ?>'
			, 'model'	: '<?php echo $model; ?>'
			, 'group'	: '<?php echo $group; ?>'
			, 'session_id'	: '<?php echo $this->Session->id; ?>'
			, 'type'	: '<?php echo $validateType; ?>'
		<?php if (isset($foreign_key) && !empty($foreign_key)) : ?>
			, 'foreign_key'	: '<?php echo $foreign_key; ?>'
		<?php endif; ?>
		}
		, 'onError'		: function(file, errorCode, errorMsg, errorString) {
			var messageMap = <?php echo json_encode($errorMessages); ?>;
			var friendlyMessage = messageMap[errorMsg];
			$('#' + file.id).find('.data').html('<br>' + friendlyMessage);
		}
		, 'onUploadComplete'	: function(file, data, response) {
			$.ajax({
				type	: 'POST'
				, url	: '<?php echo $successUrl; ?>/' + data
				, data	: {
					'assocAlias'	: '<?php echo $assocAlias; ?>'
					, 'model'	: '<?php echo $model; ?>'
					, 'attachmentType' : '<?php echo $attachmentType; ?>'
					<?php if (isset($plug) && !empty($plug)) : ?>
					, 'plug'	: '<?php echo $plug; ?>'
					<?php endif; ?>
					<?php if (isset($troller) && !empty($troller)) : ?>
					, 'troller'	: '<?php echo $troller; ?>'
					<?php endif; ?>
					<?php if (isset($foreign_key) && !empty($foreign_key)) : ?>
					, 'foreign_key'	: '<?php echo $foreign_key; ?>'
					<?php endif; ?>
				}
				, success: function(data)  {
					var $attachments = $('#<?php echo 'tableId' . Inflector::camelize($model . $group . $modelId); ?> .attachments');
					var count = 0;
					//find the highest index for an attachment and increment the count
					$attachments.find(".attachment input").each(function(){
						var idMatch = $(this).attr("name").match(/data\[[^\]]+\]\[(\d+)\].*/);
						var thisCount = 0;
						if ( idMatch && idMatch.length > 1 ){
							thisCount = parseInt(idMatch[1]);
						}
						if (thisCount + 1 > count) {
							count = thisCount + 1;
						}
					});
					$attachments.append(data.replace(/%TEMP%/g, count.toString()));
					
					if (typeof(ATTACHMENT_ADDED) == 'function') {
						$attachments.find(".attachment").each(function (){
							ATTACHMENT_ADDED(this);
						});
					}
				}
			});
		}
	});
});
</script>