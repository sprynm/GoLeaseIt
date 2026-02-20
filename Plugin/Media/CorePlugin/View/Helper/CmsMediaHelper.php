<?php
/**
 * Media Helper File
 *
 * Copyright (c) 2007-2011 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.3
 *
 * @package	   media
 * @subpackage media.views.helpers
 * @copyright  2007-2011 David Persson <davidpersson@gmx.de>
 * @license	   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	   http://github.com/davidpersson/media
 */
require_once 'Mime/Type.php';

/**
 * Media Helper Class
 *
 * To load the helper just include it in the helpers property
 * of a controller:
 * {{{
 *	   var $helpers = array('Form', 'Html', 'Media.Media');
 * }}}
 *
 * If needed you can also pass additional path to URL mappings when
 * loading the helper:
 * {{{
 *	   var $helpers = array('Media.Media' => array(MEDIA_FOO => 'foo/'));
 * }}}
 *
 * Nearly all helper methods take so called partial paths. Partial paths are
 * dynamically expanded path fragments for let you specify paths to files in a
 * very short way.
 *
 * @see file()
 * @see __construct()
 * @link http://book.cakephp.org/view/99/Using-Helpers
 * @package	   media
 * @subpackage media.views.helpers
 */
class CmsMediaHelper extends AppHelper {

/**
 * Helpers
 */
	public $helpers = array('Html' => array('className' => 'appHtml'));

/**
 * Tags
 */
	public $tags = array(
		'audio'			 => '<audio%s>%s%s</audio>',
		'video'			 => '<video%s>%s%s</video>',
		'source'		 => '<source%s/>',
		'object'		 => '<object%s>%s%s</object>',
		'param'			 => '<param%s/>'
	);

/**
 * Directory paths mapped to URLs. Can be modified by passing custom paths as
 * settings to the constructor.
 */
	protected $_paths = array(
		MEDIA_STATIC => MEDIA_STATIC_URL,
		MEDIA_TRANSFER => MEDIA_TRANSFER_URL,
		MEDIA_FILTER => MEDIA_FILTER_URL
	);

/**
 * Constructor
 *
 * Merges user supplied map settings with default map
 *
 * @param array $settings An array of base directory paths mapped to URLs. Used for determining
 *						  the absolute path to a file in `file()` and for determining the URL
 *						  corresponding to an absolute path. Paths are expected to end with a
 *						  trailing slash.
 * @return void
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->_paths = array_merge($this->_paths, (array)$settings);
	}

/**
 * For websites with the transfer folder in APP instead of webroot, turns a file path
 * into a URL pointing to AttachmentsController::view().
 *
 * @param string $path Absolute or partial path to a file
 * @param boolean $full Forces the URL to be fully qualified
 * @param array $options: 'downloadname' => 'string'
 * @return string|void An URL to the file
 */
	public function transferUrl($path = null, $full = false, $options = array()) {
		if (!Cms::minVersion('1.0.3')) {
			return $this->url($path, $full);
		}

		$file = $this->file($path);
		if (!$file) {
			return null;
		}

		// Chop up the file into its path info and send it like that to the router.
		$file = str_replace(MEDIA_TRANSFER,  '', $file);
		$file = pathInfo($file);
		
		//check for null values, which can happen if the file is too large to upload correctly.
		if(!isset($file['dirname']) || !isset( $file['filename']) || !isset( $file['extension'])) {
			return null;
		}
		
		return Router::url(
			array('plugin' => 'media', 'controller' => 'attachments', 'action' => 'view', 'admin' => false, $file['dirname'], $file['filename'], $file['extension'], (isset($options['downloadname']) && !empty($options['downloadname']) ? $options['downloadname']: '')), 
			$full
		);
	}

/**
 * Turns a file path into an URL (without passing it through `Router::url()`)
 *
 * Reimplemented method from Helper
 *
 * @param string $path Absolute or partial path to a file
 * @param boolean $full Forces the URL to be fully qualified
 * @return string|void An URL to the file
 */
	public function url($path = null, $full = false) {
		if (!$path = $this->webroot($path)) {
			return null;
		}
		if ($full && strpos($path, '://') === false) {
			$path = FULL_BASE_URL . $path;
		}
		return $path;
	}

/**
 * Webroot
 *
 * Reimplemented method from Helper
 *
 * @param string $path Absolute or partial path to a file
 * @return string|void An URL to the file
 */
	public function webroot($path) {
		if (!$file = $this->file($path)) {
			return null;
		}

		foreach ($this->_paths as $directory => $url) {
			if (strpos($file, $directory) !== false) {
				if ($url === false) {
					return null;
				}
				$path = str_replace($directory, $url, $file);
				break;
			}
		}
		$path = str_replace('\\', '/', $path);

		if (strpos($path, '://') !== false) {
			return $path;
		}
		return $this->webroot . $path;
	}

/**
 * Generates HTML5 markup for one ore more media files
 *
 * Determines correct dimensions for all images automatically. Dimensions for all
 * other media should be passed explictly within the options array in order to prevent
 * the browser refloating the layout.
 *
 * @param string|array $paths Absolute or partial path to a file (or an array thereof)
 * @param array $options The following options control the output of this method:
 *						 - autoplay: Start playback automatically on page load, defaults to `false`.
 *						 - preload: Start buffering when page is loaded, defaults to `false`.
 *						 - controls: Show controls, defaults to `true`.
 *						 - loop: Loop playback, defaults to `false`.
 *						 - fallback: A string containing HTML to use when element is not supported.
 *						 - poster: The path to a placeholder image for a video.
 *						 - url: If given wraps the result with a link.
 *						 - full: Will generate absolute URLs when `true`, defaults to `false`.
 *
 *						 The following HTML attributes may also be passed:
 *						 - id
 *						 - class
 *						 - alt: This attribute is required for images.
 *						 - title *** REMOVED *** https://radarhill.lighthouseapp.com/projects/141554-pyramid-version-2/tickets/33-image-should-not-have-title-tags
 *						 - width, height: For images the method will try to automatically determine
 *										  the correct dimensions if no value is given for either
 *										  one of these.
 * @return string|void
 */
	public function embed($paths, $options = array()) {
		//
		$default = array(
			'autoplay' => false,
			'preload' => false,
			'controls' => true,
			'loop' => false,
			'fallback' => null,
			'poster' => null,
			'full' => false,
			'lazyload' => true
		);
		//
		$optionalAttributes = array(
			'alt' => null,
			'id' => null,
			//'title' => null,
			'class' => null,
			'width' => null,
			'height' => null,
			'itemprop' => null
		);
		//
		if (isset($options['url'])) {
			$link = $options['url'];
			unset($options['url']);

			return $this->Html->link($this->embed($paths, $options), $link, array(
				'escape' => false
			));
		}
		//
		$options	= array_merge($default, $options);
		//
		extract($options, EXTR_SKIP);
		//
		if (!$sources = $this->_sources((array)$paths, $full)) {
			//
			return;
		}
		//
		$attributes = array_intersect_key($options, $optionalAttributes);

		switch($sources[0]['name']) {
			case 'audio':
				$body = null;

				foreach ($sources as $source) {
					$body .= sprintf(
						$this->tags['source'],
						$this->_parseAttributes(array(
							'src' => $source['url'],
							'type' => $source['mimeType']
					)));
				}
				
				$attributes += compact('autoplay', 'controls', 'preload', 'loop');
				return sprintf(
					$this->tags['audio'],
					$this->_parseAttributes($attributes),
					$body,
					$fallback
				);
			case 'document':
				break;
			case 'image':
				//
				$attributes = $this->_addDimensions($sources[0]['file'], $attributes);
				//
				if (isset($attributes['class']) && !empty($attributes['class'])) {
					//
					$attributes['class'] = $attributes['class'];
				}
				//
				if (isset($options['loading']) && !empty($options['loading'])) {
					//
					$attributes['loading'] = $options['loading'];
				}
				//
				return $this->Html->image($sources[0]['url'], $attributes);
			case 'video':
				$body = null;

				foreach ($sources as $source) {
					$body .= sprintf(
						$this->tags['source'],
						$this->_parseAttributes(array(
							'src' => $source['url'],
							'type' => $source['mimeType']
					)));
				}
				if ($poster) {
					$attributes = $this->_addDimensions($this->file($poster), $attributes);
					$poster = $this->url($poster, $full);
				}

				$attributes += compact('autoplay', 'controls', 'preload', 'loop', 'poster');
				return sprintf(
					$this->tags['video'],
					$this->_parseAttributes($attributes),
					$body,
					$fallback
				);
			default:
				break;
		}
	}

/**
 * Generates markup for a single media file using the `object` tag similar to `embed()`.
 *
 * @param string|array $paths Absolute or partial path to a file. An array can be passed to be make
 *							  this method compatible with `embed()`, in which case just the first file
 *							  in that array is actually used.
 * @param array $options The following options control the output of this method. Please note that
 *						 support for these options differs from type to type.
 *						 - autoplay: Start playback automatically on page load, defaults to `false`.
 *						 - controls: Show controls, defaults to `true`.
 *						 - loop: Loop playback, defaults to `false`.
 *						 - fallback: A string containing HTML to use when element is not supported.
 *						 - url: If given wraps the result with a link.
 *						 - full: Will generate absolute URLs when `true`, defaults to `false`.
 *
 *						 The following HTML attributes may also be passed:
 *						 - id
 *						 - class
 *						 - alt
 *						 - title *** REMOVED *** https://radarhill.lighthouseapp.com/projects/141554-pyramid-version-2/tickets/33-image-should-not-have-title-tags
 *						 - width, height
 * @return string
 */
	public function embedAsObject($paths, $options = array()) {
		$default = array(
			'autoplay' => false,
			'controls' => true,
			'loop' => false,
			'fallback' => null,
			'full' => false
		);
		$optionalAttributes = array(
			'alt' => null,
			'id' => null,
			//'title' => null,
			'class' => null,
			'width' => null,
			'height' => null
		);

		if (isset($options['url'])) {
			$link = $options['url'];
			unset($options['url']);

			return $this->Html->link($this->embed($paths, $options), $link, array(
				'escape' => false
			));
		}
		$options = array_merge($default, $options);
		extract($options + $default);

		if (!$sources = $this->_sources((array)$paths, $full)) {
			return;
		}
		$attributes	 = array('type' => $sources[0]['mimeType'], 'data' => $sources[0]['url']);
		$attributes += array_intersect_key($options, $optionalAttributes);

		switch ($sources[0]['mimeType']) {
			/* Windows Media */
			case 'video/x-ms-wmv': /* official */
			case 'video/x-ms-asx':
			case 'video/x-msvideo':
				$attributes += array(
					'classid' => 'clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6'
				);
				$parameters = array(
					'src' => $url,
					'autostart' => $autoplay,
					'controller' => $controls,
					'pluginspage' => 'http://www.microsoft.com/Windows/MediaPlayer/'
				);
				break;
			/* RealVideo */
			case 'application/vnd.rn-realmedia':
			case 'video/vnd.rn-realvideo':
			case 'audio/vnd.rn-realaudio':
				$attributes += array(
					'classid' => 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA',
				);
				$parameters = array(
					'src' => $sources[0]['url'],
					'autostart' => $autoplay,
					'controls' => isset($controls) ? 'ControlPanel' : null,
					'console' => 'video' . uniqid(),
					'loop' => $loop,
					'nologo' => true,
					'nojava' => true,
					'center' => true,
					'pluginspage' => 'http://www.real.com/player/'
				);
				break;
			/* QuickTime */
			case 'video/quicktime':
				$attributes += array(
					'classid' => 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
					'codebase' => 'http://www.apple.com/qtactivex/qtplugin.cab'
				);
				$parameters = array(
					'src' => $sources[0]['url'],
					'autoplay' => $autoplay,
					'controller' => $controls,
					'showlogo' => false,
					'pluginspage' => 'http://www.apple.com/quicktime/download/'
				);
				break;
			/* Mpeg */
			case 'video/mpeg':
				$parameters = array(
					'src' => $sources[0]['url'],
					'autostart' => $autoplay,
				);
				break;
			/* Flashy Flash */
			case 'application/x-shockwave-flash':
				$attributes += array(
					'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
					'codebase' => 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab'
				);
				$parameters = array(
					'movie' => $sources[0]['url'],
					'wmode' => 'transparent',
					'FlashVars' => 'playerMode=embedded',
					'quality' => 'best',
					'scale' => 'noScale',
					'salign' => 'TL',
					'pluginspage' => 'http://www.adobe.com/go/getflashplayer'
				);
				break;
			case 'application/pdf':
				$parameters = array(
					'src' => $sources[0]['url'],
					'toolbar' => $controls,
					'scrollbar' => $controls,
					'navpanes' => $controls
				);
				break;
			case 'audio/x-wav':
			case 'audio/mpeg':
			case 'audio/ogg':
			case 'audio/x-midi':
				$parameters = array(
					'src' => $sources[0]['url'],
					'autoplay' => $autoplay
				);
				break;
			default:
				$parameters = array(
					'src' => $sources[0]['url']
				);
				break;
		}
		return sprintf(
			$this->tags['object'],
			$this->_parseAttributes($attributes),
			$this->_parseParameters($parameters),
			$fallback
		);
	}

/**
 * Get the name of a media for a path
 *
 * @param string $path Absolute or partial path to a file
 * @return string|void i.e. `image` or `video`
 */
	public function name($path) {
		if ($file = $this->file($path)) {
			return Mime_Type::guessName($file);
		}
	}

/**
 * Get MIME type for a path
 *
 * @param string $path Absolute or partial path to a file
 * @return string|void
 */
	public function mimeType($path) {
		if ($file = $this->file($path)) {
			return Mime_Type::guessType($file);
		}
	}

/**
 * Get size of file
 *
 * @param string $path Absolute or partial path to a file
 * @return integer|void
 */
	public function size($path) {
		if ($file = $this->file($path)) {
			return filesize($file);
		}
	}

/**
 * Resolves partial path to an absolute path by trying to find an existing file matching the
 * pattern `{<base path 1>, <base path 2>, [...]}/<provided partial path without ext>.*`.
 * The base paths are coming from the `_paths` property.
 *
 * Examples:
 * img/cern					>>> MEDIA_STATIC/img/cern.png
 * img/mit.jpg				>>> MEDIA_TRANSFER/img/mit.jpg
 * s/<...>/img/hbk.jpg		>>> MEDIA_FILTER/s/<...>/img/hbk.png
 *
 * @param string $path A relative or absolute path to a file.
 * @return string|boolean False on error or if path couldn't be resolved otherwise
 *						  an absolute path to the file.
 */
	public function file($path, $version = null) {
		$path = $this->_extractPath($path);
		if ($version) {
			$path = $version . DS . $path;
		}

		// Most recent paths are probably searched more often
		$bases = array_reverse(array_keys($this->_paths));

		if (Folder::isAbsolute($path)) {
			return file_exists($path) ? $path : null;
		}

		$extension = null;
		extract(pathinfo($path), EXTR_OVERWRITE);

		if (!isset($filename)) {
			$filename = substr($basename, 0, isset($extension) ? - (strlen($extension) + 1) : 0);
		}

		foreach ($bases as $base) {
			if (file_exists($base . $path)) {
				return $base . $path;
			}
			//
			$dirname	= isset($dirname) && strlen($dirname) > 0
					? $dirname . DS
					: '';
			//
			$files = glob($base . $dirname . $filename . '.*', GLOB_NOSORT | GLOB_NOESCAPE);

			if (count($files) > 1) {
				$message  = "MediaHelper::file - ";
				$message .= "A relative path (`{$path}`) was given which triggered search for ";
				$message .= "files with the same name but not the same extension.";
				$message .= "This resulted in multiple files being found. ";
				$message .= "However the first file being found has been picked.";
				trigger_error($message, E_USER_NOTICE);
			}
			if ($files) {
				return array_shift($files);
			}
		}
	}

/**
 * A convenience method to display a link to an image with a thumbnail. $version
 * is the vesrion of the image to which the thumbnail should link.
 *
 * @param	array	$item
 * @param	string	$version		OPTIONAL
 * @param	array	$imageOptions	OPTIONAL
 * @param	array	$linkOptions	OPTIONAL
 * @return	string
 */
	public function thumbnail($item, $version = 'large', $imageOptions = array(), $linkOptions = array()) {
		$thumbVersion = (isset($imageOptions['thumbVersion'])) ? $imageOptions['thumbVersion'] : 'thumb';
		$thumb = $this->image($item, $thumbVersion, $imageOptions);
		
		$large = $this->file($item, $version);
		if (!$thumb || !$large) {
			return null;
		}

		$defaults = array(
			'escape'	=> false
			, 'title'	=> $item['alternative']
			,
		);

		$linkOptions = array_merge($defaults, $linkOptions);
		$largeUrl = $this->url($large);
		$link = $this->Html->link($thumb, $largeUrl, $linkOptions);
		return $link;
	}

/**
 * A convenience method to display an image.
 *
 * @param	array	$item
 * @param	string	$version	OPTIONAL
 * @return	string
 */
	public function image($item, $version = null, $options = array()) {
		$file = $this->file($item, $version);

		if (!$file) {
			return null;
		}

		$defaults = array(
			'alt' => $item['alternative'],
			'restrict' => array('image')
		);
		$options = array_merge($defaults, $options);
		return $this->embed($file, $options);
	}

/**
 * A convenience method to display the first/main image of an image gallery. If an array
 * with only one image is passed (i.e. not a gallery) then that image is displayed.
 *
 * @param	array	$gallery
 * @param	string	$version	OPTIONAL
 * @param	array	$options	OPTIONAL
 * @return
 */
	public function mainImage($gallery, $version = 'thumb', $options = array()) {
		if (isset($gallery[0])) {
			$image = $gallery[0];
		} else {
			$image = $gallery;
		}
		if (!$image) {
			return null;
		}
		return $this->image($image, $version, $options);
	}

/**
 * Generates a link to a document.
 *
 * The $options array is employed in the Html->link helper.
 * http://book.cakephp.org/2.0/en/appendices/glossary.html#term-html-attributes
 *
 * The $item array is normally the $item['Image']. This can contain variables such as the link text.
 * Example $item['Image']['alternative'] would become the clickable text. Without a $item['Image']['alternative']
 * the $item['Image']['basename'] (also know as the file name) would become the clickable/textual link.
 *
 * @param	array	$item
 * @param	array	$options	OPTIONAL
 * @return	string
 */
	public function document($item, $options = array()) {
		//
		$file = $this->file($item);
		//
		if (!$file) {
			return null;
		}
		//
		$defaults = array(
			'target'		=> '_blank'
			, 'downloadname'	=> $item['basename']
			, 'link_text'		=> ''
			,
		);
		//
		$options = array_merge($defaults, $options);
		//
		if (strlen($options['link_text']) > 0) {
			//
			$title	= $options['link_text'];
		} elseif ($item['alternative']) {
			//
			$title	= $item['alternative'];
		} else {
			//
			$title	= $item['basename'];
		}
		//
		return $this->Html->link($title, $this->transferUrl($file, $full = false, $options), $options);
	}

/**
 * Generates a URL for an upload item. $version is only needed
 * for images.
 *
 * @param	array	$item
 * @param	string	$version	OPTIONAL
 * @return	string
 */
	public function path($item, $version = null) {
		$file = $this->file($item, $version);
		if (!$file) {
			return null;
		}
		return $this->url($file);
	}

/**
 * Gets the validation error map from CmsAttachment. First sets the validate array to ensure that 
 * the proper messages are grabbed.
 *
 * @param string $validateType Optional
 * @return array
 */
	public function validationErrors($validateType = null) {
		$Attachment = ClassRegistry::init('Media.Attachment');
		$validate = 'validate' . ucfirst($validateType);
		if (isset($Attachment->{$validate})) {
			$Attachment->setValidation($validateType);
			$Attachment->setValidationErrors();
		}
		return $Attachment->validationErrorCodes;
	}

/**
 * Takes an array of paths and generates and array of source items.
 *
 * @param array $paths An array of	relative or absolute paths to files.
 * @param boolean $full When `true` will generate absolute URLs.
 * @return array An array of sources each one with the keys `name`, `mimeType`, `url` and `file`.
 */
	protected function _sources($paths, $full = false) {
		$sources = array();

		foreach ($paths as $path) {
			if (!$url = $this->url($path, $full)) {
				return;
			}
			if (strpos('://', $path) !== false) {
				$file = parse_url($url, PHP_URL_PATH);
			} else {
				$file = $this->file($path);
			}
			$mimeType = Mime_Type::guessType($file);
			$name = Mime_Type::guessName($mimeType);

			$sources[] = compact('name', 'mimeType', 'url', 'file');
		}
		return $sources;
	}

/**
 * Adds dimensions to an attributes array if possible.
 *
 * @param string $file An absolute path to a file.
 * @param array $attributes
 * @return array The modified attributes array.
 */
	protected function _addDimensions($file, $attributes) {
		if (isset($attributes['width']) || isset($attributes['height'])) {
			return $attributes;
		}
		if (function_exists('getimagesize')) {
			list($attributes['width'], $attributes['height']) = getimagesize($file);
		}
		return $attributes;
	}

/** PUBLIC VERSION
 * Adds dimensions to an attributes array if possible.
 *
 * @param string $file An absolute path to a file.
 * @param array $attributes
 * @return array The modified attributes array.
 */
	public function addDimensions($file, $attributes) {
		//
		$this->_addDimensions($file, $attributes);
	}
	
/**
 * Resolves $path into a path strig readable by the file() function. If $path is a string it's just
 * returned as-is; otherwise, the necessary elements are extracted from the array.
 *
 * @parm mixed Either string or array
 * @return string
 */
	protected function _extractPath($path) {
		if (is_string($path)) {
			return $path;
		}
		
		$return = null;
		if (isset($path['dirname'])) {
			$return .= $path['dirname'];
		}
		
		if (isset($path['basename'])) {
			$return .= DS . $path['basename'];
		}

		return $return;
	}
/**
 * Generates attributes from options. Overwritten from Helper::_parseAttributes
 * to take new minimized HTML5 attributes used here into account.
 *
 * @param array $options
 * @return string
 */
	protected function _parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = null) {
		$attributes = array();
		$minimizedAttributes = array('autoplay', 'controls', 'autobuffer', 'loop');

		foreach ($options as $key => $value) {
			if (in_array($key, $minimizedAttributes)) {
				if ($value === 1 || $value === true || $value === 'true' || $value == $key) {
					$attributes[] = sprintf('%s="%s"', $key, $key);
					unset($options[$key]);
				}
			}
		}
		return parent::_parseAttributes($options) . ' ' . implode(' ', $attributes);
	}

