<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

if ( ! defined('PROJECT_BASE'))
{
	define('PROJECT_BASE',	realpath(dirname(__FILE__).'/../').'/');

	define('BASEPATH',		PROJECT_BASE.'system/');
	define('APPPATH',		PROJECT_BASE.'application/');
}
// define('EXT', '.php');

// @todo provide a way to set various config options



// set up a highly controlled CI environment
require_once './lib/common.php';
require_once './lib/ci_testcase.php';