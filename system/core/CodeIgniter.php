<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Core Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CodeIgniter {
	/**
	 * CodeIgniter singleton instance
	 *
	 * @var	 object
	 * @access  private
	 */
	private static $instance = NULL;

	/**
	 * Constructor
	 */
	private function __construct()
	{
		// Define a custom error handler so we can log PHP errors
		set_error_handler('_exception_handler');

		// Kill magic quotes for older versions
		if ( ! is_php('5.3'))
		{
			@set_magic_quotes_runtime(0);
		}

		// Set a liberal script execution time limit
		if (function_exists('set_time_limit') == TRUE && @ini_get('safe_mode') == 0)
		{
			@set_time_limit(300);
		}

		log_message('debug', 'CodeIgniter Class Initialized');
	}

	/**
	 * Initialize
	 *
	 * This function handles loading Config and Loader, which are special.
	 * Loading them requires $instance to be set, so we can't do it in the ctor above.
	 *
	 * @return	void
	 */
	protected function _init()
	{
		// Get Config and load constants
		$this->load_core_class('Config');
		$this->config->get('constants.php', NULL);

		// Load Loader
		$this->load =& load_class('Loader', 'core');
	}

	/**
	 * Load core class
	 *
	 * Loads a core class and registers it with core object
	 *
	 * @param	string	class name
	 * @return	object
	 */
	public function load_core_class($class)
	{
		// Load class, immediately assign, and return object
		$name = strtolower($class);
		$this->$name =& load_class($class, 'core');
		return $this->$name;
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
	 * @return	boolean	TRUE on success, otherwise FALSE
	 */
	public function call_controller($class, $method, array $args = array(), $name = '')
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
				// Call _remap
				$this->$name->_remap($method, $args);
				return TRUE;
			}
			else if ($this->is_callable($class, $method))
			{
				// Call method
				call_user_func_array(array(&$this->$name, $method), $args);
				return TRUE;
			}
		}

		// Neither _remap nor method could be called
		return FALSE;
	}

	/**
	 * Get instance
	 *
	 * Returns singleton instance of core object
	 *
	 * @return	object
	 */
	public static function &instance()
	{
		// Check for existing instance
		if (is_null(self::$instance))
		{
			// Load the CodeIgniter subclass, if found
			$class = 'CodeIgniter';
			$pre = config_item('subclass_prefix');
			$file = APPPATH.'core/'.$pre.$class.'.php';
			if (file_exists($file))
			{
				include($file);
				$class = $pre.$class;
			}

			// Instantiate object as subclass if defined, otherwise as base name
			self::$instance = new $class();
			self::$instance->_init();
		}
		return self::$instance;
	}
}

// ------------------------------------------------------------------------

/**
 * System Initialization File
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Front-controller
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

/**
 * CodeIgniter Version
 *
 * @var string
 *
 */
	/**
	 * CodeIgniter Version
	 *
	 * @var string
	 */
	define('CI_VERSION', '2.1.0-dev');

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
	require(BASEPATH.'core/Common.php');

/*
 * ------------------------------------------------------
 *  Set the subclass_prefix
 * ------------------------------------------------------
 *
 * Normally the "subclass_prefix" is set in the config file.
 * The subclass prefix allows CI to know if a core class is
 * being extended via a library in the local application
 * "libraries" folder. Since CI allows config items to be
 * overriden via data set in the main index. php file,
 * before proceeding we need to know if a subclass_prefix
 * override exists. If so, we will set this value now,
 * before any classes are loaded
 * Note: Since the config file data is cached it doesn't
 * hurt to load it here.
 */
	if (isset($assign_to_config['subclass_prefix']) && $assign_to_config['subclass_prefix'] != '')
	{
		get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
	}

/*
 * ------------------------------------------------------
 *  Load the application core
 * ------------------------------------------------------
 */
	// Instantiate CodeIgniter [DEPRECATED - call CodeIgniter::instance() directly]
	function &get_instance()
	{
		return CodeIgniter::instance();
	}
	$CI =& CodeIgniter::instance();

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */
	$CI->load_core_class('Benchmark');
	$CI->benchmark->mark('total_execution_time_start');
	$CI->benchmark->mark('loading_time:_base_classes_start');

/*
 * ------------------------------------------------------
 *	Do we have any manually set config items in the index.php file?
 * ------------------------------------------------------
 */
	if (isset($assign_to_config))
	{
		$CI->config->_assign_to_config($assign_to_config);
	}