/**
 * Generates `param` tags
 *
 * @param array $options
 * @return string
 */
	protected function _parseParameters($options) {
		$parameters = array();
		$options = Set::filter($options);

		foreach ($options as $key => $value) {
			if ($value === true) {
				$value = 'true';
			} elseif ($value === false) {
				$value = 'false';
			}
			$parameters[] = sprintf(
				$this->tags['param'],
				$this->_parseAttributes(array('name' => $key, 'value' => $value))
			);
		}
		return implode("\n", $parameters);
	}

/**
 * Generates `img` tag
 *
 * @param string $url
 * @param array $options
 * @return string
 */
	public function lazyload($url, $options = array()) {
	//
		if (!$url) {
			return null;
		}
	//
		if (is_array($url))
		{
		//
			$paths			= $this->_sources((array)$this->file($url, $options['version']))[0];
		//
			$options['alt']	= $url['alternative'];
		//
			$url			= $paths['url'];
		//
			$file			= $paths['file'];
		} else
		{
		//
			$file			= $url;
		}
	//
		$options = $this->_addDimensions($file, $options);
	//
		$defaults = array(
			'data-original' => $url
			, 'class'	=> 'lazyload'
			,
		);
	//
		$options = array_merge($defaults, $options);
	//
		unset($options['lazyload'], $options['version']);
	//
		$tags	= '';
	//
		foreach ($options AS $K => $V)
		{
		//
			$tags	.= $K . '="' . $V . '" ';
		}
	//
		$this->Html->script('Media.lazyload/jquery.lazyload.min', array('inline' => false, 'once' => true));
	//
		return '<img ' . trim($tags) . '>';
	}

