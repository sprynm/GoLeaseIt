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
						<path d="M3.654 1.328a.678.678 0 0 1 .737-.131l2.522 1.01c.405.162.655.57.628 1l-.12 1.93a.678.678 0 0 1-.365.559l-1.128.564a11.745 11.745 0 0 0 6.76 6.76l.564-1.128a.678.678 0 0 1 .56-.365l1.928-.12a.678.678 0 0 1 1 .628l1.01 2.522a.678.678 0 0 1-.13.737l-1.2 1.2a1.745 1.745 0 0 1-1.846.402A17.568 17.568 0 0 1 2.98 3.175a1.745 1.745 0 0 1 .402-1.846l1.2-1.2z"/>
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

