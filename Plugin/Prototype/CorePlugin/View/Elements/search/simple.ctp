<p>Simple Search</p>
<?php
echo $this->Form->create('PrototypeItem', array(
	'url' => array('plugin' => 'prototype', 'controller' => 'prototype_items', 'action' => 'search', 'instance' => $instance['PrototypeInstance']['slug']), 
	'class' => 'simple-search'
));
echo $this->Form->input('search', array(
	'label' => 'Search',
	'type' => 'text'
));
echo $this->Form->end('Search');
?>