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

class PathChecker
{
	private static $include_paths = [];
	private static $exclude_paths = [];

	/**
	 * @param array $paths directory or file path
	 * @return array
	 * @throws RuntimeException
	 */
	protected static function normalizePaths(array $paths)
	{
		$new_paths = [];
		$excluded = false;
		foreach ($paths as $path)
		{
			// Path starting with '-' has special meaning (excluding it)
			if (substr($path, 0, 1) === '-')
			{
				$excluded = true;
				$path = ltrim($path, '-');
			}

			$real_path = realpath($path);
			if ($real_path === FALSE)
			{
				throw new RuntimeException($path . ' does not exist?');
			}
			if (is_dir($real_path))
			{
				$real_path = $real_path . '/';
			}
			$new_paths[] = $excluded ? '-'.$real_path : $real_path;
		}
		array_unique($new_paths, SORT_STRING);
		sort($new_paths, SORT_STRING);
		return $new_paths;
	}

	public static function setIncludePaths(array $dir)
	{
		self::$include_paths = self::normalizePaths($dir);
	}

	public static function setExcludePaths(array $dir)
	{
		self::$exclude_paths = self::normalizePaths($dir);
	}

	public static function getIncludePaths()
	{
		return self::$include_paths;
	}

	public static function getExcludePaths()
	{
		return self::$exclude_paths;
	}

	public static function check($path)
	{
		// Whitelist first
		$is_white = false;
		foreach (self::$include_paths as $white_dir) {
			$len = strlen($white_dir);
			if (substr($path, 0, $len) === $white_dir)
			{
				$is_white = true;
			}
		}
		if ($is_white === false)
		{
			return false;
		}

		// Then blacklist
		foreach (self::$exclude_paths as $black_dir) {
			// Check excluded path that starts with '-'.
			// '-' is smaller than '/', so this checking always comes first.
			if (substr($black_dir, 0, 1) === '-')
			{
				$black_dir = ltrim($black_dir, '-');
				$len = strlen($black_dir);
				if (substr($path, 0, $len) === $black_dir)
				{
					return true;
				}
			}

			$len = strlen($black_dir);
			if (substr($path, 0, $len) === $black_dir)
			{
				return false;
			}
		}

		return true;
	}
}
