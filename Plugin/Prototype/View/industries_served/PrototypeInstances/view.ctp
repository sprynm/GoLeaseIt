<?php
$this->Prototype->instanceCss();
$this->Prototype->instanceJs();

if ($this->Prototype->hasCategories()):
	echo $this->element('Prototype.category_summary');
else:
	echo $this->element('Prototype.item_summary');
endif;
?>

<?php echo $instance['PrototypeInstance']['footer_text']; ?>