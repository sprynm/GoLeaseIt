<?php
/**
 * CmsBlogPostsController class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsBlogPostsController.html
 * @package		 Cms.Plugin.Blog.Controller 
 * @since		 Pyramid CMS v 1.0
 */
class CmsBlogPostsController extends BlogAppController {

	public $components = array('RequestHandler');
	
	public $helpers = array('Text');

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender() {
		//
		parent::beforeRender();
		//
		// Temporary SEO hold for Success Stories while the section remains mounted at
		// the root-level Blog slug. Remove after the final IA/URL cutover is live.
		if (empty($this->request->params['admin'])) {
			$this->set('extraHeaderCode', '<meta name="robots" content="noindex,follow">');
		}
		//
		if (
			(!isset($this->viewVars['pageHeading']) || strlen(trim((string)$this->viewVars['pageHeading'])) === 0)
			&& isset($this->viewVars['_page']['Page']['page_heading'])
			&& strlen($this->viewVars['_page']['Page']['page_heading']) > 0
		) {
			//
			$this->set(
				'pageHeading'
				, $this->viewVars['_page']['Page']['page_heading']
			);
		}
		//
		if (
			(!isset($this->viewVars['titleTag']) || strlen(trim((string)$this->viewVars['titleTag'])) === 0)
			&& isset($this->viewVars['_page']['Page']['title'])
			&& strlen($this->viewVars['_page']['Page']['title']) > 0
		) {
			//
			$title_separator	= Configure::read('Settings.Site.title_separator');
			//
			$name			= Configure::read('Settings.Site.name');
			//
			$common_head_title	= Configure::read('Settings.Site.common_head_title');
			//
			$this->set(
				'titleTag'
				, $this->viewVars['_page']['Page']['title'] . ' ' . $title_separator . ' ' . $name . ' ' . $title_separator . ' ' . $common_head_title
			);
		}
	}

/**
 * Populate the standard page masthead variables from a blog post.
 *
 * @param array $blogPost Blog post record with Image data.
 * @param string $titlePrefix Optional prefix for preview titles.
 * @return void
 */
	protected function _setPostHeroVars($blogPost, $titlePrefix = '') {
		$title = trim((string)Hash::get($blogPost, 'BlogPost.title', ''));
		$summary = trim(preg_replace('/\s+/', ' ', strip_tags((string)Hash::get($blogPost, 'BlogPost.summary', ''))));

		$page = isset($this->viewVars['_page']) && is_array($this->viewVars['_page']) ? $this->viewVars['_page'] : array();
		if (empty($page['Page']) || !is_array($page['Page'])) {
			$page['Page'] = array();
		}

		$page['Page']['name'] = $title;
		$page['Page']['banner_summary'] = $summary;

		$this->set('page', $page);
		$this->set('banner', array('Image' => !empty($blogPost['Image']) ? $blogPost['Image'] : array()));
		$this->set('heroImageVersion', 'banner-fhdl');
		$this->set('heroImageWidth', 1920);
		$this->set('heroImageHeight', 350);
		$this->set('ogImageVersion', 'banner-fhdl');
		$this->set('pageHeading', $title);

		$titlePrefix = trim((string)$titlePrefix);
		if ($titlePrefix !== '') {
			$title = $titlePrefix . ' ' . $title;
		}

		$titleTag = $title . ' ' . Configure::read('Settings.Site.title_separator') . ' ' . Configure::read('Settings.Site.name');
		$this->set('titleTag', $titleTag);
	}

/**
 * Admin restore
 *
 * @return void
 */
	public function admin_restore($id) {
	//
		if(!$id) {
			throw new NotFoundException(__('Post not found.'));
		}
	//
		$this->BlogPost->updateAll(
			array('BlogPost.deleted_date' => NULL, 'BlogPost.deleted' => 0),
			array('BlogPost.id' => $id)
		);
	//	
		$blogPost = $this->BlogPost->findById($id);
	//
		$this->BlogPost->BlogPostComment->updateAll(
			array('BlogPostComment.deleted_date' => NULL, 'BlogPostComment.deleted' => 0),
			array('BlogPostComment.blog_post_id' => $id, 'BlogPostComment.deleted_date != ' => '2001-01-01 00:00:00')
		);
	//
		$this->Notify->success('The Post and associated Post Comment(s) restored successfully.');
	//
		$this->redirect(array('action' => 'admin_index'));

	}

/**
 * Admin restore
 *
 * @return void
 */
	public function admin_restore_without($id) {
	//
		if(!$id) {
			throw new NotFoundException(__('Post not found.'));
		}
	//
		$this->BlogPost->updateAll(
			array('BlogPost.deleted_date' => NULL, 'BlogPost.deleted' => 0),
			array('BlogPost.id' => $id)
		);
	//
		$this->Notify->success('The Post restored successfully.');
	//
		$this->redirect(array('action' => 'admin_index'));

	}

/**
 * Admin edit
 *
 * @return void
 */
	public function admin_edit($id = null) {
		
		$this->BlogPost->id = $id;

		$blogCategories = $this->BlogPost->BlogCategory->generateTreeList();

		$blogTags = $this->BlogPost->BlogTag->find('list');

		$blogPostComments = $this->BlogPost->BlogPostComment->find('all', array('conditions' => array('BlogPostComment.blog_post_id' => $id)));

		$this->set(compact('blogPostComments', 'blogCategories', 'blogTags'));

		if (!empty($this->request->data)) {
			
			if (isset($this->{$this->modelClass}->validateAdmin)) {   
				$this->{$this->modelClass}->setValidation('admin');
			}
			if(empty($this->request->data['PublishingInformation']['start'])) {
				$this->request->data['PublishingInformation']['start'] =  date('Y-m-d H:i:s');
			}

			if ($this->{$this->modelClass}->saveAll($this->request->data, array('deep' => true))) {
				$this->Notify->handleSuccessfulSave();
			} else {
				$this->Notify->handleFailedSave();
			}
		}
		if ($id && empty($this->request->data)) {
			$this->request->data = $this->{$this->modelClass}->find('edit', array(
				'conditions' => array($this->modelClass . '.id' => $id)
			));
		}

	}
/**
 * Admin index
 *
 * @return void
 */
	public function admin_index() {
		$blogPosts = $this->BlogPost->findforAdminIndex();
		$sort = substr(Configure::read('Settings.Blog.post_order'), 0, 13) == 'BlogPost.rank';
		$this->set(compact('blogPosts', 'sort'));
	}

/**
 * Admin view
 *
 * @return void
 */
	public function admin_view($id) {
		$this->BlogPost->id = $id;
		$this->BlogPost->contain(array('BlogCategory', 'BlogTag', 'User'));
		$this->set('BlogPost', $this->BlogPost->read());
		$blogPostComments = $this->BlogPost->BlogPostComment->findForPost($id);
		$this->set(compact('blogPostComments'));
	}
/**
 * Index
 *
 * @return void
 */
	public function index() {
		//handle RSS feeds
		if ($this->RequestHandler->isRss() ) {
			
			$category = (isset($this->params->named['category'])) ? " -- " . $this->params->named['category'] : "";
			
			//set headers
			$this->set('pluginSlug', Configure::read('Plugins.Blog.slug'));
			$this->set('channelData', array(
				'title' => Configure::read('Settings.Site.name') . $category,
				'link' => Router::url('/', true),

				'description' => __(Configure::read('Settings.Site.common_head_title')),
				'language' => 'en-us',
			));
			
			$this->set('posts', $this->BlogPost->findForRss($this->params->named['category']));
			
		}
		
		//post ordering conditions
		//default to publishing date with stickied posts first
		if (Configure::read('Settings.Blog.post_order')) { 
			$explode = explode(' ', Configure::read('Settings.Blog.post_order'));
			$order = array(
				'BlogPost.sticky'	=> 'desc'
				, $explode[0] => $explode[1]
				,
			);
		} else {
			$order = array(
				'BlogPost.sticky'	=> 'desc'
				, 'PublishingInformation.start'	=> 'desc'
				,
			);
		}

		$options = array(
			'limit' => 25,
			'order' => $order,
			'contain' => array('User', 'BlogCategory', 'Image', 'BlogTag'),
			'conditions' => array()
		);
		
		//start constructing the blog title
		$title = Configure::read('Plugins.Blog.alias');
		
		//add in the Posts text or its alias such as News or Articles if set
		if (Configure::read('Plugins.Blog.posts_alias')){
			$title .= " " . Configure::read('Plugins.Blog.posts_alias');
		} else {
			$title .= ' Posts';
		}
		
		//override the title with the page title if there is one
		if (!empty($this->viewVars['_page']['Page']['title'])) {
			$title = $this->viewVars['_page']['Page']['title'];
		}
		
		//add in conditions for a specific category
		if (!empty($this->params->named['category'])){
			$joins = array();
			if (isset($options['joins'])){
				$joins = $options['joins'];
			}
			$categoryOptions = $this->_category();
			$options = Hash::merge($options, $categoryOptions);
			
			//merge in the joins properly
			$options['joins'] = am($joins, $categoryOptions['joins']);
			
			$categorizedText = "Categorized Under %BLOG_CATEGORY%";
			
			if (Configure::read('Settings.Blog.category_header_template')){
				$categorizedText = Configure::read('Settings.Blog.category_header_template');
			}
			
			$title .= Configure::read('Settings.Site.title_separator') . ' ' . str_replace("%BLOG_CATEGORY%", $this->category['BlogCategory']['name'], $categorizedText);
		}
		
		//add in conditions for tagged posts
		if (!empty($this->params->named['tag'])) {
			$joins = array();
			if (isset($options['joins'])){
				$joins = $options['joins'];
			}
			
			$tagOptions = $this->_tag();
			
			$options = Hash::merge($options, $tagOptions);
			
			//merge in the joins properly
			$options['joins'] = am($joins, $tagOptions['joins']);

			$taggedText = "Tagged %BLOG_TAG%";
			
			if (Configure::read('Settings.Blog.tag_header_template')){
				$taggedText = Configure::read('Settings.Blog.tag_header_template');
			}
			
			$title .= Configure::read('Settings.Site.title_separator') . ' ' . str_replace("%BLOG_TAG%", $this->tag['BlogTag']['name'], $taggedText);
		}
		
		//add in conditions for archived posts
		if (!empty($this->params->named['year'])) {
			
			$publishedText = "Published %DATE_PUBLISHED%";
			$publishedFormat = "F Y";
			$publishedFormatNoMonth = "Y";
			
			if (Configure::read('Settings.Blog.date_published_header_template')){
				$publishedText = Configure::read('Settings.Blog.date_published_header_template');
			}
			
			if (!empty($this->params->named['month'])){
				//make sure that the month is represented in two digits
				$month = $this->params->named['month'];
				if (strlen($month) < 2){
					$month =  '0' . $month ;
				}
				
				$options['conditions'][]["DATE_FORMAT(PublishingInformation.start, '%Y%m')"] = $this->params->named['year'] . $month;
				$title .= Configure::read('Settings.Site.title_separator') . ' ' . str_replace("%DATE_PUBLISHED%", date($publishedFormat, strtotime($this->params['year'] . '-' . $month . '-01')) , $publishedText);
			} else {
				$options['conditions'][]["DATE_FORMAT(PublishingInformation.start, '%Y')"] = $this->params->named['year'];
				$title .= Configure::read('Settings.Site.title_separator') . ' ' . str_replace("%DATE_PUBLISHED%", date($publishedFormatNoMonth, strtotime($this->params['year'] . '-01-01')) , $publishedText);
			}
		}
		//set the return limit as per the blog settings
		$options['limit'] = Configure::read('Settings.Blog.posts_per_page') ? Configure::read('Settings.Blog.posts_per_page') : 10;
		
		//set the page title with allowing the loaded Page to override it with its title
		$this->PageSettings->pageTitle($title);
		$this->set('pageHeading', $title);
		
		//exclude deleted and unpublished posts
		$options['published'] = true;
		$options['conditions']['BlogPost.deleted'] = false;
		
		//paginate the results
		$this->paginate = $options;
		$blogPosts = $this->paginate();
		
		$this->set('blogPosts', $blogPosts);
	}
	
/**
 * Finds and sets the selected category in the view. Returns the conditions
 * with a join on the blog posts -> categories table and the blog categories table
 * //where the lft value is between the selected category's lft and rght value.
 * Called from index() action for both HTML and RSS views
 *
 * @throws NotFoundException
 * @return array
 */
	protected function _category() {
		$category = $this->BlogPost->BlogCategory->find('first', array(
			'conditions' => array(
				'slug' => $this->params->named['category']
			)
		));
		if (!$category) {
			throw new NotFoundException(__('Invalid Blog Category'));
		}
		
		$this->category = $category;
		$this->set(compact('category'));
		
		$options['joins'] = array(
			array(
				'type' => 'INNER',
				'table' => 'blog_post_categories',
				'alias' => 'BlogPostCategories',
				'conditions' => array(
					'BlogPost.id = BlogPostCategories.blog_post_id'
					, 'BlogPostCategories.blog_category_id' => intval($category['BlogCategory']['id'])
				),
			)
		);

		return $options;
	}

/**
 * Finds and sets the selected tag in the view. Returns the conditions where
 * the blog_post_tag_id in the join model is the id of the selected tag.
 * Called from index() action for both HTML and RSS views
 *
 * @throws NotFoundException
 * @return array
 */
	protected function _tag() {
		$tag = $this->BlogPost->BlogTag->find('first',  array(
			'conditions' => array(
				'BlogTag.slug' => $this->params->named['tag']
			)
		));
		
		if (!$tag) {
			throw new NotFoundException(__('Invalid Blog Tag'));
		}
		
		$this->tag = $tag;
		$this->set(compact('tag'));
		
		$options['joins'] = array(
			array(
				'type' => 'INNER'
				, 'table' => 'blog_post_tags'
				, 'alias' => 'BlogPostTags'
				, 'conditions' => array(
					'BlogPost.id = BlogPostTags.blog_post_id'
					, 'BlogPostTags.blog_tag_id' => intval($tag['BlogTag']['id'])
				)
			)
		);

		return $options;
	}

/**
 * The public posts view with comments
 *
 * @throws NotFoundException
 * @return void
 */
	public function view() {
	
		$slug = $this->params['slug'];
		
		$this->BlogPost->contain(array('BlogCategory', 'BlogTag', 'Image'));
		
		$blogPost = $this->BlogPost->find('first', array(
			'conditions' => array(
				'PublishingInformation.published' => 1,
				'BlogPost.deleted' => false,
				'BlogPost.slug' => $slug
			)
		));
		
		$commentLimit = 10;
		
		if (Configure::read("Settings.Site.default_pagination_limit")){			
			$commentLimit = Configure::read("Settings.Site.default_pagination_limit");
		}
		if (Configure::read("Settings.Blog.comment_pagination_limit")) {
			$commentLimit = Configure::read("Settings.Blog.comment_pagination_limit");
		}
		
		$this->paginate = array(
			'BlogPostComment' => array( 'limit' => $commentLimit, 'order'=>'BlogPostComment.created desc', 'paramType'=>'querystring' )
		);
		
		$blogPostComments = Hash::extract( $this->paginate(
			$this->BlogPost->BlogPostComment
			, array(
				'BlogPostComment.blog_post_id' => $blogPost['BlogPost']['id']
				, 'BlogPostComment.approved'=>true
				, 'BlogPostComment.deleted'=>false
			)
		), '{n}.BlogPostComment');
		
		
		$blogPost['BlogPostComment'] = $blogPostComments;
		
		if (!$blogPost) {
			throw new NotFoundException(__('Post not found.'));
		}

		$this->_setPostHeroVars($blogPost);
		
		$commentModeration = Configure::read('Settings.Blog.comment_moderation');
		
		$this->set('blogPost', $blogPost);
	}
	
/**
 * Blog preview function allows logged-in administrators to view unpublished posts.
 **/
	
