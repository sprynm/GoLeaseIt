<?php
$showHeaderNotice = ((int) $this->Settings->show('HeaderNotice.display_header_notice') === 1);
if ($showHeaderNotice) {
	echo $this->element('header-notice');
}
?>
<header class="site-header primary-hdr<?php echo $showHeaderNotice ? ' site-header--with-notice' : ''; ?>">
	<div class="c-container">
		<div class="c-header">
		<a href="/" class="logo">
			<img src="/img/logo.svg" width="275" height="28" alt="<?php echo $this->Settings->show('Site.name'); ?>">
		</a>

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
			<nav class="site-nav" role="navigation" aria-label="Main navigation" data-site-nav>
				<?php echo $this->Navigation->show(1); ?>
				
			</nav>

		</div>
		</div>
	</div>
</header>
<dialog id="site-nav-drawer" class="site-nav-drawer" aria-label="Mobile navigation">
	<div class="site-nav-drawer__head">
		<strong>Menu</strong>
		<button class="site-nav-drawer__close" type="button" aria-label="Close menu">x</button>
	</div>
	<div class="site-nav-drawer__body" data-nav-drawer-body></div>
</dialog>
