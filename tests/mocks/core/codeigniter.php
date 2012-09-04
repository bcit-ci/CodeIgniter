<?php

class Mock_Core_CodeIgniter extends CodeIgniter {
	public $_log = '';
	private $core_pre = FALSE;

    /**
     * Don't finalize on destruct
     */
    public function __destruct()
    {
        // Do nothing
    }

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
	 * Prefix core class names on demand
	 */
	public function load_core_class($class, $obj_name = '')
	{
		// Add prefix if enabled
		if ($this->core_pre)
		{
			if ($obj_name == '') $obj_name = strtolower($class);
			$class = $this->core_pre.$class;
		}

		// Call parent method
		parent::load_core_class($class, $obj_name);
	}

	/**
	 * Load base core classes
	 */
	public function load_base($pre = FALSE)
	{
		$this->core_pre = $pre;
		$this->_load_base();
	}

	/**
	 * Load routing core classes
	 */
	public function load_routing($pre = FALSE)
	{
		$this->core_pre = $pre;

		// Pre-load Hooks, which got loaded in _load_base()
		$this->load_core_class('Hooks');

		$this->_load_routing();
	}

	/**
	 * Load support core classes
	 */
	public function load_support($pre = FALSE)
	{
		$this->core_pre = $pre;

		// Pre-load Benchmark and Loader, which got loaded in _load_base()
		$this->load_core_class('Benchmark');
		$this->load_core_class('Loader', 'load');

		$this->_load_support();
	}

    /**
     * Run routed controller
     */
    public function run_controller($pre = FALSE)
    {
		$this->core_pre = $pre;

		// Pre-load Benchmark, Hooks, Loader and Router, which were previously loaded
		$this->load_core_class('Benchmark');
		$this->load_core_class('Hooks');
		$this->load_core_class('Loader', 'load');
		$this->load_core_class('Router');

        $this->_run_controller();
    }

    /**
     * Run finalize
     */
    public function finalize($pre = FALSE)
    {
		$this->core_pre = $pre;

		// Pre-load Benchmark, Hooks, Router and Output, which were previously loaded
		$this->load_core_class('Benchmark');
		$this->load_core_class('Hooks');
		$this->load_core_class('Router');
		$this->load_core_class('Output');

        $this->_finalize();
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
	protected static function _status_exit($status = 0, $msg = '')
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
}

