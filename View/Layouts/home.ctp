<?php // home.ctp

echo $this->element('layout/head');
echo $this->element('layout/nav');

$banner      = !empty($banner) ? $banner : array();
$page        = !empty($page) ? $page : array();
$pageHeading = !empty($pageHeading) ? $pageHeading : '';
$pageIntro   = !empty($pageIntro) ? $pageIntro : '';
$serviceInstanceId = 1;
$PrototypeInstance = ClassRegistry::init('Prototype.PrototypeInstance');
$serviceInstance = $PrototypeInstance->find('first', array(
	'conditions' => array(
		'PrototypeInstance.slug' => 'service-boxes',
		'PrototypeInstance.deleted' => 0,
	),
	'fields' => array('PrototypeInstance.id', 'PrototypeInstance.name'),
	'recursive' => -1,
	'cache' => true,
));
if (!empty($serviceInstance['PrototypeInstance']['id'])) {
	$serviceInstanceId = (int) $serviceInstance['PrototypeInstance']['id'];
}

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

$serviceBoxesTitle = $getPageFieldValue('home_services_title');
$serviceBoxesHtml = '';
$serviceBoxesPath = APP . 'Plugin' . DS . 'Prototype' . DS . 'View' . DS . 'service-boxes';
if (is_dir($serviceBoxesPath)) {
	$serviceBoxesHtml = (string) $this->element('feature_boxes', array(
		'featureInstanceId' => $serviceInstanceId,
		'numberOfFeaturesLimit' => 4,
	));
}
$hasServiceBoxes = trim($serviceBoxesHtml) !== '';



echo $this->element('layout/home_masthead', array(
	'banner'      => $banner,
	'page'        => $page,
	'pageHeading' => $pageHeading,
));
?>


<div id="content" class="site-wrapper site-wrapper--default home">
	<div class="c-frame c-container--normal cq-main c-region">

		<section class="c-stack">
			<main>
				<?php

				if ($pageIntro !== '') {
					echo h($pageIntro);
				}

				echo $this->Session->flash();

				echo $this->fetch('content');

				?>
			</main>
		</section>
	</div>
	<?php if ($hasServiceBoxes): ?>
		<section class="home-service-boxes u-bg-muted observe animate">
			<div class="c-frame c-container--normal c-region">
				<?php if ($serviceBoxesTitle !== ''): ?>
					<h2 class="home-service-boxes__title u-text-center"><?php echo h($serviceBoxesTitle); ?></h2>
				<?php endif; ?>
				<?php echo $serviceBoxesHtml; ?>
			</div>
		</section>
	<?php endif; ?>
</div><!-- "content" ends -->
<?php
echo $this->element('layout/footer');
?>
