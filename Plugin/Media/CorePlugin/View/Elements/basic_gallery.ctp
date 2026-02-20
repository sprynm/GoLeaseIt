<?php 
if (count($gallery) < 1) {
	return;
}

echo $this->element('fancybox');
?>
<div class="gallery">
	<?php
	foreach ($gallery as $key => $image) {
		$imgThumb = $this->Media->getImage($image, 'thumb', array('www' => true));
		list($width, $height, $type, $attr) = getimagesize($imgThumb);
		$imgAlt = $image['alternative'];
		$imgLarge = $this->Media->getImage($image, 'large');	
		
		echo "
		<a href='" . $imgLarge . "' data-fancybox='gallery' data-caption='" . $imgAlt . "' class='fancybox'>
			<img src='" . $imgThumb . "' width='" . $width . "' height='" . $height . "' alt='" . $imgAlt . "' loading='lazy'>
		</a>";
	}
	?>
</div>