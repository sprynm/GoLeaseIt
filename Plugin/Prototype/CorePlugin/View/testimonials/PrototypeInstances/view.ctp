<?php 
  foreach ($items as $testimonial) {
?>
  <blockquote class="testimonial">
	  <?php echo $testimonial['PrototypeItem']['testimonial']; ?>
    <div class="testimonial-citation">
      <span class="testimonial-name"><?php echo $testimonial['PrototypeItem']['name']; ?></span>
      <?php
        if($testimonial['PrototypeItem']['byline'] != "") {
          echo "<span class='testimonial-title'>" . $testimonial['PrototypeItem']['byline'] . "</span>";
        }
        if($testimonial['PrototypeItem']['link'] != "") {
          echo "<span class='testimonial-link'>" . $this->Html->link($testimonial['PrototypeItem']['link'], $testimonial['PrototypeItem']['link'], array('target' => '_blank')) . "</span>";
        }
      ?>
    </div>
  </blockquote>
<?php 
	}
?>
<?php echo $instance['PrototypeInstance']['footer_text']; ?>