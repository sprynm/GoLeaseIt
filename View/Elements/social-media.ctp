<?php
$socialMediaHTML = "";
$iconRoot = 'icons/social-media/';

$links = array(
	'facebook' => $this->Settings->show('Site.SocialMedia.facebook'),
	'twitter' => $this->Settings->show('Site.SocialMedia.twitter'),
	'instagram' => $this->Settings->show('Site.SocialMedia.instagram'),
	'youtube' => $this->Settings->show('Site.SocialMedia.youtube'),
	'linkedin' => $this->Settings->show('Site.SocialMedia.linkedin'),
);

foreach ($links as $key => $url) {
	if ($url == '') {
		continue;
	}
	$label = ucfirst($key);
	$iconPath = WWW_ROOT . 'img' . DS . $iconRoot . $key . '.svg';
	$icon = '';
	if (file_exists($iconPath)) {
		$icon = file_get_contents($iconPath);
		$icon = preg_replace('/<\?xml.*?\?>/i', '', $icon);
		if (preg_match('/<svg\\b[^>]*\\bclass=/i', $icon)) {
			$icon = preg_replace('/<svg\\b([^>]*?)\\bclass=(["\'])([^"\']*)(\\2)([^>]*)>/i', '<svg$1 class="$3 social-icon__svg" aria-hidden="true" focusable="false"$5>', $icon, 1);
		} else {
			$icon = preg_replace('/<svg\\b([^>]*)>/i', '<svg$1 class="social-icon__svg" aria-hidden="true" focusable="false">', $icon, 1);
		}
	}
	$socialMediaHTML .= $this->Html->link(
		$icon . '<span class="u-visually-hidden">' . $label . '</span>',
		$url,
		array(
			'class' => 'social-icon social-icon--' . $key,
			'target' => '_blank',
			'rel' => 'noopener',
			'escape' => false,
		)
	);
}

if ($socialMediaHTML != "") {
	?>
	<div class="social-media">
		<?php echo $socialMediaHTML; ?>
	</div>
	<?php
}
?>
