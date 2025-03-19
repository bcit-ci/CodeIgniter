<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\MethodPatcher;

class_alias('Kenjis\MonkeyPatch\Patcher\MethodPatcher\PatchManager', '__PatchManager__');

use Kenjis\MonkeyPatch\MonkeyPatchManager;
use Kenjis\MonkeyPatch\Patcher\Backtrace;
use Kenjis\MonkeyPatch\InvocationVerifier;

class PatchManager
{
	private static $patches = [];
	private static $expected_invocations = [];
	private static $invocations = [];

	/**
	 * Set a method patch
	 * 
	 * @param string $class
	 * @param array $params [method_name => return_value]
	 */
	public static function set($class, $params)
	{
		self::$patches[$class] = $params;
	}

	/**
	 * Clear all patches and invocation data
	 */
	public static function clear()
	{
		self::$patches = [];
		self::$expected_invocations = [];
		self::$invocations = [];
	}

	public static function getReturn($class, $method, $params)
	{
		if (MonkeyPatchManager::$debug)
		{
			$trace = debug_backtrace();
			$info = Backtrace::getInfo('MethodPatcher', $trace);
			
			$file = $info['file'];
			$line = $info['line'];
			
			if (isset($info['class_method']))
			{
				$called_method = $info['class_method'];
			}
			elseif (isset($info['function']))
			{
				$called_method = $info['function'];
			}
			else
			{
				$called_method = 'n/a';
			}
			
			$log_args = function () use ($params) {
				$output = '';
				foreach ($params as $arg) {
					$output .= var_export($arg, true) . ', ';
				}
				$output = rtrim($output, ', ');
				return $output;
			};
			MonkeyPatchManager::log(
				'invoke_method: ' . $class.'::'.$method . '(' . $log_args() . ') on line ' . $line . ' in ' . $file . ' by ' . $called_method
			);
//			var_dump($trace); exit;
		}

		self::$invocations[$class.'::'.$method][] = $params;

		if (
			isset(self::$patches[$class])
			&& array_key_exists($method, self::$patches[$class])
		)
		{
			$patch = self::$patches[$class][$method];
		}
		else
		{
			return __GO_TO_ORIG__;
		}

		if (is_callable($patch))
		{
			return call_user_func_array($patch, $params);
		} else {
			return $patch;
		}
	}

	public static function setExpectedInvocations($class_method, $times, $params)
	{
		self::$expected_invocations[$class_method][] = [$params, $times];
	}

	public static function verifyInvocations()
	{
		InvocationVerifier::verify(self::$expected_invocations, self::$invocations);
	}
}
