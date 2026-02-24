<?php
$instanceId = isset($instanceId) ? (int) $instanceId : 0;
$introHeading = isset($introHeading) ? trim((string) $introHeading) : '';
$introBody = isset($introBody) ? trim((string) $introBody) : '';
$introCtaText = isset($introCtaText) ? trim((string) $introCtaText) : '';
$introCtaLink = isset($introCtaLink) ? trim((string) $introCtaLink) : '';
$sectionClasses = isset($sectionClasses) ? trim((string) $sectionClasses) : 'section-white section-industries';
$limit = isset($limit) ? (int) $limit : 6;

$items = array();
if ($instanceId > 0) {
	$options = array(
		'order' => 'PrototypeItem.rank ASC'
	);
	if ($limit > 0) {
		$options['limit'] = $limit;
	}
	$items = $this->Prototype->instanceItems($instanceId, $options);
}

if (!is_array($items)) {
	$items = array();
}

if (empty($items)) {
	return;
}
?>
<section class="<?php echo h($sectionClasses); ?>">
	<div class="c-container c-container--full">
		<div class="industry-grid">
			<div class="industry-grid__intro">
				<?php if ($introHeading !== ''): ?>
					<h2><?php echo h($introHeading); ?></h2>
				<?php endif; ?>
				<?php if ($introBody !== ''): ?>
					<p><?php echo h($introBody); ?></p>
				<?php endif; ?>
				<?php if ($introCtaText !== '' && $introCtaLink !== ''): ?>
					<?php echo $this->Html->link($introCtaText, $introCtaLink, array('class' => 'btn btn--hero')); ?>
				<?php endif; ?>
			</div>

			<?php foreach ($items as $entry): ?>
				<?php
				$item = isset($entry['PrototypeItem']) ? $entry['PrototypeItem'] : array();
				$heading = '';
				if (!empty($item['heading'])) {
					$heading = $item['heading'];
				} elseif (!empty($item['name'])) {
					$heading = $item['name'];
				} elseif (!empty($item['title'])) {
					$heading = $item['title'];
				}

				$itemLink = '';
				if (!empty($item['cta_link'])) {
					$itemLink = trim((string) $item['cta_link']);
				}
				if ($itemLink === '' && !empty($item['url'])) {
					$itemLink = trim((string) $item['url']);
				}

				$image = (!empty($entry['Image']) && !empty($entry['Image'][0])) ? $entry['Image'][0] : null;
				$imageAlt = ($image && !empty($image['alternative'])) ? $image['alternative'] : $heading;
				$tileClass = $itemLink !== '' ? 'tile tile--linked' : 'tile';
				?>
				<?php if ($itemLink !== ''): ?>
					<a class="<?php echo $tileClass; ?>" href="<?php echo h($itemLink); ?>">
						<?php if ($image): ?>
							<span class="tile__media">
								<picture>
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" media="(min-width: 1241px)">
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'medium')); ?>" media="(min-width: 481px)">
									<img src="<?php echo $this->Media->getImage($image, array('version' => 'thumb')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
								</picture>
							</span>
						<?php endif; ?>
						<span class="tile__overlay"></span>
						<span class="tile__content">
							<?php if ($heading !== ''): ?>
								<span class="tile__heading"><?php echo h($heading); ?></span>
							<?php endif; ?>
						</span>
					</a>
				<?php else: ?>
					<article class="<?php echo $tileClass; ?>">
						<?php if ($image): ?>
							<span class="tile__media">
								<picture>
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" media="(min-width: 1241px)">
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'medium')); ?>" media="(min-width: 481px)">
									<img src="<?php echo $this->Media->getImage($image, array('version' => 'thumb')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
								</picture>
							</span>
						<?php endif; ?>
						<span class="tile__overlay"></span>
						<div class="tile__content">
							<?php if ($heading !== ''): ?>
								<h3 class="tile__heading"><?php echo h($heading); ?></h3>
							<?php endif; ?>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
