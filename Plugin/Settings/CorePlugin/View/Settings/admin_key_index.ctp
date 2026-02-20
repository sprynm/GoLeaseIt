<?php $this->extend('Administration.Common/index-page'); ?>
<?php
//
$this->set('header', 'Add/Edit Settings');
//
$changeOrder	= $this->request->query('change_order')
		? true
		: false;
//
$this->start('actionLinks');
echo $this->Html->link(__(($changeOrder ? 'Back To List' : 'Change Item Order')), (!$changeOrder ? array('?' => array('change_order' => true)) : array()), array('class' => 'change-order'));
echo $this->AdminLink->link(__('New Setting'), array('action' => 'key_edit'));
$this->end('actionLinks');
//
if ($changeOrder) :
$this->start('formStart');
	echo $this->Form->Create('Setting', array('url' => array('action' => 'sort', 'admin' => true, 'Setting', 'Settings')));
	$this->end('formStart');
	
	$this->start('formEnd');
	echo $this->Form->submit('Save Order');
	echo $this->Form->end();
	$this->end('formEnd');
// ($changeOrder)
endif;
//
$count = 0;
?>

<table class="admin-table sortable">
	<?php echo $this->element('Administration.index/table_caption', array( 'paginate' => false )); ?>
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('key');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<?php echo $this->element('Administration.index/actions_header', array('paginate'=>false)); ?>
      <?php // 
      // 
      echo $changeOrder	? '<th class="icon-column sort-column">Sort</th>'
      			: '';
      ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($settings as $setting): ?>
		<tr class="sortable_row">
			<td><?php echo $setting['Setting']['key']; ?></td>
			<td><?php echo $setting['Setting']['type']; ?></td>
			<?php echo $this->element('Administration.index/actions_column', array('item' => $setting['Setting'], 'edit' => array('action' => 'key_edit', $setting['Setting']['id']))); ?>
			
      <?php // 
	// 
	if ($changeOrder) :
	      	//
		echo '<td class="icon-column sorting">' . $this->element('Administration.index/actions/sort', array('model' => 'Setting', 'count' => $count, 'item' => $setting['Setting'])) . '</td>';
		//
		$count++;
	// ($changeOrder)
	endif;
      ?>
      
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>
