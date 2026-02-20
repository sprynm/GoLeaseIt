<?php
/**
 * CmsCsvHelper class
 *
 * Aids in CSV output.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsCsvHelper.html
 * @package		 Cms.View.Helper  
 * @since		 Pyramid CMS v 1.0
 */
class CmsCsvHelper extends AppHelper {

/**
 * Settings array
 */
	public $settings = array();

/**
 * Temporary buffer
 */
	protected $_buffer = null;

/**
 * Default values for settings
 */
	protected $_defaults = array(
		'delimiter' => ',',
		'enclosure' => '"',
		'filename' => 'export.csv'
	);

/**
 * Internally-used line
 */
	protected $_line = array();

/**
 * Constructor
 *
 * @see Helper::__construct
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = array_merge($this->_defaults, $settings);
		$this->clear();
	}

/**
 * Clears the helper and prepares for a new export.
 *
 * @return void
 */
	public function clear() {
		$this->_line = array();
		$this->_buffer = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
	}

/**
 * Adds a field to the current line
 *
 * @param mixed value
 * @return void
 */
	public function addField($value) {
		$this->_line[] = $value;
	}

/**
 * Ends a row
 *
 * @return void
 */
	public function endRow() {
		$this->addRow($this->_line);
		$this->_line = array();
	}

/**
 * Adds a row to the CSV
 *
 * @param array row
 * @return void
 */
	public function addRow($row) {
		fputcsv($this->_buffer, $row, $this->settings['delimiter'], $this->settings['enclosure']);
	}

/**
 * Renders the PHP headers
 *
 * @return void
 */
	public function renderHeaders() {
		header("Content-type:application/csv");
		header("Content-disposition:attachment;filename=" . $this->settings['filename']);
	}

/**
 * Sets the filename.
 *
 * @param string filename
 * @return void
 */
	public function setFilename($filename) {
		$this->settings['filename'] = $filename;
		if (strtolower(substr($this->settings['filename'], -4)) != '.csv') {
			$this->settings['filename'] .= '.csv';
		}
	}

/**
 * Renders the CSV
 *
 * @param boolean
 * @param encoding conversion - to
 * @param encoding conversion - from
 * @return string
 */
	public function render($outputHeaders = true, $toEncoding = null, $fromEncoding = "auto") {
		if ($outputHeaders) {
			if (is_string($outputHeaders)) {
				$this->setFilename($outputHeaders);
			}
			$this->renderHeaders();
		}
		
		rewind($this->_buffer);
		$output = stream_get_contents($this->_buffer);
		if ($toEncoding) {
			$output = mb_convert_encoding($output, $toEncoding, $fromEncoding);
		}
		
		return $this->output($output);
	}

}