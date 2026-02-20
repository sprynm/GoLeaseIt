<?php
  $this->Prototype->instanceCss();
  $this->Prototype->instanceJs();

  foreach ($items as $item) { 
?>
    <div class="link">
      <h2 class="link-name"><?php echo $this->Html->link($item['PrototypeItem']['name'], $item['PrototypeItem']['link'], array('target' => '_blank')); ?></h2>
      <?php
			  if($item['PrototypeItem']['description'] != "") {
					echo $item['PrototypeItem']['description'];
				}
				echo $this->Html->link($item['PrototypeItem']['link'], $item['PrototypeItem']['link'], array('target' => '_blank'));
			?>
    </div>
<?php 
  } 
  echo $instance['PrototypeInstance']['footer_text']; 
?>