<?php $this->extend('Administration.Common/edit-page'); ?>
<?php
$this->set('header', 'Add/Edit Nav Item: ' . $navigationMenu['NavigationMenu']['name']);

$this->start('formStart');
echo $this->Form->create('NavigationMenuItem', array('class' => 'editor_form', 'url' => $this->request->here));
$this->end('formStart');
?>
<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link('Basic', '#tab-basic'); ?></li>
<?php $this->end('tabs'); ?>
<?php
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Navigation Item'), array('action' => 'edit', $navigationMenu['NavigationMenu']['id']));
echo $this->AdminLink->link(__('Back to Menu Items'), array('controller' => 'navigation_menus', 'action' => 'edit', $navigationMenu['NavigationMenu']['id']));
$this->end('actionLinks');
?>
<?php
echo $this->Html->script('Navigation.item_edit', array('inline' => false));
echo $this->Html->css('Navigation.item_edit', null, array('inline' => false));
?>
<div id="tab-basic">
	<h2>Menu Item Details</h2>
	<?php
	echo $this->Form->input('id');
	echo $this->Form->input('name');
	echo $this->Form->input('navigation_menu_id', array('type' => 'hidden', 'value' => $navigationMenu['NavigationMenu']['id']));
	echo $this->Form->input('foreign_model', array('type' => 'hidden'));
	echo $this->Form->input('foreign_key', array('type' => 'hidden'));
	echo $this->Form->input('foreign_plugin', array('type' => 'hidden'));
	echo $this->Form->input('url', array('type' => 'hidden'));
	?>
	<h3>Links to: <span id="links-to-text"></span></h3>
	<div id="nav-tabs">
		<div class="nav-tabs">
			<ul>
				<?php foreach ($targetUrls as $key => $val): ?>
				<li><?php echo $this->Html->link(Inflector::humanize($key), '#'.Inflector::slug($key).'-links'); ?></li>
				<?php endforeach; ?>
				<li><?php echo $this->Html->link('Custom Link', '#custom-link'); ?></li>
			</ul>
			<?php 
			reset($targetUrls); 
			foreach ($targetUrls as $name => $sections):
			?>
			<div id="<?php echo Inflector::slug($name); ?>-links">
				<ul class="nav-link-list">
					<?php foreach ($sections as $section => $items): ?>
						<li><?php echo $section; ?>
						<ul>
						<?php foreach ($items as $item => $info): ?>
						<li><?php echo $this->Html->link($info['name'], '#', array('title' => Inflector::humanize($name) . ': ' . $info['name'], 'id' => $info['model'] . '-' . $info['key'] . '-' . Inflector::camelize(isset($aliases[$name]) ? $aliases[$name] : $name))); ?></li>
						<?php endforeach ;?>
						</ul>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endforeach; ?>
			<div id="custom-link">
				<?php
				echo $this->Form->input('custom_link');
				//echo $this->Form->submit('Use link', array('id' => 'custom-link-submit'));
				?>
			</div>
		</div>
	</div>
	<?php
	echo $this->Form->input('parent_id', array(
		'label' => 'Optional Parent',
		'type' => 'select',
		'options' => $navigationMenuItems,
		'display' => 'name',
		'empty' => '- No Parent -'
	));
	
	echo $this->Form->input('new_window', array(
		'legend' => 'Link Opens In:',
		'type' => 'radio',
		'options' => array(0 => 'Same Tab/Window', 1 => 'New Tab/Window'),
		'value' => isset($this->request->data['NavigationMenuItem']['new_window']) ? $this->request->data['NavigationMenuItem']['new_window'] : 0
	));
	?>
</div>