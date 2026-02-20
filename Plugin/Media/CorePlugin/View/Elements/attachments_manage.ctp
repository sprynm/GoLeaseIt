<?php
/**
 * Attachments Element File
 *
 * Element listing associated attachments of the view's model.
 * Add, delete (detach) an Attachment. This element requires
 * the media helper to be loaded and `$this->data` to be populated.
 *
 * Possible options:
 *  - `'previewVersion'` Defaults to `'xxs'`.
 *  - `'assocAlias'` Defaults to `'Attachment'`.
 *  - `'model'` Defaults to the model of the current form.
 *  - `'title'` Defaults to the plural form of `'assocAlias'`.
 *
 * Copyright (c) 2007-2010 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
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
if (!isset($group)){
	$group = ucwords(Inflector::humanize(Inflector::underscore($assocAlias)));
}
if (!isset($model)) {
	$model = $this->CmsForm->model();
}
$modelId = $this->CmsForm->value($this->CmsForm->model().'.id');
if (!isset($title)) {
	$title = sprintf(__('%s', true), Inflector::pluralize($assocAlias));
}
if (isset($single) && $single == true) {
	echo $this->element('single', array('plugin' => 'media', 'previewVersion' => $previewVersion, 'assocAlias' => $assocAlias, 'model' => $model, 'title' => $title));
	return;
}
$count = 0;
echo $this->CmsHtml->script('/media/js/ajax.js', array('inline' => false, 'once' => true));
?>
<div class="attachments element">
	<!-- Existing Attachments -->
<?php 
if (isset($this->data[$assocAlias])){
?>
	<h3>Existing Files</h3>
	<table>
	<thead>
	<tr>
	<th><input name="delete-all" type="checkbox" value=="delete-all" class="check-all">Delete</input></th>
	<th>&nbsp;</th>
	<th>File</th>
	<th>Caption</th>
	</tr>
	</thead>
	<tbody>
<?php
	//debug( $this->data[$assocAlias] );
	while ($count < count($this->data[$assocAlias])){
		$item = $this->data[$assocAlias][$count];
		//debug($item);
		// Build a few display variables.
		$preview = null;
		$size = null;
		$name = null;
		$type = null;
		if ($file = $media->file($item)) {
			$url = $media->url($file);
			$preview = $media->file($previewVersion . '/', $item);
	 		$Media = Media::factory($file);
			$size = $media->size($file);
			if (isset($number)) {
				$size = $number->toReadableSize($size);
			} else {
				$size .= ' Bytes';
			}
			$name = $item['basename'];
			$type = strtolower($Media->name);
		}
?>
	<tr>
	<td>
<?php 
		echo $this->CmsForm->input($assocAlias . '.' . $count . '.delete', array(
			'type' => 'checkbox', 'label' => false, 'div' => false, 'value' => 0
		)); 
?>
	</td>
	<td>
<?php
		echo $this->CmsForm->hidden($assocAlias . '.' . $count . '.id', array('value' => $item['id']));
		echo $this->CmsForm->hidden($assocAlias . '.' . $count . '.model', array('value' => $model));
		echo $this->CmsForm->hidden($assocAlias . '.' . $count . '.group', array('value' => $item['group']));
		echo $this->CmsForm->hidden($assocAlias . '.' . $count . '.dirname', array('value' => $item['dirname']));
		echo $this->CmsForm->hidden($assocAlias . '.' . $count . '.basename', array('value' => $item['basename']));

		if( $file )
		{
			echo $media->embed( $preview , array( 'restrict' => array( 'image' ) ) );
		} else {
			echo '&nbsp;';
		}
?>
	</td>
	<td>
<?php 
		if ($file){
			$file_name = ( strlen( $item['basename'] ) > 20 ) ?  substr( $item['basename'] , 0 , 17 ) . '...' :  $item['basename'] ;
			echo $this->CmsHtml->link( $file_name , $url ) . '<br />' . $type . ' - ' . $size;
		} else {
			echo '&nbsp';
		}
?>
	</td>
	<td>
<?php 
		echo $this->CmsForm->input($assocAlias . '.' . $count . '.alternative', array('value' => $item['alternative'], 'div' => false, 'label' => false)); 
?>
	</td>
</tr>
<?php 
		$count++; 
	} //endwhile
?>
	</tbody>
	</table>
<?php 
} //endif
?>
	<!-- New Attachment -->
	<h3>Add a New File</h3>
<?php 
echo $this->element('new_file', array('plugin' => 'media', 'count' => $count, 'assocAlias' => $assocAlias, 'model' => $model, 'group' => $group)); 
echo $this->CmsHtml->link('Add Another File', '#', array('class' => 'add_new_photo', 'rel' => $assocAlias . '|' . $model . '|' . str_replace(' ', '_', $group))); 
?>
</div>

