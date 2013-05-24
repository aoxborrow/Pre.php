<?php

// setup error reporting
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

// load Pre lib
require '../src/Paste/Pre.php';

// namespace shortcut
use Paste\Pre as Pre;


// test class
class PreObject {
	
	public $public = 'public';
	protected $protected = 'protected';
	private $private = 'private';
	public static $static = 'static';
	
	public function __construct() {
		$this->runtime = 'runtime';
	}
	
	public function func() {
		return 'are we having func() yet?';
	}
}

// Pretty JSON
$json = file_get_contents('sample.json');
$pretty_json = Pre::json($json);
echo Pre::render($pretty_json, 'json:');

// PreObject
$po = new PreObject;
$po->member = new StdClass;
$po->member->property = 'property';
$po->member->submember = new PreObject;
echo Pre::render($po, 'PreObject Test:');

// StdClass
$sc = new StdClass;
$sc->property = 'property';
$sc->bool = TRUE;
$sc->float = 5.678;
$sc->int = 678;
$sc->null = NULL;
$sc->member = $po;
$sc->member->submember = new PreObject;
echo Pre::render($sc, 'StdClass Test:');

echo Pre::render($_SERVER, '_SERVER:');


