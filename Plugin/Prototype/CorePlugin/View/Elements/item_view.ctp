<?php // item_view.ctp
// 
echo $this->Html->tag('p', 'File: ' . __FILE__);
// Image(s)
if (isset($item['Image']) && !empty($item['Image'])):
    // Single image
    if ($instance['PrototypeInstance']['item_image_type'] == 'single'):
        echo $this->Media->mainImage($item['Image'], 'thumb');
    else:
        echo $this->element('Media.basic_gallery', array('gallery' => $item['Image']));
    endif;
endif;
// Document(s)
if (isset($item['Document']) && !empty($item['Document'])):
    // Single image
    if ($instance['PrototypeInstance']['item_document_type'] == 'single'):
        echo $this->Media->document($item['Document'][0]);
    else:
        echo $this->element('Media.basic_repository', array('repository' => $item['Document']));
    endif;
endif;
?>