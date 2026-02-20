<tr class="sortable_row">
	<td>
		<?php
		echo $this->Form->input('MetaKey.' . $count . '.id', array(
			'type' => 'hidden', 
			'value' => isset($item['id']) ? $item['id'] : null
		));
	    echo $this->Form->input('MetaKey.' . $count . '.name', array(
			'type' => 'text', 
			'value' => isset($item['name']) ? $item['name'] : null,
			'label' => false,
			'div' => false
		));
		?>
	</td>
	<td>
	<?php
	echo $this->Form->input('MetaKey.' . $count . '.type', array(
		'type' => 'select',
		'options' => $types,
		'value' => isset($item['type']) ? $item['type'] : null,
		'label' => false,
		'div' => false
	));
	?>
	</td>
	<td>
	<?php
	if ($item['allow_default']):
		echo $this->Form->input('MetaKey.' . $count . '.default', array(
			'type' => 'text', 
			'value' => isset($item['default']) ? $item['default'] : null,
			'label' => false,
			'div' => false
		));
	else:
		echo '&nbsp;';
	endif;
	?>
	<td class="actions icon-column">
		<?php
		if (isset($item['id'])):
		    echo $this->element('Administration.index/actions/delete', array('url' => array('plugin' => 'metas', 'controller' => 'meta_keys', 'action' => 'delete', 'admin' => true, $item['id'])));
		else:
			echo '&nbsp;';
		endif;
		?>
	</td>
</tr>