<?php
//
//determine if the model for CustomFieldValue exists and if not generate the skeleton
$FCustomFieldValue = new File(App::pluginPath("CustomFields").DS.'Model'.DS."CustomFieldValue.php");
if (!$FCustomFieldValue->exists()){
	$class = new Zend_CodeGenerator_Php_Class();

	$class->setName('CustomFieldValue');
	$class->setExtendedClass('CmsCustomFieldValue');
	$body = "App::uses('CmsCustomFieldValue', 'CustomFields.Model');";

	$deployCode = new Pyramid_CodeGenerator_Php_File(array(
		'body' => $body,
		'classes' => array(
			$class
		),		        
	));

	// Generate the code and write to the file
	$code = $deployCode->generate();
	$FCustomFieldValue->write($code);
}
$FCustomFieldValue->close();