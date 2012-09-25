<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst. It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * CodeIgniter Version
 *
 * @var string
 */
define('CI_VERSION', '3.0-dev');

/**
 * CodeIgniter Application Core Class
 *
 * This class object is the super class that every object in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Front-controller
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/
 */
class CodeIgniter {
	/**
	 * Base paths for loading core classes
	 *
	 * @access	protected
	 * @var		array
	 */
	public $base_paths = array();

	/**
	 * App paths for loading core class extensions
	 *
	 * @var		array
	 */
	public $app_paths = array();

	/**
	 * Log threshold
	 *
	 * @var		int
	 */
	public $log_threshold = 1;

	/**
	 * Subclass prefix for core class extensions
	 *
	 * @var		string
	 */
	public $subclass_prefix = '';

	/**
	 * Is running flag to prevent run() reentry
	 *
	 * @access	protected
	 * @var		bool
	 */
	protected $_is_running = FALSE;

	/**
	 * Display cache flag
	 *
	 * @access	protected
	 * @var	 bool
	 */
	protected $_display_cache = FALSE;

	/**
	 * CodeIgniter singleton instance
	 *
	 * @access	protected
	 * @var		object
	 */
	protected static $instance = NULL;

	/**
	 * Constructor
	 *
	 * This constructor is protected in order to force instantiation
	 * through instance(), employing a singleton pattern.
	 *
	 * @param	array	Main config
	 * @param	array	Autoload config
	 * @param	string	Base path
	 * @param	string	Application path
	 * @access	protected
	 */
	protected function __construct($config, $autoload, $basepath, $apppath)
	{
		// Get subclass prefix for core loading
		if (isset($config['subclass_prefix']))
		{
			$this->subclass_prefix = $config['subclass_prefix'];
		}

		// Get log config items to pass to Log class
		$this->_log_config = array();
		foreach ($config as $key => $val)
		{
			if (substr($key, 0, 4) == 'log_')
			{
				$this->_log_config[$key] = $val;
			}
		}

		// Get log threshold
		if (isset($this->_log_config['log_threshold']))
		{
			$this->log_threshold = $this->_log_config['log_threshold'];
		}

		// Save configs until we can pass them off to CI_Config and CI_Loader
		$this->_core_config = $config;
		$this->_autoload = $autoload;

		// Set core class paths
		$this->base_paths = array($apppath, $basepath);
		$this->app_paths = array($apppath);

		// Check autoload for package paths to add
		if (isset($autoload['packages']))
		{
			foreach ((array)$autoload['packages'] as $path)
			{
				// Resolve path and add to paths
				$path = self::resolve_path($path);
				array_unshift($this->base_paths, $path);
				array_unshift($this->app_paths, $path);
			}
		}

		// Define a custom error handler so we can log PHP errors
		// This must come after the config items and paths above,
		// because the exception handler relies on some of those elements.
		$this->_catch_exceptions();

		// Kill magic quotes for older versions
		if ( ! is_php('5.4'))
		{
			@ini_set('magic_quotes_runtime', 0);
		}

		$this->log_message('debug', 'CodeIgniter Class Initialized');
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// Finalize run and send output
		$this->_finalize();
	}

