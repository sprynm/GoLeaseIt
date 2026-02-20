<?php 
extract($data);
echo $this->Html->link($PrototypeCategory['name'], $this->ModelLink->link('Prototype.PrototypeCategory', $PrototypeCategory['id'])); 
?>