<?php

// setup error reporting
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

// load Pre lib
require '../src/Paste/Pre.php';
use Paste\Pre;

// setup Pre function shortcut
if (! function_exists('Pre')) {
	function Pre($data, $label = NULL) {
		return Paste\Pre::render($data, $label);
	}
}

// test data
$array = array(
	'null' => NULL, 
	'boolean' => FALSE, 
	'integer' => 1981,
	'float' => 25.651,
	'string' => 'string cheese',
);

// test data 
class TestClass {
	public $null = NULL;
	public $boolean = FALSE;
	public $float= 67.993;
	public $integer = 1981;
	public $string = 'string cheese';
	protected $protected = 'protected';
	private $private = 'private';
	public static $static = 'static property'; // static methods are not shown
	public $assoc_array = array('key' => 'value', 'space key' => 'space value');
	public $indexed_array = array('one', 'two', 'three');
}

// test instance
$object = new TestClass;

// Example #1: using function shortcut (without a label)
echo Pre($object);

// Example #2: using queue and configuring dimensions
// configure height/width
Pre::$config['width'] = 600;
Pre::$config['height'] = 200;

// add to data queue with a label
Pre::add($array, 'My Debug Data');

// later in the script...

// add to data queue without a label and output
echo Pre::render($object);