	/**
	 * Get instance
	 *
	 * Returns singleton instance of core object
	 * Upon initial instantiation, this function bootstraps the system by
	 * loading the main config and autoload files and its own extension class (if available).
	 * All other config files are loaded via CI_Config.
	 * All other core classes are loaded via load_core_class().
	 * All other loadables are loaded via CI_Loader.
	 * The second parameter supports overriding the APPPATH constant in unit testing.
	 *
	 * @param	string	Optional base path override (for unit tests)
	 * @param	string	Optional app path override (for unit tests)
	 * @param	string	Optional environment override (for unit tests)
	 * @return	object
	 */
	public static function &instance($basepath = NULL, $apppath = NULL, $env = NULL)
	{
		// Check for existing instance
		if (is_null(self::$instance))
		{
			global $assign_to_config;

			// Determine base and application paths
			if ( ! $basepath)
			{
				$basepath = BASEPATH;
			}
			if ( ! $apppath)
			{
				$apppath = APPPATH;
			}
			$packages = array($apppath);

			// Check for environment
			if ( ! $env && defined('ENVIRONMENT'))
			{
				$env = ENVIRONMENT;
			}

			// Load main config and autoload files
			$path = $apppath.'config/';
			foreach (array('config', 'autoload') as $name)
			{
				if ($env && file_exists($path.$env.'/'.$name.'.php'))
				{
					// Use ENVIRONMENT config
					include($path.$env.'/'.$name.'.php');
				}
				else if (file_exists($path.$name.'.php'))
				{
					// Use regular config
					include($path.$name.'.php');
				}
				else if ($name == 'config')
				{
					// Can't run without main config - error out
					static::_status_exit(503, 'The configuration file does not exist.');
				}
			}

			// Does the $config array exist?
			if ( ! isset($config) OR ! is_array($config))
			{
				static::_status_exit(503, 'Your config file does not appear to be formatted correctly.');
			}

			// Does the $autoload array exist?
			if ( ! isset($autoload) OR ! is_array($autoload))
			{
				$autoload = array();
			}
			else if (isset($autoload['packages']))
			{
				foreach ((array)$autoload['packages'] as $path)
				{
					array_unshift($packages, self::resolve_path($path));
				}
			}

			// Are any values being dynamically replaced?
			if ( ! empty($assign_to_config) > 0)
			{
				$config = array_merge($config, $assign_to_config);
			}

			// Load the CodeIgniter subclass, if found
			$class = static::_get_class();
			if (isset($config['subclass_prefix']))
			{
				$pre = $config['subclass_prefix'];
				$filenm = 'core/'.$pre.$class.'.php';
				foreach ($packages as $path)
				{
					$file = $path.$filenm;
					if (file_exists($file))
					{
						include($file);
						$class = $pre.$class;
						break;
					}
				}
			}

			// Instantiate object as subclass if defined, otherwise as base name
			self::$instance = new $class($config, $autoload, $basepath, $apppath);
		}

		return self::$instance;
	}

	/**
	 * Resolve package path
	 *
	 * This function is used to identify absolute paths in the filesystem
	 * as well as relative paths in the include directories.
	 *
	 * @param	string	Initial path
	 * @return	string	Resolved path
	 */
	public static function resolve_path($path)
	{
		// Assert trailing slash
		$path = rtrim($path, '\/').'/';

		// See if path exists as-is
		if ( ! file_exists($path))
		{
			// Strip any leading slash and pair with include directories
			$dir = ltrim($path, '\/');
			foreach (explode(PATH_SEPARATOR, get_include_path()) as $include)
			{
				$include = rtrim($include, '\/');
				if (file_exists($include.'/'.$dir))
				{
					// Found include path - assemble and return
					return $include.'/'.$dir;
				}
			}
		}

		// Return path as-is
		return $path;
	}

	/**
	 * Get called class
	 *
	 * We need a way to get the called class in instance(), and when running
	 * PHP < 5.3 get_called_class() is not available. For unit testing, this
	 * function can be overloaded in the mock class to return that class
	 * name for instantiation instead of CodeIgniter.
	 */
	protected static function _get_class()
	{
		// Return name of current class
		return __CLASS__;
	}

	/**
	 * Set status and exit
	 *
	 * This function is for internal use while bootloading,
	 * before show_error can be called. It provides a mechanism
	 * for unit tests to override the exit calls.
	 *
	 * @param	int		Status code
	 * @param	string	Exit message
	 * @return	void
	 */
	protected static function _status_exit($status = 0, $msg = '')
	{
		// Set status header and exit with message
		if ($status)
		{
			set_status_header($status);
		}
		exit($msg);
	}

