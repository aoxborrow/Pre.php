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
		'string_counts' => TRUE,
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


	// pretty print JSON string
	// http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/
	public static function json($json) {
		
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = TRUE;

		for ($i = 0; $i <= $strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			// If this character is the end of an element,
			// output a new line and indent the next line.
			} else if (($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}
			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[')
					$pos ++;
				for ($j = 0; $j < $pos; $j++)
					$result .= $indentStr;
			}

			$prevChar = $char;
		}

		return $result;

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
			
			// store object class name
			// TODO: compile list of class names by searching: object(PreObject)#4 (2) {
			// TODO: fix all class names on list
			// TODO: like this: stdClass #3 object(4) {
			$class = (gettype($data) == 'object') ? get_class($data) : FALSE;

			// capture var_dump
			ob_start();
			var_dump($data);
			$data = ob_get_clean();
			
			if (! empty($label))
				$data = '<span style="color: #222; font-weight: bold; background-color: #eee; font-size: 11px; padding: 3px 5px;">'.$label."</span> $data";
			
			// :private messies up our regex
			$data = str_replace(':private]', ']<span style="color: #444; font-style: italic;">:private</span>', $data);

			// :protected messies up our regex
			$data = str_replace(':protected]', ']<span style="color: #444; font-style: italic;">:protected</span>', $data);
			
			// we have an object class -- fix formatting
			if ($class) {
				
				// remove it from private members
				$data = str_replace(':"'.$class.'"', '', $data);
				
				// fix spacing on first line
				$data = str_replace('object('.$class.')#', '<span style="color: #666; font-weight: bold;">(object) '.$class.'</span> #', $data);
			
			}

			// fix spacing
			$data = preg_replace('/=\>\s+/', ' => ', $data);

			// de-emphasize or hide string(0) labels
			if ($string_counts) {
				$data = preg_replace('/string\(([0-9]+)\)/', '<span style="color: #777; font-style: italic;">str(\\1)</span>', $data);
			} else {
				$data = preg_replace('/string\(([0-9]+)\) /', '', $data);
			}

			// de-emphasize NULL a little
			$data = preg_replace('/=\> NULL/', '<span style="color: #666; font-weight: bold;">=> NULL</span>', $data);

			// de-emphasize int
			$data = preg_replace('/int\(([0-9-]+)\)/', '<span style="color: #777; font-style: italic;">int(<b>\\1</b>)</span>', $data);

			// de-emphasize float
			$data = preg_replace('/float\(([0-9\.-]+)\)/', '<span style="color: #777; font-style: italic;">float(<b>\\1</b>)</span>', $data);

			// de-emphasize bool
			$data = preg_replace('/bool\(([A-Za-z]+)\)/', '<span style="color: #666; font-weight: bold; text-transform: uppercase;">\\1</span>', $data);

			// de-emphasize array
			$data = preg_replace('/array\(([0-9]+)\)/', '<span style="color: #777; font-style: italic;">array(\\1)</span>', $data);

			// array keys are bolder
			// TODO: match keys with => at end to avoid false positives
			// $data = preg_replace('/\[\"([A-Za-z0-9_ +\-\(\)\":]+)\"\]/', '<span style="color: #444; font-weight: bold;">["\\1"]</span>', $data);
			$data = preg_replace('/\[\"([A-Za-z0-9_\ \@+\-\(\)\":]+)\"\]/', '<span style="color: #444; font-weight: bold;">["\\1"]</span>', $data);

			// numeric keys are bolder
			// TODO: match keys with => at end to avoid false positives
			$data = preg_replace('/\[([0-9]+)\]/', '<span style="color: #444; font-weight: bold;">[\\1]</span>', $data);
		
			// use tabs
			// $data = str_replace('  ', "\t", $data);
			$data = str_replace('  ', "    ", $data);
			
			// bold the first line
			$data = preg_replace('/(.*)\n/', "\\1\n", $data, 1);
			
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
 