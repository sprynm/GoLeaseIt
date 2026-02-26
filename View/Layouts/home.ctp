<?php // home.ctp

echo $this->element('layout/head');
echo $this->element('layout/nav');

$banner      = !empty($banner) ? $banner : array();
$page        = !empty($page) ? $page : array();
$pageHeading = !empty($pageHeading) ? $pageHeading : '';
$pageIntro   = !empty($pageIntro) ? $pageIntro : '';

$PrototypeInstance = ClassRegistry::init('Prototype.PrototypeInstance');

$getPageFieldValue = function ($key) use ($page) {
	if (!empty($page['Page'][$key])) {
		return trim((string) $page['Page'][$key]);
	}

	if (!empty($page['CustomFieldValue']) && is_array($page['CustomFieldValue'])) {
		foreach ($page['CustomFieldValue'] as $fieldValue) {
			if (!empty($fieldValue['key']) && $fieldValue['key'] === $key) {
				return trim((string) $fieldValue['val']);
			}
		}
	}

	return '';
};

$findPrototypeIdBySlug = function ($slug) use ($PrototypeInstance) {
	$slug = trim((string) $slug);
	if ($slug === '') {
		return 0;
	}

	$instance = $PrototypeInstance->find('first', array(
		'conditions' => array(
			'PrototypeInstance.slug' => $slug,
			'PrototypeInstance.deleted' => 0,
		),
		'fields' => array('PrototypeInstance.id'),
		'recursive' => -1,
		'cache' => true,
	));

	if (!empty($instance['PrototypeInstance']['id'])) {
		return (int) $instance['PrototypeInstance']['id'];
	}

	return 0;
};

$findPrototypeIdBySlugs = function ($slugs) use ($findPrototypeIdBySlug) {
	foreach ((array) $slugs as $slug) {
		$id = $findPrototypeIdBySlug($slug);
		if ($id > 0) {
			return $id;
		}
	}
	return 0;
};

$industriesPrototypeId = (int) $getPageFieldValue('home_industries_instance_id');
if ($industriesPrototypeId <= 0) {
	$industriesPrototypeId = $findPrototypeIdBySlug($getPageFieldValue('home_industries_instance_slug'));
}
if ($industriesPrototypeId <= 0) {
	$industriesPrototypeId = $findPrototypeIdBySlugs(array('industries-we-serve', 'industries_served'));
}

$industriesHeading = $getPageFieldValue('home_industries_heading');
$industriesBody = $getPageFieldValue('home_industries_body');
$industriesCtaText = $getPageFieldValue('home_industries_cta_text');
$industriesCtaLink = $getPageFieldValue('home_industries_cta_link');

$processPrototypeId = (int) $getPageFieldValue('home_process_instance_id');
if ($processPrototypeId <= 0) {
	$processPrototypeId = $findPrototypeIdBySlug($getPageFieldValue('home_process_instance_slug'));
}
if ($processPrototypeId <= 0) {
	$processPrototypeId = $findPrototypeIdBySlugs(array('home-process-steps', 'process-steps'));
}

$processHeading = $getPageFieldValue('home_process_heading');
$processBody = $getPageFieldValue('home_process_body');
$processCtaText = $getPageFieldValue('home_process_cta_text');
$processCtaLink = $getPageFieldValue('home_process_cta_link');

$storiesPrototypeId = (int) $getPageFieldValue('home_stories_instance_id');
if ($storiesPrototypeId <= 0) {
	$storiesPrototypeId = $findPrototypeIdBySlug($getPageFieldValue('home_stories_instance_slug'));
}
if ($storiesPrototypeId <= 0) {
	$storiesPrototypeId = $findPrototypeIdBySlugs(array('home-stories', 'success-stories'));
}
$storiesLimit = (int) $getPageFieldValue('home_stories_limit');
if ($storiesLimit <= 0) {
	$storiesLimit = 2;
}

