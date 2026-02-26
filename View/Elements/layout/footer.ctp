		<footer>
			<div class="c-container c-container--full">
				<div class="ftr-container">
				<?php
					$siteName = trim((string) $this->Settings->show('Site.name'));
					$footerSocial = trim((string) $this->element('social-media'));
					$footerNav = trim((string) $this->Navigation->show(1));
					$phone = trim((string) $this->Settings->show('Site.Contact.phone'));
					if ($phone === '') {
						$phone = trim((string) $this->Settings->show('Site.Contact.toll_free'));
					}
					$phoneHref = preg_replace('/[^0-9\+]/', '', $phone);
				?>

				<div class="ftr-top">
					<nav class="ftr-nav" aria-label="Footer navigation">
						<?php if ($footerNav !== ''): ?>
							<?php echo $footerNav; ?>
						<?php endif; ?>
					</nav>

					<div class="ftr-utility">
						<?php if ($phone !== '' && $phoneHref !== ''): ?>
							<a class="ftr-phone" href="tel:<?php echo h($phoneHref); ?>">
								<svg viewBox="0 0 20 20" aria-hidden="true">
									<path d="M2.003 5.884L2 5a2 2 0 012-2h.055A2 2 0 015.96 4.518l.42 1.68a2 2 0 01-.46 1.93l-.516.516a11.064 11.064 0 005.95 5.95l.516-.516a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0117 15.055V15a2 2 0 01-2 2h-.084a17 17 0 01-14.916-14.913z"/>
								</svg>
								<?php echo h($phone); ?>
							</a>
						<?php endif; ?>
						<?php if ($footerSocial !== ''): ?>
							<div class="ftr-social">
								<?php echo $footerSocial; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="copyright">
					&copy; 2026 <?php echo h($siteName); ?>. | A Commercial Equipment Financing website by <?php echo $this->Html->link('Radar Hill Web Design', $this->Settings->show('Site.Footer.portfolio_link'), array('rel' => 'nofollow')); ?> The content of this website is the responsibility of the website owner.
				</div>
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
		// image links with data-lightbox config are handled by media-lightbox.js
		// add 'jquery.cookie' if easy jQuery cookie use is needed
		$scriptArray = array('library', 'navigation-modern', 'observers', 'media-lightbox', 'legal-notice');
		
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
