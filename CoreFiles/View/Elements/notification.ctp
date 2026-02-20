<div class="notification <?php echo $class; ?> png_bg">
	<?php
	echo $this->Html->link(
		'Close &nbsp;' . $this->Html->image('icons/cross_grey_small.png', array('alt' => 'close', 'title' => 'Close this notification')),
		'#',
		array('escape' => false, 'class' => 'close')
	);
	?>
	<div<?php echo $class == 'error' ? ' role="alert" aria-relevant="all"' : ''; ?>><?php echo $message; ?></div>
</div>