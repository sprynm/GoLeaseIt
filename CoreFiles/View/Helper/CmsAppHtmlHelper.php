<?php
App::uses('HtmlHelper', 'View/Helper');

/**
 * CMS core-level HTML helper
 *
 * Extends the Cake core HtmlHelper.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsAppHtmlHelper.html
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsAppHtmlHelper extends HtmlHelper {

/**
 * Helpers
 */
	public $helpers = array(
		'Navigation.Navigation'
	);

/**
 * Adds the Responsive helper if the plugin is installed.
 *
 * @see HtmlHelper::__construct
 */
	public function __construct(View $View, $settings = array()) {
		if (CmsPlugin::isInstalled('Responsive')) {
			$this->helpers[] = 'Responsive.Responsive';
		}
		
		//override the javascript element templates
		// type="text/javascript" is deprecated
		$this->_tags['javascriptblock'] = '<script%s>%s</script>';
		$this->_tags['javascriptstart'] = '<script>';
		$this->_tags['javascriptlink'] = '<script src="%s"%s></script>';

		return parent::__construct($View, $settings);
	}

/**
 * Takes an array, which could be flat or multidimensional, and converts it to an HTML list.
 * If $type is null then the list type(s) will be automatically set based on the type of array
 * keys - OL for numeric keys and UL for non-numeric keys.
 *
 * @param array $data
 * @param string $type The type of list, either "ul" or "ol", or null for auto detection
 * @return string The HTML
 */ 
	public function arrayToList($data, $type = null) {
		if (empty($data)) {
			return null;
		}

		$string = '';
		$count = 0;

		foreach ($data as $key => $val) {
			if ($count == 0) {
				if ($type) { // Set during the function call
					$string = '<' . $type . '>';
				} else if (is_numeric($key)) {
					$type = 'ol';
					$string = '<ol>';
				} else {
					$type = 'ul';
					$string = '<ul>';
				}
			}

			$string .= '<li>';

			if ($type == 'ul') {
				$string .= $key;
			}

			if (is_array($val)) {
				$string .= $this->arrayToList($val);
			} else {
				if ($type == 'ul') {
					$string .= ': ';
				}
				$string .= $val;
			}

			$string .= '</li>';
			$count++;
		}

		$string .= '</' . $type . '>';
		return $string;
	} 

/**
 * Inserts a <link rel="canonical" href="X" /> tag, stripping out (primarily) pagination parameters.
 *
 * @param string url - optional url to put in the canonical tag in place of the automatic one.
 * @return string
 */
	public function canonical($url = null) {
		if (!$url) {
			
			$unset = array('sort', 'direction', 'dir');
			$route = Router::parse($this->request->here);
	
	
			foreach ($unset as $val) {
				if (isset($route['named'][$val])) {
					unset($route['named'][$val]);
				}
			}
			//because blog routing is messed up at the source - sg 9 Nov 2015
			if($route['plugin'] == 'blog') {
				return $this->blogCanonical($route);
			}
			
			foreach ($route['named'] as $key => $val) {
				$route[$key] = $val;
			}
			foreach ($route['pass'] as $key => $val) {
				$route[$key] = $val;
			}
			unset($route['named']);
			unset($route['pass']);

			$url = Router::url($route, true);
		}

		
		return $this->tag(
			'link',
			null,
			array(
				'rel' => 'canonical',
				'href' => $url
			)
		);
	}
	
	public function blogCanonical($route) {
		//
 		$url = array(
			'plugin' => 'blog'
		);
		
		
		if($route['action'] == 'archive'){
			$url['action'] = 'archive';
			$url['controller'] = null;
			if(isset($route['year'])) {
				$url[] = $route['year'];
			}
			if(isset($route['month'])) {
				$url[] = $route['month'];
			}
		}
		
		if(isset($route['slug'])){
			$url['controller'] = 'blog_posts';
			$url['action'] = 'view';
			$url['slug'] = $route['slug'];
		}
		
		if(isset($route['category'])){
			$url['controller'] = 'categories';
			$url['action'] = $route['category'];
		}
		
		if(isset($route['tag'])){
			$url['controller'] = 'tags';
			$url['action'] = $route['tag'];
		}		
		
		$url = Router::url(null, true); // Router::url($url, true)
		
		return $this->tag(
			'link',
			null,
			array(
				'rel' => 'canonical',
				'href' => $url
			)
		);
	}

/**
 * Image function - overrides HtmlHelper::image() in order to add responsive image capabilities.
 *
 * The responsive data attributes are only added if (1) the Responsive plugin is installed, (2) responsive images
 * are enabled (admin setting), and (3) responsive is not disabled for this particular image by passing
 * a responsive => false options key.
 *
 * @link http://wiki.radarhill.net/index.php?title=Pyramid:Responsive_Plugin#Responsive_Images
 * @see HtmlHelper::image
 */
	public function image($path, $options = array()) {
		if (!CmsPlugin::isInstalled('Responsive') || !Configure::read('Settings.Responsive.enable_responsive_images')) {
			return parent::image($path, $options);
		}

		if (!strstr($path, MEDIA_URL)) {
			return parent::image($path, $options);
		}
		
		if (!isset($options['responsive'])) {
			return parent::image($path, $options);			
		}

		if (isset($options['responsive']) && $options['responsive'] === false) {
			return parent::image($path, $options);			
		}

		$watermark = false;
		if(isset($options['watermark']) && $options['watermark'] == true) {
			$watermark = true;
		}
		
		$breakpoints = $this->Responsive->imageBreakpoints($path, $watermark);

		// The 'src' key of $breakpoints represents the smallest version and should replace
		// $path here.
		if (isset($breakpoints['src']) && $breakpoints['src'] != null) {
			$path = $breakpoints['src'];
			unset($breakpoints['src']);
		}

		if (!empty($breakpoints)) {
			$options = array_merge($options, $breakpoints);
		}
		
		return parent::image($path, $options);
	}