/*
 * ------------------------------------------------------
 *  Instantiate the hooks class
 * ------------------------------------------------------
 */
	$CI->load_core_class('Hooks');

/*
 * ------------------------------------------------------
 *  Is there a "pre_system" hook?
 * ------------------------------------------------------
 */
	$CI->hooks->_call_hook('pre_system');

/*
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class
 * ------------------------------------------------------
 *
 * Note: Order here is rather important as the UTF-8
 * class needs to be used very early on, but it cannot
 * properly determine if UTf-8 can be supported until
 * after the Config class is instantiated.
 *
 */
	$CI->load_core_class('Utf8');

/*
 * ------------------------------------------------------
 *  Instantiate the output class
 * ------------------------------------------------------
 *
 * Note: By instantiating Output before Router, we ensure
 * it is available to support 404 overrides in case of a
 * call to show_404().
 *
 */
	$CI->load_core_class('Output');

/*
 * ------------------------------------------------------
 *  Instantiate the URI class
 * ------------------------------------------------------
 */
	$CI->load_core_class('URI');

/*
 * ------------------------------------------------------
 *  Instantiate the routing class and set the routing
 * ------------------------------------------------------
 */
	$CI->load_core_class('Router');
	$CI->router->_set_routing();

	// Set any routing overrides that may exist in the main index file
	if (isset($routing))
	{
		$CI->router->_set_overrides($routing);
	}

/*
 * ------------------------------------------------------
 *	Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */
	if ($CI->hooks->_call_hook('cache_override') === FALSE && $CI->output->_display_cache($CI->config, $CI->uri) == TRUE)
	{
		exit;
	}

/*
 * -----------------------------------------------------
 * Load the security class for xss and csrf support
 * -----------------------------------------------------
 */
	$CI->load_core_class('Security');

/*
 * ------------------------------------------------------
 *  Load the Input class and sanitize globals
 * ------------------------------------------------------
 */
	$CI->load_core_class('Input');

/*
 * ------------------------------------------------------
 *  Load the Language class
 * ------------------------------------------------------
 */
	$CI->load_core_class('Lang');

/*
 * ------------------------------------------------------
 *  Autoload libraries, etc.
 * ------------------------------------------------------
 */
	$CI->load->ci_autoloader();

	// Set a mark point for benchmarking
	$CI->benchmark->mark('loading_time:_base_classes_end');

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook?
 * ------------------------------------------------------
 */
	$CI->hooks->_call_hook('pre_controller');

/*
 * ------------------------------------------------------
 *  Load the local controller
 * ------------------------------------------------------
 */
	// Get the parsed route and identify class, method, and arguments
	$route = $CI->router->fetch_route();
	$args = array_slice($route, CI_Router::SEG_CLASS);
	$class = array_shift($args);
	$method = array_shift($args);

	// Mark a start point so we can benchmark the controller
	$CI->benchmark->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

	// Load the controller, but don't call the method yet
	if ($CI->load->controller($route, '', FALSE) == FALSE)
	{
		show_404($class.'/'.$method);
	}

	// Set special "routed" reference to routed Controller
	$CI->routed =& $CI->$class;

/*
 * ------------------------------------------------------
 *  Is there a "post_controller_constructor" hook?
 * ------------------------------------------------------
 */
	$CI->hooks->_call_hook('post_controller_constructor');

/*
 * ------------------------------------------------------
 *  Call the requested method
 * ------------------------------------------------------
 */
	if ($CI->call_controller($class, $method, $args) == FALSE)
	{
		// Both _remap and $method failed - go to 404
		show_404($class.'/'.$method);
	}

	// Mark a benchmark end point
	$CI->benchmark->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');

/*
 * ------------------------------------------------------
 *  Is there a "post_controller" hook?
 * ------------------------------------------------------
 */
	$CI->hooks->_call_hook('post_controller');

/*
 * ------------------------------------------------------
 *  Send the final rendered output to the browser
 * ------------------------------------------------------
 */
	if ($CI->hooks->_call_hook('display_override') === FALSE)
	{
		$CI->output->_display();
	}

/*
 * ------------------------------------------------------
 *  Is there a "post_system" hook?
 * ------------------------------------------------------
 */
	$CI->hooks->_call_hook('post_system');

/*
 * ------------------------------------------------------
 *  Close the DB connection if one exists
 * ------------------------------------------------------
 */
	if (class_exists('CI_DB') && isset($CI->db))
	{
		$CI->db->close();
	}


/* End of file CodeIgniter.php */
/* Location: ./system/core/CodeIgniter.php */
