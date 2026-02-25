<?php
$instanceId = isset($instanceId) ? (int) $instanceId : 0;
$introHeading = isset($introHeading) ? trim((string) $introHeading) : '';
$introBody = isset($introBody) ? trim((string) $introBody) : '';
$introCtaText = isset($introCtaText) ? trim((string) $introCtaText) : '';
$introCtaLink = isset($introCtaLink) ? trim((string) $introCtaLink) : '';
$limit = isset($limit) ? (int) $limit : 2;
if ($limit <= 0) {
	$limit = 2;
}

if ($instanceId <= 0) {
	return;
}

$items = $this->Prototype->instanceItems($instanceId, array(
	'order' => 'PrototypeItem.rank ASC',
	'limit' => $limit,
));

if (!is_array($items) || empty($items)) {
	return;
}
?>
<section class="section-soft">
	<div class="c-container c-container--full">
		<div class="home-why home-why--testimonials home-region">
			<div class="home-why__intro">
				<?php if ($introHeading !== ''): ?>
					<h2><?php echo h($introHeading); ?></h2>
				<?php endif; ?>
				<?php if ($introBody !== ''): ?>
					<p><?php echo h($introBody); ?></p>
				<?php endif; ?>
				<?php if ($introCtaText !== '' && $introCtaLink !== ''): ?>
					<div>
						<?php echo $this->Html->link($introCtaText, $introCtaLink, array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="testimonials-grid">
				<?php foreach ($items as $entry): ?>
					<?php
					$item = isset($entry['PrototypeItem']) ? $entry['PrototypeItem'] : array();
					$rawQuote = '';
					if (!empty($item['testimonial'])) {
						$rawQuote = (string) $item['testimonial'];
					} elseif (!empty($item['description'])) {
						$rawQuote = (string) $item['description'];
					} elseif (!empty($item['text'])) {
						$rawQuote = (string) $item['text'];
					}
					$quote = trim(strip_tags($rawQuote));

					$byline = '';
					if (!empty($item['byline'])) {
						$byline = trim((string) $item['byline']);
					} elseif (!empty($item['name'])) {
						$byline = trim((string) $item['name']);
					}

					$rating = 5;
					if (!empty($item['rating'])) {
						$rating = (int) $item['rating'];
					}
					$rating = max(1, min(5, $rating));
					$displayByline = ltrim($byline, "- \t\n\r\0\x0B");
					?>
					<div class="testimonial-card">
						<div class="star-rating" aria-label="<?php echo h($rating); ?> out of 5 stars">
							<?php for ($i = 0; $i < $rating; $i++): ?>
								<span class="star-rating__star" aria-hidden="true">★</span>
							<?php endfor; ?>
						</div>
						<?php if ($quote !== ''): ?>
							<p class="testimonial-card__quote"><?php echo h($quote); ?></p>
						<?php endif; ?>
						<?php if ($byline !== ''): ?>
							<p class="testimonial-card__attr">- <?php echo h($displayByline); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
