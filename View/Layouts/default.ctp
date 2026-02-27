<?php
echo $this->element('layout/head');
echo $this->element('layout/nav');
echo $this->element('layout/body_masthead', array(
	'banner' => isset($banner) ? $banner : array(),
	'page' => isset($page) ? $page : array(),
	'pageHeading' => isset($pageHeading) ? $pageHeading : '',
));

$bannerData = isset($banner) ? $banner : array();
$hasBannerImage = isset($bannerData['Image'][0]) && !empty($bannerData['Image'][0]);
$heroHeading = isset($pageHeading) ? trim((string)$pageHeading) : '';
if ($heroHeading === '' && isset($page['Page']['name'])) {
	$heroHeading = trim((string)$page['Page']['name']);
}

$curTop = $this->Navigation->topCurrentItem();
$subNav = null;
$hasSubNav = false;
$showSectionNav = false;

$toBool = function ($value, $default) {
	if ($value === null) {
		return (bool)$default;
	}

	if (is_bool($value)) {
		return $value;
	}

	$normalized = strtolower(trim((string)$value));
	if ($normalized === '') {
		return (bool)$default;
	}

	if (in_array($normalized, array('1', 'true', 'yes', 'on'), true)) {
		return true;
	}

	if (in_array($normalized, array('0', 'false', 'no', 'off'), true)) {
		return false;
	}

	return (bool)$default;
};

$sectionNavFieldValue = null;
if (isset($page['Page']) && is_array($page['Page']) && array_key_exists('show_section_nav', $page['Page'])) {
	$sectionNavFieldValue = $page['Page']['show_section_nav'];
}

if ($sectionNavFieldValue === null && !empty($page['CustomFieldValue']) && is_array($page['CustomFieldValue'])) {
	foreach ($page['CustomFieldValue'] as $fieldValue) {
		if (!empty($fieldValue['key']) && $fieldValue['key'] === 'show_section_nav') {
			$sectionNavFieldValue = isset($fieldValue['val']) ? $fieldValue['val'] : null;
			break;
		}
	}
}

$showSectionNav = $toBool($sectionNavFieldValue, false);

if ($showSectionNav && isset($curTop['NavigationMenuItem']['id'])) {
	$subNav = $this->Navigation->showChildren($curTop['NavigationMenuItem']['id'], true);
	if (!empty($subNav) && $subNav !== '<ul></ul>') {
		$hasSubNav = true;
	}
}

$layoutClass = $hasSubNav ? 'c-sidebar' : 'c-stack';

?>
<div id="content" class="site-wrapper site-wrapper--default article-layout<?php echo !$hasBannerImage ? ' article-layout--no-banner' : ''; ?>">
	<div class="c-container c-container--article cq-main c-region">
		<div class="<?php echo $layoutClass; ?>">
			<main class="default layout-default article-body article-layout__body">
				<?php
				if (!$hasBannerImage && $heroHeading !== '') {
					echo '<h1>' . h($heroHeading) . '</h1>';
				}

				if (!empty($pageIntro)) {
					echo $this->Html->div('layout-rail', $pageIntro);
				}

				echo $this->Session->flash();
				echo $this->fetch('content');
				?>
			</main>

			<?php if ($hasSubNav): ?>
				<nav class="subnav subnav--list subnav--sticky" aria-label="Section navigation">
					<?php echo $subNav; ?>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</div><!-- "content" ends -->
<?php
echo $this->element('layout/footer');
?>