/**
 * Wrapper for convenience of both $this->file and $this->url.
 *
 * @param array of image details
 * @param string version to use
 * @return string|void An URL to the file
 */
	public function fullFilePath($item, $version = 'thumb') {
	//
		if (!$item)
		{
		//
			return;
		}
	//
		return $this->url($this->file($item, $version), true);
	}

	
/**
 * Creates a picture element with the breakpoints for the specified versions
 * 
 * @param array $item Array that contains the Image model for the images
 * @param array $versions Array that contains the list of breakpoints by version key
 * @param array $options Optional values for alt, title, class, etc
 * @param bool $absolutePath Output the absolute path for file links
 * @return string A picture element
 */
	public function picture($image, $versions, $options = null, $absolutePath = false) {
		
		$defaults = array();
		if (!empty($image['alternative'])) {
			$defaults['alt'] = $image['alternative'];
		}
		
		$options = am($defaults, $options);
		
		$picture = "<picture>\n";
		$baseVersion = null;
		$lastVersion = null;
		
		foreach ($versions as $version => $breakpoint) {
			$file = $this->file(  $image, $version );
			$link = $this->url($file, $absolutePath);
			
			if (empty($file) || empty($link)) {
				//couldn't link to that version
				$picture .= "<!-- Failed to find a file for version '$version' -->\n";
			} else {
				if (empty($breakpoint)){
					$baseVersion = $link;
					$picture .= $this->Html->image($link, $options) . "\n";
				} else {
					$lastVersion = $link;
					$picture .= "<source srcset=\"$link\" media=\"$breakpoint\">\n";
				}
			}
		}
		
		if (empty($baseVersion) && !empty($lastVersion)){
			//picture element needs at least one img to work at all so give it the last version if there wasn't a default set
			$picture .= "<!-- no default set, using last found version -->\n";
			$picture .= $this->Html->image($lastVersion, $options) . "\n";
		}
		
		$picture .= "</picture>\n";
		return $picture;
	}

