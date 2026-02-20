<?php
CakePlugin::load('Search', array('path' => CMS . 'Plugin' . DS . 'Search' . DS));

App::import('Model', 'Model', false);
$model = new Model(array(
	'name' => 'BootstrapInstance',
	'table' => 'prototype_instances',
	'ds' => 'default'
));

// Had to put head_title stuff here because putting in the schema was not working for some reason.
if (!$model->hasField('use_page_banner_images')) {
//if (!$model->hasField('head_title')) {
	AppCache::clear();
	$model->query("ALTER TABLE " . $model->tablePrefix . "prototype_instances ADD COLUMN use_page_banner_images TINYINT(1) NOT NULL DEFAULT '1'");
	$model->query("ALTER TABLE " . $model->tablePrefix . "prototype_instances ADD COLUMN use_page_banner_image_categories TINYINT(1) NOT NULL DEFAULT '1'");
	$model->query("ALTER TABLE " . $model->tablePrefix . "prototype_instances ADD COLUMN use_page_banner_image_items TINYINT(1) NOT NULL DEFAULT '1'");
	/*$model->query("ALTER TABLE prototype_instances ADD COLUMN head_title varchar(150) NOT NULL");
	$model->query("ALTER TABLE prototype_categories ADD COLUMN head_title varchar(150) NOT NULL");
	$model->query("ALTER TABLE prototype_items ADD COLUMN head_title varchar(150) NOT NULL");*/
	AppCache::clear();
	header("Location: {$_SERVER['REQUEST_URI']}");
	exit();
}

$instances = $model->find('all', array(
	'cache' => true,
	'fields' => array('id', 'name', 'slug')
));
foreach ($instances as $instance) {
	Configure::write('Plugins.' . $instance['BootstrapInstance']['name'], array(
		'name' => 'Prototype',
		'alias' => $instance['BootstrapInstance']['name'],
		'slug' => $instance['BootstrapInstance']['slug'],
		'_instance' => $instance,
		'internal' => 0
	));
}
