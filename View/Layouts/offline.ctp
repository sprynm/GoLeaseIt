<!DOCTYPE HTML>
<html lang="en">

<head>
	<title><?php echo $titleTag; ?></title>
	<meta charset="utf-8" />
	<meta name="msvalidate.01" content="<?php echo $this->Settings->show('Site.Bing.verification_code'); ?>" />
	<meta name="robots" content="NOODP" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php
	echo $this->Html->canonical();
	echo $this->Html->meta('icon');
	echo $this->fetch('meta');
	echo $this->Html->css(array('offline'));
	echo $this->fetch('css');
	?>
	<?php
	if (isset($extraHeaderCode)):
		echo $extraHeaderCode;
	endif;
	//
	echo $this->Settings->show('Site.Google.analytics_code');
	?>
</head>

<body>
	<a href="#content" class="skiplink">Skip to Content</a>
	<header>
		<div class="wrapper">
			<?php echo $this->Html->link(
				$this->Html->image('company-logo.png', array('width' => '220', 'height' => '144', 'alt' => $this->Settings->show('Site.name'), 'title' => $this->Settings->show('Site.name'))),
				'/',
				array('escape' => false, 'class' => 'logo')
			); ?>
		</div>
	</header>

	<div class="contact-info-container">
		<div class="wrapper">
			<p class="address"><?php echo $this->Settings->show('Site.Contact.address'); ?> <span>|</span>
				<?php echo $this->Settings->show('Site.Contact.city'); ?>,
				<?php echo $this->Settings->show('Site.Contact.province_state'); ?>
				<?php echo $this->Settings->show('Site.Contact.postal_zip'); ?></p>

			<p class="contact-info"><strong>p:</strong> <?php echo $this->Settings->show('Site.Contact.phone'); ?>
				<span>|</span> <strong>e:</strong> <a
					href="mailto:<?php echo $this->Settings->show('Site.Contact.email'); ?>"><?php echo $this->Settings->show('Site.Contact.email'); ?></a>
			</p>
		</div>
	</div>

	<div class="wrapper">
		<div class="content" id="content">
			<?php if (isset($pageHeading) && !empty($pageHeading)): ?>
				<h1><?php echo $pageHeading; ?></h1>
			<?php endif; ?>
			<?php
			//
			if (isset($pageIntro)) {
				//
				echo $pageIntro;
			}
			//
			echo $this->fetch('content');
			//
			echo $this->Session->flash();
			?>

			<div class="contact-form">
				<hr />
				<h2>Contact Us</h2>
				{{block type="EmailForm" id="1"}}
			</div>
		</div>
	</div>

	<footer>
		<div class="wrapper">
			<div class="copyright">
				<?php
				if (isset($page) && $page['Page']['id'] == 1) {
					$rhText = $this->Html->link('Radar Hill Web Design', $this->Settings->show('Site.Footer.portfolio_link'), array('rel' => 'nofollow'));
				} else {
					$rhText = "Radar Hill Web Design";
				}
				?>
				Copyright &copy; <?php echo $this->Copyright->year(); ?> <?php echo $this->Copyright->name(); ?> | A
				<?php echo $this->Settings->show('Site.Footer.industry_identifier'); ?> website by
				<?php echo $rhText; ?><br />
				The content of this website is the responsibility of the website owner.
			</div>
		</div>
	</footer>
	<?php //
	//
	$scriptArray = array(
		'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'
		,
		'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js'
		,
		'library'
		,
		'jquery-ui.min'
		,
		'cms'
		,
	);
	$reCaptchaSiteKey = trim((string)$this->Settings->show('ReCaptcha.Google.sitekey'));
	//
	if ($reCaptchaSiteKey !== '' && !$this->Settings->show('ReCaptcha.invisible')):
		//
		$scriptArray[] = 'https://www.google.com/recaptcha/api.js';
		// (!$this->Settings->show('ReCaptcha.invisible'))
	endif;
	//
	echo $this->Html->script($scriptArray);
	//
	echo $this->fetch('script');
	//
	if (isset($extraFooterCode)):
		//
		echo $extraFooterCode;
		// (isset($extraFooterCode))
	endif;
	// 
	if ($reCaptchaSiteKey !== '' && $this->Settings->show('ReCaptcha.invisible')):
		//
		echo $this->Html->script(
			array(
				'ReCaptcha.recaptcha_callback'
				,
				'https://www.google.com/recaptcha/api.js?onload=reCaptchaOnloadCallback&render=explicit'
				,
			)
			,
			array(
				'async' => true
				,
				'defer' => true
				,
			)
		);
		// (!$this->Settings->show('ReCaptcha.invisible'))
	endif;
	?>
	<script>
		WebFont.load({
			google: {
				families: ['Lato:400,400i,700,700i,900,900i,300,300i']
			}
		});
	</script>
</body>

</html>
