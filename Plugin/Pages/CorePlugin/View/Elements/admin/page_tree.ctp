<?php
// The "super admin" control hides "super admin" (i.e. action-mapped) pages in the main tab and "static" pages in the super admin tab
if (isset($extraData['superAdmin'])):
	$superAdmin = $extraData['superAdmin'];
else:
	$superAdmin = false;
endif;

extract($data);

//this is where the link is generated. Next step is Router::url? I guess? Might be time to read the docs instead.

$link = Router::url($this->ModelLink->link('Pages.Page', $Page['id']), true);

$title = $Page['page_heading'];
if (isset($Page['internal_name']) && !empty($Page['internal_name'])):
	$title = $Page['internal_name'];
endif;

$deleteLink = array('action' => 'delete', $Page['id']);
$canDelete = false;
if (AccessControl::inGroup('Super Administrator')):
	$canDelete = true;
elseif (!$Page['protected'] && !$Page['action_map'] && AccessControl::isAuthorized($deleteLink)):
	$canDelete = true;
endif;

$canEdit = true;
if (!AccessControl::inGroup('Super Administrator') && $Page['action_map']):
	$canEdit = false;
endif;

$published = !empty($Page['plugin']) || ($this->Pages->isPublished($Page['id']) && $this->Pages->parentsArePublished($Page['id']));

$class = 'menu-item rounded-corners';
if (!$published):
	$class .= ' unpublished';
	$title .= ' <span class="unpublished-text">(Unpublished/Invisible)</span>';
endif;
if (!empty($Page['password'])):
	$title .= ' <span class="unpublished-text">(Password protected)</span>';
endif;

if (!Cms::minVersion('1.0.4')):
	if ($superAdmin && !$Page['action_map']):
		$class .= ' hide-page';
	elseif (!$superAdmin && $Page['action_map']):
		$class .= ' hide-page';
	endif;
endif;
?>
<div class="<?php echo $class; ?>">

	<?php 
	
		//echo "can edit: " . $canEdit;
	if ($canEdit) { ?>
			<span class="item-name">
				<?php echo $this->Html->link( $title, array('action' => 'edit', $Page['id']), array('escape' => false)); ?>
			</span>
	<?php } else { ?>
			<span class="item-name"><?php echo $title; ?></span>
	<?php } ?>
	<span class="item-controls">
		<?php
		echo $this->Html->link('View', $link, array('target' => '_blank'));
		if ($canEdit):
			echo $this->Html->link('Edit', array('action' => 'edit', $Page['id']));
		endif;
		if ($canDelete):
			echo $this->Html->link('Delete', $deleteLink, null, "Are you sure you want to delete " . $Page['title'] . "?");
		endif;
		?>
	</span>
</div>
