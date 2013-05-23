<?php 
/**
 * Pre - a handsome replacement for print_r & var_dump.
 *
 * @author     Aaron Oxborrow <aaron@pastelabs.com>
 * @link       http://github.com/paste/Pre
 * @copyright  (c) 2013 Aaron Oxborrow
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Paste;

class Pre {
	
	// add data to be shown next output
	public static $data = array();
	
	// default config for output
	public static $config = array(
		'width' => 'auto',
		'height' => 'auto',
		'hide_string_counts' => FALSE,
	);
	
	// add data to be shown next time pre() is called
	public static function data($data = NULL) {
	
		// $pre_data will be cleared after it is output
		self::$data[] = $data;
	
	}
	
	// simple wrapper for var_dump that outputs within a styled <pre> tag and fixes some whitespace and formatting
	public static function render($data) {
		
		// add supplied to bottom of list
		self::data($data);
		
		// extract config to this context for convenience
		extract(self::$config);
		
		// pre styling
		$style = 'font-family: Menlo, monospace; color: #000; font-size: 11px !important; line-height: 17px !important; text-align: left;';
		
		// you can specify dimensions for a scrollble div
		if ($height !== 'auto' OR $width !== 'auto') {

			// add "px" to height and width
			if (is_numeric($height) AND ! strstr($height, '%')) $height .= 'px';
			if (is_numeric($width) AND ! strstr($width, '%')) $width .= 'px';
			
			// all scrollables get a border
			$style .= " border: 1px solid #ddd; padding: 10px; overflow: scroll; height: $height; width: $width;";
			
		}

		// styled pre tag
		$pre = '<pre style="'.$style.'">';
		
		// iterate over data objects and var_dump 'em
		foreach (self::$data as $data) {

			// capture var_dump
			ob_start();
			var_dump($data);
			$data = ob_get_clean();

			// :private messus up our regex
			$data = str_replace(':private]', '] <span style="color: #777; font-style: italic;">(private)</span>', $data);

			// fix spacing
			$data = preg_replace('/=\>\s+/', ' => ', $data);

			// hide string(0) things a little
			if ($hide_string_counts) {
				$data = preg_replace('/string\(([0-9]+)\) /', '', $data);
			} else {
				$data = preg_replace('/string\(([0-9]+)\)/', '<span style="color: #777; font-style: italic;">str(\\1)</span>', $data);
			}

			// hide NULL a little
			$data = preg_replace('/=\> NULL/', '<span style="color: #666; font-weight: bold;">=> NULL</span>', $data);

			// same for int
			$data = preg_replace('/int\(([0-9-]+)\)/', '<span style="color: #777; font-style: italic;">int(\\1)</span>', $data);

			// same for float
			$data = preg_replace('/float\(([0-9\.-]+)\)/', '<span style="color: #777; font-style: italic;">float(\\1)</span>', $data);

			// same for bool
			$data = preg_replace('/bool\(([A-Za-z]+)\)/', '<span style="color: #666; font-weight: bold; text-transform: uppercase;">\\1</span>', $data);

			// same for array
			$data = preg_replace('/array\(([0-9]+)\)/', '<span style="color: #777; font-style: italic;">array(\\1)</span>', $data);

			// array keys are bolder
			$data = preg_replace('/\[\"([A-Za-z0-9_ +\-\(\)\":]+)\"\]/', '<span style="color: #444; font-weight: bold;">["\\1"]</span>', $data);

			// numeric keys are bolder
			$data = preg_replace('/\[([0-9]+)\]/', '<span style="color: #444; font-weight: bold;">[\\1]</span>', $data);
		
			// use tabs
			$data = str_replace('  ', "\t", $data);
		
			// bold the first line
			$data = preg_replace('/(.*)\n/', "<b>\\1</b>\n", $data, 1);
			
			// add some separators
			$pre .= "$data\n";
		
		}
		
		// close pre tag
		$pre .= "</pre>\n\n";
		
		// reset data
		self::$data = array();

		// return output
		return $pre;
		
	}

}
 