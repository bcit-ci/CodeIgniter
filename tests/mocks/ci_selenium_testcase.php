<?php

class CI_Selenium_TestCase extends PHPUnit_Extensions_Selenium2TestCase {
	
	public static $ci_testcase;

	// --------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();
		
		static::$ci_testcase = new CI_TestCase();
	}
	
	// --------------------------------------------------------------------
	
	public function setUp()
	{
		if (method_exists($this, 'set_up'))
		{
			$this->set_up();
		}
	}
	
	// --------------------------------------------------------------------
	
	public function tearDown() 
	{
		if (method_exists($this, 'tear_down'))
		{
			$this->tear_down();
		}
	}

	/**
	 * Overwrite runBare
	 *
	 * PHPUnit instantiates the test classes before
	 * running them individually. So right before a test
	 * runs we set our instance. Normally this step would
	 * happen in setUp, but someone is bound to forget to
	 * call the parent method and debugging this is no fun.
	 */
	public function runBare()
	{
		$this->ci_test_instance = $this;
		parent::runBare();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Overload method, to emulate multiple inheritance
	 */
	public function __get($property) 
	{
		if (isset(static::$ci_testcase->$property))
		{
			return static::$ci_testcase->$property;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Overload method, to emulate multiple inheritance
	 */
	public function __set($property, $value) 
	{
		if (isset(static::$ci_testcase->$property))
		{
			static::$ci_testcase->$property = $value;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * This overload is useful to create a stub, that need to have a specific method.
	 */
	public function __call($method, $args)
	{
		if (method_exists(static::$ci_testcase, $method))
		{
			return call_user_func_array(array(static::$ci_testcase, $method), $args);
		}
		elseif ($this->{$method} instanceof Closure) 
		{
			return call_user_func_array($this->{$method},$args);
		} 
		else 
		{
			return parent::__call($method, $args);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Overload method, to emulate multiple inheritance.
	 */
	public static function __callStatic($method, $args)
	{
		if (method_exists(static::$ci_testcase, $method))
		{
			return call_user_func_array(array(static::$ci_testcase, $method), $args);
		}
		else 
		{
			return parent::__callStatic($method, $args);
		}
	}
}