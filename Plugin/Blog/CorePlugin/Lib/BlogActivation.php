<?php
App::uses('PluginActivation', 'PluginTools.Lib');

/**
 * BlogActivation class
 *
 * Performs tasks related to Blog plugin activation/deactivation.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classBlogActivation.html
 * @package      Cms.Plugin.Blog.Lib  
 * @since        Pyramid CMS v 1.0
 */
class BlogActivation extends PluginActivation {

	

/** 
 * Extra plugin data
 */
	protected $_extraData = array(
		// dummy categories
		'Blog.BlogCategory' => array(
			array(
				'name' => 'News'
			) 
			, array(
				'name' => 'Social Media'
			)
			, array(
				'name' => 'Sports'
			)
		) // dummy tags
		, 'Blog.BlogTag' => array(
			array(
				'name' => 'Cats'
			) 
			, array(
				'name' => 'Dogs'
			) 
			, array(
				'name' => 'Squirrels'
			)
			, 
		) // dummy blog posts
		, 'Blog.BlogPost' => array(
			array(
				'title' => 'When We Had...'
				, 'summary' => 'When we had finished our prayers and viewed the spectacle, we turned in the direction of the city; '
				, 'body' => 'When we had finished our prayers and viewed the spectacle, we turned in the direction of the city; and at that instant Polemarchus the son of Cephalus chanced to catch sight of us from a distance as we were starting on our way home, and told his servant to run and bid us wait for him. The servant took hold of me by the cloak behind, and said: Polemarchus desires you to wait.</br>'
				, 'slug' => 'when-we-had'
				, 'allow_commenting' => 1
				
			)
			, array(
				'title' => 'I turned round'
				, 'summary' => 'There he is, said the youth, coming after you, if you will only wait.'
				, 'body' => 'There he is, said the youth, coming after you, if you will only wait.</br>
					</br>
					Certainly we will, said Glaucon; and in a few minutes Polemarchus appeared, and with him Adeimantus, Glaucon\'s 
					brother, Niceratus the son of Nicias, and several others who had been at the procession.</br>
					</br>
					Socrates - POLEMARCHUS - GLAUCON - ADEIMANTUS</br>
					</br>
					Polemarchus said to me: I perceive, Socrates, that you and our companion are already on your way to the city.</br>'
				, 'slug' => 'i-turned-round'
				,
			)
			, array(
				'title' => 'You are not far wrong, I said.'
				, 'summary' => 'You are not far wrong, I said.</br>
					But do you see, he rejoined, how many we are?</br>
					Of course.'
				, 'body' => 'But do you see, he rejoined, how many we are?</br>
					Of course.</br>
					And are you stronger than all these? for if not, you will have to remain where you are.</br>
					</br>
					May there not be the alternative, I said, that we may persuade you to let us go?</br>
					</br>
					But can you persuade us, if we refuse to listen to you? he said.</br>
					Certainly not, replied Glaucon.</br>
					Then we are not going to listen; of that you may be assured.</br>
					Adeimantus added: Has no one told you of the torch-race on horseback in honour of the goddess which will take place in the evening?</br>
					</br>
					With horses! I replied: That is a novelty. Will horsemen carry torches and pass them one to another during the race?</br>
					</br>'
				, 'slug' => 'far-wrong'
				
			)
			, array(
				'title' => 'Yes, said Polemarchus'
				, 'summary' => 'Yes, said Polemarchus, and not only so, but a festival will he celebrated at night, which you certainly ought to see. Let us rise soon after supper and see this festival; there will be a gathering '
					, 'body' => 'Yes, said Polemarchus, and not only so, but a festival will he celebrated at night, which you certainly ought to see. Let us rise soon after supper and see this festival; there will be a gathering of young men, and we will have a good talk. Stay then.</br>
					</br>
					Glaucon said: I suppose, since you insist, that we must.</br>
					Very good, I replied.</br>
					</br>
					Glaucon - CEPHALUS - SOCRATES</br>
					</br>
					Accordingly we went with Polemarchus to his house; and there we found his brothers Lysias and Euthydemus, and with them Thrasymachus the Chalcedonian, Charmantides the Paeanian, and Cleitophon the son of Aristonymus. There too was Cephalus the father of Polemarchus, whom I had not seen for a long time, and I thought him very much aged. He was seated on a cushioned chair, and had a garland on his head, for he had been sacrificing in the court; and there were some other chairs in the room arranged in a semicircle, upon which we sat down by him. He saluted me eagerly, and then he said: --</br>
					</br>
					You don\'t come to see me, Socrates, as often as you ought: If I were still able to go and see you I would not ask you to come to me. But at my age I can hardly get to the city, and therefore you should come oftener to the Piraeus. For let me tell you, that the more the pleasures of the body fade away, the greater to me is the pleasure and charm of conversation. Do not then deny my request, but make our house your resort and keep company with these young men; we are old friends, and you will be quite at home with us.</br>
					</br>'
				, 'slug' => 'polemarchus'
				,
			)
			, array(
				'title' => 'I replied:'
				, 'summary' => 'I replied: There is nothing which for my part I like better, Cephalus, than conversing with aged men; for I regard them as travellers who have gone a journey which I too may have to go, and of whom I ought to enquire, whether the way is smooth and easy, or rugged and difficult...'
				, 'body' => 'I replied: There is nothing which for my part I like better, Cephalus, than conversing with aged men; for I regard them as travellers who have gone a journey which I too may have to go, and of whom I ought to enquire, whether the way is smooth and easy, or rugged and difficult. And this is a question which I should like to ask of you who have arrived at that time which the poets call the "threshold of old age" --Is life harder towards the end, or what report do you give of it?</br>'
				, 'slug' => 'i-replied'
			)
		
		), 'Blog.BlogPostCategory' => array(
			array( 
				'id' => 1,
				'blog_category_id' => 3,
				'blog_post_id' => 1
			) 
			, array( 
				'id' => 2,
				'blog_category_id' => 3,
				'blog_post_id' => 2
			)
			, array( 
				'id' => 3,
				'blog_category_id' => 2,
				'blog_post_id' => 3
			)
			, array( 
				'id' => 4,
				'blog_category_id' => 2,
				'blog_post_id' => 4
			)
			, array( 
				'id' => 5,
				'blog_category_id' => 2,
				'blog_post_id' => 5
			)
		)
		, 'Blog.BlogPostTag' => array(
			array( 
				'id' => 1,
				'blog_tag_id' => 1,
				'blog_post_id' => 1
			) 
			, array( 
				'id' => 2,
				'blog_tag_id' => 2,
				'blog_post_id' => 1
			)
			, array( 
				'id' => 3,
				'blog_tag_id' => 3,
				'blog_post_id' => 1
			)
			, array( 
				'id' => 4,
				'blog_tag_id' => 1,
				'blog_post_id' => 2
			)
			, array( 
				'id' => 5,
				'blog_tag_id' => 2,
				'blog_post_id' => 2
			)
		)
		// dummy comments	
		 ,  'Blog.BlogPostComment' => array(
			 array(
				'blog_post_id' => 1
				, 'name' => 'john'
				, 'email' => 'john@home.com'
				, 'text' => 'I agree.'
				, 'approved' => 1
			 )
			 , 
			 array(
				'blog_post_id' => 1
				, 'name' => 'Jill'
				, 'email' => 'jill@office.com'
				, 'text' => 'I disagree.'
				, 'approved' => 1
			 )
			 , array(
				'blog_post_id' => 1
				, 'name' => 'Peter'
				, 'email' => 'peter@sea.com'
				, 'text' => 'I am on a boat.'
				, 'approved' => 1
			 ), 	
		)
		, 'Media.AttachmentVersion' => array(
			array(
				'model' => 'BlogPost', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'thumb', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 100, 
				'height' => 100
			),
			array(
				'model' => 'BlogPost',
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'medium', 
				'type' => 'fitCrop', 
				'convert' => 'image/jpeg', 
				'width' => 300, 
				'height' => 300
			),
			array(
				'model' => 'BlogPost',
				'foreign_key' => null,
				'group' => 'Image',
				'name' => 'banner-fhdl',
				'type' => 'fitCrop',
				'convert' => 'image/jpeg',
				'width' => 1920,
				'height' => 350
			),
			array(
				'model' => 'BlogPost',
				'foreign_key' => null,
				'group' => 'Image',
				'name' => 'banner-med',
				'type' => 'fitCrop',
				'convert' => 'image/jpeg',
				'width' => 1440,
				'height' => 350
			),
			array(
				'model' => 'BlogPost',
				'foreign_key' => null,
				'group' => 'Image',
				'name' => 'banner-sm',
				'type' => 'fitCrop',
				'convert' => 'image/jpeg',
				'width' => 800,
				'height' => 450
			),
			array(
				'model' => 'BlogPost',
				'foreign_key' => null,
				'group' => 'Image',
				'name' => 'banner-xsm',
				'type' => 'fitCrop',
				'convert' => 'image/jpeg',
				'width' => 540,
				'height' => 375
			),
			array(
				'model' => 'BlogPost', 
				'foreign_key' => null, 
				'group' => 'Image', 
				'name' => 'large', 
				'type' => 'fit', 
				'convert' => 'image/jpeg', 
				'width' => 800, 
				'height' => 600
			)
		)
	);
/**
 * Permissions
 */
	protected $_permissions = array(
		array(
			'Permission' => array('plugin' => 'blog', 'controller' => '*', 'action' => 'admin', 'description' => 'Blog management'),
			'Group' => array('Group' => array(2))
		)
	);

/**
 * Settings to be installed
 */
	protected $_settings = array(
		array(
			'key' => 'Blog.posts_per_page',
			'value' => '10',
			'title' => 'Posts per page',
			'description' => '',
			'type' => 'text'
		),
		array(
			'key' => 'Blog.commenting_enabled',
			'value' => '0',
			'title' => 'Allow Commenting - must also be enabled specific posts',
			'description' => '',
			'type' => 'checkbox'
		),
		array(
			'key' => 'Blog.post_order',
			'value' => 'PublishingInformation.start DESC',
			'title' => 'Order for blog post index',
			'type' => 'select',
			'options' => 'PublishingInformation.start DESC,PublishingInformation.start ASC,BlogPost.rank ASC,BlogPost.rank DESC',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.comment_moderation',
			'value' => '1',
			'title' => 'Require comment moderation',
			'description' => 'Comments will require an administrator\'s approval before appearing on the site.',
			'type' => 'checkbox'
		),
		array(
			'key' => 'Blog.comment_notification',
			'value' => '0',
			'title' => 'Notify me when a comment is posted',
			'description' => 'You will receive an email when a comment is added to a post.',
			'type' => 'checkbox'
		),
		array(
			'key' => 'Blog.comment_notification_email',
			'value' => '',
			'title' => 'Comment notification email (If blank, email address on "Site" tab is used)',
			'description' => 'Email that is sent notifications of comment submissions.',
			'type' => 'text'
		),
		array(
			'key' => 'Blog.notification_from_email',
			'value' => '',
			'title' => 'Comment from email (If blank, email address on "Site" tab is used)',
			'description' => 'Email address that sends notifications of comment submissions.',
			'type' => 'text'
		),
		array(
			'key' => 'Blog.blog_post_images',
			'value' => '1',
			'title' => 'Images',
			'description' => 'An option to include images along with BlogPosts.',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.display_gravatars',
			'value' => '1',
			'title' => 'Display gravatar avatars',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.share.all',
			'value' => '1',
			'title' => 'Display share all button',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.share.twitter',
			'value' => '0',
			'title' => 'Display Twitter share button',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.share.facebook',
			'value' => '0',
			'title' => 'Display Facebook share button',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.share.google_plus',
			'value' => '0',
			'title' => 'Display Google Plus share button',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.share.pinterest',
			'value' => '0',
			'title' => 'Display Pinterest share button',
			'type' => 'checkbox',
			'super_admin' => 1
		),
		
		array(
			'key' => 'Blog.page_heading',
			'value' => 'Blog',
			'title' => 'Page Heading',
			'type' => 'text',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.category_header_template',
			'value' => 'Categorized Under %BLOG_CATEGORY%',
			'description' => 'The text that show beside the page header when filtering by a specific category. %BLOG_CATEGORY% gets replaced with the category name.',
			'title' => 'Blog Category Header Template',
			'type' => 'text',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.tag_header_template',
			'value' => 'Tagged %BLOG_TAG%',
			'description' => 'The text that show beside the page header when filtering by a specific tag. %BLOG_TAG% gets replaced with the tag name.',
			'title' => 'Blog Tag Header Template',
			'type' => 'text',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.date_published_header_template',
			'value' => 'Published %DATE_PUBLISHED%',
			'description' => 'The text that show beside the page header when filtering by date. %DATE_PUBLISHED% gets replaced with the month (if any) and year.',
			'title' => 'Blog Date Published Header Template',
			'type' => 'text',
			'super_admin' => 1
		),
		array(
			'key' => 'Blog.intro',
			'value' => 'Welcome to my blog.',
			'title' => '',
			'description' => '',
			'type' => 'wysiwyg'
		),
	);

/**
 * after schema update callback
 *
 * @see CmsPluginActivation::afterSchemaUpdate
 */
	public function afterSchemaUpdate($schemaVersion) {
		switch ($schemaVersion) {
			// Adding a post ordering setting
			case '3':
				$data = array(
					array(
						'key' => 'Blog.post_order',
						'value' => 'PublishingInformation.start DESC',
						'title' => 'Order for blog post index',
						'type' => 'select',
						'options' => 'PublishingInformation.start DESC,PublishingInformation.start ASC,BlogPost.rank ASC,BlogPost.rank DESC',
						'super_admin' => 1
					)
				);

				foreach ($data as $key => $data) {
					ClassRegistry::init('Settings.Setting')->create();
					$saved = ClassRegistry::init('Settings.Setting')->saveAll($data, array('deep' => true));
				}
			break;

			case '4':
				$AttachmentVersion = ClassRegistry::init('Media.AttachmentVersion');
				$versions = array(
					array(
						'model' => 'BlogPost',
						'foreign_key' => null,
						'group' => 'Image',
						'name' => 'banner-fhdl',
						'type' => 'fitCrop',
						'convert' => 'image/jpeg',
						'width' => 1920,
						'height' => 350
					),
					array(
						'model' => 'BlogPost',
						'foreign_key' => null,
						'group' => 'Image',
						'name' => 'banner-med',
						'type' => 'fitCrop',
						'convert' => 'image/jpeg',
						'width' => 1440,
						'height' => 350
					),
					array(
						'model' => 'BlogPost',
						'foreign_key' => null,
						'group' => 'Image',
						'name' => 'banner-sm',
						'type' => 'fitCrop',
						'convert' => 'image/jpeg',
						'width' => 800,
						'height' => 450
					),
					array(
						'model' => 'BlogPost',
						'foreign_key' => null,
						'group' => 'Image',
						'name' => 'banner-xsm',
						'type' => 'fitCrop',
						'convert' => 'image/jpeg',
						'width' => 540,
						'height' => 375
					),
				);

				foreach ($versions as $version) {
					$existing = $AttachmentVersion->find('first', array(
						'conditions' => array(
							'AttachmentVersion.model' => $version['model'],
							'AttachmentVersion.foreign_key' => $version['foreign_key'],
							'AttachmentVersion.group' => $version['group'],
							'AttachmentVersion.name' => $version['name'],
						),
						'recursive' => -1,
					));

					if (empty($existing['AttachmentVersion']['id'])) {
						$AttachmentVersion->create();
						$AttachmentVersion->save($version);
					}
				}
			break;
		}
	}
}
