<?php
$adminInstances = ClassRegistry::init('Prototype.PrototypeInstance')->find('list', array(
	'fields' => array(
		'PrototypeInstance.id', 
		'PrototypeInstance.slug'
	), 
	'cache' => 'admin_prototype_instance_list'
));

// Extra routing information needed by this plugin
$instances = ClassRegistry::init('Prototype.PrototypeInstance')->find('list', array(
	'fields' => array(
		'PrototypeInstance.id', 
		'PrototypeInstance.slug'
	), 
	
	'conditions' => array('PrototypeInstance.allow_instance_view' => true),
	'cache' => 'prototype_instance_list',
	'published' => true
));

$instanceMatch = implode('|', $instances);
$adminInstanceMatch = implode('|', $adminInstances);

$categories = ClassRegistry::init('Prototype.PrototypeCategory')->find('list', array(
	'fields' => array(
		'PrototypeCategory.id', 
		'PrototypeCategory.slug'
	), 
	'cache' => 'prototype_category_list'
));
$categoryMatch = implode('|', $categories);

ClassRegistry::init('Prototype.PrototypeItem');

if (!empty($adminInstanceMatch)) {
	Router::connect('/admin/:instance', array(
		'plugin' =>  'prototype',
		'controller' => 'prototype_items', 
		'action' => 'index',
		'admin' => true
		), array(
			'instance' => $adminInstanceMatch
		)
	);

	Router::connect('/admin/:instance/items/:action/*', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_items', 
		'admin' => true
		), array(
			'instance' => $adminInstanceMatch
		)
	);

	Router::connect('/admin/:instance/categories/:action/*', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_categories', 
		'admin' => true
		), array(
			'instance' => $adminInstanceMatch
		)
	);
	
	Router::connect('/admin/:instance/:action/*', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_instances', 
		'admin' => true
		), array(
			'instance' => $adminInstanceMatch
		)
	);
}

if (!empty($instanceMatch)) {
	// Category and item routes
	Router::connect('/:instance/search/*', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_items', 
		'action' => 'search'
		), array(
			'instance' => $instanceMatch
		)
	);
	Router::connect('/:instance/featured', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_items', 
		'action' => 'featured'
		), array(
			'instance' => $instanceMatch
		)
	);
	Router::connect('/:instance/view/:id-:slug', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_items', 
		'action' => 'view'
		), array(
			'instance' => $instanceMatch, 
			'slug' => '[a-z0-9_-]+',
			'id' => '[0-9]+'
		)
	);
	Router::connect('/:instance/:category/*', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_categories', 
		'action' => 'view'
		), array(
			'instance' => $instanceMatch, 
			'category' => $categoryMatch
		)
	);
	Router::connect('/:instance/*', array(
		'plugin' => 'prototype', 
		'controller' => 'prototype_instances', 
		'action' => 'view'
		), array(
			'instance' => $instanceMatch
		)
	);
}
