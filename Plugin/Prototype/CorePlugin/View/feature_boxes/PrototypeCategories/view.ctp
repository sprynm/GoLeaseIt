<?php 
if (isset($category['PrototypeCategory']['description'])):
	echo $category['PrototypeCategory']['description'];
endif;

echo $this->element('Prototype.item_summary');
?>

<?php echo $instance['PrototypeInstance']['footer_text']; ?>