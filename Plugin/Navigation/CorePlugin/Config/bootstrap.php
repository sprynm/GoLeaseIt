<?php
/**
 * Navigation cache configuration setup.
 */
AppCache::config('navigation', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'probability' => 100,
	'groups' => array('navigation')
));

// Add the 'link' group description
Configure::write('Cache.groups.navigation', 'Navigation menus.');