/**
 * Return a variety syntax for a document.
 * 
 * @param array $item Array that contains the document details
 * @param array $options Array - detailed below
 *
 * Defaults are:
 *  
 * Media::getDocument($item, array('www' => false, 'link' => false, 'downloadname' => $item['basename']);
 *
 * www => false (default) | true:
 *	false will return the path line only = /media/attachments/view/doc/showboats_international_big_fish/pdf/showboats_international_big_fish.pdf
 *	true will return a complete URL = http://www.domain.com/media/attachments/view/doc/showboats_international_big_fish/pdf/showboats_ international_big_fish.pdf
 * 
 * link => false (default) | true:
 *	false will return the path line only = /media/attachments/view/doc/showboats_international_big_fish/pdf/showboats_international_big_fish.pdf
 *	true will return an href with the full URL = <a href="/media/attachments/view/doc/showboats_international_big_fish/pdf/showboats_international_big_fish.pdf" target="_blank" downloadname="showboats_international_big_fish.pdf" rel="noopener">ShowBoats International - Big Fish</a>
 *
 * downloadname => $item['basename'] (default) | string
 * The string will dictate the name of the file should the site visitor decide to download the file.
 *	Array('downloadname' => 'Chuck Norris.pdf') will return /media/attachments/view/doc/showboats_international_big_fish/pdf/Chuck%20Norris.pdf
 *	Array('link' => true, 'downloadname' => 'Chuck Norris.pdf') will return <a href="/media/attachments/view/doc/showboats_international_big_fish/pdf/Chuck%20Norris.pdf" target="_blank" downloadname="Chuck Norris.pdf" rel="noopener">ShowBoats International - Big Fish</a>
 */

	public function getDocument($item, $options = array()) {
		//
		$return	= '';
		//
		if (!$item) {
			//
			return $return;
		}
		//
		$file	= $this->file($item);
		//
		if (!$file) {
			//
			return $file;
		}
		//
		$full	= isset($options['www']) ? $options['www']: false;
		//
		$link	= isset($options['link']) ? $options['link']: false;
		//
		unset($options['www'], $options['link']);
		//
		$defaults = array(
				'downloadname'	=> $item['basename']
				,
			);
		//
		$options = am($defaults, $options);
		//
		$return	= $link ? $this->document($item, $options): $this->transferUrl($file, $full, $options);
		//
		return $return;
	}

