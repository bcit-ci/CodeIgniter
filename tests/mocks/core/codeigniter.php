<?php

class Mock_Core_CodeIgniter extends CodeIgniter {
	public $_log = '';

	/**
	 * Override logging to write to a string instead of incurring the Log class
	 */
	public function log_message($level = 'error', $message, $php_error = FALSE)
	{
		if ($this->log_threshold !== 0)
		{
			$this->_log .= $message;
		}
	}

	/**
	 * Reset the instance variable so another test instance can be loaded
	 */
	public static function reset()
	{
		self::$instance = NULL;
	}

	/**
	 * Overload _get_class so we instantiate this class instead of CodeIgniter directly
	 * This is necessary because get_called_class() is not available in PHP < 5.3
	 */
	protected static function _get_class()
	{
		return __CLASS__;
	}

	/**
	 * Throw an exception instead of exiting
	 */
	protected static function _status_exit($status, $msg)
	{
		throw new RuntimeException('CI '.$status.' Exit: '.$msg);
	}

    /**
     * Prevent error handler registration
     */
    protected function _catch_exceptions()
    {
        // Do nothing
    }

	/**
	 * Throw an exception instead of loading the Exception class
	 */
	public function _exception_handler($severity, $message, $filepath, $line)
	{
		throw new RuntimeException('CI Exception: '.$message);
	}
}

