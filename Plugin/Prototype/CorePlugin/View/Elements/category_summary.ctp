<?php
echo $this->Tree->generate(
	$categories,
	array('plugin' => 'prototype', 'element' => 'category_tree', 'model' => 'PrototypeCategory', 'type' => 'ul', 'ulClass' => null, 'liClass' => null, 'liId' => null)
);
?>