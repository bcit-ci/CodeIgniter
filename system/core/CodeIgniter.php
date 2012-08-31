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
 * bundled with this package in the files license.txt / license.rst.  It is
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
	 * CodeIgniter singleton instance
	 *
	 * @access	protected
	 * @var		object
	 */
	protected static $instance = NULL;

	/**
	 * Log path
	 *
	 * @var		string
	 */
	public $log_path = APPPATH.'logs/';

	/**
	 * Log threshold
	 *
	 * @var		int
	 */
	public $log_threshold = 0;

	/**
	 * Log date format
	 *
	 * @var		string
	 */
	public $log_date_format = 'Y-m-d H:i:s';

	/**
	 * Subclass prefix for core class extensions
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $subclass_prefix = '';

	/**
	 * Paths for loading core classes
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_core_paths = array();

	/**
	 * Paths for loading core class extensions
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_ext_paths = array();

	/**
	 * Is running flag to prevent run() reentry
	 *
	 * @access	protected
	 * @var		bool
	 */
	protected $_is_running = FALSE;

	/**
	 * Constructor
	 *
	 * This constructor is protected in order to force instantiation
	 * through instance(), employing a singleton pattern.
	 *
	 * @param   array   Main config
	 * @param	array	Autoload config
	 * @access  protected
	 */
	protected function __construct($config, $autoload)
	{
		// Set special members from config items
		foreach (array('log_path', 'log_threshold', 'log_date_format', 'subclass_prefix') as $key)
		{
			if (isset($config[$key]))
			{
				$this->$key = $config[$key];
			}
		}

		// Save configs until we can pass them off to CI_Config and CI_Loader
		$this->_main_config = $config;
		$this->_autoload = $autoload;

		// Set core class paths
		$this->_core_paths = array(APPPATH, BASEPATH);
		$this->_ext_paths = array(APPPATH);

		// Check autoload for package paths to add
		if (isset($autoload['packages']))
		{
			foreach ((array)$autoload['packages'] as $path)
			{
				$this->_core_paths[] = $path;
				$this->_ext_paths[] = $path;
			}
		}

		// Define a custom error handler so we can log PHP errors
		// This must come after the config items and paths above,
		// because the exception handler relies on those elements.
		set_error_handler(array($this, '_exception_handler'));

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
	 * loading the main config file and its own extension class (if available).
	 * All other config files are loaded via CI_Config.
	 * All other core classes are loaded via load_core_class().
	 * All other loadables are loaded via CI_Loader.
	 * The second parameter supports overriding the APPPATH constant in unit testing.
	 *
	 * @param	array	Config overrides
	 * @param	string	Application path override
	 * @return	object
	 */
	public static function &instance(array $assign_to_config = NULL, $apppath = NULL)
	{
		// Check for existing instance
		if (is_null(self::$instance))
		{
			// Determine application path
			if ( ! $apppath)
			{
				$apppath = APPPATH;
			}
			$packages = array($apppath);

			// Load main config and autoload files
			$path = $apppath.'config/';
			foreach (array('config', 'autoload') as $name)
			{
				if (defined('ENVIRONMENT') && file_exists($path.ENVIRONMENT.'/'.$name.'.php'))
				{
					// Use ENVIRONMENT config
					include($path.ENVIRONMENT.'/'.$name.'.php');
				}
				else if (file_exists($path.$name.'.php'))
				{
					// Use regular config
					include($path.$name.'.php');
				}
				else if ($name == 'config')
				{
					// Can't run without main config - error out
					set_status_header(503);
					exit('The configuration file does not exist.');
				}
			}

			// Does the $config array exist?
			if ( ! isset($config) || ! is_array($config))
			{
				set_status_header(503);
				exit('Your config file does not appear to be formatted correctly.');
			}

			// Does the $autoload array exist?
			if ( ! isset($autoload) || ! is_array($autoload))
			{
				$autoload = array();
			}
			else if (isset($autoload['packages']))
			{
				$packages = $packages + (array)$autoload['packages'];
			}

			// Are any values being dynamically replaced?
			if ( ! empty($assign_to_config) > 0)
			{
				$config = array_merge($config, $assign_to_config);
			}

			// Load the CodeIgniter subclass, if found
			$class = 'CodeIgniter';
			$pre = isset($config['subclass_prefix']) ? $config['subclass_prefix'] : '';
			foreach ($packages as $path)
			{
				$file = $path.'core/'.$pre.$class.'.php';
				if (file_exists($file))
				{
					include($file);
					$class = $pre.$class;
					break;
				}
			}

			// Instantiate object as subclass if defined, otherwise as base name
			self::$instance = new $class($config, $autoload);
		}

		return self::$instance;
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
			foreach ($this->_core_paths as $path)
			{
				$file = $path.'core/'.$class.'.php';
				if (file_exists($file))
				{
					include($file);
					break;
				}
			}

			// Make sure class is loaded
			if ( ! class_exists($name))
			{
				set_status_header(503);
				exit('Unable to locate the specified class: '.$class.'.php');
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
			foreach ($this->_ext_paths as $path)
			{
				$file = $path.'core/'.$this->subclass_prefix.$class.'.php';
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
			$this->load_core_class('Log');
			$this->log->configure($this->log_path, $this->log_threshold, $this->log_date_format);
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
	 * @param	string	class name
	 * @param	string	method
	 * @param	array	arguments
	 * @param	string	optional object name
	 * @param	bool	TRUE to return output
	 * @return	mixed	Output if $return, TRUE on success, otherwise FALSE
	 */
	public function call_controller($class, $method, array $args = array(), $name = '', $return = FALSE)
	{
		// Default name if not provided
		if (empty($name))
		{
			$name = strtolower($class);
		}

		// Class must be loaded, and method cannot start with underscore, nor be a member of the base class
		if (isset($this->$name) && strncmp($method, '_', 1) != 0 &&
		in_array(strtolower($method), array_map('strtolower', get_class_methods('CI_Controller'))) == FALSE)
		{
			// Check for _remap
			if ($this->is_callable($class, '_remap'))
			{
				// Call _remap, capturing output if requested
				if ($return)
				{
					$this->output->fork_output();
				}
				$this->$name->_remap($method, $args);
				return $return ? $this->output->get_forked() : TRUE;
			}
			else if ($this->is_callable($class, $method))
			{
				// Call method, capturing output if requested
				if ($return)
				{
					$this->output->fork_output();
				}
				call_user_func_array(array(&$this->$name, $method), $args);
				return $return ? $this->output->get_forked() : TRUE;
			}
		}

		// Neither _remap nor method could be called
		return FALSE;
	}

	/**
	 * Run the CodeIgniter application
	 *
	 * @param	array	Routing overrides
	 * @return	void
	 */
	public function run($routing = NULL)
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
		$this->_load_routing($routing);

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

		// Get Config and load constants
		$this->load_core_class('Config');
		$this->config->get('constants.php', NULL);

		// Load the hooks class and call pre_system (depends on Config)
		$this->load_core_class('Hooks');
		$this->hooks->_call_hook('pre_system');

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
	 * @param	array	Routing overrides
	 * @return	void
	 */
	protected function _load_routing($routing = NULL)
	{
		// Load the UTF-8 class (depends on Config)
		$this->load_core_class('Utf8');

		// Load the URI class (depends on Config)
		$this->load_core_class('URI');

		// Load the output class (depends on Config)
		// Note: By load Output before Router, we ensure it is available to support
		// 404 overrides in case of a call to show_404().
		$this->load_core_class('Output');

		// Load the Router class and set routing (depends on Config, Loader, and URI)
		$this->load_core_class('Router');
		$this->router->_set_routing();

		// Set any routing overrides that may exist in the main index file
		if ( ! empty($routing))
		{
			$this->router->_set_overrides($routing);
		}

		// Is there a valid cache file?  If so, we're done...
		if ($this->hooks->_call_hook('cache_override') === FALSE && $this->output->_display_cache() === TRUE)
		{
			exit;
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
		$this->load->ci_autoloader();

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
		$this->_hooks->_call_hook('pre_controller');

		// Get the parsed route and identify class, method, and arguments
		$route = $this->router->fetch_route();
		$args = array_slice($route, this_Router::SEG_CLASS);
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
		$this->hooks->_call_hook('post_controller_constructor');

		if ($this->call_controller($class, $method, $args) == FALSE)
		{
			// Both _remap and $method failed - go to 404
			show_404($class.'/'.$method);
		}
	}

	/**
	 * Finalize bencharks and hooks and send output
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _finalize()
	{
		// Check for Benchmark class
		if (isset($this->benchmark))
		{
			// Mark a benchmark end point
			$this->benchmark->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');
		}

		// Check for Hooks class
		if (isset($this->hooks))
		{
			// Call post_controller hook
			$this->hooks->_call_hook('post_controller');

			// Send the final rendered output to the browser
			if ($this->hooks->_call_hook('display_override') === FALSE && isset($this->output))
			{
				$this->output->_display();
			}
				
			// Call post_system hook
			$this->hooks->_call_hook('post_system');
		}
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
 * DEPRECATED - call CodeIgniter::instance() directly
 *
 * @return	object	CodeIgniter instance
 */
function &get_instance()
{
	return CodeIgniter::instance();
}

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require(BASEPATH.'core/Common.php');


/* End of file CodeIgniter.php */
/* Location: ./system/core/CodeIgniter.php */
