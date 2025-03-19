<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

function exit__($status = null)
{
	$trace = debug_backtrace();
	$file = $trace[0]['file'];
	$line = $trace[0]['line'];
	$class = isset($trace[1]['class']) ? $trace[1]['class'] : null;
	$method = $trace[1]['function'];

	if ($class === null)
	{
		$message = 'exit() called in ' . $method . '() function';
	}
	else
	{
		$message = 'exit() called in ' . $class . '::' . $method . '()';
	}

	$exception_name = Kenjis\MonkeyPatch\MonkeyPatchManager::getExitExceptionClassname();
	$exception = new $exception_name($message);
	$exception->file = $file;
	$exception->line = $line;
	$exception->class = $class;
	$exception->method = $method;
	$exception->exit_status = $status;

	throw $exception;
}
