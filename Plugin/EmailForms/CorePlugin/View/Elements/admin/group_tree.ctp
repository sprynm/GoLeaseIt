<?php
extract($data);
?>
<div class="menu-item rounded-corners">
	<strong>GROUP: <?php echo $name; ?></strong>
</div>
<?php 
echo $this->Tree->generate(
	$EmailFormField,
	array('plugin' => 'email_forms', 'element' => 'admin/field_tree', 'model' => 'EmailFormField', 'type' => 'ol', 'ulClass' => null, 'liClass' => null, 'liId' => 'list_field_', 'startDepth' => 1)
);
?>