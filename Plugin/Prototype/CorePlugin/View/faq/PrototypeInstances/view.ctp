<?php 
	echo $this->Html->scriptBlock(
		"$(document).ready(function(){
		  $('.answer').hide();
			
			$('.question').click(function(){
			  $(this).next('.answer').slideToggle();
			  $(this).toggleClass('open');
			});
		});", 
		array('inline' => false)
	);

  foreach ($items as $faq){ 
?>
	<article class="faq">
		<h2 class="question"><?php echo $faq['PrototypeItem']['name']; ?></h2>
		<div class="answer"><?php echo $faq['PrototypeItem']['answer']; ?></div>
	</article>
<?php 
  } 
?>
<?php echo $instance['PrototypeInstance']['footer_text']; ?>