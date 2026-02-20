<?php
echo $this->element('layout/head');
echo $this->element('layout/nav');
echo $this->element('layout/body_masthead', array(
	'banner' => isset($banner) ? $banner : array(),
	'page' => isset($page) ? $page : array(),
	'pageHeading' => isset($pageHeading) ? $pageHeading : '',
));

$curTop = $this->Navigation->topCurrentItem();
$subNav = null;
$hasSubNav = false;

if (isset($curTop['NavigationMenuItem']['id'])) {
	$subNav = $this->Navigation->showChildren($curTop['NavigationMenuItem']['id'], true);
	if (!empty($subNav) && $subNav !== '<ul></ul>') {
		$hasSubNav = true;
	}
}

$layoutClass = $hasSubNav ? 'c-sidebar' : 'c-stack';

$servicesInstanceId = 0;
$PrototypeInstance = ClassRegistry::init('Prototype.PrototypeInstance');
$servicesInstance = $PrototypeInstance->find('first', array(
	'conditions' => array(
		'PrototypeInstance.slug' => 'our-services',
		'PrototypeInstance.deleted' => 0,
	),
	'fields' => array('PrototypeInstance.id'),
	'recursive' => -1,
	'cache' => true,
));
if (!empty($servicesInstance['PrototypeInstance']['id'])) {
	$servicesInstanceId = (int)$servicesInstance['PrototypeInstance']['id'];
}
?>
<div id="content" class="site-wrapper site-wrapper--default">
	<div class="c-frame c-container--normal cq-main c-region">
		<div class="<?php echo $layoutClass; ?>">
			<main class="default layout-default">
				<?php
				if (!empty($pageIntro)) {
					echo $this->Html->div('layout-rail', $pageIntro);
				}

				echo $this->Session->flash();
				echo $this->fetch('content');

				if ($servicesInstanceId > 0) {
					echo $this->element('service_platters', array(
						'instanceId' => $servicesInstanceId,
					));
				}
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