/**
 * Overrides HtmlHelper::link() to add the option to mark a link as 'current' if it's the current URL.
 *
 * Pass a 'current' => true option in the $options array to give the link a class of 'current' if it
 * links to the current URL.
 *
 * @see HtmlHelper::link
 */
	public function link($title, $url = null, $options = array(), $confirmMessage = false) {
	//
		if (isset($options['markCurrent']) && $options['markCurrent'] == true)
		{
		//
			unset($options['markCurrent']);
		//
			if ($this->Navigation->isCurrentPage(Router::url($url)))
			{
			//
				if (isset($options['class']))
				{
				//
					$options['class'] .= ' current';
				} else
				{
				//
					$options['class'] = 'current';
				}
			}
		}
	//
		if (isset($options['target']) && $options['target'] == '_blank')
		{
		//
			$options['rel']	= 'noopener';
		}

		return parent::link($title, $url, $options, $confirmMessage);
	}

/**
 * Calls TextHelper::tokenize (which is actually String::tokenize) and then trims off the left and right parentheses
 * from each tokenized item.
 *
 * @see String::tokenize
 * @return string
 */
	public function tokenize($string) {
		$split = String::tokenize($string);
		foreach ($split as $key => $val) {
      //BN 20170828: only strip enclosing braces if both are present at the beginning and end of the string
      if (substr($val,0,1) == '(' && substr($val, -1) == ')') {
        $split[$key] = substr($val, 1, -1);
      }
		}
		return $split;
	}
		
/**
 * Constructs a link element to the fonts.google.com repository for the listed fonts
 *
 * @param array $fonts The array of fonts to be fetched
 * @return string Html link element
 */
		
	public function googleFonts($fonts) {
		$url = null;
		
		foreach ( $fonts as $key => $font ) {
			$variants = '';
			
			if (is_numeric($key)) {
				//linear array
				$fontName = $font;
			} else {
				//array with font variants
				$fontName = $key;
				$variants = ':'.join($font, ',');
			}
			
			$fontName = urlencode($fontName);
			
			if (empty($url)){
				//first font in the list so start the link
				$url = 'https://fonts.googleapis.com/css?family=';
			} else {
				// otherwise put pipe character "|" converted to a URL entity between fonts
				$url .= '%7c';
			}
			
			$url .= $fontName . $variants;
		}
		
		echo '<link rel="stylesheet" href="'.$url.'">';
	}
	
	
/** 
 * Overload the css method to allow for the 'once' parameter
 * 'once' defaults to true
 */
	
	public function css($path, $rel = null, $options = array()) {
		$options += array('once' => true);
		
		if (!isset($this->_includedCss)){
			$this->_includedCss = array();
		}
		
		foreach ((array)$path as $index => $file) {
			if ($options['once'] && isset($this->_includedCss[$file])) {
				$this->_includedCss[$file] = true;
				if (!is_array($path)){
					return null;
				}
				unset($path[$index]);
			}
		}
		unset($options['once']);
		
		if (is_array($path) && empty($path)){
			return null;
		}
		
		return parent::css($path, $rel, $options);
	}
	
	public function afterLayout($viewFile = '', $content = "") {
		if ($this->adminCheck()) {
			return $content;
		}
		
		//list of short codes to replace with their respectively named blocks
		$blocks = array('script', 'css');
		
		foreach ($blocks as $blockName) {
			//grab the block
			$blockContent = $this->_View->fetch($blockName);
			
			$shortCode = '{{block type="' . $blockName . '"}}';
			$shortCodePos = strrpos($content, $shortCode);
			
			//if the short code was found then replace the code with the block
			if ($shortCodePos !== false) {
				$content = substr_replace($content, $blockContent, $shortCodePos, strlen($shortCode));
			}
		}
		
		$Page = ClassRegistry::init('Pages.Page');
		//convert /page/{n} links to their actual page slug links
		$content = preg_replace_callback("/\<a [^\>]*href=\"\/page\/([\d]+)\"[^\>]*\>/", function ( $matches ) use ($Page) {
			$url = Router::url($Page->link($matches[1]));
			return str_replace("href=\"/page/".$matches[1]."\"", "href=\"" . $url . "\"", $matches[0]);
		}, $content );
		
		return $content;
	}
	
	
/**
 * Returns true if the request is an admin request
 *
 * @return boolean
 */
	public function adminCheck() {
		return (isset($this->request->prefix) && $this->request->prefix == 'admin');
	}
}