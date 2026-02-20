<!DOCTYPE html>
<html lang="en">
  <head>
		<?php
		$gtm_association_code = $this->Settings->show('Site.Google.gtm_association_code');
		if (!empty($gtm_association_code)) :
			echo $this->element('gtm_head', array('gtm_association_code' => $gtm_association_code));
		endif;
		?>
    <title><?php echo $titleTag; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script>
			(function () {
				try {
					if (window.localStorage && window.localStorage.getItem("legal_notice_dismissed") === "1") {
						document.documentElement.classList.add("legal-notice-dismissed");
					}
				} catch (_error) {}
			})();
		</script>
		<?php
		echo $this->Html->canonical();
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');
		// Additional metas should the site be employing the Blog plugin.
		echo $this->element('metas');
		$cssArray = array('stylesheet');

		echo $this->Html->css($cssArray);
		echo $this->Html->css('print', null, array('media' => 'print'));
		echo '{{block type="css"}}';

		if (isset($extraHeaderCode)) :
			echo $extraHeaderCode;
		endif;
		?>
		<?php
		if (isset($page) && !empty($page['Page']['schema_code'])) :
			echo "\n" . $page['Page']['schema_code'] . "\n";
		endif;
		?>
  </head>
  <body id="<?php echo $bodyId; ?>" <?php if (isset($bodyClass) && !empty($bodyClass)) { echo 'class="'.$bodyClass.'"'; } ?>>
		<?php
		if (!empty($gtm_association_code)) :
			echo $this->element('gtm_body', array('gtm_association_code' => $gtm_association_code));
		endif;
		echo $this->element('schema/location');
		?>
    <?php echo $this->Html->link('Skip to main content', '#content', array('id' => 'skiplink')); ?>
