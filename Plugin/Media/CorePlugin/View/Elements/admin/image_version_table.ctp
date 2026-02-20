<?php
$this->Html->script('Media.version_ajax', array('inline' => false, 'once' => true));
$this->Html->script('Media.spectrum', array('inline' => false, 'once' => true));
$this->Html->css('Media.spectrum', null, array('inline' => false));



if (!isset($model)):
	$model = $this->Form->model();
endif;

if (!isset($foreignKey)):
    $foreignKey = isset($this->data[$model]['id']) ? $this->data[$model]['id'] : null;
endif;

if (!$name && $name !== false):
    $name = 'Item Image Versions';
endif;

if (!isset($alias)):
    $alias = 'AttachmentVersion';
endif;

if (!isset($group)):
    $group = 'Image';
endif;

if (!isset($item)):
    $item = '';
endif;

if (!isset($versionData)):
    $versionData = $this->request->data[$alias];
endif;

$startCount = intval(Configure::read("ImageVersions.startCount"));

$tableId = 'version-table-' . $startCount;

?>
<?php if ($name): ?>
<h3><?php echo $name; ?></h3>
<?php endif; ?>
<?php
//
	if (isset($versions) && empty($versions))
	{
	//
		print '<h3>This image version has not been saved to the database. Save me!</h3>';
	}
?>
<table id="<?php echo $tableId; ?>" rel="<?php echo str_replace(' ', '_', $group) . '|' . $alias . '|' . $model . '|' . $foreignKey; ?>">
    <thead>
        <tr>
            <th>Version</th>
            <th>Type</th>
            <th>Format</th>
            <th>Width</th>
            <th>Height</th>
            <th>bgcolour</th>
						<?php if (isset($versions[0]['compression'])): ?>
            <th title="Compression (0.0 - 9.9 | higher = more compression )">Compression</th>
						<?php endif; ?>
            <th class="actions icon-column">Delete</th>
            <th>Regenerate Version</th>
        </tr>
    </thead>
    <tbody>
<?php
//
	$i = $startCount+1;
//
	foreach ($versionData AS $version)
	{
	//
		echo $this->element(
			'Media.admin/image_version_row'
			, array(
				'version'	=> $version
				, 'alias'	=> $alias
				, 'count'	=> $i
				, 'model'	=> $model
				, 'group' => $group
			)
		);
	//
		$i++;
	}
	
	Configure::write("ImageVersions.startCount", $i);
?>
    </tbody>
</table>
<h4 id="add_header"><?php echo $this->Html->link('Add Another Version', '#', array('class' => 'add_new_version', 'rel' => $tableId)); ?></h4>
<script>
$(document).ready(function() {

$(".colour_picker").spectrum({
    color: "#f00",
    showInput: true,
    preferredFormat: "hex",
    change: function(color) {
        $('#colour_picker_' + $(this).data('colourpicker')).val(color.toHexString());
    }
});

});
</script>