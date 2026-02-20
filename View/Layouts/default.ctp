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
