<?php


// Need a way to change dependencies (core libs and laoded libs)
// Need a way to set the CI class

class CodeIgniterTestCase extends PHPUnit_Framework_TestCase {
		
	public $ci_instance;
	public static $test_instance;
	public static $global_map = array(
		'benchmark'	=> 'bm',
		'config'	=> 'cfg',
		'hooks'		=> 'ext',
		'utf8'		=> 'uni',
		'router'	=> 'rtr',
		'output'	=> 'out',
		'security'	=> 'sec',
		'input'		=> 'in',
		'lang'		=> 'lang',
		
		// @todo the loader is an edge case
		'loader'	=> 'load'
	);
	
	function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
	
	// Change what get_instance returns
	function ci_instance($obj)
	{
		$this->ci_instance = $obj;
	}
	
	// --------------------------------------------------------------------
	
	function ci_set_instance_var($name, $obj)
	{
		$this->ci_instance->$name =& $obj;
	}
	
	// --------------------------------------------------------------------
	
	// Set a class to a mock before it is loaded
	function ci_library($name)
	{
		
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
	function &ci_core_class($name)
	{
		$name = strtolower($name);
		
		if (isset(self::$global_map[$name]))
		{
			$class_name = ucfirst($name);
			$global_name = self::$global_map[$name];
		}
		elseif (in_array($name, self::$global_map))
		{
			$class_name = ucfirst(array_search($name, self::$global_map));
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
	function ci_set_core_class($name, $obj)
	{
		$orig =& $this->ci_core_class($name);
		$orig = $obj;
	}
	
	// --------------------------------------------------------------------
	
	static function ci_config($item)
	{
		return '';
	}
}

// EOF