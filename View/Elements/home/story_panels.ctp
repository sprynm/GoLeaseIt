<?php
$instanceId = isset($instanceId) ? (int) $instanceId : 0;
$limit = isset($limit) ? (int) $limit : 0;
if ($instanceId <= 0) {
	return;
}

$options = array(
	'order' => 'PrototypeItem.rank ASC',
);
if ($limit > 0) {
	$options['limit'] = $limit;
}

$items = $this->Prototype->instanceItems($instanceId, $options);
if (!is_array($items) || empty($items)) {
	return;
}

$itemValue = function ($item, $keys) {
	foreach ((array) $keys as $key) {
		if (!empty($item[$key])) {
			return trim((string) $item[$key]);
		}
	}
	return '';
};
?>
<section class="u-surface-base">
	<div class="home-stories">
			<?php foreach ($items as $index => $entry): ?>
				<?php
				$item = !empty($entry['PrototypeItem']) ? $entry['PrototypeItem'] : array();
				$image = (!empty($entry['Image']) && !empty($entry['Image'][0])) ? $entry['Image'][0] : null;
				$kicker = $itemValue($item, array('kicker', 'label'));
				$title = $itemValue($item, array('title', 'name'));
				$body = $itemValue($item, array('description', 'text', 'summary'));
				$ctaText = $itemValue($item, array('cta_text', 'cta_link_text'));
				$ctaLink = $itemValue($item, array('cta_link', 'link', 'url'));
				$imageAlt = ($image && !empty($image['alternative'])) ? trim((string) $image['alternative']) : $title;
				$imageFirst = ($index % 2 === 0);
				?>
				<article class="story-platter <?php echo $imageFirst ? 'story-platter--image-first' : 'story-platter--content-first'; ?>">
					<?php if ($imageFirst && $image): ?>
						<div class="story-platter__media">
							<img src="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
						</div>
					<?php endif; ?>

					<div class="story-platter__content">
						<?php if ($kicker !== ''): ?>
							<p class="section-kicker"><?php echo h($kicker); ?></p>
						<?php endif; ?>
						<?php if ($title !== ''): ?>
							<h2 class="story-platter__title"><?php echo h($title); ?></h2>
						<?php endif; ?>
						<?php if ($body !== ''): ?>
							<p class="story-platter__body"><?php echo h($body); ?></p>
						<?php endif; ?>
						<?php if ($ctaText !== '' && $ctaLink !== ''): ?>
							<div>
								<?php echo $this->Html->link($ctaText, $ctaLink, array('class' => 'btn btn--primary', 'escape' => false)); ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if (!$imageFirst && $image): ?>
						<div class="story-platter__media">
							<img src="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
						</div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
	</div>
</section>


