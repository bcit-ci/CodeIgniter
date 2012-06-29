<?php

class CI_TestCase extends PHPUnit_Framework_TestCase {

	protected $ci_config;
	protected $ci_instance;
	protected static $ci_test_instance;

	private $global_map = array(
		'benchmark'	=> 'bm',
		'config'	=> 'cfg',
		'hooks'		=> 'ext',
		'utf8'		=> 'uni',
		'router'	=> 'rtr',
		'output'	=> 'out',
		'security'	=> 'sec',
		'input'		=> 'in',
		'lang'		=> 'lang',
		'loader'	=> 'load',
		'model'		=> 'model'
	);

	// --------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
		$this->ci_config = array();
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

	// --------------------------------------------------------------------

	public static function instance()
	{
		return self::$ci_test_instance;
	}

	// --------------------------------------------------------------------

	public function ci_set_config($key, $val = '')
	{
		if (is_array($key))
		{
			$this->ci_config = $key;
		}
		else
		{
			$this->ci_config[$key] = $val;
		}
	}

	// --------------------------------------------------------------------

	public function ci_get_config()
	{
		return $this->ci_config;
	}

	// --------------------------------------------------------------------

	public function ci_instance($obj = FALSE)
	{
		if ( ! is_object($obj))
		{
			return $this->ci_instance;
		}

		$this->ci_instance = $obj;
	}

	// --------------------------------------------------------------------

	public function ci_instance_var($name, $obj = FALSE)
	{
		if ( ! is_object($obj))
		{
			return $this->ci_instance->$name;
		}

		$this->ci_instance->$name =& $obj;
	}

	// --------------------------------------------------------------------

	/**
	 * Grab a core class
	 *
	 * Loads the correct core class without extensions
	 * and returns a reference to the class name in the
	 * globals array with the correct key. This way the
	 * test can modify the variable it assigns to and
	 * still maintain the global.
	 */
	public function &ci_core_class($name)
	{
		$name = strtolower($name);

		if (isset($this->global_map[$name]))
		{
			$class_name = ucfirst($name);
			$global_name = $this->global_map[$name];
		}
		elseif (in_array($name, $this->global_map))
		{
			$class_name = ucfirst(array_search($name, $this->global_map));
			$global_name = $name;
		}
		else
		{
			throw new Exception('Not a valid core class.');
		}

		if ( ! class_exists('CI_'.$class_name))
		{
			require_once BASEPATH.'core/'.$class_name.'.php';
		}

		$GLOBALS[strtoupper($global_name)] = 'CI_'.$class_name;
		return $GLOBALS[strtoupper($global_name)];
	}

	// --------------------------------------------------------------------

	// convenience function for global mocks
	public function ci_set_core_class($name, $obj)
	{
		$orig =& $this->ci_core_class($name);
		$orig = $obj;
	}

	// --------------------------------------------------------------------
	// Internals
	// --------------------------------------------------------------------

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
		self::$ci_test_instance = $this;
		parent::runBare();
	}

	// --------------------------------------------------------------------

	public function helper($name)
	{
		require_once(BASEPATH.'helpers/'.$name.'_helper.php');
	}

	// --------------------------------------------------------------------

	/**
	 * This overload is useful to create a stub, that need to have a specific method.
	 */
	public function __call($method, $args)
	{
		if ($this->{$method} instanceof Closure)
		{
			return call_user_func_array($this->{$method},$args);
		}
		else
		{
			return parent::__call($method, $args);
		}
	}

}