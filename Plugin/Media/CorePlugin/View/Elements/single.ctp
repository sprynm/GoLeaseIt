<?php // 
// 
if (!isset($this->Media) || !is_a($this->Media, 'MediaHelper')) {
	$message = 'Attachments Element - The media helper is not loaded but required.';
	trigger_error($message, E_USER_NOTICE);
	return;
}

if (!isset($previewVersion)) {
	$previewVersion = 'preview';
}

/* Set $assocAlias and $model if you're using this element multiple times in one form */

if (!isset($model)):
	$model = $this->Form->model();
endif;
if (!isset($assocAlias)):
	$assocAlias = 'Attachment';
else:
    $assocAlias = Inflector::singularize($assocAlias);
endif;

if (!isset($group)):
    $group = ucwords(Inflector::humanize(Inflector::underscore($assocAlias)));
endif;
if (!isset($modelId)):
	$modelId = $this->Form->value($this->Form->model().'.id');
endif;

$modelId = $this->Form->value($this->Form->model().'.id');

if (!isset($title)) {
	$title = sprintf(__('%s'), Inflector::pluralize($assocAlias));
}

//
if (isset($uploadify) && $uploadify && !isset($this->request->data[$assocAlias][0])) :
	//
	$this->request->data[$assocAlias][0]	= $item;
// 
endif;

?>
<div class="single-file">
<?php
echo $this->Form->hidden($assocAlias . '.0.model', array('value' => $model));
echo $this->Form->hidden($assocAlias . '.0.group', array('value' => $group));
// 
##echo $this->Html->tag('p', 'single 54 $this->request->data:' . $this->Html->tag('pre', print_r($this->request->data, true)));
// 
##echo $this->Html->tag('p', 'single 56 $this->request->data[$assocAlias]:' . $this->Html->tag('pre', print_r($this->request->data[$assocAlias], true)));
//
if (isset($this->request->data[$assocAlias]) && !empty($this->request->data[$assocAlias]) && !empty($this->request->data[$assocAlias][0]['id'])){
	$attachment = $this->request->data[$assocAlias][0];

	echo $this->Form->hidden($assocAlias . '.0.id', array('value' => $attachment['id']));
	echo $this->Form->hidden($assocAlias . '.0.dirname', array('value' => $attachment['dirname']));
	echo $this->Form->hidden($assocAlias . '.0.basename', array('value' => $attachment['basename']));

	 $item = $this->request->data[$assocAlias][0];

	// Build a few display variables.
	$preview = null;
	$size = null;
	$name = null;
	$type = null;


//
	if ($this->params['controller'] == 'prototype_categories' && isset($this->request->data['PrototypeCategory']['prototype_instance_id']) && !empty($this->request->data['PrototypeCategory']['prototype_instance_id']))
	{
	//
		$foreign_key	= $this->request->data['PrototypeCategory']['prototype_instance_id'];
	} elseif (isset($this->request->data['PrototypeInstance']['id']) && !empty($this->request->data['PrototypeInstance']['id']))
	{
	//
		$foreign_key	= $this->request->data['PrototypeInstance']['id'];
	} else
	{
	//
		$foreign_key	= '';
	}

//
##echo $this->Html->tag('p', 'single 88 $item:' . $this->Html->tag('pre', print_r($item, true)));

	if ($file = $this->Media->file($item)) {
		//
		$crop	= (stripos($item['group'], 'Image') !== false) ? '<br>' . $this->Html->link(
			'Crop'
			, array(
				'plugin'	=> 'media'
				, 'controller'	=> 'attachments'
				, 'action'	=> 'crop'
				, 'version'	=> (isset($version) ? $version : 'thumb')
				, 'plug'	=> (isset($plug) && !empty($plug)) ? $plug: $this->params['plugin']
				, 'troller'	=> (isset($troller) && !empty($troller)) ? $troller: $this->params['controller']
				, 'foreign_key'	=> $foreign_key
				, 'admin'	=> true
				, 'banner'	=> (strpos($item['group'], 'Banner') !== false ? true : false)
				, $item['id']
				,
			)
		)
		: ' ';
		//
		echo $this->Media->image($item, $previewVersion, array('lazyload' => null));
		//
		echo '<br>' . $this->Html->link('View Original', $this->Media->transferUrl($file), array('target' => '_blank')) . $crop;
	}
}
//
echo $this->Html->tag(
	'div'
	, 'File upload limit is ' . ini_get('upload_max_filesize') . ' each.'
);
// 
if (!isset($uploadify)) :
	//
	echo $this->Form->input($assocAlias . '.0.file', array(
			'label' => __('Upload/Replace File'),
			'type' => 'file',
			'error' => array(
				'error'		=> __('An error occurred while transferring the file.'),
				'resource'	=> __('The file is invalid.'),
				'access'	=> __('The file cannot be processed.'),
				'location'	=> __('The file cannot be transferred from or to location.'),
				'permission'	=> __('Executable files cannot be uploaded.'),
				'size'		=> __('The file is too large.'),
				'pixels'	=> __('The file is too large.'),
				'extension'	=> __('The file has the wrong extension.'),
				'mimeType'	=> __('The file has the wrong MIME type.'),
	)));
// (!isset($uploadify))
endif;
//
if( !empty( $validateType ) ) 
{
	echo $this->Form->input( $assocAlias . '.0.validateType' , array( 'type' => 'hidden' , 'value' => $validateType , ) );
}
// 
if (isset($attachment['id'])) :
	//
	echo $this->Form->input($assocAlias . '.0.alternative', array(

		'label' => __('Caption'),
		'error' => __('Please provide a caption/textual alternative.')
	));
// 
//endif;
// 
//if (isset($attachment['id'])) :
	//
	echo $this->Html->link(
		'Delete file'
		, '/admin/media/attachments/delete/' . $attachment['id']
		, array(
			'onclick'	=> 'return confirm("Please confirm you want to delete this image.");'
			,
		)
	);
	//
	$this->Html->script('Media.attachment_row_actions', array('inline'=>false, 'once'=>true));
// (isset($attachment['id']))
endif;
?>
</div>