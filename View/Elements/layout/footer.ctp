		<footer>
			<div class="ftr-container">
				<?php
					$siteName = trim((string) $this->Settings->show('Site.name'));
					$siteEmail = trim((string) $this->Settings->show('Site.email'));
					$footerSocial = trim((string) $this->element('social-media'));
					$footerDescription = 'Commercial lease financing made straightforward for Canadian businesses.';
					$phone = !empty($siteContact['phone']) ? trim((string) $siteContact['phone']) : '';
					$address = !empty($siteContact['address']) ? trim((string) $siteContact['address']) : '';
					$city = !empty($siteContact['city']) ? trim((string) $siteContact['city']) : '';
					$province = !empty($siteContact['province_state']) ? trim((string) $siteContact['province_state']) : '';
					$postal = !empty($siteContact['postal_zip']) ? trim((string) $siteContact['postal_zip']) : '';
					$cityLine = trim(preg_replace('/\s+/', ' ', $city . ', ' . $province . ' ' . $postal));
				?>

				<div class="ftr-grid">
					<div class="ftr-brand">
						<a class="ftr-logo-link" href="<?php echo $this->Html->url('/'); ?>" aria-label="<?php echo h($siteName !== '' ? $siteName : 'Home'); ?>">
							<img class="ftr-logo-img" src="/img/logo.svg" alt="<?php echo h($siteName); ?>">
						</a>
						<p class="ftr-brand-copy"><?php echo h($footerDescription); ?></p>
					</div>

					<nav class="ftr-nav" aria-label="Footer services">
						<h4 class="ftr-heading">Services</h4>
						<?php
						$footerServicesMenu = trim((string)$this->Navigation->show(3));
						if ($footerServicesMenu !== '') {
							echo $footerServicesMenu;
						}
						?>
					</nav>

					<nav class="ftr-nav" aria-label="Footer company">
						<h4 class="ftr-heading">Company</h4>
						<?php
						$footerCompanyMenu = trim((string)$this->Navigation->show(4));
						if ($footerCompanyMenu !== '') {
							echo $footerCompanyMenu;
						}
						?>
					</nav>

					<div class="ftr-contact">
						<h4 class="ftr-heading">Contact</h4>
						<?php if ($phone !== ''): ?>
							<div class="ftr-contact-line"><?php echo $this->Html->link(h($phone), 'tel:' . $phone, array('escape' => false)); ?></div>
						<?php endif; ?>
						<?php if ($siteEmail !== ''): ?>
							<div class="ftr-contact-line"><?php echo $this->Html->link(h($siteEmail), 'mailto:' . $siteEmail, array('escape' => false)); ?></div>
						<?php endif; ?>
						<?php if ($address !== ''): ?>
							<div class="ftr-contact-line"><?php echo h($address); ?></div>
						<?php endif; ?>
						<?php if ($cityLine !== '' && $cityLine !== ','): ?>
							<div class="ftr-contact-line"><?php echo h($cityLine); ?></div>
						<?php endif; ?>
						<?php if ($footerSocial !== ''): ?>
							<div class="ftr-contact-social">
								<?php echo $footerSocial; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="copyright">
					&copy; 2026 | A website by <?php echo $this->Html->link('Radar Hill Web Design', $this->Settings->show('Site.Footer.portfolio_link'), array('rel' => 'nofollow')); ?><br>
					The content of this website is the responsibility of the website owner.
				</div>
			</div>
		</footer>
		<?php echo $this->element('legal-notice'); ?>
		<?php
		$loadJqueryForDebug = class_exists('Configure') ? (bool)Configure::read('debug') : false;
		if ($loadJqueryForDebug):
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
		<?php
		endif;
		?>
		<?php 
		// add 'youtube' if you will be embedding youtube videos (http://labnol.org/?p=27941)
		// add 'jquery.fancybox' and 'fancybox-init' if using fancybox, add _jquery.fancybox into stylesheet.scss
		// add 'jquery.cookie' if easy jQuery cookie use is needed
		$scriptArray = array('library', 'navigation-modern', 'observers', 'legal-notice');
		
		echo $this->fetch('pluginScriptBottom');
		// Un-remark this if the site needs the VrebListings plugin.
		// if ($this->Vreb->includeVrebAssets(isset($page) ? $page : null)) :
		// 	echo '{{block type="script"}}';
		// 	$scriptArray[] = 'VrebListings.jquery.flexslider-min';
		// 	$scriptArray[] = 'VrebListings.js.cookie';
		// 	$scriptArray[] = 'VrebListings.extra_1';
		// 	$scriptArray[] = 'VrebListings.vreb_listings';
		// endif;
		
		echo $this->Html->script($scriptArray);
		?>
		<?php
		$reCaptchaSiteKey = trim((string)$this->Settings->show('ReCaptcha.Google.sitekey'));
		if ($reCaptchaSiteKey !== ''):
			$recaptchaInvisible = (bool)$this->Settings->show('ReCaptcha.invisible');
			if ($recaptchaInvisible):
				echo $this->Html->script(
					array(
						'ReCaptcha.recaptcha_callback',
						'https://www.google.com/recaptcha/api.js?onload=reCaptchaOnloadCallback&render=explicit',
					),
					array(
						'async' => true,
						'defer' => true,
					)
				);
			else:
				echo $this->Html->script('https://www.google.com/recaptcha/api.js');
			endif;
		endif;
		?>
		<script>
			(function () {
				var needsForms = document.querySelector(
					'form [name^="data[EmailFormSubmission]"], .radios.required input[type="radio"], .checkboxes.required input[type="checkbox"], .error-message'
				);
				if (!needsForms) return;

				if (document.querySelector('script[src$="/js/forms.js"]')) return;

				var script = document.createElement("script");
				script.src = "/js/forms.js";
				script.defer = true;
				document.body.appendChild(script);
			})();
		</script>
		<?php

		if (isset($extraFooterCode)) :
			echo $extraFooterCode;
		// (isset($extraFooterCode))
		endif;
		?>
  </body>
</html>
