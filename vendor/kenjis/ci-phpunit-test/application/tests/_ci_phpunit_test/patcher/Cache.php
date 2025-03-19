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

use RuntimeException;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Cache
{
	private static $project_root;
	private static $cache_dir;
	private static $src_cache_dir;
	private static $tmp_function_blacklist_file;
	private static $tmp_function_whitelist_file;
	private static $tmp_patcher_list_file;
	private static $tmp_include_paths_file;
	private static $tmp_exclude_paths_file;

	public static function setProjectRootDir($dir)
	{
		self::$project_root = realpath($dir);
		if (self::$project_root === false)
		{
			throw new LogicException("No such directory: $dir");
		}
	}

	public static function setCacheDir($dir)
	{
		self::createDir($dir);
		self::$cache_dir = realpath($dir);
		
		if (self::$cache_dir === false)
		{
			throw new LogicException("No such directory: $dir");
		}
		
		self::$src_cache_dir = self::$cache_dir . '/src';
		self::$tmp_function_whitelist_file = 
			self::$cache_dir . '/conf/func_whiltelist.php';
		self::$tmp_function_blacklist_file = 
			self::$cache_dir . '/conf/func_blacklist.php';
		self::$tmp_patcher_list_file = 
			self::$cache_dir . '/conf/patcher_list.php';
		self::$tmp_include_paths_file = 
			self::$cache_dir . '/conf/include_paths.php';
		self::$tmp_exclude_paths_file = 
			self::$cache_dir . '/conf/exclude_paths.php';
	}

	public static function getCacheDir()
	{
		return self::$cache_dir;
	}

	public static function getSrcCacheFilePath($path)
	{
		$len = strlen(self::$project_root);
		$relative_path = substr($path, $len);

		if ($relative_path === false)
		{
			return false;
		}

		return self::$src_cache_dir . '/' . $relative_path;
	}

	protected static function createDir($dir)
	{
		if (! is_dir($dir))
		{
			if (! @mkdir($dir, 0777, true))
			{
				throw new RuntimeException('Failed to create folder: ' . $dir);
			}
		}
	}

	/**
	 * @param string $path original source file path
	 * @return string|false
	 */
	public static function getValidSrcCachePath($path)
	{
		$cache_file = self::getSrcCacheFilePath($path);

		if (
			is_readable($cache_file) && filemtime($cache_file) > filemtime($path)
		)
		{
			return $cache_file;
		}

		return false;
	}

	/**
	 * Write to src cache file
	 * 
	 * @param string $path   original source file path
	 * @param string $source source code
	 */
	public static function writeSrcCacheFile($path, $source)
	{
		$cache_file = self::getSrcCacheFilePath($path);
		if ($cache_file !== false)
		{
			self::writeCacheFile($cache_file, $source);
		}
	}

	/**
	 * Write to cache file
	 * 
	 * @param string $path   file path
	 * @param string $contents file contents
	 */
	public static function writeCacheFile($path, $contents)
	{
		$dir = dirname($path);
		self::createDir($dir);
		file_put_contents($path, $contents);
	}

	public static function getTmpFunctionBlacklistFile()
	{
		return self::$tmp_function_blacklist_file;
	}

	public static function createTmpListDir()
	{
		if (is_readable(self::$tmp_function_blacklist_file))
		{
			return;
		}

		$dir = dirname(self::$tmp_function_blacklist_file);
		self::createDir($dir);

		touch(self::$tmp_function_blacklist_file);
	}

	public static function appendTmpFunctionBlacklist($function)
	{
		file_put_contents(
			self::getTmpFunctionBlacklistFile(), $function . "\n", FILE_APPEND
		);
	}

	protected static function writeTmpConfFile($filename, array $list)
	{
		$contents = implode("\n", $list);
		file_put_contents(
			self::$$filename, $contents
		);
	}

	public static function writeTmpFunctionWhitelist(array $functions)
	{
		return self::writeTmpConfFile(
			'tmp_function_whitelist_file', $functions
		);
	}

	public static function writeTmpPatcherList(array $patchers)
	{
		return self::writeTmpConfFile(
			'tmp_patcher_list_file', $patchers
		);
	}

	public static function writeTmpIncludePaths(array $paths)
	{
		return self::writeTmpConfFile(
			'tmp_include_paths_file', $paths
		);
	}

	public static function writeTmpExcludePaths(array $paths)
	{
		return self::writeTmpConfFile(
			'tmp_exclude_paths_file', $paths
		);
	}

	protected static function getTmpConfFile($filename)
	{
		if (is_readable(self::$$filename))
		{
			return file(
				self::$$filename,
				FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
			);
		}
		return [];
	}

	public static function getTmpFunctionWhitelist()
	{
		return self::getTmpConfFile('tmp_function_whitelist_file');
	}

	public static function getTmpPatcherList()
	{
		return self::getTmpConfFile('tmp_patcher_list_file');
	}

	public static function getTmpIncludePaths()
	{
		return self::getTmpConfFile('tmp_include_paths_file');
	}

	public static function getTmpExcludePaths()
	{
		return self::getTmpConfFile('tmp_exclude_paths_file');
	}

	/**
	 * @param string $orig_file original source file
	 * @return string removed cache file
	 */
	public static function removeSrcCacheFile($orig_file)
	{
		$cache = self::getSrcCacheFilePath($orig_file);
		@unlink($cache);
		MonkeyPatchManager::log('remove_src_cache: ' . $cache);
		return $cache;
	}

	public static function clearSrcCache()
	{
		self::recursiveUnlink(self::$src_cache_dir);
		MonkeyPatchManager::log('clear_src_cache: cleared ' . self::$src_cache_dir);
	}

	public static function clearCache()
	{
		self::recursiveUnlink(self::$cache_dir);
		MonkeyPatchManager::log('clear_cache: cleared ' . self::$cache_dir);
	}

	/**
	* Recursive Unlink
	*
	* @param string $dir
	*/
	protected static function recursiveUnlink($dir)
	{
		if (! is_dir($dir))
		{
			return;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$dir, RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				rmdir($file);
			} else {
				unlink($file);
			}
		}

		rmdir($dir);
	}
}
