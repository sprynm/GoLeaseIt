<?php
extract ($data);

$link = $this->Navigation->itemLink($NavigationMenuItem);

$published = $this->Publishing->isPublished($NavigationMenuItem, 'Navigation.NavigationMenuItem');

$title = $NavigationMenuItem['name'];
$class = 'menu-item rounded-corners';
if (!$published):
    $class .= ' unpublished';
    $title .= ' <span class="unpublished-text">(Unpublished/Invisible)</span>';
endif;
?>
<div class="<?php echo $class; ?>">
  <span class="item-name">
    <?php echo $title; ?><br />
    <?php
    if ($link):
        echo $this->Html->link($link, $link, array('target' => '_blank')); 
    endif;
    ?>
  </span>
  <span class="item-controls">
      <?php
      echo $this->Html->link('Edit', array('controller' => 'navigation_menu_items', 'action' => 'edit', $this->data['NavigationMenu']['id'], $NavigationMenuItem['id']));
      echo $this->Html->link('Delete', array('controller' => 'navigation_menu_items', 'action' => 'delete', $NavigationMenuItem['id']));
      ?>
  </span>
</div>
