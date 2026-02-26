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

$articleCtaHeading = trim((string)$this->Settings->show('Site.article_cta_heading'));
if ($articleCtaHeading === '') {
	$articleCtaHeading = 'Ready to Move Forward?';
}

$articleCtaBody = trim((string)$this->Settings->show('Site.article_cta_body'));
if ($articleCtaBody === '') {
	$articleCtaBody = 'Ready to secure the equipment your business needs? Our team moves quickly with clear terms and practical solutions built around your cash flow.';
}

$articleCtaLink = trim((string)$this->Settings->show('Site.article_cta_link'));
if ($articleCtaLink === '') {
	$articleCtaLink = '/contact';
}

$articleCtaText = trim((string)$this->Settings->show('Site.article_cta_text'));
if ($articleCtaText === '') {
	$articleCtaText = 'APPLY ONLINE →';
}
?>
<div id="content" class="site-wrapper site-wrapper--default article-layout">
	<div class="c-container c-container--article cq-main c-region">
		<div class="<?php echo $layoutClass; ?>">
			<main class="default layout-default article-body article-layout__body">
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

	<section class="cta-band cta-band--article" aria-labelledby="article-cta-heading">
		<div class="cta-band__inner">
			<h2 id="article-cta-heading" class="cta-band__heading"><?php echo h($articleCtaHeading); ?></h2>
			<p class="cta-band__body"><?php echo h($articleCtaBody); ?></p>
			<?php if ($articleCtaLink !== '' && $articleCtaText !== ''): ?>
				<div class="cta-band__actions">
					<?php echo $this->Html->link($articleCtaText, $articleCtaLink, array('class' => 'btn btn--primary u-btn-lg', 'escape' => false)); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div><!-- "content" ends -->
<?php
echo $this->element('layout/footer');
?>
