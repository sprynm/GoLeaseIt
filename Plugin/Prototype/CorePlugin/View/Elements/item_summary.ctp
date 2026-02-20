<?php
if (!isset($paginatedItems)):
	$paginatedItems = false;
endif;

if ($paginatedItems == true):
	echo $this->element('pagination/top', array('showOptions' => false, 'createForm' => false));
endif;
?>
<ul>
    <?php foreach ($items as $item): ?>
    <li><?php echo $this->Html->link($item['PrototypeItem']['name'], $this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id'])); ?></li>
    <?php endforeach; ?>
</ul>
<?php
if ($paginatedItems == true):
	echo $this->element('pagination/bottom');
endif;
?>
