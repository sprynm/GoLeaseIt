<?php 

/**
 * Attachments Element File
 *
 * Element listing associated attachments of the view's model.
 * Add, delete (detach) an Attachment. This element requires
 * the media helper to be loaded and `$this->request->data` to be populated.
 *
 * Possible options:
 *  - `'previewVersion'` Defaults to `'xxs'`.
 *  - `'assocAlias'` Defaults to `'Attachment'`.
 *  - `'model'` Defaults to the model of the current form.
 *  - `'title'` Defaults to the plural form of `'assocAlias'`.
 *  - `'uploadify'` Defaults to true, but an alternate will be shown if javascript is disabled and Uploadify plugin won't work.
 *
 * Copyright (c) 2007-2010 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * Modified to show attachments as drag and drop cells or in list view
 *
 * PHP version 5
 * CakePHP version 1.3
 *
 * @packagemedia
 * @subpackage media.views.elements
 * @copyright  2007-2010 David Persson <davidpersson@gmx.de>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://github.com/davidpersson/media
 */

//echo $this->Html->script('Media.attachment_row_actions', array('inline' => false, 'once' => true)); 
$this->Html->script('Media.attachment-group-actions', array('inline' => false, 'once' => true)); 
$this->Html->css('Media.attachment-group.css', '', array('once'=>true, 'inline'=>false));

if (!isset($this->Media) || !is_a($this->Media, 'MediaHelper')) {
	$message = 'Attachments Element - The media helper is not loaded but required.';
	trigger_error($message, E_USER_NOTICE);
	return;
}

if (!isset($previewVersion)) {
	$previewVersion = 'preview';
}

/* Set $assocAlias and $model if you're using this element multiple times in one form */

if (!isset($assocAlias)) {
	$assocAlias = 'Attachment';
} else {
	$assocAlias = Inflector::singularize($assocAlias);
}

if (!isset($model)) {
	$model = $this->Form->model();
}

if (!isset($group)){
	$group = ucwords(Inflector::humanize(Inflector::underscore($assocAlias)));
}

if (!isset($modelId)):
	$modelId = $this->Form->value($this->Form->model().'.id');
endif;

// This variable, if set to true, will cause the image table to be set even if $modelId is null.
if (!isset($forceForm)) {
	$forceForm = false;
}

if (!isset($title)) {
	$title = sprintf(__('%s'), Inflector::pluralize($assocAlias));
}

if (!isset($uploadify)): 
	$uploadify = true;
endif;

// For custom Uploadify validation - passed to the controller in the Uploadify AJAX.
if (!isset($validateType)):
	$validateType = null;
endif;

// Attachment type so that images are displayed correctly in the attachment_row element if the assocAlias doesn't
// include "image" in its name.
if (!isset($attachmentType)):
	$attachmentType = null;
endif;

if (empty($foreign_key)) {
	if ($this->params['controller'] == 'prototype_categories' 
			&& isset($this->request->data['PrototypeCategory']['prototype_instance_id']) 
			&& !empty($this->request->data['PrototypeCategory']['prototype_instance_id'])) {
	//
		$foreign_key	= $this->request->data['PrototypeCategory']['prototype_instance_id'];
	} elseif (isset($this->request->data['PrototypeInstance']['id']) && !empty($this->request->data['PrototypeInstance']['id'])) {
	//
		$foreign_key	= $this->request->data['PrototypeInstance']['id'];
	} else {
	//
		$foreign_key	= $modelId;
	}
}


if (isset($single) && $single == true) {
	echo $this->element('Media.single', array(
		'previewVersion' => $previewVersion, 
		'assocAlias' => $assocAlias, 
		'model' => $model, 
		'title' => $title,
		'group' => $group,
		'validateType' => $validateType
	));
	return;
}
$count = 0;

if (empty($uploadify)):
	echo $this->Html->script('Media.ajax', array('inline' => false, 'once' => true));
endif;

if (!$modelId && !$forceForm):
	echo '<h3>Please save this item before continuing.</h3>';
	return;
endif;

$attachments = Hash::extract($this->request->data, $assocAlias);
//
$thisTableIdIs	= 'tableId' . Inflector::camelize($model . $group . $modelId);

