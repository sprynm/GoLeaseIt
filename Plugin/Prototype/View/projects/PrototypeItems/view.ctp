<div class="proj-dtl">	
	<div class="col frst-col">
		<?php
    // Image(s)
    if (isset($item['Image']) && !empty($item['Image'])):
      // Single image
      if ($instance['PrototypeInstance']['item_image_type'] == 'single'):
        echo $this->Media->mainImage($item['Image'], 'thumb');
      else:
        $galleryGroup = 'prototype-gallery-' . (int)$item['PrototypeItem']['id'];
        $firstImage = $item['Image'][0];
        $firstAlt = isset($firstImage['alternative']) ? (string)$firstImage['alternative'] : '';
        $firstLightboxConfig = array(
          'type' => 'image',
          'group' => $galleryGroup,
        );
        if ($firstAlt !== '') {
          $firstLightboxConfig['caption'] = $firstAlt;
        }
        echo $this->Html->link(
          $this->Media->image($item['Image'][0], 'large'),
          $this->Media->getImage($item['Image'][0]),
          array(
            'escape' => false, 
            'rel' => 'gallery', 
            'class' => 'port-photos gallery__link',
            'data-lightbox' => json_encode($firstLightboxConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
            'aria-label' => $firstAlt !== '' ? $firstAlt : 'Open image',
          )
        );
        array_shift($item['Image']);
        echo $this->element('Media.basic_gallery', array('gallery' => $item['Image'], 'group' => $galleryGroup));
      endif;
    endif;
    ?>		
	</div>
	<div class="col scnd-col">
		<?php echo $item['PrototypeItem']['description']; ?>		
	</div>
</div>

<?php echo $instance['PrototypeInstance']['footer_text']; ?>
