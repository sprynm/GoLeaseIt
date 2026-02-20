<?php
//
if (!isset($this->Media) || !is_a($this->Media, 'MediaHelper')) :
	//
	$message	= 'Attachments Element - The media helper is not loaded but required.';
	//
	trigger_error($message, E_USER_NOTICE);
	//
	return;
// (!isset($this->Media) || !is_a($this->Media, 'MediaHelper'))
endif;
//
$this->Html->css('Media.uploadifive/uploadifive', null, array('inline' => false, 'once' => true));
//
$this->Html->script('Media.uploadifive/jquery.uploadifive.min', array('inline' => false, 'once' => true));
//
$inputId		= 'inputId' . Inflector::camelize($model . $group . $foreignKey);
//
$uploaderUrl		= Router::url(array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'upload', 'admin' => false));
//
$successUrl		= Router::url(array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'insert_row', 'admin' => false));
//
$checkscriptUrl		= Router::url(array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'checkScript', 'admin' => false));
// For custom Uploadify validation - passed to the controller in the Uploadify AJAX.
if (!isset($validateType)) :
	//
	$validateType	= null;
// (!isset($validateType))
endif;
//
$errorMessages		= $this->Media->validationErrors($validateType);
//
if (!isset($previewVersion)) :
	//
	$previewVersion = 'preview';
// (!isset($previewVersion))
endif;
// Set $assocAlias and $model if you're using this element multiple times in one form
if (!isset($model)) :
	//
	$model = $this->Form->model();
// (!isset($model))
endif;
//
if (!isset($assocAlias)) :
	//
	$assocAlias = 'Attachment';
// 
else:
	//
	$assocAlias = Inflector::singularize($assocAlias);
// (!isset($assocAlias))
endif;
//
if (!isset($group)) :
	//
	$group = strtolower($assocAlias);
// (!isset($group))
endif;
//
if (!isset($modelId)) :
	//
	$modelId = $this->Form->value($this->Form->model() . '.id');
// (!isset($modelId))
endif;
//
$modelId	= $this->Form->value($this->Form->model() . '.id');
//
if (!isset($title)) :
	//
	$title = sprintf(__('%s'), Inflector::pluralize($assocAlias));
// (!isset($title))
endif;
// 
if (!isset($plug)) :
	//
	$plug = 'Page';
// (!isset($plug))
endif;
//
if (!isset($troller)) :
	//
	$troller = 'pages';
// (!isset($troller))
endif;
//
if (!isset($foreign_key)) :
	//
	$foreign_key = $modelId;
// (!isset($foreign_key))
endif;
// 
$altFileUploader	= $this->element(
				'Media.new_file'
				, array(
					'count'		=> 0
					, 'assocAlias'	=> $assocAlias
					, 'modelId'	=> $modelId
					, 'model'	=> $model
					, 'group'	=> $group
					, 'validateType' => $validateType
					, 'attachmentType' => $group
					//, 'imageItem'	=> $imageItem
					,
				)
			); 
?>
	<span class="uploadify"></span>
	<div id="<?php echo 'div_banner_' . $inputId; ?>">
	<?php // 
	// 
	echo $this->element(
		'Media.single'
		, array(
			'assocAlias'	=> 'Image'
			, 'model'	=> 'Page'
			, 'group'	=> 'Image'
			, 'foreignKey'	=> $this->Form->value('Page.id')
			, 'imageItem'	=> $imageItem
			, 'item'	=> $imageItem
			, 'uploadify'	=> true
			, 
		)
	);
	?>
	</div>
	<?php
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
		, 'fileSizeLimit'	: 10000
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
					, 'attachmentType' : '<?php echo $group; ?>'
					<?php if (isset($plug) && !empty($plug)) : ?>
					, 'plug'	: '<?php echo $plug; ?>'
					<?php endif; ?>
					<?php if (isset($troller) && !empty($troller)) : ?>
					, 'troller'	: '<?php echo $troller; ?>'
					<?php endif; ?>
					<?php if (isset($foreign_key) && !empty($foreign_key)) : ?>
					, 'foreign_key'	: '<?php echo $foreign_key; ?>'
					<?php endif; ?>
					, 'uploadify'	: true
					, 'single'	: true
				}
				, success: function(data)  {
					//
					var pageBannerDiv	= $('#<?php echo 'div_banner_' . $inputId; ?>');
					//
					$(pageBannerDiv).html(data);
				}
			});
		}
	});
});
</script>