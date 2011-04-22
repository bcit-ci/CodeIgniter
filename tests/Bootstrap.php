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


// Omit files in the PEAR & PHP Paths from ending up in the coverage report
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(PEAR_INSTALL_DIR);	
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(PHP_LIBDIR);	
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(PROJECT_BASE.'tests');

// Omit Tests from the coverage reports.
// PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhiteList('../system/core');
PHP_CodeCoverage_Filter::getInstance()->addFileToBlackList('../system/core/CodeIgniter.php');

unset($dir);