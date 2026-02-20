<?php
App::uses('ThemeView', 'View');

/**
 * The default view class used by all CMS controllers. It inherits almost
 * everything from the core View class, but adds support for multiple plugin
 * paths for view files.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsView.html
 * @package      Cms.View
 * @since        Pyramid CMS v 1.0
 */
class CmsView extends View {

/**
 * Giving the view access to the controller object. To be used with care.
 *
 * @see View::__construct
 */
	public function __construct(Controller $controller = null) {
		$this->_Controller = $controller;
		return parent::__construct($controller);
	}

/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin Optional plugin name to scan for view files.
 * @param boolean $cached Set to true to force a refresh of view paths.
 * @return array paths
 */
	protected function _paths($plugin = null, $cached = true) {
		if ($plugin === null && $cached === true && !empty($this->_paths)) {
			return $this->_paths;
		}

		$paths = array();
		$viewPaths = App::path('View');
		$corePaths = array_merge(App::core('View'), App::core('Console/Templates/skel/View'));

		if (!empty($plugin)) {
			$count = count($viewPaths);
			for ($i = 0; $i < $count; $i++) {
				if (!in_array($viewPaths[$i], $corePaths)) {
					$paths[] = $viewPaths[$i] . 'Plugin' . DS . $plugin . DS;
				}
			}
			$paths = array_merge($paths, Cms::pluginPaths($plugin));
			$paths = array_merge(App::path('View', $plugin), $paths);
		}

		$paths = array_unique(array_merge($paths, $viewPaths));
		if (!empty($this->theme)) {
			$themePaths = array();
			foreach ($paths as $path) {
				if (strpos($path, DS . 'Plugin' . DS) === false) {
					if ($plugin) {
						$themePaths[] = $path . 'Themed' . DS . $this->theme . DS . 'Plugin' . DS . $plugin . DS;
					}
					$themePaths[] = $path . 'Themed' . DS . $this->theme . DS;
				}
			}
			$paths = array_merge($themePaths, $paths);
		}
		$paths = array_merge($paths, $corePaths);
		if ($plugin !== null) {
			return $paths;
		}

		return $this->_paths = $paths;
	}
	
	
/**
 * Renders a layout. Returns output from _render(). Returns false on error.
 * Several variables are created for use in layout.
 *
 * - `title_for_layout` - A backwards compatible place holder, you should set this value if you want more control.
 * - `content_for_layout` - contains rendered view file
 * - `scripts_for_layout` - Contains content added with addScript() as well as any content in
 *   the 'meta', 'css', and 'script' blocks. They are appended in that order.
 *
 * Deprecated features:
 *
 * - `$scripts_for_layout` is deprecated and will be removed in CakePHP 3.0.
 *   Use the block features instead. `meta`, `css` and `script` will be populated
 *   by the matching methods on HtmlHelper.
 * - `$title_for_layout` is deprecated and will be removed in CakePHP 3.0
 * - `$content_for_layout` is deprecated and will be removed in CakePHP 3.0.
 *   Use the `content` block instead.
 *
 * @param string $content Content to render in a view, wrapped by the surrounding layout.
 * @param string $layout Layout name
 * @return mixed Rendered output, or false on error
 * @throws CakeException if there is an error in the view.
 */
	public function renderLayout($content, $layout = null) {
		$layoutFileName = $this->_getLayoutFileName($layout);
		if (empty($layoutFileName)) {
			return $this->Blocks->get('content');
		}

		if (!$this->_helpersLoaded) {
			$this->loadHelpers();
		}
		if (empty($content)) {
			$content = $this->Blocks->get('content');
		}
		$this->getEventManager()->dispatch(new CakeEvent('View.beforeLayout', $this, array($layoutFileName)));

		$scripts = implode("\n\t", $this->_scripts);
		$scripts .= $this->Blocks->get('meta') . $this->Blocks->get('css') . $this->Blocks->get('script');

		$this->viewVars = array_merge($this->viewVars, array(
			'content_for_layout' => $content,
			'scripts_for_layout' => $scripts,
		));

		if (!isset($this->viewVars['title_for_layout'])) {
			$this->viewVars['title_for_layout'] = Inflector::humanize($this->viewPath);
		}

		$this->_currentType = self::TYPE_LAYOUT;
		
		$content =  $this->_render($layoutFileName);

		//adjust the afterLayout event to allow for content to be modified from within the listener
		$afterLayoutEvent = new CakeEvent('View.afterLayout', $this, array($layoutFileName, $content));
		$afterLayoutEvent->modParams = 1;
		$this->getEventManager()->dispatch( $afterLayoutEvent );
		if (isset($afterLayoutEvent->data[1])) {
			$content = $afterLayoutEvent->data[1];
		}
		
		$this->Blocks->set('content', $content);
		return $this->Blocks->get('content');
	}
	
}
