<?php

// setup error reporting
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

// load Pre lib
require 'src/Paste/Pre.php';
use Paste\Pre;

// setup Pre function shortcut
if (! function_exists('Pre')) {
	function Pre($data, $label = NULL) {
		return Paste\Pre::render($data, $label);
	}
}

// Test Class for demonstration
class TestClass {
	public $null = NULL;
	public $boolean = FALSE;
	public $float= 67.993;
	public $integer = 1981;
	public $string = 'public';
	protected $protected = 'protected';
	private $private = 'private';
	public static $static = 'static property'; // static methods are not shown
	public $assoc_array = array('key' => 'value', 'space key' => 'space value');
	public $indexed_array = array(1 => 'value #1', 2 => 'value #2', 3 => 'value #3');
}

// let's see it
$test = new TestClass;

// using function shorcut (no label)
echo Pre($test);

// or add to data queue with a label
Pre::add(array(NULL, FALSE, TRUE), 'My Array');
echo Pre::render();

// VS print_r && var_dump
echo '<br><pre>';
echo print_r($test, TRUE);
echo "\n\n";
var_dump($test);
echo '</pre>';


