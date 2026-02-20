<?php
$itemData = isset($item['PrototypeItem']) ? $item['PrototypeItem'] : array();

$title = '';
if (!empty($itemData['heading'])) {
	$title = $itemData['heading'];
} elseif (!empty($itemData['name'])) {
	$title = $itemData['name'];
} elseif (!empty($itemData['title'])) {
	$title = $itemData['title'];
}

$content = '';
if (!empty($itemData['content'])) {
	$content = $itemData['content'];
} elseif (!empty($itemData['text'])) {
	$content = $itemData['text'];
}

$image = (!empty($item['Image']) && !empty($item['Image'][0])) ? $item['Image'][0] : null;
$imageAlt = ($image && !empty($image['alternative'])) ? $image['alternative'] : $title;
?>

<article class="c-split-platter">
	<?php if ($image): ?>
		<div class="c-split-platter__media">
			<picture>
				<img src="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
			</picture>
		</div>
	<?php endif; ?>

	<div class="c-split-platter__body">
		<?php if ($title !== ''): ?>
			<h2 class="c-split-platter__title"><?php echo h($title); ?></h2>
		<?php endif; ?>

		<?php if ($content !== ''): ?>
			<div class="c-split-platter__content">
				<?php echo $content; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
