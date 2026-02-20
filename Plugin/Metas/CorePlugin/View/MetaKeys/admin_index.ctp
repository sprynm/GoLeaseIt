<?php $this->extend('Administration.Common/index-page'); ?>
<?php $this->set('header', 'Default Meta Tag Keys'); ?>
<?php
echo $this->Html->script('Metas.ajax', array('once' => true, 'inline' => false)); 

$this->start('formStart');
	echo $this->Form->create('MetaKey', array('class' => 'editor_form', 'url' => $this->request->here));
$this->end('formStart');

$this->start('formEnd');
	echo $this->Form->end('Save');
$this->end('formEnd');
?>

<table class="sortable field-group">
	<thead>
		<th>Name</th>
		<th>Type</th>
		<th>Default</th>
		<th class="icon-column">Delete</th>
	</thead>
	<tbody>
		<?php 
		foreach ($this->request->data as $key => $val): 
			echo $this->element('Metas.admin/table_row', array(
				'count' => $key,
				'item' => $val['MetaKey']
			));
		endforeach;
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11">
			<?php
			echo $this->Html->link(
				'Add a new meta key', 
				array('plugin' => 'metas', 'controller' => 'meta_keys', 'action' => 'new', 'admin' => true),
				array('class' => 'add-new-meta')
			);
			?>
			</td>
		</tr>
	</tfoot>
</table>