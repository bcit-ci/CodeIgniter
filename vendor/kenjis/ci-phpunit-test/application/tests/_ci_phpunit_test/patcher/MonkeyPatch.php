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

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher\Proxy;
use Kenjis\MonkeyPatch\Patcher\ConstantPatcher\Proxy as ConstProxy;
use Kenjis\MonkeyPatch\Patcher\MethodPatcher\PatchManager;

class MonkeyPatch
{
	/**
	 * Patch on function
	 * 
	 * @param string $function     function name
	 * @param mixed  $return_value return value
	 * @param string $class_name   class::method to apply this patch
	 */
	public static function patchFunction($function, $return_value, $class_method = null)
	{
		Proxy::patch__($function, $return_value, $class_method);
	}

	/**
	 * Reset all patched fuctions
	 */
	public static function resetFunctions()
	{
		Proxy::reset__();
	}

	/**
     * Patch on constant
     * 
     * @param type $constant
     * @param type $value
     * @param type $class_method
     */
	public static function patchConstant($constant, $value, $class_method = null)
	{
		ConstProxy::patch($constant, $value, $class_method);
	}

	/**
	 * Reset all patched constants
	 */
	public static function resetConstants()
	{
		ConstProxy::reset();
	}

	/**
	 * Patch on class method
	 * 
	 * @param string $class
	 * @param array $params [method_name => return_value]
	 */
	public static function patchMethod($class, $params)
	{
		PatchManager::set($class, $params);
	}

	/**
	 * Reset all patched class method
	 */
	public static function resetMethods()
	{
		PatchManager::clear();
	}

	protected static function getClassname($class_method)
	{
		if (strpos($class_method, '::') === false)
		{
			return 'Kenjis\MonkeyPatch\Patcher\FunctionPatcher\Proxy';
		}
		else
		{
			return 'Kenjis\MonkeyPatch\Patcher\MethodPatcher\PatchManager';
		}
	}

	/**
	 * @param string $class_method class::method or function name
	 * @param int    $times        times
	 * @param array $params        parameters
	 */
	public static function verifyInvokedMultipleTimes(
		$class_method, $times, array $params = null
	)
	{
		$classname = self::getClassname($class_method);
		$classname::setExpectedInvocations(
			$class_method, $times, $params
		);
	}

	/**
	 * @param string $class_method class::method or function name
	 * @param array $params        parameters
	 */
	public static function verifyInvoked($class_method, array $params = null)
	{
		$classname = self::getClassname($class_method);
		$classname::setExpectedInvocations(
			$class_method, '+', $params
		);
	}

	/**
	 * @param string $class_method class::method or function name
	 * @param array $params        parameters
	 */
	public static function verifyInvokedOnce($class_method, array $params = null)
	{
		$classname = self::getClassname($class_method);
		$classname::setExpectedInvocations(
			$class_method, 1, $params
		);
	}

	/**
	 * @param string $class_method class::method or function name
	 * @param array $params        parameters
	 */
	public static function verifyNeverInvoked($class_method, array $params = null)
	{
		$classname = self::getClassname($class_method);
		$classname::setExpectedInvocations(
			$class_method, 0, $params
		);
	}

	/**
	 * Run function verifcations
	 */
	public static function verifyFunctionInvocations()
	{
		Proxy::verifyInvocations();
	}

	/**
	 * Run method verifcations
	 */
	public static function verifyMethodInvocations()
	{
		PatchManager::verifyInvocations();
	}
}
