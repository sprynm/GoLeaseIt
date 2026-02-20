<?php
if (isset($refreshDownload) && $refreshDownload != false):
	echo $this->Html->scriptBlock('
		$(document).ready(function() {
			window.location.href="' . $refreshDownload . '";
		});
	');
endif;
?>
<?php
echo $page['Page']['content'];
?>