	/**
	 * Load a core class
	 *
	 * Loads and registers a core class
	 * This function must not call log_message or show_error in order
	 * to avoid infinite recursion.
	 *
	 * @param	string	Class name
	 * @param	string	Object name
	 * @return	void
	 */
	public function load_core_class($class, $obj_name = '')
	{
		// Determine name
		if ($obj_name === '')
		{
			$obj_name = strtolower($class);
		}

		// Check if already loaded
		if (isset($this->$obj_name))
		{
			return;
		}

		// Check if class exists
		$name = 'CI_'.$class;
		if ( ! class_exists($name))
		{
			// Look for file in core paths
			$filenm = 'core/'.$class.'.php';
			foreach ($this->base_paths as $path)
			{
				$file = $path.$filenm;
				if (file_exists($file))
				{
					include($file);
					break;
				}
			}

			// Make sure class is loaded
			if ( ! class_exists($name))
			{
				static::_status_exit(503, 'Unable to locate the specified class: '.$class.'.php');
			}
		}

		// Check for class extension
		$ext = $this->subclass_prefix.$class;
		if (class_exists($ext))
		{
			// Instantiate extension class instead
			$name = $ext;
		}
		else
		{
			// Look for file in extension paths
			$filenm = 'core/'.$ext.'.php';
			foreach ($this->app_paths as $path)
			{
				$file = $path.$filenm;
				if (file_exists($file))
				{
					include($file);

					// See if extension class exists now
					if (class_exists($ext))
					{
						// Instantiate extension class instead
						$name = $ext;
						break;
					}
				}
			}
		}

		// Load class
		$this->$obj_name = new $name();
	}

	/**
	 * Log an error message
	 *
	 * @param	string	Error level
	 * @param	string	Error message
	 * @param	bool	PHP error flag
	 * @return	void
	 */
	public function log_message($level = 'error', $message, $php_error = FALSE)
	{
		// Check threshold
		if ($this->log_threshold === 0)
		{
			return;
		}

		// Check for log class
		if ( ! isset($this->log))
		{
			// Load logger and pass config collected during bootstrapping
			$this->load_core_class('Log');
			$this->log->configure($this->_log_config);
			unset($this->_log_config);
		}

		// Write message
		$this->log->write_log($level, $message, $php_error);
	}

	/**
	 * Determine if a class method can actually be called (from outside the class)
	 *
	 * @param	mixed	class name or object
	 * @param	string	method
	 * @return	boolean	TRUE if publicly callable, otherwise FALSE
	 */
	public function is_callable($class, $method)
	{
		// Just return whether the case-insensitive method is in the public methods
		return in_array(strtolower($method), array_map('strtolower', get_class_methods($class)));
	}

	/**
	 * Call a controller method
	 *
	 * Requires that controller already be loaded, validates method name, and calls
	 * _remap if available.
	 *
	 * @param	string	Class name
	 * @param	string	Method
	 * @param	array	Optional arguments
	 * @param	string	Optional object name
	 * @param	bool	TRUE to return call result (or NULL if failed)
	 * @return	bool	TRUE or call result on success, otherwise FALSE or NULL (if $return == TRUE)
	 */
	public function call_controller($class, $method, array $args = array(), $name = '', $return = FALSE)
	{
		// Default name if not provided
		if (empty($name))
		{
			$name = strtolower($class);
		}

		// Track whether it ran and what it returned
		$ran = FALSE;
		$result = NULL;

		// Class must be loaded, and method cannot start with underscore, nor be a member of the base class
		if (isset($this->$name) && strncmp($method, '_', 1) != 0 && ! $this->is_callable('CI_Controller', $method))
		{
			// Check for _remap
			if ($this->is_callable($class, '_remap'))
			{
				// Call _remap
				$result = $this->$name->_remap($method, $args);
				$ran = TRUE;
			}
			else if ($this->is_callable($class, $method))
			{
				// Call method
				$result = call_user_func_array(array(&$this->$name, $method), $args);
				$ran = TRUE;
			}
		}

		// Return result or success status
		return $return ? $result : $ran;
	}

