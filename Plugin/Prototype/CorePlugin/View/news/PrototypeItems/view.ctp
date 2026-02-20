<?php echo $this->Time->format('M. j, Y', $item['PublishingInformation']['start']); ?>
<?php echo $item['PrototypeItem']['description']; ?>
<?php
// Returns a list of custom fields and values
echo $this->CustomField->fieldValueList($customFields);
?>

<?php
// Image(s)
if (isset($item['Image']) && !empty($item['Image'])):
    // Single image
    if ($instance['PrototypeInstance']['item_image_type'] == 'single'):
        echo $this->Media->mainImage($item['Image'], 'thumb');
    else:
        echo $this->element('Media.basic_gallery', array('gallery' => $item['Image']));
    endif;
endif;
?>

<?php
// Document(s)
if (isset($item['ItemDocument']) && !empty($item['Document'])):
    // Single image
    if ($instance['PrototypeInstance']['item_document_type'] == 'single'):
        echo $this->Media->document($item['Document'][0]);
    else:
        echo $this->element('Media.basic_repository', array('repository' => $item['Document']));
    endif;
endif;
?>

<?php echo $instance['PrototypeInstance']['footer_text']; ?>