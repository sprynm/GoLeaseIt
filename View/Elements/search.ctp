<?php
	echo $this->Form->create(
		false
		, array(
			'type'		=> 'get'
			, 'url'		=> array('plugin' => 'sitemap', 'controller' => 'sitemap', 'action' => 'index_search')
			, 'id'		=> 'CmsPluginIndexSearchFormHeader'
			, 'class'	=> 'search sitemap_search_form'
			, 'role' => 'search'
		)
	);
	
	echo $this->Form->input(
		'q_find'
		, array(
			'label' => false
			, 'div'		=> false
			, 'value' 	=> isset($this->request->query['q_find']) ? $this->request->query['q_find'] : null
			, 'id'		=> 'CmsPluginQFindHeader'
			, 'class'	=> 'sitemap_search_input_header'
			, 'placeholder' => 'Site Search...'
			, 'aria-label' => 'search'
		)
	);

	echo $this->Form->submit(
		'Search'
		, array(
			'div'		=> false,
			'value' => 'Search',
			'class' => 'search-btn'
		)
	);

	echo $this->Form->end();
?>