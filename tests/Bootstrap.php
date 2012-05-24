<?php

// Errors on full!
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

$dir = realpath(dirname(__FILE__));

// Path constants
define('PROJECT_BASE',	realpath($dir.'/../').'/');
define('BASEPATH',		PROJECT_BASE.'system/');
define('APPPATH',		PROJECT_BASE.'application/');
define('VIEWPATH',		PROJECT_BASE.'');

// Get vfsStream either via PEAR or composer
if (file_exists('vfsStream/vfsStream.php'))
{
	require_once 'vfsStream/vfsStream.php';
}
else
{
	include_once PROJECT_BASE.'vendor/autoload.php';
	class_alias('org\bovigo\vfs\vfsStream', 'vfsStream');
	class_alias('org\bovigo\vfs\vfsStreamDirectory', 'vfsStreamDirectory');
	class_alias('org\bovigo\vfs\vfsStreamWrapper', 'vfsStreamWrapper');
}

// Prep our test environment
include_once $dir.'/mocks/core/common.php';
include_once $dir.'/mocks/autoloader.php';
spl_autoload_register('autoload');

unset($dir);