<p class="error">Sorry - we can't find the page you're looking for.</p>
<h2>You might find these links helpful: </h2>
<?php foreach ($this->Navigation->error404Sitemap() as $key => $list) { ?>
	<div class="sitemap-list">
		<?php 
		if (!empty($list['NavigationMenu']['sitemap_display_label'])):
		?>
		<h2><?php echo $list['NavigationMenu']['name']; ?></h2>
		<?php
		endif;
		?>
		<?php 
		echo $this->Navigation->show(
			$list['NavigationMenu']['id']
			, true
			, array(
				'ulClass' => null
				, 'liClass' => null
				, 'ulId' => 'sitemap_nav_' . Inflector::slug( strtolower($list['NavigationMenu']['name']) )
			)
		); 
		?>
	</div>
<?php } ?>