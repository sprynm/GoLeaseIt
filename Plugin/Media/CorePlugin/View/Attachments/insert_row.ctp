<?php // 
// 
##echo $this->Html->tag('p', 'insert_row $imageItem:' . $this->Html->tag('pre', print_r($imageItem, true)));
// 
$whichElement	= isset($single) && $single
		? 'single'
		: 'attachment_row';
// 
$imageItem	= isset($imageItem)
		? $imageItem
		: array();
//
echo $this->element(
	'Media.' . $whichElement
	, array(
		'item'		=> $item
		, 'imageItem'	=> $imageItem
		, 'assocAlias'	=> $assocAlias
		, 'count'	=> $count
		, 'model'	=> $model
		, 'plug'	=> $plug
		, 'troller'	=> $troller
		,
	)
);