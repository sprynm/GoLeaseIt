<?php
extract($data);
$link = Router::url($this->ModelLink->link('Prototype.PrototypeCategory', $PrototypeCategory['id']), true);

$title = $PrototypeCategory['name'];
?>
<div class="menu-item rounded-corners">
	<span class="item-title">
		<strong><?php echo $title; ?></strong>
		<span class="item-controls">
			<?php
			echo $this->Html->link('Edit', array('action' => 'edit', 'instance' => $this->Prototype->fetch('PrototypeInstance.slug'), $PrototypeCategory['id']));
			echo $this->Html->link('Delete', array('action' => 'delete', 'instance' => $this->Prototype->fetch('PrototypeInstance.slug'), $PrototypeCategory['id']), null, "Are you sure you want to delete this category?");
			?>
		</span>
	</span>
	<?php echo $this->Html->link($link, $link, array('target' => '_blank')); ?>
</div>