	/**
	 * Call a controller method and get its output
	 *
	 * Requires that controller already be loaded, validates method name, and calls
	 * _remap if available.
	 *
	 * @param	string	Reference to output string
	 * @param	string	Class name
	 * @param	string	Method
	 * @param	array	Optional arguments
	 * @param	string	Optional object name
	 * @return	bool	TRUE on success, otherwise FALSE
	 */
	public function get_controller_output(&$out, $class, $method, array $args = array(), $name = '')
	{
		// Capture output and call controller
		$this->output->stack_push();
		$result = $this->call_controller($class, $method, $args, $name);
		$out = $this->output->stack_pop();

		// Return success or failure
		return $result;
	}

	/**
	 * Run the CodeIgniter application
	 *
	 * This function employs a simple mutex pattern to prevent recursive calls.
	 * By breaking down the run sequence, it makes it easier to override parts
	 * of the sequence in an extension class, and to unit test the operations
	 * in smaller chunks.
	 *
	 * @return	void
	 */
	public function run()
	{
		// Check running flag
		if ($this->_is_running)
		{
			return;
		}

		// Set running flag
		$this->_is_running = TRUE;

		// Load the base classes
		$this->_load_base();

		// Load the routing and support classes
		// We will exit from here if a cache is found
		$this->_load_routing();

		// Load the support classes
		$this->_load_support();

		// Load and run the routed controller
		$this->_run_controller();

		// Clear running flag
		$this->_is_running = FALSE;
	}

	/**
	 * Load base classes
	 *
	 * This function loads the base-level core classes
	 * that lay the foundation for the rest of the core
	 * Benchmark, Config, Hooks, and Loader
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _load_base()
	{
		// Load Benchmark and start timer
		$this->load_core_class('Benchmark');
		$this->benchmark->mark('total_execution_time_start');
		$this->benchmark->mark('loading_time:_base_classes_start');

		// Get Config
		$this->load_core_class('Config');

		// Load the hooks class and call pre_system (depends on Config)
		$this->load_core_class('Hooks');
		$this->hooks->call_hook('pre_system');

		// Load Loader as 'load' (depends on Config)
		$this->load_core_class('Loader', 'load');
	}

	/**
	 * Load routing classes
	 *
	 * This function loads the second level of core classes
	 * leading up to routing.
	 * UTF-8, URI, Output, and Routing
	 * If a cache is found, we output it and exit at the end of the call.
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _load_routing()
	{
		global $routing;

		// Load the UTF-8 class (depends on Config)
		$this->load_core_class('Utf8');

		// Load the URI class (depends on Config)
		$this->load_core_class('URI');

		// Load the output class (depends on Config)
		// Note: By loading Output before Router, we ensure it is available to support
		// 404 overrides in case of a call to show_404().
		$this->load_core_class('Output');

		// Load the Router class and set routing (depends on Config, Loader, and URI)
		$this->load_core_class('Router');
		$this->router->_set_routing();

		// Set any routing overrides that may exist in the main index file
		if ( isset($routing) && ! empty($routing))
		{
			$this->router->_set_overrides($routing);
		}

		// Is there a valid cache file? If so, we're done...
		if ($this->hooks->call_hook('cache_override') === FALSE && $this->output->_display_cache() === TRUE)
		{
			$this->_display_cache = TRUE;
			static::_status_exit();
		}
	}

	/**
	 * Load support classes
	 *
	 * This function loads the third level of core classes
	 * that offer support for the Controller.
	 * Security, Input, Language, and autoload resources
	 *
	 * @return	void
	 */
	protected function _load_support()
	{
		// Load the Security class (depends on URI)
		$this->load_core_class('Security');

		// Load the Input class (depends on Config and Security)
		$this->load_core_class('Input');

		// Load the Language class (depends on Config)
		$this->load_core_class('Lang');

		// Autoload libraries, etc.
		$this->load->_ci_autoloader();

		// Mark end of core loading
		$this->benchmark->mark('loading_time:_base_classes_end');
	}

