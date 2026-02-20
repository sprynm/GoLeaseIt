<?php
if (!isset($count)):
    $count = 0;
endif;
if (!isset($model)):
	$model = $this->Form->model();
endif;
if (!isset($assocAlias)):
	$assocAlias = "Attachment";
else:
    $assocAlias = Inflector::singularize($assocAlias);
endif;

if (!isset($group)):
    $group = strtolower($assocAlias);
endif;
if (!isset($modelId)):
	$modelId = $this->Form->value($this->Form->model().".id");
endif;

?><div class="new">

<?php // 
// 
##echo $this->Html->tag('p', '$imageItem:' . $this->Html->tag('pre', print_r($imageItem, true)));
	//
	echo $this->Form->hidden($assocAlias . ".".$count.".model", array("value" => $model)) 
	. $this->Form->hidden($assocAlias . ".".$count.".group", array("value" => $group))
	. $this->Form->input($assocAlias . ".".$count.".file", array(
		"label" => __("File"),
		"type"  => "file",
		"error" => array(
			"error"      => __("An error occured while transferring the file."),
			"resource"   => __("The file is invalid."),
			"access"     => __("The file cannot be processed."),
			"location"   => __("The file cannot be transferred from or to location."),
			"permission" => __("Executable files cannot be uploaded."),
			"size"       => __("The file is too large."),
			"pixels"     => __("The file is too large."),
			"extension"  => __("The file has the wrong extension."),
			"mimeType"   => __("The file has the wrong MIME type."),
	)));
	//
	if (isset($imageItem) && $imageItem) :
		//
		echo $this->Form->input($assocAlias . ".".$count.".alternative", array(
			"label" => __("Caption"),
			"value" => "",
			"error" => __("Please provide a caption/textual replacement.")
		));
	// (isset($imageItem) && $imageItem)
	endif;
	//
	echo $this->Form->input($assocAlias . ".".$count.".published", array("type" => "hidden", "value" => 1));
?></div>