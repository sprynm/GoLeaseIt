<?php
$this->Prototype->instanceCss();
$this->Prototype->instanceJs();
?>

<?php foreach ($items as $item): ?>
<div class="people">
	<h4><?php echo $item['PrototypeItem']['name']; ?> -  <?php echo $item['PrototypeItem']['position']; ?></h4>
	<?php
	if ($item['Image']):
		echo $this->Media->mainImage($item['Image']);
	endif;
	?>
	<div class="info">
	<?php echo $item['PrototypeItem']['description']; ?> 
	</div>
</div>
<?php endforeach; ?>
<?php echo $instance['PrototypeInstance']['footer_text']; ?>