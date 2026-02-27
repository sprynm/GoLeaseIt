<?php
$showHeaderNotice = ((int) $this->Settings->show('HeaderNotice.display_header_notice') === 1);
if ($showHeaderNotice) {
	echo $this->element('header-notice');
}

$headerPhone = trim((string) $this->Settings->show('Site.Contact.phone'));
if ($headerPhone === '') {
	$headerPhone = trim((string) $this->Settings->show('Site.Contact.toll_free'));
}
$headerPhoneHref = preg_replace('/[^0-9\+]/', '', $headerPhone);

$headerCtaLink = trim((string) $this->Settings->show('HeaderNotice.link'));
if ($headerCtaLink === '') {
	$headerCtaLink = '/contact';
}

$headerCtaText = trim((string) $this->Settings->show('HeaderNotice.link_text'));
if ($headerCtaText === '') {
	$headerCtaText = 'Apply Online →';
}
?>
<header class="site-header primary-hdr<?php echo $showHeaderNotice ? ' site-header--with-notice' : ''; ?>">
	<div class="c-container c-container--full">
		<div class="c-header">
		<a href="/" class="logo">
			<img src="/img/logo.svg" width="285" height="57" alt="<?php echo $this->Settings->show('Site.name'); ?>">
		</a>

		<nav class="site-nav" role="navigation" aria-label="Main navigation" data-site-nav>
			<?php echo $this->Navigation->show(1); ?>
		</nav>

		<div class="header-cta">
			<?php if ($headerPhone !== '' && $headerPhoneHref !== ''): ?>
				<a class="util-phone" href="tel:<?php echo h($headerPhoneHref); ?>">
					<svg viewBox="0 0 20 20" aria-hidden="true">
						<path d="M2.003 5.884L2 5a2 2 0 012-2h.055A2 2 0 015.96 4.518l.42 1.68a2 2 0 01-.46 1.93l-.516.516a11.064 11.064 0 005.95 5.95l.516-.516a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0117 15.055V15a2 2 0 01-2 2h-.084a17 17 0 01-14.916-14.913z"/>
					</svg>
					<?php echo h($headerPhone); ?>
				</a>
			<?php endif; ?>

			<?php if ($headerCtaLink !== '' && $headerCtaText !== ''): ?>
				<?php echo $this->Html->link($headerCtaText, $headerCtaLink, array('class' => 'btn btn--primary btn-sm', 'escape' => false)); ?>
			<?php endif; ?>
		</div>

		<div class="hdr-links">
			<button
				id="site-nav-toggle"
				class="site-nav__toggle"
				type="button"
				aria-controls="site-nav-drawer"
				aria-expanded="false"
				aria-haspopup="dialog"
				aria-label="Open site menu"
			>
				Menu
			</button>
		</div>
		</div>
	</div>
</header>
<dialog id="site-nav-drawer" class="site-nav-drawer" aria-label="Mobile navigation" aria-labelledby="site-nav-drawer-title">
	<div class="site-nav-drawer__head">
		<strong id="site-nav-drawer-title">Menu</strong>
		<button class="site-nav-drawer__close" type="button" aria-label="Close menu">✕ <span>Close</span></button>
	</div>
	<div class="site-nav-drawer__body" data-nav-drawer-body></div>
</dialog>

