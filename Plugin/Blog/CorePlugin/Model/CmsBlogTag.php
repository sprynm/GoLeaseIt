<?php
/**
 * CmsBlogTag class
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsBlogTag.html
 * @package		 Cms.Plugin.Blog.Model
 * @since		 Pyramid CMS v 1.0
 */
class CmsBlogTag extends BlogAppModel {

/**
 * Behaviours
 */
	public $actsAs = array(
		'Sluggable',
		'Versioning.SoftDelete'
	);

/**
 * Validators
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty'
		),
		'Copyable'
	);

/**
 * Has many
 */
	public $hasMany = array(
		'BlogPost' => array(
			'className' => 'Blog.BlogPost',
			'joinTable' => 'blog_post_tags',
			'foreignKey' => 'blog_tag_id',
			'associationForeignKey' => 'blog_post_id',
			'unique' => true
		)
	);

/**
 * Link Format
 */
	public $linkFormat = array(
		'link' => array(
			'plugin' => 'blog',
			'controller' => 'blog_posts',
			'action' => 'index',
			'tag' => '{alias}.slug'
		)
	);

/**
 * After find
 *
 * @param array
 * @param boolean
 * @return void
 */
	public function afterFind($results, $primary = false) {
		$slug = Configure::read('Plugins.Blog.slug');

		foreach ($results as $i => &$result) {
			if (!isset($result['slug']) && !isset($result['BlogTag']['slug'])) {
				continue;
			}

			$linkTemplate = $this->linkFormat['link'];
			$linkTemplate['admin'] = false;
			if (isset($result['BlogTag'])) {
				$linkTemplate['tag'] = $result['BlogTag']['slug'];
				$result['BlogTag']['url'] = Router::url($linkTemplate);
			} else {
				$linkTemplate['tag'] = $result['slug'];
				$result['url'] = Router::url($linkTemplate);
			}
		}

		return $results;
	}

/**
 * Returns an array of records for use in the sitemap plugin. Called by the sitemap
 * event listener in CmsBlogTagsEventListener.
 *
 * @return void
 */
	public function findForSitemap() {
		$slug = Configure::read('Plugins.Blog.slug');
		$blogTags = $this->find('all', array(
			'published' => true,
			'fields' => array('modified', 'id', 'slug', 'name'),
		));

		$a = array();

		foreach ($blogTags as $i => $blogTag) {
			if (isset($blogTags[$i]['BlogTag']['slug'])) {
				$linkTemplate = $this->linkFormat['link'];
				$linkTemplate['admin'] = false;
				$linkTemplate['tag'] = $blogTags[$i]['BlogTag']['slug'];
				$blogTags[$i]['BlogTag']['url'] = Router::url($linkTemplate, true);
				$a[] = $blogTags[$i];
			}
		}

		return $a;
	}

}
