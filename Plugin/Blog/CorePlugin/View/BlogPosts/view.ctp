<?php //
//
$estimated_time	= $this->BlogPost->estimatedReadTime($blogPost);
?>
<div class="main-content">
  <article class="blog-detail-post" itemscope itemtype="http://schema.org/BlogPosting">
		<meta itemprop="headline" content="<?php echo h($blogPost['BlogPost']['title']); ?>">
		<?php 
		//Post Date
		/*
		?>
		<h3>Posted on <time datetime="<?php echo date('c', strtotime($blogPost['PublishingInformation']['start'])); ?>" itemprop="datePublished"><?php echo date('l F jS, Y', strtotime($blogPost['PublishingInformation']['start'])); ?></time></h3>
		<?php 
		*/
		?>
		<?php
			if($estimated_time != "") {
		?>
			<div class="read-time"><?php echo 'Estimated Read Time: ' . $estimated_time; ?></div>
		<?php
			}
		?>    
		<?php
		if (!empty($blogPost['Image'])):
			echo '<meta itemprop="image" content="' . $this->Media->fullFilePath($blogPost['Image'][0], 'banner-fhdl') . '" />';
		endif;
		?>
    <div class="blog-detail-entry" itemprop="text"><?php echo $blogPost['BlogPost']['body']; ?></div>
		<?php
		// Image(s) now render in the shared page masthead.
		if (!empty($blogPost['Image'])):
		?>
			<meta itemprop="thumbnailUrl" content="<?php echo h($this->Media->fullFilePath($blogPost['Image'][0], 'thumb')); ?>">
		<?php endif; ?>
		<div class="blog-footer-buttons">
			<?php echo $this->SocialMedia->socialMediaButtons('blog'); ?>
			<a href="javascript:if(window.print)window.print()" class="btn btn--secondary btn-sm u-btn-no-icon">Print</a>
		</div>
		<?php 
		if ( !empty($blogPost['BlogCategory']) || !empty($blogPost['BlogTag']) ): 
		?>
		<div class="blog-detail-footer">
			<?php if (!empty($blogPost['BlogCategory'])): ?>
				<div class="categories">
					<strong>Categories:</strong> 
					<?php 
					foreach ($blogPost['BlogCategory'] as $blogCategory):
						?>
						<a class="btn btn--secondary btn-sm u-btn-no-icon" href="<?php echo $blogCategory['url']; ?>" itemprop="keywords"><?php echo $blogCategory['name']; ?></a>
						<?php 
					endforeach;
					?>
				</div>
			<?php endif; ?>
	
			<?php if (!empty($blogPost['BlogTag'])): ?>
				<div class="tags">
					<strong>Tags:</strong> 
					<?php 
					foreach ($blogPost['BlogTag'] as $blogTag):
					?>
						<a class="btn btn--secondary btn-sm u-btn-no-icon" href="<?php echo $blogTag['url']; ?>" itemprop="keywords"><?php echo $blogTag['name']; ?></a>
					<?php 
					endforeach;
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php 
		endif; 
		?>
		<?php 
		//check if commenting is allowed for this post
		if (!empty($blogPost['BlogPost']['allow_commenting']) && Configure::read('Settings.Blog.commenting_enabled')):
			//show comments for this post
			if (!empty($blogPost['BlogPostComment'])): 
			?>
			<div class="blog-comments">
				<h2>Comments</h2>
				<ul class="comments">
					<?php 
					foreach ($blogPost['BlogPostComment'] as $blogPostComment): 
						if ($blogPostComment['approved']): 
						?>
						<li itemprop="comment" itemscope itemtype="http://schema.org/Comment">	
							<div class="comment-meta">
								<?php if (!empty($blogPostComment['gravatar'])): ?>
								<span class="gravatar"><?php echo $blogPostComment['gravatar']; ?></span>
								<?php endif; ?>
							<span itemprop="author"><?php echo $blogPostComment['name']; ?></span> <time datetime="<?php echo date('c', strtotime($blogPostComment['created'])); ?>" itemprop="dateCreated">- <?php echo date('F j, Y', strtotime($blogPostComment['created'])); ?></time></div>
							<p itemprop="text"><?php echo $blogPostComment['text']; ?></p>
						</li>
						<?php 
						endif;
					endforeach; 
					?>
				</ul>
			</div>
			<?php 
			endif;
			
			//comment pagination
			//make sure that the slug param is set because it should be but isn't for some reason
			$this->passedArgs['slug'] = $blogPost['BlogPost']['slug'];
			echo $this->element('pagination/bottom');
			
			//new comment form
			echo $this->Blog->commentForm($blogPost['BlogPost']['id']);
			?>
		<?php
		endif; //if commenting is turned on
		?>
  </article>
  <?php 
  //show tags and categories for this post
	$categories = array();
  foreach ($blogPost['BlogCategory'] as $category):
    array_push($categories, array( 'BlogCategory'=> $category ));
  endforeach;
  
	$tags = array();
	
  foreach ($blogPost['BlogTag'] as $tag):
    array_push($tags, array( 'BlogTag'=> $tag ));
  endforeach;
  ?>
</div>
