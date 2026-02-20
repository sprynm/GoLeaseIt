<?php
//
	if (isset($version) && is_array($version) && isset($model))
	{
	//
		$version['model'] = $model;
	}
// Existing record
	if (isset($version) && is_array($version) && isset($version))
	{
	//
		extract($version);
	} else
	{
	// New record
		$id = null;
		$name = null;
		$type = 'basic';
		$convert = 'image/pdf';
		$width = null;
		$height = null;
	}
?>
<tr id="image-version-<?php echo $count; ?>">
    <td>
    <?php
    echo $this->Form->hidden($alias . '.' . $count . '.id', array('value' => $id));

    echo $this->Form->hidden($alias . '.' . $count . '.foreign_key', array('value' => $foreign_key));
    
    // Can't delete thumb.
    if ($name == 'thumb'):
        echo $name;
    else:
        echo $this->Form->input($alias . '.' . $count . '.name', array('div' => false, 'label' => false, 'value' => $name, 'size' => '10'));
    endif;
    
    echo $this->Form->input($alias . '.' . $count . '.model', array('type' => 'hidden', 'value' => $model));
    echo $this->Form->input($alias . '.' . $count . '.group', array('type' => 'hidden', 'value' => $group));
    ?>
    </td>
    <td>
    <?php 
    echo $this->Form->input($alias . '.' . $count . '.type', array(
        'div' => false, 
        'label' => false, 
        'type' => 'select', 
        'options' => $this->MediaVersion->cropTypeList(),
        'value' => $type
    )); 
    ?>
    </td>
    <td>
    <?php 
    echo $this->Form->input($alias . '.' . $count . '.convert', array(
        'div' => false, 
        'label' => false,
        'type' => 'select',
        'options' => $this->MediaVersion->imageConvertList(),
        'value' => $convert
    )); 
    ?>
    </td>
    <td><?php echo $this->Form->input($alias . '.' . $count . '.width', array('div' => false, 'label' => false, 'value' => $width, 'class' => 'small',  'style' => 'width:4em;')); ?></td>
    <td><?php echo $this->Form->input($alias . '.' . $count . '.height', array('div' => false, 'label' => false, 'value' => $height, 'class' => 'small', 'style' => 'width:4em;' )); ?></td>
    <td><span class="colour_picker" data-colourpicker="<?php print $count; ?>">colour</span> <?php echo $this->Form->input($alias . '.' . $count . '.bgcolour', array('div' => false, 'label' => false, 'value' => !empty($bgcolour) ? $bgcolour: '#ff0000', 'class' => 'small', 'size' => '10', 'id' => "colour_picker_" . $count)); ?></td>
    <?php  if (isset($version)): ?>
			<?php if (isset($version['compression'])): ?>
		<td><?php 
		echo $this->Form->input($alias . '.' . $count . '.compression', array('div' => false, 'label' => false, 'value' => $compression, 'class' => 'small',  'style' => 'width:4em;'));
		?></td>
			<?php endif; ?>
    <td class="actions icon-column">
        <?php
        // Can't delete thumb.
        if ($name != 'thumb' && isset($version['id']) && !empty($version['id'])):
            echo $this->element('Administration.index/actions/delete', array(
                'url' => array('plugin' => 'media', 'controller' => 'attachment_versions', 'action' => 'delete', $version['id']),
                'item' => $version
            ));
        endif;
        ?>
    </td>
	
		<td>
			<?php
				$linkParams = array(
					'action' => 'regenerate',
					'model' => $model, 
					'version' => $name, 
					'foreign_key' => $foreign_key, 
					'group' => $group,
				);
				
				if (!empty($this->request->query['attachmentModel'])):
					$linkParams['attachmentModel'] = $this->request->query['attachmentModel'];
					//since the attachmentModel might have a '.' it can't be the last named param
					$linkParams['padding_param'] = 1;
				endif;
				
				echo $this->AdminLink->link('Regenerate ' . $name, $linkParams ); 
			?>
		</td>
	
	
    <?php endif; ?>
</tr>
