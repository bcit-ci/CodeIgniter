<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher;

use LogicException;

class Backtrace
{
	private static $map = [
		'FunctionPatcher' => 1,
		'MethodPatcher'   => 0,
		'ConstantPatcher' => 0,
	];

	public static function getInfo($patcher, $backtrace)
	{
		if (! isset(self::$map[$patcher]))
		{
			throw new LogicException("No such a patcher: $patcher");
		}
		$offset = self::$map[$patcher];

		// Supports PHP7 optimization
		if (version_compare(PHP_VERSION, '6.0.0', '>'))
		{
			if ($backtrace[$offset]['function'] === '__callStatic')
			{
				$offset--;
			}
		}

		$file = isset($backtrace[$offset]['file'])
				? $backtrace[$offset]['file'] : null;
		$line = isset($backtrace[$offset]['line'])
				? $backtrace[$offset]['line'] : null;

		if (isset($backtrace[$offset+2]))
		{
			$class  = isset($backtrace[$offset+2]['class'])
					? $backtrace[$offset+2]['class']
					: null;
			$function = $backtrace[$offset+2]['function'];
		}
		else
		{
			$class = null;
			$function = null;
		}

		if (isset($class))
		{
			$method = $function;
			$class_method = $class . '::' . $function;
			$function = null;
		}
		else
		{
			$method = null;
			$class_method = null;
		}

		return [
			'file' => $file,
			'line' => $line,
			'class' => $class,
			'method' => $method,
			'class_method' => $class_method,
			'function' => $function,
		];
	}
}
