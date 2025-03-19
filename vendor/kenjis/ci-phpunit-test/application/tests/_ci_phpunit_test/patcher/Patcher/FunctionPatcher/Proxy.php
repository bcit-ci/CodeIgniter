<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

class_alias('Kenjis\MonkeyPatch\Patcher\FunctionPatcher\Proxy', '__FuncProxy__');

use LogicException;
use ReflectionFunction;
use ReflectionException;

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher;
use Kenjis\MonkeyPatch\Patcher\Backtrace;
use Kenjis\MonkeyPatch\MonkeyPatchManager;
use Kenjis\MonkeyPatch\Cache;
use Kenjis\MonkeyPatch\InvocationVerifier;

class Proxy
{
	private static $patches = [];
	private static $patches_to_apply = [];
	private static $expected_invocations = [];
	private static $invocations = [];

	/**
	 * Set a function patch
	 * 
	 * This method has '__' suffix, because if it matches real function name,
	 * '__callStatic()' catch it.
	 * 
	 * @param string $function     function name
	 * @param mixed  $return_value return value or callable
	 * @param string $class_name   class::method to apply this patch
	 * 
	 * @throws LogicException
	 */
	public static function patch__($function, $return_value, $class_method = null)
	{
		$function = strtolower($function);

		if (FunctionPatcher::isBlacklisted($function))
		{
			$msg = "<red>Can't patch on '$function'. It is in blacklist.</red>";
			self::outputMessage($msg);
			throw new LogicException($msg);
		}
		if (! FunctionPatcher::isWhitelisted($function))
		{
			$msg = "<red>Can't patch on '$function'. It is not in whitelist. If you want to patch it, please add it to 'functions_to_patch' in 'tests/Bootstrap.php'. But note that there are some limitations. See <https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md#patching-functions> for details.</red>";
			self::outputMessage($msg);
			throw new LogicException($msg);
		}

		self::$patches[$function] = $return_value;
		self::$patches_to_apply[$function] = strtolower($class_method);
	}

	/**
	 * Clear all patches and invocation data
	 * 
	 * This method has '__' suffix, because if it matches real function name,
	 * '__callStatic()' catch it.
	 */
	public static function reset__()
	{
		self::$patches = [];
		self::$patches_to_apply = [];
		self::$expected_invocations = [];
		self::$invocations = [];
	}

	public static function setExpectedInvocations($function, $times, $params)
	{
		self::$expected_invocations[strtolower($function)][] = [$params, $times];
	}

	public static function verifyInvocations()
	{
		InvocationVerifier::verify(self::$expected_invocations, self::$invocations);
	}

	protected static function logInvocation($function, $arguments)
	{
		if (MonkeyPatchManager::$debug)
		{
			$trace = debug_backtrace();
			$info = Backtrace::getInfo('FunctionPatcher', $trace);

			$file = $info['file'];
			$line = $info['line'];
			$method = isset($info['class_method']) ? $info['class_method'] : $info['function'];

			$log_args = function () use ($arguments) {
				$output = '';
				foreach ($arguments as $arg) {
					$output .= var_export($arg, true) . ', ';
				}
				$output = rtrim($output, ', ');
				return $output;
			};
			MonkeyPatchManager::log(
				'invoke_func: ' . $function . '(' . $log_args() . ') on line ' . $line . ' in ' . $file . ' by ' . $method . '()'
			);
		}
	}

	protected static function checkCalledMethod($function)
	{
		$trace = debug_backtrace();
		$info = Backtrace::getInfo('FunctionPatcher', $trace);
		
		$class = strtolower($info['class']);
		$class_method = strtolower($info['class_method']);

		// Patches the functions only in the class
		if (strpos(self::$patches_to_apply[$function], '::') === false)
		{
			if (self::$patches_to_apply[$function] !== $class)
			{
				return false;
			}
			return true;
		}
		//Patches the functions only in the class method
		else
		{
			if (self::$patches_to_apply[$function] !== $class_method)
			{
				return false;
			}
			return true;
		}
	}

