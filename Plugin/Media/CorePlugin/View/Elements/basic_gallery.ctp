<?php 
if (count($gallery) < 1) {
	return;
}

$lightboxGroup = '';
if (isset($group) && $group !== '') {
	$lightboxGroup = (string)$group;
}
?>
<div class="c-gallery">
	<div class="gallery">
		<?php
		foreach ($gallery as $key => $image) {
			$imgThumb = $this->Media->getImage($image, 'thumb', array('www' => true));
			list($width, $height, $type, $attr) = getimagesize($imgThumb);
			$imgAlt = isset($image['alternative']) ? (string)$image['alternative'] : '';
			$imgLarge = $this->Media->getImage($image, 'large');
			$lightboxConfig = array(
				'type' => 'image',
			);
			if ($lightboxGroup !== '') {
				$lightboxConfig['group'] = $lightboxGroup;
			}
			if ($imgAlt !== '') {
				$lightboxConfig['caption'] = $imgAlt;
			}
			$lightboxConfigAttr = h(json_encode($lightboxConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
			$ariaLabel = $imgAlt !== '' ? $imgAlt : 'Open image';

			echo "
			<a href='" . h($imgLarge) . "' class='gallery__link' data-lightbox='" . $lightboxConfigAttr . "' aria-label='" . h($ariaLabel) . "'>
				<img src='" . h($imgThumb) . "' width='" . $width . "' height='" . $height . "' alt='" . h($imgAlt) . "' loading='lazy'>
			</a>";
		}
		?>
	</div>
</div>
