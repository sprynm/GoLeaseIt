<?php
$blockId = isset($blockId) ? (int) $blockId : 0;
$wrapperClass = isset($wrapperClass) ? trim((string) $wrapperClass) : '';

if ($blockId <= 0) {
	return;
}

$ContentBlock = ClassRegistry::init('ContentBlocks.ContentBlock');
$block = $ContentBlock->find('first', array(
	'conditions' => array(
		'ContentBlock.id' => $blockId,
		'ContentBlock.deleted' => 0
	),
	'fields' => array('ContentBlock.id', 'ContentBlock.content'),
	'published' => true,
	'recursive' => -1,
	'cache' => true
));

if (empty($block['ContentBlock']['content'])) {
	return;
}

$content = (string) $block['ContentBlock']['content'];
?>
<?php if ($wrapperClass !== ''): ?>
	<section class="<?php echo h($wrapperClass); ?>">
		<?php echo $content; ?>
	</section>
<?php else: ?>
	<?php echo $content; ?>
<?php endif; ?>
