<?php
/**
 * This is email configuration file.
 *
 * Use it to configure email transports of Cake.
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
 * @since         CakePHP(tm) v 2.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * In this file you set up your send email details.
 *
 * @package       cake.config
 */
/**
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *		Mail 		- Send using PHP mail function
 *		Smtp		- Send using SMTP
 *		Debug		- Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email.  Transports should be named 'YourTransport.php',
 * where 'Your' is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */
class EmailConfig {

	public $default = array(
		'transport' => 'Mail',
		'from' => 'you@localhost',
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);

/*
https://www.smtp2go.com/setupguide/cakephp/
https://www.smtp2go.com/setupguide/cakephp-using-cakeemail/
*/
	// 
	public $smtp2go = array(
		'transport' => 'Smtp',
		'host' => 'tls://mail.smtp2go.com',
		'port' =>  465,
		'username' => '',
		'password' => '',
	);

	// https://help.opensrs.com/hc/en-us/articles/204769868-Set-up-Hosted-Email-Services-
	public $open_srs = array(
		'transport' => 'Smtp',
		'from' => array('do-not-reply@cubancigar-shop.com' => 'Cuban Cigar Shop'),
		'host' => 'mail.hostedemail.com', // mail.emailhome.com
		'port' =>  25, // 8025 465 tls: 587 25
		'timeout' => 30,
		'username' => 'do-not-reply@cubancigar-shop.com',
		'password' => 'bumpyT85me51',
		'client' => null,
		'log' => true,
		'tls' => true
	);

	// 
	public $dreamhost = array(
		'transport' => 'Smtp',
		'from' => array('' => 'Cuban Cigar Shop'),
		'host' => 'smtp.dreamhost.com',
		'port' =>  587,
		'timeout' => 30,
		'username' => '',
		'password' => '',
		'client' => null,
		'log' => true,
		'tls' => true
	);

	//
	public $office365 = array(
		'transport' => 'Smtp',
		'from' => array('info@heirloomlinens.com' => 'Heirloom Linens'),
		'host' => 'smtp.office365.com',
		'port' =>  587,
		'timeout' => 30,
		'username' => 'info@heirloomlinens.com',
		'bcc' => 'roger@radarhill.com',
		'password' => 'Joc07778',
		'client' => null,
		'log' => true,
		'tls' => true
	);

	//
	public $fast = array(
		'from' => 'you@localhost',
		'sender' => null,
		'to' => null,
		'cc' => null,
		'bcc' => null,
		'replyTo' => null,
		'readReceipt' => null,
		'returnPath' => null,
		'messageId' => true,
		'subject' => null,
		'message' => null,
		'headers' => null,
		'viewRender' => null,
		'template' => false,
		'layout' => false,
		'viewVars' => null,
		'attachments' => null,
		'emailFormat' => null,
		'transport' => 'Smtp',
		'host' => 'localhost',
		'port' => 25,
		'timeout' => 30,
		'username' => 'user',
		'password' => 'secret',
		'client' => null,
		'log' => true,
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);

}