?>

<div class="attachment-group" id="<?php print $thisTableIdIs; ?>"<?php 
	if (!empty($deleteUrl)):
		echo ' data-delete-link="' . htmlspecialchars($deleteUrl) . '"'; 
	endif;
?>>
	<div class="attachment-controls<?php echo empty($attachments)?' hidden':''?>">
		<div class="view-as">
			<label>View as:</label>
			<a class="button grid selected" href="#">
				Grid
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 19">
					<rect width="7" height="7"/>
					<rect x="12" width="7" height="7"/>
					<rect x="12" y="12" width="7" height="7"/>
					<rect y="12" width="7" height="7"/>
				</svg>
			</a>
			<a class="button list" href="#">
				List
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 20">
					<rect width="4" height="4"/>
					<rect y="16" width="4" height="4"/>
					<rect y="8" width="4" height="4"/>
					<rect x="7" width="20" height="4"/>
					<rect x="7" y="16" width="20" height="4"/>
					<rect x="7" y="8" width="20" height="4"/>
				</svg>
			</a>
		</div>
		<div class="actions">
			<div class="select-all"><input type="checkbox" id="SelectAll<?php echo $thisTableIdIs; ?>"><label for="SelectAll<?php echo $thisTableIdIs; ?>">Select All</label></div>
			<div class="delete-selected">Delete Selected</div>
		</div>
	</div>
	
	<?php 
	if (empty($attachments)):
	?>
		<div class="attachments empty">Nothing has been uploaded yet.</div>
	<?php
	else:
	?>
	<div class="attachments grid">
		<div class="headings">
			<div></div>
			<div>File</div>
			<div></div>
			<div>Caption</div>
			<div>Sort</div>
		</div>
	<?php foreach ($attachments as $count => $attachment ): ?> 
		<?php 
		$rowOptions = array(
			'count'=>$count
			, 'attachment'=>$attachment
			, 'model'=>$model
			, 'foreign_key'=>$foreign_key
			, 'assocAlias'=>$assocAlias
			, 'previewVersion'=>$previewVersion
			, 'attachmentType'=>$attachmentType
		);
		
		if (isset($cropUrl)):
			$rowOptions['cropUrl'] = $cropUrl;
		endif;
		
		echo $this->element("Media.attachment_row", $rowOptions);
		?>
	<?php endforeach; ?>
	</div> 
	<?php 
	endif;
	?> 
 
	<?php 
	$altFileUploader = $this->element('Media.new_file', array(
			'count' => $count, 
			'assocAlias' => $assocAlias, 
			'modelId' => $modelId, 
			'model' => $model, 
			'group' => $group,
			'validateType' => $validateType,
			'attachmentType' => $attachmentType
			)) . $this->Html->link('Add Another File', '#', array('class' => 'add_new_photo', 'rel' => $assocAlias . '|' . $model . '|' . str_replace(' ', '_', $group))); 
	?>

	<span class="uploadify">
	<?php
	//
	if (!empty($uploadify)):
	
		$uploadifyArgs = array(
			'model' => $model,
			'group' => $group,
			'modelId' => $modelId,
			'assocAlias' => $assocAlias,
			'validateType' => $validateType,
			'attachmentType' => $attachmentType,
			'plug' => $this->params['plugin'],
			'troller' => $this->params['controller'],
			'foreign_key' => $foreign_key,
			'altFileUploader' => $altFileUploader
		);
		
		if (isset($uploaderUrl)):
			$uploadifyArgs['uploaderUrl'] = $uploaderUrl;
		endif;
		
		if (isset($successUrl)):
			$uploadifyArgs['successUrl'] = $successUrl;
		endif;
		
		echo $this->element('Media.uploadifive', $uploadifyArgs);

	?>
	</span>

	<style type="text/css">
		.uploadify {display: none;}
	</style>
	<script type="text/javascript">
		document.write('<style type="text/css">.uploadify {display: inline;}</style>');
	</script>

	<noscript>
	<h3>Add a New File</h3>
	<?php 
		echo $altFileUploader;
	?>
	</noscript>
	<?php 
	else:
		echo $altFileUploader;

	endif; // if ($uploadify): ?>
</div>