$testimonialsPrototypeId = (int) $getPageFieldValue('home_testimonials_instance_id');
if ($testimonialsPrototypeId <= 0) {
	$testimonialsPrototypeId = $findPrototypeIdBySlug($getPageFieldValue('home_testimonials_instance_slug'));
}
if ($testimonialsPrototypeId <= 0) {
	$testimonialsPrototypeId = $findPrototypeIdBySlugs(array('home-testimonials', 'testimonials'));
}

$testimonialsHeading = $getPageFieldValue('home_testimonials_heading');
$testimonialsBody = $getPageFieldValue('home_testimonials_body');
$testimonialsCtaText = $getPageFieldValue('home_testimonials_cta_text');
$testimonialsCtaLink = $getPageFieldValue('home_testimonials_cta_link');
$testimonialsLimit = (int) $getPageFieldValue('home_testimonials_limit');
if ($testimonialsLimit <= 0) {
	$testimonialsLimit = 2;
}

$midContentBlockId = (int) $getPageFieldValue('home_mid_content_block_id');
$midContentBlockWrapperClass = $getPageFieldValue('home_mid_content_block_wrapper_class');

$bottomCtaBlockId = (int) $getPageFieldValue('home_bottom_cta_block_id');
if ($bottomCtaBlockId <= 0) {
	$bottomCtaBlockId = (int) $getPageFieldValue('home_cta_block_id');
}

$bottomCtaWrapperClass = $getPageFieldValue('home_bottom_cta_wrapper_class');
if ($bottomCtaWrapperClass === '') {
	$bottomCtaWrapperClass = '';
}


echo $this->element('layout/home_masthead', array(
	'banner'      => $banner,
	'page'        => $page,
	'pageHeading' => $pageHeading,
));
?>


<div id="content" class="site-wrapper site-wrapper--default home">
	<?php if ($industriesPrototypeId > 0): ?>
		<?php echo $this->element('home/industries_served', array(
			'instanceId' => $industriesPrototypeId,
			'introHeading' => $industriesHeading,
			'introBody' => $industriesBody,
			'introCtaText' => $industriesCtaText,
			'introCtaLink' => $industriesCtaLink,
			'sectionClasses' => 'u-surface-muted section-industries',
			'limit' => 5,
		)); ?>
	<?php endif; ?>

	<?php if ($processPrototypeId > 0): ?>
		<?php echo $this->element('home/process_steps', array(
			'instanceId' => $processPrototypeId,
			'introHeading' => $processHeading,
			'introBody' => $processBody,
			'introCtaText' => $processCtaText,
			'introCtaLink' => $processCtaLink,
		)); ?>
	<?php endif; ?>

	<?php if ($storiesPrototypeId > 0): ?>
		<?php echo $this->element('home/story_panels', array(
			'instanceId' => $storiesPrototypeId,
			'limit' => $storiesLimit,
		)); ?>
	<?php endif; ?>

	<?php if ($testimonialsPrototypeId > 0): ?>
		<?php echo $this->element('home/why_testimonials', array(
			'instanceId' => $testimonialsPrototypeId,
			'introHeading' => $testimonialsHeading,
			'introBody' => $testimonialsBody,
			'introCtaText' => $testimonialsCtaText,
			'introCtaLink' => $testimonialsCtaLink,
			'limit' => $testimonialsLimit,
		)); ?>
	<?php endif; ?>

	<?php if ($midContentBlockId > 0): ?>
		<?php echo $this->element('home/content_block_by_id', array(
			'blockId' => $midContentBlockId,
			'wrapperClass' => $midContentBlockWrapperClass,
		)); ?>
	<?php endif; ?>

	<!-- <div class="c-container cq-main c-region">

		<section class="c-stack">
			<main>
				<?php
				echo $this->Session->flash();
				?>
			</main>
		</section>
	</div> -->

	<?php if ($bottomCtaBlockId > 0): ?>
		<?php echo $this->element('home/content_block_by_id', array(
			'blockId' => $bottomCtaBlockId,
			'wrapperClass' => $bottomCtaWrapperClass,
		)); ?>
	<?php endif; ?>
</div><!-- "content" ends -->
<?php
echo $this->element('layout/footer');
?>
