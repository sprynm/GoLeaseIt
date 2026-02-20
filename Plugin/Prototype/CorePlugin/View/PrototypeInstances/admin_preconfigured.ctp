<?php
$this->set('header', 'Preconfigured Prototype Instances');
?>
<?php
echo $this->CmsForm->create(false, array('url' => '/'.$this->params['url']['url']));
?>
<ul>
	<?php foreach ($preconfiguredPlugins as $name => $info): ?>
	<li><h3><?php echo $name; ?></h3>
	<?php echo $this->CmsForm->input($name, array('type' => 'checkbox', 'label' => 'Install')); ?>
		<ul>
			<li>
				Instance details: 
				<ul>
					<?php foreach ($info['PrototypeInstance'] as $key => $val): ?>
					<li><?php echo Inflector::humanize($key); ?>: <?php echo is_numeric($val) ? $this->AdminLink->yesNo($val) : $val; ?></li>
					<?php endforeach; ?>
				</ul>
			</li>
			<?php if (!empty($info['PrototypeItemField'])): ?>
			<li>
				Item extra fields: 
				<ul>
					<?php foreach ($info['PrototypeItemField'] as $itemField): ?>
					<ul>
						<li>Name: <?php echo $itemField['name']; ?></li>
						<li>Type: <?php echo $itemField['type']; ?></li>
						<?php if ($itemField['type'] == 'select'): ?>
						<li>Options: <?php echo $itemField['options']; ?></li>
						<?php endif; ?>
						<li>Default: <?php echo $itemField['default']; ?></li>
					</ul>
					<?php endforeach; ?>
				</ul>
			</li>
			<?php endif; ?>
			<?php if ($info['PrototypeInstance']['use_categories'] == 1 && !empty($info['PrototypeCategoryField'])): ?>
			<li>
				Category details: 
				<ul>
					<?php foreach ($info['PrototypeCategoryField'] as $categoryField): ?>
					<ul>
						<li>Name: <?php echo $categoryField['name']; ?></li>
						<li>Type: <?php echo $categoryField['type']; ?></li>
						<?php if ($categoryField['type'] == 'select'): ?>
						<li>Options: <?php echo $categoryField['options']; ?></li>
						<?php endif; ?>
						<li>Default: <?php echo $categoryField['default']; ?></li>
					</ul>
					<?php endforeach; ?>
				</ul>
			</li>
			<?php endif; ?>
		</ul>
	</li>
	<?php endforeach; ?>
</ul>
<?php echo $this->CmsForm->end('submit'); ?>