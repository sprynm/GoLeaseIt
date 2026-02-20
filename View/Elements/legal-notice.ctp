<?php
$legalEnabled = ((int)$this->Settings->show('LegalNotice.enabled') === 1);
$legalText = trim((string)$this->Settings->show('LegalNotice.text'));

if (!$legalEnabled || $legalText === '') {
	return;
}

$dismissLabel = trim((string)$this->Settings->show('LegalNotice.dismiss_label'));
if ($dismissLabel === '') {
	$dismissLabel = 'Got it';
}

?>
<section
	class="legal-notice"
	data-legal-notice
	aria-label="Legal notice"
>
	<div class="c-frame c-container--normal">
		<div class="legal-notice__panel">
			<div class="legal-notice__text">
				<?php echo $legalText; ?>
			</div>
			<button
				type="button"
				class="legal-notice__dismiss btn js-close"
				data-function="close"
				data-close-target=".legal-notice"
				data-legal-dismiss
			>
				<?php echo h($dismissLabel); ?>
			</button>
		</div>
	</div>
</section>
