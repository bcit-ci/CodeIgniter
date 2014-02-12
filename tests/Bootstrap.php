<?php

// Errors on full!
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

$dir = realpath(dirname(__FILE__));

// Path constants
defined('PROJECT_BASE') OR define('PROJECT_BASE', realpath($dir.'/../').'/');
defined('SYSTEM_PATH') OR define('SYSTEM_PATH', PROJECT_BASE.'system/');

// Get vfsStream either via PEAR or composer
foreach (explode(PATH_SEPARATOR, get_include_path()) as $path)
{
	if (file_exists($path.DIRECTORY_SEPARATOR.'vfsStream/vfsStream.php'))
	{
		require_once 'vfsStream/vfsStream.php';
		break;
	}
}

if ( ! class_exists('vfsStream') && file_exists(PROJECT_BASE.'vendor/autoload.php'))
{
	include_once PROJECT_BASE.'vendor/autoload.php';
	class_alias('org\bovigo\vfs\vfsStream', 'vfsStream');
	class_alias('org\bovigo\vfs\vfsStreamDirectory', 'vfsStreamDirectory');
	class_alias('org\bovigo\vfs\vfsStreamWrapper', 'vfsStreamWrapper');
}

// Define CI path constants to VFS (filesystem setup in CI_TestCase::setUp)
defined('BASEPATH') OR define('BASEPATH', vfsStream::url('system/'));
defined('APPPATH') OR define('APPPATH', vfsStream::url('application/'));
defined('VIEWPATH') OR define('VIEWPATH', APPPATH.'views/');
defined('ENVIRONMENT') OR define('ENVIRONMENT', 'development');

// Set localhost "remote" IP
isset($_SERVER['REMOTE_ADDR']) OR $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Prep our test environment
include_once $dir.'/mocks/core/common.php';
include_once SYSTEM_PATH.'core/Common.php';


if (extension_loaded('mbstring'))
{
	defined('MB_ENABLED') OR define('MB_ENABLED', TRUE);
	mb_internal_encoding('UTF-8');
	mb_substitute_character('none');
}
else
{
	defined('MB_ENABLED') OR define('MB_ENABLED', FALSE);
}

if (extension_loaded('iconv'))
{
	defined('ICONV_ENABLED') OR define('ICONV_ENABLED', TRUE);
	iconv_set_encoding('internal_encoding', 'UTF-8');
}
else
{
	defined('ICONV_ENABLED') OR define('ICONV_ENABLED', FALSE);
}

include_once $dir.'/mocks/autoloader.php';
spl_autoload_register('autoload');

unset($dir);