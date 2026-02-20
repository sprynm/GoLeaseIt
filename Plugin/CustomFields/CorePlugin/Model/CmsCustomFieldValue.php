<?php
class CmsCustomFieldValue extends CustomFieldsAppModel {
	
/**
 * hasMany associations
 */
	public $hasMany = array(
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Attachment.model'=>'CustomFieldValue'),
			'dependent' => true
		)
	);
}
?>