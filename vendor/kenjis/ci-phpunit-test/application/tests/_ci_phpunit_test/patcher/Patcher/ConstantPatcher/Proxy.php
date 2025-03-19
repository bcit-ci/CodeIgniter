<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\ConstantPatcher;

class_alias('Kenjis\MonkeyPatch\Patcher\ConstantPatcher\Proxy', '__ConstProxy__');

use LogicException;
use ReflectionConstant;
use ReflectionException;

use Kenjis\MonkeyPatch\Patcher\ConstantPatcher;
use Kenjis\MonkeyPatch\Patcher\Backtrace;
use Kenjis\MonkeyPatch\MonkeyPatchManager;
use Kenjis\MonkeyPatch\Cache;
use Kenjis\MonkeyPatch\InvocationVerifier;

class Proxy
{
	private static $patches = [];
	private static $patches_to_apply = [];

	/**
	 * Set a constant patch
	 * 
	 * @param string $constant      constant name
	 * @param mixed  $value         value
	 * @param string $class_method  class::method to apply this patch
	 * 
	 * @throws LogicException
	 */
	public static function patch($constant, $value, $class_method = null)
	{
		self::$patches[$constant] = $value;
		self::$patches_to_apply[$constant] = strtolower($class_method);
	}

	/**
	 * Clear all patches and invocation data
	 */
	public static function reset()
	{
		self::$patches = [];
		self::$patches_to_apply = [];
	}

	protected static function logInvocation($constant)
	{
		if (MonkeyPatchManager::$debug)
		{
			$trace = debug_backtrace();
			$info = Backtrace::getInfo('ConstantPatcher', $trace);

			$file = $info['file'];
			$line = $info['line'];
			$method = isset($info['class_method']) ? $info['class_method'] : $info['function'];

			MonkeyPatchManager::log(
				'invoke_const: ' . $constant . ') on line ' . $line . ' in ' . $file . ' by ' . $method . '()'
			);
		}
	}

	protected static function checkCalledMethod($constant)
	{
		$trace = debug_backtrace();
		$info = Backtrace::getInfo('ConstantPatcher', $trace);
		
		$class = strtolower($info['class']);
		$class_method = strtolower($info['class_method']);

		// Patches the constants only in the class
		if (strpos(self::$patches_to_apply[$constant], '::') === false)
		{
			if (self::$patches_to_apply[$constant] !== $class)
			{
				return false;
			}
			return true;
		}
		//Patches the constants only in the class method
		else
		{
			if (self::$patches_to_apply[$constant] !== $class_method)
			{
				return false;
			}
			return true;
		}
	}

	/**
	 * Get patched constant value
	 * 
	 * @param string $constant
	 * @return mixed
	 */
	public static function get($constant)
	{
		self::logInvocation($constant);

		if (isset(self::$patches_to_apply[$constant]))
		{
			if (! self::checkCalledMethod($constant))
			{
				MonkeyPatchManager::log(
					'invoke_const: ' . $constant . ' not patched (out of scope)'
				);
				return constant($constant);
			}
		}

		if (array_key_exists($constant, self::$patches))
		{
			MonkeyPatchManager::log('invoke_const: ' . $constant . ' patched');
			return self::$patches[$constant];
		}

		MonkeyPatchManager::log(
			'invoke_const: ' . $constant . ' not patched (no patch)'
		);
		return constant($constant);
	}
}
