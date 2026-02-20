<?php
if (isset($item) && !isset($attachment)):
	$attachment = $item;
endif;

foreach (array('attachment', 'model', 'foreign_key') as $var):
	if (!isset($$var)):
		trigger_error("attachment_row.ctp: no $var was set.");
		return;
	endif;
endforeach;

if (!isset($assocAlias)):
	$assocAlias = 'Attachment';
else:
	$assocAlias = Inflector::singularize($assocAlias);
endif;

if (!isset($previewVersion)):
	$previewVersion = 'preview';
endif;

if (!isset($attachmentType)):
	$attachmentType = null;
endif;

if (!isset($count)):
	$count = "%TEMP%";
endif;

//if there's an invalid basename then reload the item and set the correct basename for it
if ( !empty(Hash::extract($this->validationErrors, $assocAlias . '.' . $count . ".basename")) ):
	$Attachment = ClassRegistry::init('Media.Attachment');
	$oldAttachment = $Attachment->findById(Hash::extract($this->request->data, $assocAlias . '.' . $count . ".id"));
	$attachment['basename'] = $oldAttachment[$Attachment->alias]['basename'];
endif;

if ($file = $this->Media->file($attachment)):
	$url = $this->Media->transferUrl($file);
	$size = $this->Media->size($file);
	//$name = $this->Media->name($file);
	if (isset($this->Number)):
		$size = $this->Number->toReadableSize($size);
	else:
		$size .= ' Bytes';
	endif;
endif;

$versions = array();

//special case for prototype item images where the model and group for the version will not match the model and group for the attachment
if ($attachment['group'] != 'Document') {	
	if ($model=='PrototypeItem') {
		$versions = ClassRegistry::init('Media.AttachmentVersion')->findForRegen('PrototypeInstance', $foreign_key, 'Item Image');
	} else if ($model=='PrototypeCategory') {
		$versions = ClassRegistry::init('Media.AttachmentVersion')->findForRegen('PrototypeInstance', $foreign_key, 'Category Image');
	} else {
		$versions = ClassRegistry::init('Media.AttachmentVersion')->findForRegen($model, $foreign_key, $attachment['group']);
		//if there's no versions for this specific foreign key then they might not use a foreign_key
		if (empty($versions)){
			$versions = ClassRegistry::init('Media.AttachmentVersion')->findForRegen($model, null, $attachment['group']);
		}
		//otherwise it will be empty anyhow
	}
}

