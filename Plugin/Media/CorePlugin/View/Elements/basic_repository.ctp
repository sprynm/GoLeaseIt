<?php if (count($repository) > 0): ?>
	<ul class="repository">
		<?php foreach ($repository as $document): ?>
	  	<li><?php echo $this->Media->document($document); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>