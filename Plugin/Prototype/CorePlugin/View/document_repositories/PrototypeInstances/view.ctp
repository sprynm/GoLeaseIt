<?php
$this->Prototype->instanceCss();
$this->Prototype->instanceJs();
?>

<ul class="document-repository">
	<?php 
	foreach ($items as $item): 
		$link = $this->Media->path($item['ItemDocument'][0]);
	?>
	<li><?php echo $this->Html->link($item['PrototypeItem']['name'], $link, array('target' => '_blank')); ?></li>
	<?php endforeach; ?>
</ul>
<?php echo $instance['PrototypeInstance']['footer_text']; ?>