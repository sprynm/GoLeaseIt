<?php
$bannerData = isset($banner) ? $banner : array();
$pageData = isset($page) ? $page : array();
$pageHeading = isset($pageHeading) ? trim($pageHeading) : '';

$hasBannerImage = isset($bannerData['Image'][0]) && !empty($bannerData['Image'][0]);
$bannerImage = $hasBannerImage ? $bannerData['Image'][0] : array();

$heroHeading = $pageHeading;
if ($heroHeading === '' && !empty($pageData['Page']['name'])) {
	$heroHeading = trim($pageData['Page']['name']);
}

$heroAlt = '';
if ($hasBannerImage && !empty($bannerImage['alternative'])) {
	$heroAlt = trim($bannerImage['alternative']);
} elseif ($heroHeading !== '') {
	$heroAlt = $heroHeading;
} else {
	$heroAlt = $this->Settings->show('Site.name');
}

$fallbackWebp = '';
$fallbackPng = '';
$commonHeroSetting = trim((string)$this->Settings->show('Site.common_head_image'));

if ($commonHeroSetting !== '') {
	$extractedSrc = '';
	if (preg_match('/src=["\']([^"\']+)["\']/', $commonHeroSetting, $match)) {
		$extractedSrc = $match[1];
	} elseif (filter_var($commonHeroSetting, FILTER_VALIDATE_URL) || strpos($commonHeroSetting, '/') === 0) {
		$extractedSrc = $commonHeroSetting;
	}

	if ($extractedSrc !== '') {
		$fallbackWebp = $extractedSrc;
		$fallbackPng = $extractedSrc;
	}
}

$renderFallbackPicture = function ($alt) use ($fallbackWebp, $fallbackPng) {
	if ($fallbackWebp === '' && $fallbackPng === '') {
		return '';
	}

	ob_start();
	?>
	<picture>
		<?php if ($fallbackWebp !== '' && $fallbackWebp !== $fallbackPng): ?>
			<source srcset="<?php echo $fallbackWebp; ?>" type="image/webp">
		<?php endif; ?>
		<img src="<?php echo $fallbackPng !== '' ? $fallbackPng : $fallbackWebp; ?>" alt="<?php echo h($alt); ?>" loading="lazy" decoding="async">
	</picture>
	<?php
	return ob_get_clean();
};

$pageHeroClasses = array('page-hero', 'page-hero--single');
if ($hasBannerImage || $fallbackWebp !== '' || $fallbackPng !== '') {
	$pageHeroClasses[] = 'page-hero--has-media';
}

$showHeroHeading = ($heroHeading !== '');
$shouldRenderHero = $showHeroHeading || $hasBannerImage || $fallbackWebp !== '' || $fallbackPng !== '';

if ($shouldRenderHero):
?>
<section class="<?php echo h(implode(' ', $pageHeroClasses)); ?>">
	<div class="page-hero__bg"></div>
	<?php if ($hasBannerImage || $fallbackWebp !== '' || $fallbackPng !== ''): ?>
		<div class="page-hero__image-bg">
			<?php if ($hasBannerImage): ?>
				<picture>
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-fhdl')); ?>" media="(min-width: 1441px)">
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-med')); ?>" media="(min-width: 801px)">
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-sm')); ?>" media="(min-width: 641px)">
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-xsm')); ?>">
					<img src="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-fhdl')); ?>" width="1980" height="300" alt="<?php echo h($heroAlt); ?>" loading="lazy" decoding="async">
				</picture>
			<?php else: ?>
				<?php echo $renderFallbackPicture($heroAlt); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="page-hero__diagonal"></div>

	<div class="page-hero__inner">
		<div class="page-hero__content">
			<?php if (!empty($pageData['Page']['banner_header'])): ?>
				<p class="page-hero__eyebrow"><?php echo h($pageData['Page']['banner_header']); ?></p>
			<?php endif; ?>

			<?php if ($showHeroHeading): ?>
				<h1 class="page-hero__title"><?php echo h($heroHeading); ?></h1>
			<?php endif; ?>

			<?php if (!empty($pageData['Page']['banner_summary'])): ?>
				<p class="page-hero__summary"><?php echo h($pageData['Page']['banner_summary']); ?></p>
			<?php endif; ?>

			<?php
			$primaryCta = !empty($pageData['Page']['banner_cta']) && !empty($pageData['Page']['banner_cta_link']);
			$secondaryCta = !empty($pageData['Page']['banner_cta_secondary']) && !empty($pageData['Page']['banner_cta_secondary_link']);
			?>
			<?php if ($primaryCta || $secondaryCta): ?>
				<div class="page-hero__actions">
					<?php if ($primaryCta): ?>
						<?php echo $this->Html->link($pageData['Page']['banner_cta'], $pageData['Page']['banner_cta_link'], array('class' => 'page-hero__cta', 'escape' => false)); ?>
					<?php endif; ?>
					<?php if ($secondaryCta): ?>
						<?php echo $this->Html->link($pageData['Page']['banner_cta_secondary'], $pageData['Page']['banner_cta_secondary_link'], array('class' => 'page-hero__cta page-hero__cta--secondary', 'escape' => false)); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>
