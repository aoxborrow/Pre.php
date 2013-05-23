<?php

// setup error reporting
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

// load Pre lib
require '../src/Paste/Pre.php';

// namespace shortcut
use Paste\Pre as Pre;

// testing Pre lib
echo Pre::render($_SERVER);

