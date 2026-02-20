<?php 
if (isset($category['PrototypeCategory']['description'])):
	echo $category['PrototypeCategory']['description'];
endif;?>

<div class="projects">
	<?php foreach ($items as $item): 
		if(!empty($item['Image'])) { ?>
			<article class="project">
				<picture>
					<source srcset="<?php echo $this->Media->path($item['Image'][0], 'large'); ?>" media='(min-width: 1200px)'>
					<source srcset="<?php echo $this->Media->path($item['Image'][0], 'medium'); ?>" media='(min-width: 800px)'>
					<source srcset="<?php echo $this->Media->path($item['Image'][0], 'small'); ?>" >
					<img data-src="<?php $this->Media->path($item['Image'][0], 'small'); ?>" alt="<?php echo $item['Image'][0]['alternative']; ?>" class="lazy">
				</picture>	
				<div class="details">
					<?php echo '<h3>' . $item['PrototypeItem']['name'] . '</h3>'; ?>
					<?php if(!empty($item['PrototypeItem']['preview'])) { echo '<p>' . $item['PrototypeItem']['preview'] . '</p>';}
						echo $this->Html->link('View Project',$this->ModelLink->link('Prototype.PrototypeItem', $item['PrototypeItem']['id']), array('escape' => false, 'class' => 'btn')); 
					?>
				</div>											
			</article>
		<?php } ?>
	<?php endforeach; ?>
</div>

<?php echo $instance['PrototypeInstance']['footer_text']; ?>