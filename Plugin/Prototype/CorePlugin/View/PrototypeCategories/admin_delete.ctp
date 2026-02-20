<?php 
$this->set('header', 'Delete ' . Inflector::singularize($category['PrototypeInstance']['name']) . ' Category: ' . $category['PrototypeCategory']['name']);
?>
<div id="category-delete-wrapper">
    <?php echo $this->CmsForm->create('PrototypeCategory', array('class' => 'editor_form', 'url' => '/'.$this->params['url']['url']));?>
    <p>This category has one or more child items. Before deleting, would you like to delete those child items, or move them to another category?</p>
    <?php 
    echo $this->CmsForm->submit('Delete Category and Child Items', array('name' => 'delete_all')); 
    echo $this->CmsForm->input('prototype_category_id', array(
        'label' => 'Select a New Category',
        'options' => $categories,
        'type' => 'select'
    ));    
    echo $this->CmsForm->submit('Delete Category and Move Child Items', array('name' => 'delete_move'));
    ?>
</div>