	public function preview() {
	
		$groups = Authsome::get('Group');
    
		if ($groups[0] == 'Super Administrator' || $groups[0] == 'Administrator' || in_array("blog:*:admin", Authsome::get("Permission")) || in_array("blog:blog_posts:preview", Authsome::get("Permission"))) {
      
      $id = $this->params['named']['id'];
      
      $this->BlogPost->contain(array('BlogCategory', 'BlogTag', 'BlogPostComment', 'Image'));
      
      $blogPost = $this->BlogPost->find('first', array(
        'conditions' => array(
          'BlogPost.deleted' => false,
          'BlogPost.id' => $id
        )
      ));

      if (!$blogPost) {
        throw new NotFoundException(__('Post not found.'));
      }
      
      $commentModeration = Configure::read('Settings.Blog.comment_moderation');
			
			$this->view = 'view';
			
      $this->set('blogPost', $blogPost);
			
			
			$this->_setPostHeroVars($blogPost, 'POST PREVIEW');
			
      $this->set('blogPostComments' , $this->BlogPost->BlogPostComment->find('all', array(
          'conditions' => array(
            'BlogPostComment.deleted'	=> false 
            , 'BlogPostComment.approved' => $commentModeration
          , 'BlogPostComment.blog_post_id' => $blogPost['BlogPost']['id']
        )
      )));
		} else {
			$this->Notify->error('You are not authorized to view this page.');
			$this->redirect('/login');
		}
		
	}
	
	public function admin_toggle_sticky($id, $sticky = 'on') {
		$this->BlogPost->id = $id;
		$this->BlogPost->saveField('sticky', $sticky == 'on' ? true : false );
		
		$blogPost = $this->BlogPost->find('first', array(
			'conditions' => array('BlogPost.id' => $id)
		));
		
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('blogPost'));
		$this->render('_sticky_ajax');
	}
}