	/**
	 * Load and run routed Controller
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _run_controller()
	{
		// Call pre_controller hook
		$this->hooks->call_hook('pre_controller');

		// Get the parsed route and identify class, method, and arguments
		$rtr_class = get_class($this->router);
		$route = $this->router->fetch_route();
		$args = array_slice($route, $rtr_class::SEG_CLASS);
		$class = strtolower(array_shift($args));
		$method = array_shift($args);

		// Mark a start point so we can benchmark the controller
		$this->benchmark->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

		// Load the controller, but don't call the method yet
		if ($this->load->controller($route, '', FALSE) == FALSE)
		{
			show_404($class.'/'.$method);
		}

		// Set special "routed" reference to routed Controller
		$this->routed = $this->$class;

		// Call post_controller_constructor hook
		$this->hooks->call_hook('post_controller_constructor');

		if ($this->call_controller($class, $method, $args) == FALSE)
		{
			// Both _remap and $method failed - go to 404
			show_404($class.'/'.$method);
		}
	}

	/**
	 * Finalize bencharks and hooks and send output
	 *
	 * This gets run from the destructor, so we check the existence of everything
	 * we need as opposed to assuming it's loaded.
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _finalize()
	{
		// Check for Benchmark class
		if (! $this->_display_cache && isset($this->benchmark) && isset($this->router))
		{
			// Get the parsed route and identify class and method
			$rtr_class = get_class($this->router);
			$route = $this->router->fetch_route();
			$class = strtolower($route[$rtr_class::SEG_CLASS]);
			$method = $route[$rtr_class::SEG_METHOD];

			// Mark a benchmark end point
			$this->benchmark->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');
		}

		// Check for Hooks class
		if (isset($this->hooks))
		{
			// Call post_controller hook unless we're displaying a cache
			if ( ! $this->_display_cache)
			{
				$this->hooks->call_hook('post_controller');
			}

			// Send the final rendered output to the browser
			if ($this->hooks->call_hook('display_override') === FALSE && isset($this->output))
			{
				$this->output->_display();
			}
				
			// Call post_system hook
			$this->hooks->call_hook('post_system');
		}
	}

	/**
	 * Register our exception handler
	 *
	 * Breaking out this one-liner allows us to prevent registration
	 * during unit testing, when we have other error handling mechanisms
	 * in place, and may not want this registered for all the other unit
	 * tests.
	 *
	 * @return	void
	 */
	protected function _catch_exceptions()
	{
		// Add our exception handler as a PHP error handler
		set_error_handler(array($this, '_exception_handler'));
	}

	/**
	 * Exception Handler
	 *
	 * This custom exception handler permits PHP errors to be logged in our own
	 * log files since the user may not have access to server logs. Since this
	 * function effectively intercepts PHP errors, however, we also need to
	 * display errors based on the current error_reporting level.
	 * We do that with the use of a PHP error template.
	 *
	 * @param	int		Severity
	 * @param	string	Error message
	 * @param	string	File path
	 * @param	int		Line number
	 * @return	void
	 */
	public function _exception_handler($severity, $message, $filepath, $line)
	{
		// Load Exception class
		$this->load_core_class('Exceptions');

		// Should we display the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		// And respect display_errors
		if (($severity & error_reporting()) === $severity && (bool) ini_get('display_errors') === TRUE)
		{
			$this->exceptions->show_php_error($severity, $message, $filepath, $line);
		}

		// Should we log the error? No? We're done...
		if ($this->log_threshold === 0)
		{
			return;
		}

		$this->exceptions->log_exception($severity, $message, $filepath, $line);
	}
}

/**
 * Global function to get CodeIgniter instance
 *
 * @return	object	CodeIgniter instance
 */
if ( ! function_exists('get_instance'))
{
	function &get_instance()
	{
		return CodeIgniter::instance();
	}
}

/*
 * ------------------------------------------------------
 * Load the global functions
 * ------------------------------------------------------
 */
require(BASEPATH.'core/Common.php');


/* End of file CodeIgniter.php */
/* Location: ./system/core/CodeIgniter.php */
