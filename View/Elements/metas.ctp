<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="<?php echo Configure::read('Settings.Site.name'); ?>">
<meta property="og:url" content="<?php echo Router::url( $this->here, true ); ?>">
<meta property="og:type" content="website">
<?php // 
//
if (isset($category['PrototypeCategory']) && !empty($category['PrototypeCategory'])) :
	//
	if (isset($category['PrototypeCategory']['name']) && strlen($category['PrototypeCategory']['name']) > 0) :
	?>
		<meta property="og:title" content="<?php echo $category['PrototypeCategory']['name']; ?>">
	<?php //
	// (isset($category['PrototypeCategory']['name']) && strlen($category['PrototypeCategory']['name']) > 0)
	endif;
	//
	if (isset($category['PrototypeCategory']['overview']) && strlen($category['PrototypeCategory']['overview']) > 0) :
	?>
		<meta property="og:description" content="<?php echo $this->Text->truncate(preg_replace("/\r|\n|\"/", '', strip_tags($category['PrototypeCategory']['overview'])), 200, array('ellipses' => '...')); ?>">
	<?php //
	// (isset($category['PrototypeCategory']['overview']) && strlen($category['PrototypeCategory']['overview']) > 0)
	endif;
// (isset($category['PrototypeCategory']) && !empty($category['PrototypeCategory']))
else :
?>
	<meta property="og:title" content="<?php echo (isset($titleTag) ? strip_tags($titleTag) : '') . (isset($pageHeading) && !empty($pageHeading) ? ' - ' . strip_tags($pageHeading): ''); ?>">
	<?php //
	//
	if (isset($metas['description']) && strlen($metas['description']) > 0) :
	?>
		<meta property="og:description" content="<?php echo $this->Text->truncate(preg_replace("/\r|\n|\"/", '', strip_tags($metas['description'])), 200, array('ellipses' => '...')); ?>">
<?php //
	// (isset($metas['description']) && strlen($metas['description']) > 0)
	endif;
// (isset($category['PrototypeCategory']) && !empty($category['PrototypeCategory']))
endif;
//
if (!empty($banner['Image'][0])) :
?>
	<meta property="og:image" content="<?php echo rtrim(Router::url('/', true), '/'). $this->Media->path($banner['Image'][0], 'banner-lrg'); ?>">
<?php //
// (!empty($banner['Image'][0]))
else :
?>
	<meta property="og:image" content="<?php echo rtrim(Router::url('/', true), '/'); ?>/img/fb-og-image.jpg">
<?php // 
// (!empty($banner['Image'][0]))
endif;
?>