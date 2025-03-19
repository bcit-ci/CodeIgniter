<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch;

use LogicException;
use RuntimeException;
use PhpParser\ParserFactory;
use Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

class MonkeyPatchManager
{
	public static $debug = false;

	private static $php_parser = ParserFactory::PREFER_PHP5;

	private static $log_file;
	private static $load_patchers = false;
	private static $exit_exception_classname = 
		'Kenjis\MonkeyPatch\Exception\ExitException';
	/**
	 * @var array list of patcher classname
	 */
	private static $patcher_list = [
		'ExitPatcher',
		'FunctionPatcher',
		'MethodPatcher',
		'ConstantPatcher',
	];

	public static function log($message)
	{
		if (! self::$debug)
		{
			return;
		}

		$time = date('Y-m-d H:i:s');
		list($usec, $sec) = explode(' ', microtime());
		$usec = substr($usec, 1);
		$log = "[$time $usec] $message\n";
		file_put_contents(self::$log_file, $log, FILE_APPEND);
	}

	public static function setExitExceptionClassname($name)
	{
		self::$exit_exception_classname = $name;
	}

	public static function getExitExceptionClassname()
	{
		return self::$exit_exception_classname;
	}

	public static function getPhpParser()
	{
		return self::$php_parser;
	}

	protected static function setDebug(array $config)
	{
		if (isset($config['debug']))
		{
			self::$debug = $config['debug'];
		}
		if (self::$debug)
		{
			self::$log_file = __DIR__ . '/debug.log';
		}
	}

	protected static function setDir(array $config)
	{
		if (isset($config['root_dir']))
		{
			Cache::setProjectRootDir($config['root_dir']);
		}
		else
		{
			// APPPATH is constant in CodeIgniter
			Cache::setProjectRootDir(APPPATH . '../');
		}

		if (! isset($config['cache_dir']))
		{
			throw new LogicException('You have to set "cache_dir"');
		}
		self::setCacheDir($config['cache_dir']);
	}

	protected static function setPaths(array $config)
	{
		if (! isset($config['include_paths']))
		{
			throw new LogicException('You have to set "include_paths"');
		}
		self::setIncludePaths($config['include_paths']);

		if (isset($config['exclude_paths']))
		{
			self::setExcludePaths($config['exclude_paths']);
		}
	}

	public static function init(array $config)
	{
		self::setDebug($config);

		if (isset($config['php_parser']))
		{
			self::$php_parser = constant('PhpParser\ParserFactory::'.$config['php_parser']);
		}

		self::setDir($config);
		self::setPaths($config);

		Cache::createTmpListDir();

		if (isset($config['patcher_list']))
		{
			self::setPatcherList($config['patcher_list']);
		}
		self::checkPatcherListUpdate();
		self::checkPathsUpdate();

		self::loadPatchers();

		self::addTmpFunctionBlacklist();

		if (isset($config['functions_to_patch']))
		{
			FunctionPatcher::addWhitelists($config['functions_to_patch']);
		}
		self::checkFunctionWhitelistUpdate();
		FunctionPatcher::lockFunctionList();

		if (isset($config['exit_exception_classname']))
		{
			self::setExitExceptionClassname($config['exit_exception_classname']);
		}

		// Register include stream wrapper for monkey patching
		self::wrap();
	}

	protected static function checkPathsUpdate()
	{
		$cached = Cache::getTmpIncludePaths();
		$current = PathChecker::getIncludePaths();

		// Updated?
		if ($cached !== $current)
		{
			MonkeyPatchManager::log('clear_src_cache: from ' . __METHOD__);
			Cache::clearSrcCache();
			Cache::writeTmpIncludePaths($current);
		}

		$cached = Cache::getTmpExcludePaths();
		$current = PathChecker::getExcludePaths();

		// Updated?
		if ($cached !== $current)
		{
			MonkeyPatchManager::log('clear_src_cache: from ' . __METHOD__);
			Cache::clearSrcCache();
			Cache::writeTmpExcludePaths($current);
		}
	}

	protected static function checkPatcherListUpdate()
	{
		$cached = Cache::getTmpPatcherList();

		// Updated?
		if ($cached !== self::$patcher_list)
		{
			MonkeyPatchManager::log('clear_src_cache: from ' . __METHOD__);
			Cache::clearSrcCache();
			Cache::writeTmpPatcherList(self::$patcher_list);
		}
	}

	protected static function checkFunctionWhitelistUpdate()
	{
		$cached = Cache::getTmpFunctionWhitelist();
		$current = FunctionPatcher::getFunctionWhitelist();

		// Updated?
		if ($cached !== $current)
		{
			MonkeyPatchManager::log('clear_src_cache: from ' . __METHOD__);
			Cache::clearSrcCache();
			Cache::writeTmpFunctionWhitelist($current);
		}
	}

	protected static function addTmpFunctionBlacklist()
	{
		$list = file(Cache::getTmpFunctionBlacklistFile());
		foreach ($list as $function)
		{
			FunctionPatcher::addBlacklist(trim($function));
		}
	}

	public static function isEnabled($patcher)
	{
		return in_array($patcher, self::$patcher_list);
	}

	public static function setPatcherList(array $list)
	{
		if (self::$load_patchers)
		{
			throw new LogicException("Can't change patcher list after initialisation");
		}

		self::$patcher_list = $list;
	}

	public static function setCacheDir($dir)
	{
		Cache::setCacheDir($dir);
	}

	public static function setIncludePaths(array $dir_list)
	{
		PathChecker::setIncludePaths($dir_list);
	}

	public static function setExcludePaths(array $dir_list)
	{
		PathChecker::setExcludePaths($dir_list);
	}

	public static function wrap()
	{
		IncludeStream::wrap();
	}

	public static function unwrap()
	{
		IncludeStream::unwrap();
	}

	/**
	 * @param string $path original source file path
	 * @return resource
	 * @throws LogicException
	 */
	public static function patch($path)
	{
		if (! is_readable($path))
		{
			throw new LogicException("Can't read '$path'");
		}

		// Check cache file
		if ($cache_file = Cache::getValidSrcCachePath($path))
		{
			self::log('cache_hit: ' . $path);
			return fopen($cache_file, 'r');
		}

		self::log('cache_miss: ' . $path);
		$source = file_get_contents($path);

		list($new_source, $patched) = self::execPatchers($source);

		// Write to cache file
		self::log('write_cache: ' . $path);
		Cache::writeSrcCacheFile($path, $new_source);

		$resource = fopen('php://memory', 'rb+');
		fwrite($resource, $new_source);
		rewind($resource);
		return $resource;
	}

	protected static function loadPatchers()
	{
		if (self::$load_patchers)
		{
			return;
		}

		require __DIR__ . '/Patcher/AbstractPatcher.php';
		require __DIR__ . '/Patcher/Backtrace.php';

		foreach (self::$patcher_list as $classname)
		{
			require __DIR__ . '/Patcher/' . $classname . '.php';
		}

		self::$load_patchers = true;
	}

	protected static function execPatchers($source)
	{
		$patched = false;
		foreach (self::$patcher_list as $classname)
		{
			$classname = 'Kenjis\MonkeyPatch\Patcher\\' . $classname;
			$patcher = new $classname;
			list($source, $patched_this) = $patcher->patch($source);
			$patched = $patched || $patched_this;
		}

		return [
			$source,
			$patched,
		];
	}
}
