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

use PHPUnit_Framework_TestCase;

class InvocationVerifier
{
	public static function verify(array $expected_invocations, array $invocations)
	{
		if ($expected_invocations === [])
		{
			return;
		}

		foreach ($expected_invocations as $class_method => $data)
		{
			foreach ($data as $params_times)
			{
				list($expected_params, $expected_times) = $params_times;

				$invoked = isset($invocations[$class_method]);
				if ($invoked === false)
				{
					$actual_times = 0;
				}
				elseif ($expected_params === null)
				{
					$actual_times = count($invocations[$class_method]);
				}
				else
				{
					$count = 0;
					foreach ($invocations[$class_method] as $actual_params)
					{
						if ($actual_params == $expected_params)
						{
							$count++;
						}
					}
					$actual_times = $count;
				}

				if ($expected_times === 0)
				{
					PHPUnit_Framework_TestCase::assertEquals(
						$expected_times,
						$actual_times,
						$class_method . '() expected to be not invoked, but invoked ' . $actual_times . ' times.'
					);
				}
				elseif ($expected_times === '+')
				{
					PHPUnit_Framework_TestCase::assertGreaterThanOrEqual(
						1,
						$actual_times,
						$class_method . '() expected to be invoked at least one time, but invoked ' . $actual_times . ' times.'
					);
				}
				else
				{
					PHPUnit_Framework_TestCase::assertEquals(
						$expected_times,
						$actual_times,
						$class_method . '() expected to be invoked ' . $expected_times . ' times, but invoked ' . $actual_times . ' times.'
					);
				}
			}
		}
	}
}