?>
<div class="attachment<?php 
	if ( !empty(Hash::extract($this->validationErrors, $assocAlias . '.' . $count)) ):
		echo " error";
	endif;
	?>">
	<?php
		if (!empty($attachment['id'])):
			echo $this->Form->hidden($assocAlias . '.' . $count . '.id', array('value' => $attachment['id'], 'class'=>'id'));
			echo $this->Form->hidden($assocAlias . '.' . $count . '.dirname', array('value' => $attachment['dirname']));
			echo $this->Form->hidden($assocAlias . '.' . $count . '.basename', array('value' => $attachment['basename'], 'class'=>'basename'));
		else:
			echo $this->Form->hidden($assocAlias . '.' . $count . '.is_matrix', array('value' => $attachment['is_matrix']));
			echo $this->Form->hidden($assocAlias . '.' . $count . '.matrix_url', array('value' => $attachment['matrix_url']));
			echo $this->Form->hidden($assocAlias . '.' . $count . '.matrix_num', array('value' => $attachment['matrix_num']));
		endif;
		
		echo $this->Form->hidden($assocAlias . '.' . $count . '.group', array('value' => $attachment['group']));
		echo $this->Form->hidden($assocAlias . '.' . $count . '.rank', array('value' => $attachment['rank'], 'class'=>'rank-input'));
		echo $this->Form->hidden($assocAlias . '.' . $count . '.model', array('value' => $model));
	?>
	<div class="controls">
		<div class="rank"><?php echo $attachment['rank']; ?></div>
		<?php 
		if (!empty($attachment['id'])):
		?>
		<div class="delete-controls">
			<?php
			echo $this->Html->link( 
				$this->Html->image('icons/cross.png', array('alt'=>'Delete', 'title'=>'Delete', 'width'=>'16', 'height'=>'16'))
				, '#'
				, array(
					'escape'=>false
					, 'data-action'=>array('action' => 'delete', $attachment['id'])
					, 'data-undo'=>array('action' => 'undelete', $attachment['id'])
					, 'data-attachment-id'=>$attachment['id']
					, 'class'=>'delete-attachment'
			));
			?>
			<div class="checkbox"><?php 
			echo $this->Form->checkbox($assocAlias . '.' . $count . '.delete'); 
			?></div>
		</div>
		<?php
		endif;
		?>
	</div>
	<div class="thumbnail">
		<?php 
		$thumb = "";
		
		if (!empty($attachment['is_matrix']) && !empty($attachment['matrix_url'])):
			$url = $attachment['matrix_url'];
			$thumb = '<img data-src="'.$url.'" width="80" height="80" class="thumb lazy">';
		else:
			if ((strstr(strtolower($assocAlias), 'image') || $attachmentType == 'image')):
				$thumb = $this->Media->image($attachment, $previewVersion, array('draggable'=>'false', 'class'=>array('lazy')));
			endif;
			if (empty($thumb)):
				$extensionAliases = array(
					'jpg' => array('jfif', 'jpeg')
				);
				
				$extension = substr($attachment['basename'], strrpos($attachment['basename'], ".") + 1);
				
				foreach ($extensionAliases as $true => $aliases) {
					if (in_array($extension, $aliases)) {
						$extension = $true;
					}
				}
				$iconDir = DS . "webroot" . DS . "img" . DS . 'file-icons' . DS . '512px' . DS;
				
				//use another thumbnail for other filetypes
				if (
					file_exists(APP . DS . "Plugin" . DS . "Media" . $iconDir . $extension . ".png" ) || file_exists(App::pluginPath("Media") . $iconDir . $extension . ".png" )
					|| 
					file_exists(CMS . DS . "Plugin" . DS . "Media" . $iconDir . $extension . ".png" ) || file_exists(App::pluginPath("Media") . $iconDir . $extension . ".png" )
					) {
					$thumb = $this->Html->image("Media.file-icons/512px/" . $extension . ".png", array('class'=>array( 'thumb' ), 'width'=>80, 'height'=>80, 'title'=>$attachment['basename'], 'alt'=>$attachment['basename']));
				} else {
					$thumb = $this->Html->image("Media.file-icons/512px/_blank.png", array('class'=>array( 'thumb' ), 'width'=>80, 'height'=>80, 'title'=>$attachment['basename'], 'alt'=>$attachment['basename']));
				}
				$thumb .= '<span class="name">' . $attachment['basename'] . '</span>';
			endif;
		endif;
		echo $thumb;
		?>
	</div>
	<div class="view-controls">
		<?php
			if (!empty($versions) && !empty($attachment['basename']) && empty($attachment['is_matrix'])):
				//link to the large version of the attachment
				echo $this->Html->link( 'View Original', $url, array('target' => '_blank', 'draggable'=>'false') );
				//add the crop button
				echo " | ";
				
				if (!isset($cropUrl)){
					$cropUrl = array(
						'plugin'	=> 'media'
						, 'controller'	=> 'attachments'
						, 'action'	=> 'crop'
						, 'admin'	=> true
					);
				}
				
				$cropUrl = am($cropUrl, array(
					'version'	=> array_keys($versions)[0]
					, 'plug'	=> (isset($plug) && !empty($plug)) ? $plug: $this->params['plugin']
					, 'troller'	=> (isset($troller) && !empty($troller)) ? $troller: $this->params['controller']
					, 'foreign_key'	=> $foreign_key
					, $attachment['id']
				));
				
				echo $this->Html->link(
					'Crop'
					, $cropUrl
					, array(
						'class'=>'crop-image'
						, 'draggable'=>'false'
					)
				);
			else:
				//
				$downloadName	= isset($attachment['matrix_url']) && strlen($attachment['matrix_url']) > 0
						? $attachment['foreign_key'] . '.jpg'
						: $attachment['basename'];
				//
				echo $this->Html->link( 'Download File', $url, array("download" => $downloadName));
			//
			endif;
			
			if (!empty($showSourceLink)):
				//if the attachment is linked to a custom field value we want to take the user the the model that field value is attached to rather than the field itself
				if ($attachment['model'] == 'CustomFieldValue'):
					$fieldValue = ClassRegistry::init('CustomField.CustomFieldValue')->findById($attachment['foreign_key']);
					if (!empty($fieldValue)):
						$link = array(
							'plugin'=>Inflector::underscore($this->Plugin->getModelsPlugin($fieldValue['CustomFieldValue']['model']))
							, 'controller'=>Inflector::underscore(Inflector::pluralize($fieldValue['CustomFieldValue']['model']))
							, 'action'=>'edit'
							, 'admin'=>true
							, $fieldValue['CustomFieldValue']['foreign_key']
						);
						$sourceLink = $this->Html->link('Source: '.$fieldValue['CustomFieldValue']['model'], array('admin'=>true, 'action'=>'edit') + $link);
					endif;
				else:
					$link = array(
						'plugin'=>Inflector::underscore($this->Plugin->getModelsPlugin($attachment['model']))
						, 'controller'=>Inflector::underscore(Inflector::pluralize($attachment['model']))
						, 'action'=>'edit'
						, 'admin'=>true
						, $attachment['foreign_key']
					);
					$sourceLink = $this->Html->link('Source: '.$attachment['model'], $link);
				endif;
				
				if (!empty($sourceLink)):
					echo "<br>" . $sourceLink;
				endif;
			endif;
		?>
	</div>
	<?php 
	if (empty($attachment['is_matrix'])):
	?>
	<div class="alternative">
		<?php
		echo $this->Form->input($assocAlias . '.' . $count . '.alternative', array(
			'value' => $attachment['alternative'], 
			'div' => false, 
			'label' => false
		)); 
		?>
	</div>
	<?php 
	endif;
	?>
	<div class="sort">
		<?php 
		echo $this->Html->image('icons/sort.png', array(
			'title'=>'Drag and drop to sort'
			, 'alt'=>'Drag and drop to sort'
			, 'class'=>'sort-icon'
			, 'width'=>15
			, 'height'=>16
			, 'draggable'=>'false'
		)); 
		?>
	</div>
</div>