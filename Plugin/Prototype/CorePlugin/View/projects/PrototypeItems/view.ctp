<div class="proj-dtl">	
	<div class="col frst-col">
		<?php
    // Image(s)
    if (isset($item['Image']) && !empty($item['Image'])):
      // Single image
      if ($instance['PrototypeInstance']['item_image_type'] == 'single'):
        echo $this->Media->mainImage($item['Image'], 'thumb');
      else:
        echo $this->Html->link(
          $this->Media->image($item['Image'][0], 'large'),
          $this->Media->getImage($item['Image'][0]),
          array(
            'escape' => false, 
            'rel' => 'gallery', 
            'class' => 'fancybox port-photos', 
            'data-fancybox' => 'gallery'
          )
        );
        array_shift($item['Image']);
        echo $this->element('Media.basic_gallery', array('gallery' => $item['Image']));
      endif;
    endif;
    ?>		
	</div>
	<div class="col scnd-col">
		<?php echo $item['PrototypeItem']['description']; ?>		
	</div>
</div>

<?php echo $instance['PrototypeInstance']['footer_text']; ?>