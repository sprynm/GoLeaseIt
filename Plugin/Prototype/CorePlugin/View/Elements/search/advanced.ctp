<p>Advanced Search</p>
<?php
echo $this->Form->create('PrototypeItem', array(
	'url' => array('plugin' => 'prototype', 'controller' => 'prototype_items', 'action' => 'search', 'instance' => $instance['PrototypeInstance']['slug']), 
	'class' => 'advanced-search'
));
echo $this->Form->input('name', array(
	'label' => 'Name',
	'type' => 'text'
));

foreach ($customFields as $field): 
    echo $this->CustomField->searchField($field['CustomField']);
endforeach; 

echo $this->Form->end('Search');
?>