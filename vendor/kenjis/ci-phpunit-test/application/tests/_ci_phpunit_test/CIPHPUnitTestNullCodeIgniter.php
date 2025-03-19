<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * Represents CodeIgniter instance is null
 */
class CIPHPUnitTestNullCodeIgniter
{
	public function __get($name)
	{
		throw new LogicException("CodeIgniter instance is not instantiated yet. You can't use `\$this->$name` at the moment. Please fix your test code.");
	}
}
