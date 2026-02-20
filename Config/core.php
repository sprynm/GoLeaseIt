<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The CMS will be considered installed when database.php and pyramid.php exist, both of which are created during
 * the Install plugin's install process.
 */
Configure::write('Install.installed_database', file_exists(APP . 'Config' . DS . 'database.php'));
Configure::write('Install.installed_settings', file_exists(APP . 'Config' . DS . 'pyramid.php'));
Configure::write('Install.installed', Configure::read('Install.installed_database') && Configure::read('Install.installed_settings'));

if (Configure::read('Install.installed')) {
    require APP . 'Config' . DS . 'pyramid.php';
} else {
	Configure::write('debug', 0);

	if (!defined('LOG_ERROR')) {
		define('LOG_ERROR', 2);
	}
	
	Configure::write('Session', array(
		'defaults' => 'php'
	));

	Configure::write('Error', array(
		'handler' => 'ErrorHandler::handleError',
		'level' => E_ALL & ~E_DEPRECATED,
		'trace' => true
		));

	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'ExceptionRenderer',
		'log' => true
		));
}