<?php
/**
 * Metas cache configuration setup.
 */
AppCache::config('meta', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'probability' => 100,
	'groups' => array('meta', 'model')
));

// Add the 'meta' group description
Configure::write('Cache.groups.meta', 'Item meta tag information.');