	public static function __callStatic($function, array $arguments)
	{
		$function = strtolower($function);

		self::logInvocation($function, $arguments);
		self::$invocations[$function][] = $arguments;

		if (isset(self::$patches_to_apply[$function]))
		{
			if (! self::checkCalledMethod($function))
			{
				MonkeyPatchManager::log(
					'invoke_func: ' . $function . '() not patched (out of scope)'
				);
				self::checkPassedByReference($function);
				return call_user_func_array($function, $arguments);
			}
		}

		if (array_key_exists($function, self::$patches))
		{
			MonkeyPatchManager::log('invoke_func: ' . $function . '() patched');

			if (is_callable(self::$patches[$function]))
			{
				$callable = self::$patches[$function];
				
				$return = call_user_func_array($callable, $arguments);
				if ($return !== __GO_TO_ORIG__)
				{
					return $return;
				}
				return call_user_func_array($function, $arguments);
			}

			return self::$patches[$function];
		}

		MonkeyPatchManager::log(
			'invoke_func: ' . $function . '() not patched (no patch)'
		);
		self::checkPassedByReference($function);
		return call_user_func_array($function, $arguments);
	}

	protected static function checkPassedByReference($function)
	{
		$ref_func = new ReflectionFunction($function);

		foreach ($ref_func->getParameters() as $param)
		{
			if ($param->isPassedByReference())
			{
				// Add tmp blacklist
				Cache::appendTmpFunctionBlacklist($function);

				// Remove cache file
				$backtrace = debug_backtrace();
				$info = Backtrace::getInfo('FunctionPatcher', $backtrace);
				$orig_file = $info['file'];
				$cache = Cache::removeSrcCacheFile($orig_file);

				$pr_msg = '';
				if (self::isInternalFunction($function))
				{
					$pr_msg = "<red>Please send Pull Request to add function '$function' to default config.</red>\n";
				}

				$tmp_blacklist_file = Cache::getTmpFunctionBlacklistFile();
				$msg = 
					"\n"
					. "<red>Can't patch on function '$function'.</red>\n"
					. "It has param(s) passed by reference.\n"
					. "Added it temporary blacklist file '$tmp_blacklist_file'.\n"
					. "And removed cache file '$cache'.\n"
					. "$pr_msg"
					. "\n<red>Please run phpunit again.</red>";

				self::outputMessage($msg);
				throw new LogicException($msg);
			}
		}
	}

	protected static function outputMessage($msg)
	{
		$red_begin = "\033[41m\033[37m";
		$red_end   = "\033[0m";

		$msg = str_replace(
			['<red>', '</red>'], [$red_begin, $red_end], $msg
		);
		echo $msg . "\n";
	}

	/**
	 * @param string $name function name
	 * @return bool
	 */
	protected static function isInternalFunction($name)
	{
		try {
			$ref_func = new ReflectionFunction($name);
			return $ref_func->isInternal();
		} catch (ReflectionException $e) {
			// ReflectionException: Function xxx() does not exist
			return false;
		}
	}

	public static function openssl_random_pseudo_bytes(
		$length, &$crypto_strong = null
	)
	{
		$function = 'openssl_random_pseudo_bytes';
		$arguments = [$length, $crypto_strong];
		self::logInvocation($function, $arguments);
		self::$invocations[$function][] = $arguments;

		if ($crypto_strong === null)
		{
			$crypto_strong = true;
		}

		if (array_key_exists($function, self::$patches))
		{
			if (is_callable(self::$patches[$function]))
			{
				$callable = self::$patches[$function];
				return call_user_func_array(
					$callable,
					[$length, &$crypto_strong]
				);
			}

			return self::$patches[$function];
		}

		return openssl_random_pseudo_bytes($length, $crypto_strong);
	}
}
