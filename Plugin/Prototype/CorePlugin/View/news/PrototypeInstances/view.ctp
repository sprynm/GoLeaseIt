<?php
$this->Prototype->instanceCss();
$this->Prototype->instanceJs();
?>

<?php foreach ($items as $item): ?>
	<div class="news">
		<?php echo $this->Html->link($this->Media->mainImage($item['Image'], 'thumb'), $this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id']), array('escape' => false)); ?>
		
		<h2><?php echo $this->Html->link($item['PrototypeItem']['name'], $this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id'])); ?></h2>
		<h3><?php echo $this->Time->format('M. j, Y', $item['PublishingInformation']['start']); ?></h3>
		<p><?php echo $this->Text->truncate(strip_tags($item['PrototypeItem']['description']), 300, array('exact' => false)); ?></p>
		<h3 class="read-more"><?php echo $this->Html->link('Read more', $this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id'])); ?></h3>
		<?php echo $this->element('clear_float'); ?>
	</div>
<?php endforeach; ?>
<?php echo $instance['PrototypeInstance']['footer_text']; ?>
