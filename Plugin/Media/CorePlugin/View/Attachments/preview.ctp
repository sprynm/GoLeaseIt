<?php
$delete = $this->Html->link(
	$this->Html->image(
		'icons/cross.png',
		array(
			'alt' => 'Delete',
			'title' => 'Delete',
			'width' => 16,
			'height' => 16
		)
	)
	, array(
		'plugin' => 'media', 
		'controller' => 'attachments', 
		'action' => 'delete', 
		$item['id'], 'admin' => true
	)
	, array('escape' => false,
			'class' => 'admin-btn')
	, "Are you sure you want to delete this item?"
);

if($item['group'] == 'Document') {
	echo $delete . $this->Html->link( $item['basename'], $this->Media->transferUrl($item), array( 'target' => '_blank' ));
}

if($item['group'] == 'Image') {
	echo $delete . $this->Media->image($item, 'thumb');
	
}

 