<?php

// Errors on full!
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

$dir = realpath(dirname(__FILE__));


// Path constants
define('PROJECT_BASE',	realpath($dir.'/../').'/');
define('BASEPATH',		PROJECT_BASE.'system/');
define('APPPATH',		PROJECT_BASE.'application/');


// Prep our test environment
require_once $dir.'/lib/common.php';
require_once $dir.'/lib/ci_testcase.php';

unset($dir);