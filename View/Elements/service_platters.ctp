<?php
$instanceId = isset($instanceId) ? (int)$instanceId : 0;
$limit = isset($limit) ? (int)$limit : 0;
$instanceItems = array();

if ($instanceId > 0) {
	$options = array(
		'order' => 'PrototypeItem.rank ASC'
	);
	if ($limit > 0) {
		$options['limit'] = $limit;
	}
	$instanceItems = $this->Prototype->instanceItems($instanceId, $options);
}

if (!is_array($instanceItems)) {
	$instanceItems = array();
}

$makeSlug = function ($value) {
	$value = strtolower(trim((string)$value));
	$value = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
	$value = preg_replace('/[^a-z0-9]+/', '-', (string)$value);
	$value = trim((string)$value, '-');
	return $value;
};
?>

<?php if (!empty($instanceItems)): ?>
	<section class="service-platters c-stack" aria-label="Our services">
		<?php foreach ($instanceItems as $index => $entry): ?>
			<?php
			$item = isset($entry['PrototypeItem']) ? $entry['PrototypeItem'] : array();
			$title = '';
			if (!empty($item['heading'])) {
				$title = $item['heading'];
			} elseif (!empty($item['name'])) {
				$title = $item['name'];
			} elseif (!empty($item['title'])) {
				$title = $item['title'];
			}

			$content = '';
			if (!empty($item['content'])) {
				$content = $item['content'];
			} elseif (!empty($item['text'])) {
				$content = $item['text'];
			}

			$image = (!empty($entry['Image']) && !empty($entry['Image'][0])) ? $entry['Image'][0] : null;
			$imageAlt = ($image && !empty($image['alternative'])) ? $image['alternative'] : $title;
			$sectionSlug = $makeSlug($title);
			$sectionId = $sectionSlug !== '' ? 'service-' . $sectionSlug : '';
			$classes = 'c-split-platter observe animate';
			if ($index % 2 === 1) {
				$classes .= ' c-split-platter--flip';
			}
			?>
			<article<?php if ($sectionId !== ''): ?> id="<?php echo h($sectionId); ?>"<?php endif; ?> class="<?php echo $classes; ?>">
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
						<div class="c-split-platter__content"><?php echo $content; ?></div>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</section>
<?php endif; ?>
