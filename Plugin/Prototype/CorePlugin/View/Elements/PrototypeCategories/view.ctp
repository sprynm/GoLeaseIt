<?php // view.ctp
// 
echo $this->Html->tag('p', 'File: ' . __FILE__);
//
echo $this->Html->tag('p', '$category:' . $this->Html->tag('pre', print_r($category, true)));
//
echo $this->Html->tag('p', '$items:' . $this->Html->tag('pre', print_r($items, true)));
//
echo $this->Html->tag('h2', $category['PrototypeCategory']['name']);
//
echo $this->element('Prototype.item_summary', array('category' => $category, 'items' => $items));
?>