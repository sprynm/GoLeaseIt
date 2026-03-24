<div class="faq-list">
<?php foreach ($items as $faq): ?>
<details class="faq">
	<summary class="faq__question"><?php echo $faq['PrototypeItem']['name']; ?></summary>
	<div class="faq__answer"><?php echo $faq['PrototypeItem']['answer']; ?></div>
</details>
<?php endforeach; ?>
</div>
<?php if (!empty($instance['PrototypeInstance']['footer_text'])): ?>
<div class="faq-footer">
	<?php echo $instance['PrototypeInstance']['footer_text']; ?>
</div>
<?php endif; ?>
