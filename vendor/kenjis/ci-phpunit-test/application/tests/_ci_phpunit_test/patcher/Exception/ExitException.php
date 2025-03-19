<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Exception;

use RuntimeException;

class ExitException extends RuntimeException
{
	public $file;
	public $line;
	public $class;
	public $method;
	public $exit_status;
}
