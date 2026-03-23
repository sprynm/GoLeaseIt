<?php foreach ($items as $faq): ?>
<details class="faq">
	<summary class="faq__question"><?php echo $faq['PrototypeItem']['name']; ?></summary>
	<div class="faq__answer"><?php echo $faq['PrototypeItem']['answer']; ?></div>
</details>
<?php endforeach; ?>
<?php echo $instance['PrototypeInstance']['footer_text']; ?>
