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
	);
	
	// add data to be shown next time pre() is called
	public static function data($data = NULL, $label = NULL) {
	
		// $pre_data will be cleared after it is output
		self::$data[] = array('data' => $data, 'label' => $label);
	
	}
	
	// cute shortcut -- Pre::tty()
	public static function tty($data, $label = NULL) {
		return self::render($data, $label);
	}

	// shortcut Pre::_()
	public static function _($data, $label = NULL) {
		return self::render($data, $label);
	}

	// simple wrapper for var_dump that outputs within a styled <pre> tag and fixes some whitespace and formatting
	public static function render($data, $label = NULL) {
		
		// add supplied to bottom of list
		self::data($data, $label);
		
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
			
			// pull out data and label
			$label = $data['label'];
			$data = $data['data'];
			
			// no label, use object class
			// if (empty($label) AND gettype($data) == 'object')
			// $label = get_class($data);

			// capture var_dump
			ob_start();
			var_dump($data);
			$data = ob_get_clean();
			
			// add label
			if (! empty($label))
				$data = '<span style="color: #222; font-weight: bold; background-color: #eee; font-size: 11px; padding: 3px 5px;">'.$label."</span> $data";
			
			// compile list of class names by searching: object(PreObject)#4 (2) {
			preg_match_all('/object\(([A-Za-z0-9_]+)\)\#[0-9]+\ \([0-9]+\)\ {/', $data, $objects);
			
			// we have some objects w/ class names
			if (! empty($objects)) {
			
				// just get the list of object names, without duplicates
				$objects = array_unique($objects[1]);

				// fix all class names to look like this: stdClass#3 object(4) {
				$data = preg_replace('/object\(([A-Za-z0-9_]+)\)\#([0-9]+)\ \(([0-9]+)\)\ {/', '<span style="color: #222; font-weight: bold;">\\1</span><span style="color: #444;">#\\2</span> <span style="color: #777;">obj(\\3)</span> {', $data);

				// remove class name from private members
				foreach ($objects as $object)
					$data = str_replace(':"'.$object.'"', '', $data);
				
			}

			// consistent styling of =>'s
			$arrow = '<span style="font-weight: bold; color: #aaa;"> => </span>';
			
			// style special case  NULL
			$data = preg_replace('/=>\s*NULL/', '=> <span style="color: #777; font-weight: bold;">NULL</span>', $data);
			
			// array keys are bolder
			$data = preg_replace('/\[\"([A-Za-z0-9_\ \@+\-\(\):]+)\"(:?[a-z]*)\]\s*=>\s*/', '<span style="color: #444; font-weight: bold;">["\\1"]</span>\\2'.$arrow, $data);

			// numeric keys are bolder
			$data = preg_replace('/\[([0-9]+)\]\s*=>\s*/', '<span style="color: #444; font-weight: bold;">[\\1]</span>'.$arrow, $data);
			
			// style :private and :protected
			$data = preg_replace('/(:(private|protected))/', '<span style="color: #444; font-style: italic;">\\1</span>', $data);

			// de-emphasize string labels
			$data = preg_replace('/string\(([0-9]+)\)/', '<span style="color: #777;">str(\\1)</span>', $data);

			// de-emphasize int labels
			$data = preg_replace('/int\(([0-9-]+)\)/', '<span style="color: #777;">int(<span style="text-transform: uppercase; font-weight: normal; color: #222;">\\1</span>)</span>', $data);
			
			// de-emphasize float labels
			$data = preg_replace('/float\(([0-9\.-]+)\)/', '<span style="color: #777;">float(<span style="text-transform: uppercase; font-weight: normal; color: #222;">\\1</span>)</span>', $data);

			// de-emphasize bool label
			$data = preg_replace('/bool\(([A-Za-z]+)\)/', '<span style="color: #666;">bool(<span style="text-transform: uppercase; font-weight: normal; color: #222;">\\1</span>)</span>', $data);
			// de-emphasize array labels
			$data = preg_replace('/array\(([0-9]+)\)/', '<span style="color: #777;">arr(\\1)</span>', $data);

			// boost spacing
			$data = preg_replace_callback('/^(\s*)(\S.*)$/m', function($matches) {
				// number of spaces that var_dump gave it
				$indent = strlen($matches[1]);
				// each 2 spaces is one column
				$indent = ($indent > 0) ? str_repeat("    ", $indent/2) : "";
            	return $indent.$matches[2];
        	}, $data);
			
			// add some separators
			$pre .= "$data";
		
		}
		
		// close pre tag
		$pre .= "</pre>\n\n";
		
		// reset data
		self::$data = array();

		// return output
		return $pre;
		
	}
}
 