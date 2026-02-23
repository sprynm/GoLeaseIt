<?php
$itemData = isset($item['PrototypeItem']) ? $item['PrototypeItem'] : array();
$heading = isset($itemData['heading']) && $itemData['heading'] !== '' ? $itemData['heading'] : (isset($itemData['name']) ? $itemData['name'] : '');
$title = isset($itemData['title']) ? $itemData['title'] : '';
$subheading = isset($itemData['subheading']) ? $itemData['subheading'] : '';
$content = isset($itemData['text']) ? $itemData['text'] : (isset($itemData['content']) ? $itemData['content'] : '');
$ctaText = isset($itemData['cta_link_text']) ? $itemData['cta_link_text'] : (isset($itemData['cta_text']) ? $itemData['cta_text'] : '');
$ctaLink = isset($itemData['cta_link']) ? $itemData['cta_link'] : '';

$image = (!empty($item['Image']) && !empty($item['Image'][0])) ? $item['Image'][0] : null;
$imageAlt = $image && !empty($image['alternative']) ? $image['alternative'] : $heading;
$imageCaption = $image && !empty($image['caption']) ? $image['caption'] : '';
?>

<article class="tile">
	<?php if ($image): ?>
		<figure class="tile__media">
			<picture>
				<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" media="(min-width: 1241px)">
				<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'medium')); ?>" media="(min-width: 481px)">
				<img src="<?php echo $this->Media->getImage($image, array('version' => 'thumb')); ?>" width="800" height="600" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
			</picture>
			<?php if ($imageCaption !== ''): ?>
				<figcaption class="tile__caption"><?php echo h($imageCaption); ?></figcaption>
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<span class="tile__overlay"></span>
	<div class="tile__content">
		<?php if ($heading !== ''): ?>
			<h2 class="tile__heading"><?php echo h($heading); ?></h2>
		<?php endif; ?>
		<?php if ($title !== ''): ?>
			<p class="tile__title"><?php echo h($title); ?></p>
		<?php endif; ?>
		<?php if ($subheading !== ''): ?>
			<p class="tile__subheading"><?php echo h($subheading); ?></p>
		<?php endif; ?>
		<?php if ($content !== ''): ?>
			<div class="tile__text">
				<?php echo '<p>' . nl2br(h($content)) . '</p>'; ?>
			</div>
		<?php endif; ?>
		<?php if ($ctaLink !== '' && $ctaText !== ''): ?>
			<?php echo $this->Html->link(h($ctaText), $ctaLink, array('class' => 'tile__cta btn')); ?>
		<?php endif; ?>
	</div>
</article>

<?php echo $this->CustomField->fieldValueList($itemData); ?>
