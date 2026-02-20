<?php
echo $this->element('Prototype.search/simple');
echo $this->element('Prototype.search/advanced');
?>

<?php
if (isset($items) && !empty($items)):
	echo $this->element('Prototype.item_summary', array('items' => $items));
endif;
?>