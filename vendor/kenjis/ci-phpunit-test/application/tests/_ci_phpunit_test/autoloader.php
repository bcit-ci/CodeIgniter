<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

// Autoloader for ci-phpunit-test
require __DIR__ . '/CIPHPUnitTestAutoloader.php';
require __DIR__ . '/CIPHPUnitTestFileCache.php';
$cache = new CIPHPUnitTestFileCache(
	__DIR__ . '/tmp/cache/autoload.php'
);
$autoload_dirs = CIPHPUnitTest::getAutoloadDirs();
$autoloader = new CIPHPUnitTestAutoloader($cache, $autoload_dirs);
spl_autoload_register([$autoloader, 'load']);

// Register CodeIgniter's tests/mocks/autoloader.php
define('SYSTEM_PATH', BASEPATH);
require APPPATH .'tests/mocks/autoloader.php';
spl_autoload_register('autoload');
