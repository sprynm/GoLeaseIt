<?php
echo $this->element('layout/head');
echo $this->element('layout/nav');
echo $this->element('layout/body_masthead', array(
	'banner' => isset($banner) ? $banner : array(),
	'page' => array(),
	'pageHeading' => !empty($instance['PrototypeInstance']['head_title']) ? $instance['PrototypeInstance']['head_title'] : $instance['PrototypeInstance']['name'],
));

$bannerData = isset($banner) ? $banner : array();
$hasBannerImage = isset($bannerData['Image'][0]) && !empty($bannerData['Image'][0]);
$heroHeading = '';
if (!empty($instance['PrototypeInstance']['head_title'])) {
	$heroHeading = trim((string)$instance['PrototypeInstance']['head_title']);
} elseif (!empty($instance['PrototypeInstance']['name'])) {
	$heroHeading = trim((string)$instance['PrototypeInstance']['name']);
}
?>
<div id="content" class="site-wrapper site-wrapper--default article-layout<?php echo !$hasBannerImage ? ' article-layout--no-banner' : ''; ?>">
	<div class="c-container c-container--article cq-main c-region">
		<div class="c-stack">
			<main class="default layout-default article-body article-layout__body">
				<?php
				if (!$hasBannerImage && $heroHeading !== '') {
					echo '<h1>' . h($heroHeading) . '</h1>';
				}

				if (!empty($pageIntro)) {
					echo $this->Html->div('page-lead-in u-type-feature-text', $pageIntro);
				}

				echo $this->Session->flash();
				echo $this->fetch('content');
				?>
			</main>
		</div>
	</div>
</div><!-- "content" ends -->
<?php
echo $this->element('layout/footer');
?>
