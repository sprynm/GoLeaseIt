<?php
$pageData = !empty($page) ? $page : array();
$bannerData = isset($banner) ? $banner : array();
$siteName = (string) $this->Settings->show('Site.name');
$pageHeading = isset($pageHeading) ? trim((string) $pageHeading) : '';
$pageName = !empty($pageData['Page']['name']) ? trim((string) $pageData['Page']['name']) : '';

$hasBannerImage = isset($bannerData['Image'][0]) && !empty($bannerData['Image'][0]);
$bannerImage = $hasBannerImage ? $bannerData['Image'][0] : array();

$heroHeading = $pageHeading !== '' ? $pageHeading : ($pageName !== '' ? $pageName : $siteName);
$heroEyebrow = !empty($pageData['Page']['banner_header']) ? trim((string) $pageData['Page']['banner_header']) : '';
$heroSummary = !empty($pageData['Page']['banner_summary']) ? trim((string) $pageData['Page']['banner_summary']) : '';
$heroTitleParts = preg_split('/\s*\|\s*/', $heroHeading, 2);
$heroTitleMain = !empty($heroTitleParts[0]) ? $heroTitleParts[0] : $heroHeading;
$heroTitleAccent = !empty($heroTitleParts[1]) ? $heroTitleParts[1] : '';

$getCustomFieldValue = function ($key) use ($pageData) {
	if (!empty($pageData['Page'][$key])) {
		return trim((string) $pageData['Page'][$key]);
	}

	if (!empty($pageData['CustomFieldValue']) && is_array($pageData['CustomFieldValue'])) {
		foreach ($pageData['CustomFieldValue'] as $fieldValue) {
			if (!empty($fieldValue['key']) && $fieldValue['key'] === $key) {
				return trim((string) $fieldValue['val']);
			}
		}
	}

	return '';
};

$heroTaglineHtml = $getCustomFieldValue('masthead_tagline');
if ($heroTaglineHtml === '') {
	$heroTaglineHtml = $getCustomFieldValue('page_masthead_tagline');
}

$heroSubtitle = $getCustomFieldValue('page_subtitle');
if ($heroSubtitle === '') {
	$heroSubtitle = $heroSummary !== '' ? $heroSummary : $heroHeading;
}

$heroBadge = trim((string) $this->Settings->show('Site.service_area'));
if ($heroBadge === '' && $heroEyebrow !== '') {
	$heroBadge = $heroEyebrow;
}

$heroAlt = '';
if ($hasBannerImage && !empty($bannerImage['alternative'])) {
	$heroAlt = trim($bannerImage['alternative']);
} elseif ($heroHeading !== '') {
	$heroAlt = $heroHeading;
} else {
	$heroAlt = $siteName;
}

$fallbackWebp = '';
$fallbackPng = '';
$commonHeroSetting = trim((string) $this->Settings->show('Site.common_head_image'));

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
		<img src="<?php echo $fallbackPng !== '' ? $fallbackPng : $fallbackWebp; ?>" alt="<?php echo h($alt); ?>" loading="eager" decoding="async">
	</picture>
	<?php
	return ob_get_clean();
};

$primaryCta = !empty($pageData['Page']['banner_cta']) && !empty($pageData['Page']['banner_cta_link']);
$secondaryCta = !empty($pageData['Page']['banner_cta_secondary']) && !empty($pageData['Page']['banner_cta_secondary_link']);
$heroCtaNav = trim((string) $this->Navigation->show(2));
$hasHeroCtaNav = ($heroCtaNav !== '' && $heroCtaNav !== '<ul></ul>');
?>
<section class="page-hero page-hero--home">
	<div class="page-hero__bg"></div>
	<?php if ($hasBannerImage || $fallbackWebp !== '' || $fallbackPng !== ''): ?>
		<div class="page-hero__image-bg">
			<?php if ($hasBannerImage): ?>
				<picture>
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-lrg')); ?>" media="(min-width: 1441px)">
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-med')); ?>" media="(min-width: 801px)">
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-sm')); ?>" media="(min-width: 641px)">
					<source srcset="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-xsm')); ?>">
					<img src="<?php echo $this->Media->getImage($bannerImage, array('version' => 'banner-lrg')); ?>" width="1920" height="700" alt="<?php echo h($heroAlt); ?>" loading="eager" decoding="async" fetchpriority="high">
				</picture>
			<?php else: ?>
				<?php echo $renderFallbackPicture($heroAlt); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="page-hero__diagonal"></div>

	<div class="page-hero__inner">
		<div class="page-hero__content">
			<?php if ($heroBadge !== ''): ?>
				<p class="page-hero__eyebrow"><?php echo h($heroBadge); ?></p>
			<?php endif; ?>

			<?php if ($heroTaglineHtml !== ''): ?>
				<div class="page-hero__tagline">
					<?php echo $heroTaglineHtml; ?>
				</div>
			<?php else: ?>
				<h2 class="page-hero__tagline">
					<?php echo h($heroTitleMain); ?>
					<?php if ($heroTitleAccent !== ''): ?>
						<span class="hl"><?php echo h($heroTitleAccent); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ($heroSubtitle !== ''): ?>
				<h1 class="page-hero__subtitle"><?php echo h($heroSubtitle); ?></h1>
			<?php endif; ?>

			<?php if ($hasHeroCtaNav): ?>
				<div class="page-hero__actions page-hero__actions-nav" aria-label="Hero calls to action">
					<?php echo $heroCtaNav; ?>
				</div>
			<?php elseif ($primaryCta || $secondaryCta): ?>
				<div class="page-hero__actions">
					<?php
					if ($primaryCta) {
						echo $this->Html->link($pageData['Page']['banner_cta'], $pageData['Page']['banner_cta_link'], array(
							'class' => 'btn btn--hero page-hero__cta',
							'escape' => false,
						));
					}
					if ($secondaryCta) {
						echo $this->Html->link($pageData['Page']['banner_cta_secondary'], $pageData['Page']['banner_cta_secondary_link'], array(
							'class' => 'btn btn--hero btn--hero-secondary page-hero__cta page-hero__cta--secondary',
							'escape' => false,
						));
					}
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
