<?php
$preconfigured = array(
    'Document Repositories' => array(
        'PrototypeInstance' => array(
            'use_categories' => 0,
            'item_document_type' => 'single',
            'item_summary_pagination' => 0
        ), 
        'PrototypeItemField' => array(
            array(
                'name' => 'description',
                'label' => 'Description',
                'type' => 'wysiwyg'
            )
        )
    ), 
    'Feature Boxes' => array(
        'PrototypeInstance' => array(
        	'name'			=> 'Feature Boxes',
        	'slug'			=> 'feature-boxes',
		'use_categories' 	=> 0,
		'public'		=> 1,
		'layout'		=> 'default',
		'allow_instance_view'	=> 0,
		'allow_category_views'	=> 1,
		'allow_item_views'	=> 0,
		'item_image_type'	=> 'single',
		'item_document_type'	=> 'none',
		'category_image_type'	=> 'none',
		'category_document_type'=> 'none',
		'item_summary_pagination'=> 0,
		'item_summary_pagination_limit'=> 10,
		'use_featured_items'	=> 0,
		'all_items_featured'	=> 0,
		'number_of_featured_items'=> 0,
		'autoload_featured_items_in_layouts' => 'home',
		'name_field_label' => 'Heading',
		'head_title'		=> 'Feature Boxes',
		'use_page_banner_images'=> 0,
		'fallback_to_instance_banner_image'=> 0,
		'use_page_banner_image_categories'=> 0,
		'use_page_banner_image_items'=> 0,
        ), 
        'PrototypeItemField' => array(
            array(
                'name'		=> 'subheading',
                'label'		=> 'Subheading',
                'type'		=> 'text',
                'rank'		=> 0
            ),
            array(
                'name'		=> 'text', 
                'type'		=> 'textarea', 
                'required'	=> 1,
                'validate'	=> 'notEmpty',
                'label'		=> 'Text',
                'rank'		=> 1
            ), 
            array(
                'name'		=> 'cta_link', 
                'type'		=> 'text', 
                'required'	=> 1,
                'validate'	=> 'notEmpty',
                'label'		=> 'CTA Link',
                'rank'		=> 2,
                'description'	=> 'Call to action link'
            ), 
            array(
                'name'		=> 'cta_link_text', 
                'type'		=> 'text', 
                'required'	=> 1,
                'validate'	=> 'notEmpty',
                'label'		=> 'CTA Link Text',
                'rank'		=> 3,
                'description'	=> 'Call to action link text'
            )
        ),
        'ExtraField' => array(
            array(
                'key' => 'use_featured_items', 
                'val' => '1', 
                'type' => 'checkbox', 
                'foreign_model' => 'PrototypeInstance'
            ), 
            array(
                'key' => 'autoload_featured_items_in_layouts', 
                'val' => 'home', 
                'type' => 'checkbox', 
                'foreign_model' => 'PrototypeInstance'
            ), 
            array(
                'key' => 'all_items_featured', 
                'val' => '1', 
                'type' => 'checkbox', 
                'foreign_model' => 'PrototypeInstance'
            ), 
            array(
                'key' => 'number_of_featured_items', 
                'val' => '1', 
                'type' => 'text', 
                'foreign_model' => 'PrototypeInstance'
            ), 
            array(
                'key' => 'use_featured_items', 
                'val' => '1', 
                'type' => 'text', 
                'foreign_model' => 'PrototypeInstance'
            )
        )
    ), 
    'Links' => array(
        'PrototypeInstance' => array(
            'allow_item_views' => 0,
            'item_summary_pagination' => 0,
            'item_order' => 'PrototypeItem.rank ASC'
        ), 
        'PrototypeItemField' => array(
            array(
                'name' => 'description',
                'label' => 'Description',
                'type' => 'wysiwyg',
                'required' => 0
            ),
            array(
                'name' => 'link', 
                'type' => 'text', 
                'default' => ''
            )
        )
    ), 
    'News' => array(
        'PrototypeInstance' => array(
            'use_categories' => 0, 
            'item_image_type' => 'single',
            'item_document_type' => 'single',
            'item_summary_pagination' => 0
        ), 
        'PrototypeItemField' => array(
            array(
                'name' => 'description',
                'label' => 'Description',
                'type' => 'wysiwyg'
            ),
            array(
                'name' => 'link', 
                'type' => 'text', 
                'default' => '',
                'required' => 0
            )
        )
    ), 
    'Staff' => array(
        'PrototypeInstance' => array(
            'item_image_type' => 'single',
            'allow_item_views' => 0,
            'item_summary_pagination' => 0
        ), 
        'PrototypeItemField' => array(
            array(
                'name' => 'description',
                'label' => 'Description',
                'type' => 'wysiwyg'
            ),
            array(
                'name' => 'position', 
                'type' => 'text', 
                'default' => ''
            )
        )
    ),
    'Testimonials' => array(
        'PrototypeInstance' => array(
            'use_categories' => 0,
            'allow_item_views' => 0,
            'item_summary_pagination' => 0,
            'item_order' => 'PrototypeItem.rank ASC'
        ), 
        'PrototypeItemField' => array(
            array(
                'name' => 'testimonial',
                'label' => 'Testimonial',
                'type' => 'wysiwyg',
                'rank' => 1
            ),
            array(
                'name' => 'byline', 
                'type' => 'text', 
                'default' => '',
                'rank' => 0,
                'required' => 0
            ),
            array(
                'name' => 'link', 
                'type' => 'text', 
                'default' => '',
                'rank' => 2,
                'required' => 0
            )
        )
    ), 
    'FAQ' => array(
        'PrototypeInstance' => array(
            'use_categories' => 0,
            'allow_item_views' => 0,
            'item_summary_pagination' => 0,
            'name_field_label' => 'Question',
            'item_order' => 'PrototypeItem.rank ASC'
        ),
        'PrototypeItemField' => array(
            array(
                'name' => 'answer',
                'label' => 'Answer',
                'type' => 'wysiwyg'
            )
        )
    )
);