/**
 * Return a variety syntax for an image.
 * 
 * @param array $item Array that contains the document details
 * @param string which image version requested
 * @param array $options Array - detailed below
 *
 * version => large (default) | string (the name of an alternate version: thumb, medium, etc.)
 *	returns /media/filter/large/img/showboatsinternational.jpg
 *
 * www => false (default) | true:
 *	false will return the path line only = /media/filter/large/img/showboatsinternational.jpg
 *	true will return a complete URL = http://www.domain.com/media/filter/large/img/showboatsinternational.jpg
 */
	public function getImage($item, $version = null, $options = array()) {
		//
		$return	= '';
		//
		if (!$item) {
			//
			return $return;
		}
		// 
		$defaults = array(
				'version'	=> 'large'
				, 'www'		=> false
				,
			);
		// Attempting to be backwards compatible.
		if ($version) {
			if (is_array($version) && isset($version['version'])) {
				//
				$options['version']	= $version['version'];
			} elseif (is_string($version)) {
				//
				$options['version']	= $version;
			}
		}
		//
		$options = am($defaults, $options);
		//
		$file	= $this->file($item, $options['version']);
		//
		if (!$file) {
			//
			return $file;
		}
		//
		$full	= isset($options['www']) ? $options['www']: false;
		//
		unset($options['www']);
		//
		$return	= $full ? FULL_BASE_URL . $this->url($file) : $this->url($file);
		//
		return $return;
	}
	
}