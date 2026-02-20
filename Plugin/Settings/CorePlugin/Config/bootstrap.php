<?php
/**
 * Linking cache configuration setup.
 */
AppCache::config('settings', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'probability' => 100,
	'groups' => array('settings')
));

// Add the 'link' group description
Configure::write('Cache.groups.settings', 'Website settings.');

$settings = Cache::read('loadSettings', 'settings');
if ($settings === false) {
	$settings = ClassRegistry::init('Settings.Setting')->findForLoad();
	Cache::write('loadSettings', $settings, 'settings');
}

foreach ($settings as $key => $val) {
	Configure::write('Settings.' . $key, $val);
}