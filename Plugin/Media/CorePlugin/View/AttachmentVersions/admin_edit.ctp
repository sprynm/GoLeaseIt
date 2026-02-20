<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
//
	$this->set('header', 'Add/Edit Image Versions: ' . Inflector::humanize($model));
//
	$this->set('saveReturn', false);
?>
<?php
$this->start('formStart');
    echo $this->Form->create('AttachmentVersion', array('class' => 'editor_form'));
$this->end('formStart');
?>

<?php $this->start('actionLinks'); ?>

<?php 
$group = "Image";
//for the weird case of InstalledPlugin being the model, find the group name for it
if ($model == 'InstalledPlugin'): 
	$plugin = ClassRegistry::init('InstalledPlugin')->findById($foreignKey);
	if (!empty($plugin['InstalledPlugin']['alias'])): 
		$group = $plugin['InstalledPlugin']['alias'];
	endif;
endif;
?>

<?php echo $this->AdminLink->link('Regenerate images', array('action' => 'regenerate', 'model'=>$model, 'foreign_key' => $foreignKey, 'group'=>$group, '?' => $this->request->query)); ?>
<?php 
//if these versions are associated with a custom field value then link back to the item that that value is attached to
if ($model == 'CustomFieldValue'):
	$fieldValue = ClassRegistry::init('CustomFieldValue')->findById($foreignKey);
	if (!empty($fieldValue)):
		$backLink = array(
			'plugin' => Inflector::underscore($this->Plugin->getModelsPlugin($fieldValue['CustomFieldValue']['model']))
			, 'controller' => Inflector::tableize($fieldValue['CustomFieldValue']['model'])
			, 'action' => 'admin_edit'
			, 'admin' => true
			, $fieldValue['CustomFieldValue']['foreign_key']
		);
		//url requires the instance if we are trying to get to a prototype item's edit page
		if ($backLink['controller'] == 'prototype_items'):
			$item = ClassRegistry::init('Prototype.PrototypeItem')->findById($fieldValue['CustomFieldValue']['foreign_key']);
			if (!empty($item)):
				$instanceId = $item['PrototypeItem']['prototype_instance_id'];
				$instance = ClassRegistry::init('Prototype.PrototypeInstance')->findById($instanceId);
				if (!empty($instance)):
					$backLink['instance'] = $instance['PrototypeInstance']['slug'];
				endif;
			endif;
		endif;
		
		//use the name of the type of item we are linking back to eg: Page, PrototypeItem
		echo $this->AdminLink->link('Back to ' . Inflector::humanize(Inflector::underscore($fieldValue['CustomFieldValue']['model'])), $backLink);
	endif;
endif;
?>
<?php $this->end('actionLinks'); ?>

<?php $this->start('tabs'); ?>
<?php foreach ($versions as $key => $val): ?>
    <li><?php echo $this->Html->link($key, '#tab-' . Inflector::slug($key, '-')); ?></li>
<?php endforeach; ?>
<?php $this->end('tabs'); ?>

<?php
//
	foreach ($versions as $key => $val): ?>
<div id="<?php echo 'tab-' . Inflector::slug($key, '-'); ?>">
    <?php
     echo $this->element('Media.admin/image_version_table', array(
        'name' => $key,
        'alias' => 'AttachmentVersion',
        'group' => $key,
        'model' => $model,
        'foreign_key' => $foreignKey,
        'versionData' => $val
    ));
    ?>
</div>
<?php endforeach; ?>
<script>
//
	$(document).ready(function() {
	//
		if ($('a#regenerate_link').length > 0)
		{
		//
			setTimeout(function() {
			//
				window.location.href = $('a#regenerate_link').attr('href');
			}, 1500);
		}
	});
</script>