<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

// Autoloader for PHP-Parser
// Don't use `require`, because we must require it in CIPHPUnitTest::init()
// for providing autoloading when we don't use Monkey Patching
require_once __DIR__ . '/third_party/PHP-Parser/lib/bootstrap.php';

require __DIR__ . '/IncludeStream.php';
require __DIR__ . '/PathChecker.php';
require __DIR__ . '/MonkeyPatchManager.php';
require __DIR__ . '/MonkeyPatch.php';
require __DIR__ . '/Cache.php';
require __DIR__ . '/InvocationVerifier.php';

require __DIR__ . '/functions/exit__.php';

const __GO_TO_ORIG__ = '__GO_TO_ORIG__';

class_alias('Kenjis\MonkeyPatch\MonkeyPatchManager', 'MonkeyPatchManager');

// And you have to configure for your application
//MonkeyPatchManager::init([
//	// PHP Parser: PREFER_PHP7, PREFER_PHP5, ONLY_PHP7, ONLY_PHP5
//	'php_parser' => 'PREFER_PHP5',
//	// Project root directory
//	'root_dir' => APPPATH . '../',
//	// Cache directory
//	'cache_dir' => APPPATH . 'tests/_ci_phpunit_test/tmp/cache',
//	// Directories to patch on source files
//	'include_paths' => [
//		APPPATH,
//		BASEPATH,
//	],
//	// Excluding directories to patch
//	'exclude_paths' => [
//		APPPATH . 'tests/',
//	],
//	// All patchers you use
//	'patcher_list' => [
//		'ExitPatcher',
//		'FunctionPatcher',
//		'MethodPatcher',
//		'ConstantPatcher',
//	],
//	// Additional functions to patch
//	'functions_to_patch' => [
//		//'random_string',
//	],
//]);
