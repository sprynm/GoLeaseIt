<!DOCTYPE html>
<html lang="en">
  <head>
		<?php
		$gtm_association_code	= $this->Settings->show('Site.Google.gtm_association_code');
		if (!empty($gtm_association_code)) :
			echo $this->element('gtm_head', array('gtm_association_code' => $gtm_association_code));
		// (!empty($gtm_association_code))
		endif;
		?>
    <title><?php echo $titleTag; ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php
      echo $this->Html->canonical();
      echo $this->Html->meta('icon');
			$this->Paginator->meta(array('block'=>true));
      echo $this->fetch('meta');
	  
      // Additional metas should the site be employing the Blog plugin.
      echo $this->element('metas');
      
      //echo $this->Html->css(array('stylesheet'));
      echo $this->Html->css('print', null, array('media' => 'print'));
			
			if (isset($extraHeaderCode)) {
				echo $extraHeaderCode;
			}
		?>
		<style>
			html {
  			font-size: 62.5%;
  		}

  		body {
  			--mainAccent: #133b5c;
  			--secondaryAccent: #1e5f74;
  			font-family: Helvetica, Arial, sans-serif;
   			color: #555;
  			background: #fff;
  		}

			img {
			  border: 0;
			  height: auto;
			  max-width: 100%;
			}

			.logo {
				display: block;
				max-width: 75%;
				margin: 3rem auto;
			}

			.logo img {
				display: block;
				margin: 0 auto;
			}

			.review-link:link,
			.review-link:visited {
				display: inline-block;
				vertical-align: middle;
				margin: 1rem;
				border-radius: .4rem;
				padding: .5rem 1.5rem;
				text-decoration: none;
				color: #fff;
				box-shadow: .1rem .1rem .5rem 0 rgba(0,0,0,.35);
				text-shadow: .1rem .1rem .3rem rgba(0,0,0,.35);
			}

			.review-link.google:link,
			.review-link.google:visited {
				transition: all 250ms ease-out;
				border: .1rem solid #ca0b00;
				background: linear-gradient(0deg, rgba(204,44,40,1) 0%, rgba(233,64,52,1) 100%);  
			}

			.review-link.google:hover {
				background: linear-gradient(180deg, rgba(204,44,40,1) 0%, rgba(233,64,52,1) 100%);  
				box-shadow: .1rem .1rem .5rem 0 rgba(0,0,0,0);
			}

			.review-link.google:after {
				content: "";
				mask: url(/img/icons/social-media/google.svg);
				mask-size: cover;
				background: #fff;
				display: inline-block;
				vertical-align: middle;
				width: 2.4rem;
				height: 2.4rem;
				margin: -.3rem 0 0 .8rem;
			}

			.review-link.facebook:link,
			.review-link.facebook:visited {
				transition: all 250ms ease-out;
				border: .1rem solid #324878;
				background: linear-gradient(0deg, rgba(61,88,145,1) 0%, rgba(72,103,170,1) 100%); 
			}

			.review-link.facebook:hover {
				background: linear-gradient(180deg, rgba(61,88,145,1) 0%, rgba(72,103,170,1) 100%); 
				box-shadow: .1rem .1rem .5rem 0 rgba(0,0,0,0);
			}

			.review-link.facebook:after {
				content: "";
				mask: url(/img/icons/social-media/facebook.svg);
				mask-size: cover;
				background: #fff;
				display: inline-block;
				vertical-align: middle;
				width: 2.4rem;
				height: 2.4rem;
				margin: -.3rem 0 0 .8rem;
			}

			#content {
				width: 81rem;
				max-width: calc(100% - 3rem);
				margin: 0 auto;
			}

			h1 {
				color: var(--mainAccent);
				font-size: 3.8rem;
			}

			h2 {
				color: var(--mainAccent);
				font-size: 3.2rem;
			}

			h3 {
				color: var(--mainAccent);
				font-size: 3rem;
			}

			h4 {
				color: var(--mainAccent);
				font-size: 2.6rem;
			}

			h5 {
				color: var(--mainAccent);
				font-size: 2.4rem;
			}

			h6 {
				color: var(--mainAccent);
				font-size: 2rem;
			}

			p,
			ul,
			ol,
			dl,
			pre {
				font-size: 2rem;
				line-height: 1.5;
			}

			p.note {
				font-size: 1.8rem;
			}

			cite {
				font-size: 1.6rem;
			}

			li::marker {
				color: var(--secondaryAccent);
			}

			hr {
  			background: #ddd;
  			color: #ddd;
  			border: medium none;
  			clear: both;
  			display: block;
  			height: .1rem;
  			margin: 3rem;
			}

			th {
				font-size: 2.1rem;
				padding: 1.5rem;
				font-weight: bold;
				background: var(--secondaryAccent);
				color: #fff;
			}

			td {
				font-size: 2.1rem;
				padding: 1.5rem;
				border-bottom: .1rem solid #ddd;
			}

			fieldset {
				border: .1rem solid #eee;
				border-radius: 1.2rem;
				padding: 2.4rem 2.4rem .9rem;
				box-shadow: .5rem .5rem 1.5rem 0 rgba(0,0,0,.15);
			}

			legend {
				display: none;
			}

			label {
				color: #777;
				display: block;
				font-size: 1.6rem;
				font-weight: bold;
			}

			input,
			textarea,
			select {
				box-sizing: border-box;
				font-size: 2.1rem;
				padding: .5rem;
				border-radius: .5rem;
				border: .1rem solid #ccc;
				transition: all 250ms ease-out;
				font-family: inherit;
				width: 100%;
			}

			input:hover,
			input:focus,
			select:hover,
			select:focus,
			textarea:hover,
			textarea:focus {
				border: .1rem solid var(--secondaryAccent);
			}

			input[type=submit],
			input[type=button] {
				width: auto;
				background: var(--mainAccent);
				color: #fff;
				font-weight: bold;
				cursor: pointer;
				padding: .5rem 1rem;
				border: .1rem solid var(--mainAccent);
			}

			input[type=submit]:hover,
			input[type=submit]:focus,
			input[type=button]:hover,
			input[type=button]:focus {
				background: var(--secondaryAccent);
			}

			.input {
				margin: 0 0 1.5rem;
			}

			.required {
				color: #cc0000;
			}

			.form_tip {
				font-style: italic;
				font-size: 1.6rem;
			}

			.notification {
			  border-radius: .8rem; 
			  font-size: 1.8rem;
			}

			.notification ol,
			.notification ul {
				font-size: 1.8rem;
			}

			.notification li::marker {
				color: inherit;
			}

			.notification, 
			.error-message {
			  position: relative;
			  margin: 0 0 3rem 0;
			  border: .1rem solid;
			  background-position: 1rem 1.1rem !important;
			  background-repeat: no-repeat !important;
			}

			.notification.notification--attention {
			  background-color: #fffbcc;
			  background-image: url('/img/icons/exclamation.png');
			  border-color: #e6db55;
			  color: #666452;
			}

			.notification.notification--information {
			  background-color: #dbe3ff;
			  background-image:url('/img/icons/information.png');
			  border-color: #a2b4ee;
			  color: #585b66;
			}

			.notification.notification--success {
			  background-color: #d5ffce;
			  background-image:url('/img/icons/tick_circle.png');
			  border-color: #9adf8f;
			  color: #556652;
			}

			.notification.notification--error, 
			.error-message {
			  background-color: #ffcece;
			  background-image:url('/img/icons/exclamation_circle.png');
			  border-color: #df8f8f;
			  color: #665252;
			}

			.notification div {
			  padding: 1rem 6rem 1rem 3.6rem;
			}

			.notification .close {
			  color: #990000;
			  font-size: 1.4rem;
			  position: absolute;
			  right: .5rem;
			  top: .5rem;
			}

			.notification .close:hover {
			  background:transparent;
			}

			.input .error-message {
			  color: #665252;
			  background:url('/img/icons/exclamation_circle.png') left center no-repeat;
			}

			.input .notification, 
			.input .error-message {
			  background-color: transparent;
			  padding: .4rem 0 .4rem 3.4rem;
			  border: 0;
				font-size: 1.5rem;
				color: #990000;
				font-style: italic;
				margin: .2rem .2rem 0;
				background-position: 1rem .5rem !important
			}

			/** NOTICES AND ERRORS **/
			.message {
			  clear: both;
			  color: #fff;
			  font-weight: bold;
			}

			.success,
			.message,
			.cake-error,
			.cake-debug,
			.notice,
			p.error,
			.error-message {
			  background: #ffcc00;
			  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
			  border: 1px solid rgba(0, 0, 0, 0.2);
			  margin-bottom: 1.8rem;
			  padding: .7rem 1.4rem;
			  color: #404040;
			  border-radius: 4px;
			  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
			}

			.success {
			  clear: both;
			  color: #fff;
			  border: .1rem solid rgba(0, 0, 0, 0.5);
			  background: #3B8230;
			  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
			}

			footer {
				text-align: center;
				border-top: .1rem solid #ddd;
				padding: 3rem 0;
				max-width: calc(100% - 3rem);
				margin: 3rem auto 0;
			}

			.ftr-contact {
				font-size: 1.8rem;
				margin: 0 0 1.5rem;
			}

			.divider {
				display: inline-block;
				vertical-align: middle;
				width: .1rem;
				position: relative;
				top: -.1rem;
				background: var(--secondaryAccent);
				height: 1.2rem;
				margin: 0 .4rem;
			}

			.copyright {
				font-size: 1.2rem;
				color: #777;
			}


			@media screen and (max-width: 780px) {
				.divider {
					display: block;
					height: 0;
					opacity: 0;
				}
			}

			@media screen and (max-width: 480px) {
				html {
					font-size: 2.083333333333333vw; /* 10/480 */ 
				}
			}
		</style>
  </head>
  <body id="<?php echo $bodyId; ?>" <?php if (isset($bodyClass) && !empty($bodyClass)) { echo 'class="'.$bodyClass.'"'; } ?>>
		<?php
		if (!empty($gtm_association_code)) :
			echo $this->element('gtm_body', array('gtm_association_code' => $gtm_association_code));
		endif;
		?>
 
		<header>
			<a href="/" class="logo">
				<img src="/img/admin/company-logo.png" alt="<?php echo $this->Settings->show('Site.name'); ?>">
			</a>
		</header>
	
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div><!-- "content" ends -->

		<footer>
			<div class="ftr-contact">
				<?php if($this->Settings->show('Site.Contact.address') != "") { echo $this->Settings->show('Site.Contact.address'); ?> <span class="divider"></span> <?php } ?><?php echo $this->Settings->show('Site.Contact.city'); ?>, <?php echo $this->Settings->show('Site.Contact.province_state'); ?> <?php echo $this->Settings->show('Site.Contact.postal_zip'); ?> <span class="divider"></span>
				Phone: <?php echo $this->Settings->show('Site.Contact.phone'); ?>
			</div>
  
			<div class="copyright">
				<?php
					if(isset($page) && $page['Page']['id'] == 1) {
						$rhText = $this->Html->link('Radar Hill Web Design', $this->Settings->show('Site.Footer.portfolio_link'), array('rel' => 'nofollow'));
					} else {
						$rhText = "Radar Hill Web Design";
					}
				?>  
				Copyright &copy; <?php echo $this->Copyright->year(); ?> <?php echo $this->Copyright->name(); ?> <span class="divider"></span> A <?php echo $this->Settings->show('Site.Footer.industry_identifier'); ?> website by <?php echo $rhText; ?><br>
				The content of this website is the responsibility of the website owner.
			</div> 
		</footer>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <?php 
				$scriptArray = array('library', 'lazyload.min', 'jquery.cookie', 'cms', 'forms');
			echo $this->Html->script($scriptArray);

	    if (isset($extraFooterCode)) {
	    	echo $extraFooterCode;
	    }
   	?>
  </body>
</html>
