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
 * Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class CI_Loader {
	// All these are set automatically. Don't mess with them.
	/**
	 * CodeIgniter core
	 *
	 * @var		object
	 */
	protected $CI;

	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var		int
	 */
	protected $_ci_ob_level;

	/**
	 * Configured base path (supports unit test override)
	 *
	 * @var	 string
	 */
	protected $_ci_base_path = BASEPATH;

	/**
	 * Configured app path (supports unit test override)
	 *
	 * @var	 string
	 */
	protected $_ci_app_path = APPPATH;

	/**
	 * Configured view path (supports unit test override)
	 *
	 * @var	 string
	 */
	protected $_ci_view_path = VIEWPATH;

	/**
	 * List of paths to load libraries/helpers from
	 *
	 * @var		array
	 */
	protected $_ci_library_paths = array();

	/**
	 * List of paths to load models/viewers/controllers from
	 *
	 * @var		array
	 */
	protected $_ci_mvc_paths = array();

	/**
	 * List of cached variables
	 *
	 * @var		array
	 */
	protected $_ci_cached_vars = array();

	/**
	 * List of loaded classes
	 *
	 * @var		array
	 */
	protected $_ci_classes = array();

	/**
	 * List of loaded files
	 *
	 * @var		array
	 */
	protected $_ci_loaded_files = array();

	/*
	 * List of loaded controllers
	 *
	 * @var		array
	 */
	protected $_ci_controllers = array();

	/*
	 * List of loaded models
	 *
	 * @var		array
	 */
	protected $_ci_models = array();

	/**
	 * List of loaded helpers
	 *
	 * @var		array
	 */
	protected $_ci_helpers = array();

	/**
	 * List of class name mappings
	 *
	 * @var		array
	 */
	protected $_ci_varmap = array(
		'unit_test' => 'unit',
		'user_agent' => 'agent'
	);

	/**
	 * Constructor
	 *
	 * Sets default package paths, gets the initial output buffering level,
	 * and autoloads additional paths and config files
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// Attach parent reference
		$this->CI =& get_instance();

		// Get initial buffering level
		$this->_ci_ob_level = ob_get_level();

		// Get library paths with autoloaded package paths
		$this->_ci_library_paths = (isset($this->CI->base_paths) && is_array($this->CI->base_paths)) ?
			$this->CI->base_paths : array(APPPATH, BASEPATH);

		// Get MVC paths with autoloaded package paths
		// Apply cascade default to each one
		if (isset($this->CI->app_paths) && is_array($this->CI->app_paths))
		{
			foreach ($this->CI->app_paths as $path)
			{
				$this->_ci_mvc_paths[$path] = TRUE;
			}
		}
		else
		{
			// Use default from constants
			$this->_ci_mvc_paths = array(APPPATH => TRUE);
		}

		log_message('debug', 'Loader Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Is Loaded
	 *
	 * A utility function to test if a class is in the self::$_ci_classes array.
	 * This function returns the object name if the class tested for is loaded,
	 * and returns FALSE if it isn't.
	 *
	 * It is mainly used in the form_helper -> _get_validation_object()
	 *
	 * @param		string	class being checked for
	 * @return		mixed	class object name on the CI SuperObject or FALSE
	 */
	public function is_loaded($class)
	{
		return isset($this->_ci_classes[$class]) ? $this->_ci_classes[$class] : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Class Loader
	 *
	 * This function lets users load and instantiate classes.
	 * It is designed to be called from a user's app controllers.
	 *
	 * @param	string	the name of the class
	 * @param	mixed	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	public function library($library = '', $params = NULL, $object_name = NULL)
	{
		if (is_array($library))
		{
			foreach ($library as $class)
			{
				$this->library($class, $params);
			}

			return;
		}

		if ($library === '')
		{
			return FALSE;
		}

		if ( ! is_null($params) && ! is_array($params))
		{
			$params = NULL;
		}

		$this->_ci_load_class($library, $params, $object_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Controller Loader
	 *
	 * This function lets users load and instantiate (sub)controllers.
	 *
	 * @param	mixed	Route to controller/method
	 * @param	string	Optional controller object name
	 * @param	bool	TRUE to return method result, FALSE to skip calling method
	 * @return	bool	TRUE or call result on success, otherwise FALSE or NULL (if $call == TRUE)
	 */
	public function controller($route, $name = NULL, $call = NULL)
	{
		// Set output flag to be passed
		$out = FALSE;
		return $this->controller_output($out, $route, $name, $call);
	}

	/**
	 * Controller Loader with output capture
	 *
	 * This function lets users load and instantiate (sub)controllers and
	 * return their output as a string.
	 *
	 * @param	string	Reference to output string
	 * @param	mixed	Route to controller/method
	 * @param	string	Optional controller object name
	 * @param	bool	TRUE to return method result, FALSE to skip calling method
	 * @return	bool	TRUE or call result on success, otherwise FALSE or NULL (if $call == TRUE)
	 */
	public function controller_output(&$out, $route, $name = NULL, $call = NULL)
	{
		// Check for missing class
		if (empty($route))
		{
			return $call === TRUE ? NULL : FALSE;
		}

		// Get instance and establish segment stack
		if (is_array($route))
		{
			// Assume segments have been pre-parsed by CI_Router::validate_route() - make sure there's 4
			if (count($route) <= CI_Router::SEG_METHOD)
			{
				return $call === TRUE ? NULL : FALSE;
			}
		}
		else
		{
			// Call validate_route() to break URI into segments
			$route = $this->CI->router->validate_route(explode('/', $route));
			if ($route === FALSE)
			{
				return $call === TRUE ? NULL : FALSE;
			}
		}

		// Extract segment parts
		$path = array_shift($route);
		$subdir = array_shift($route);
		$class = array_shift($route);
		$method = array_shift($route);

		// Set name if not provided
		if (empty($name))
		{
			$name = strtolower($class);
		}

		// Check if already loaded
		if ( ! in_array($name, $this->_ci_controllers, TRUE))
		{
			// Check for name conflict
			if (isset($this->CI->$name))
			{
				$msg = 'The controller name you are loading is the name of a resource that is already being used: '.
					$name;
				if ($name == 'routed')
				{
					// This could be a request from Exceptions - avoid recursive calls to show_error
					exit($msg);
				}
				show_error($msg);
			}

			// Load base class(es) if not already done
			if ( ! class_exists('CI_Controller'))
			{
				$this->_ci_include('Controller', 'core');
			}

			// Include source and instantiate object
			// The Router is responsible for providing a valid path in the route stack
			include($path.'controllers/'.$subdir.strtolower($class).'.php');
			$classnm = ucfirst($class);
			$this->CI->$name = new $classnm();

			// Mark as loaded
			$this->_ci_controllers[] = $name;
		}

		// Check call and output flags
		if ($call === FALSE)
		{
			// Call disabled - return success
			return TRUE;
		}
		else if ($out === FALSE)
		{
			// No output - just return result or success status
			return $this->CI->call_controller($class, $method, $route, $name, (bool)$call);
		}
		else
		{
			// Get output and return success status
			return $this->CI->get_controller_output($out, $class, $method, $route, $name);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @param	string	the name of the class
	 * @param	string	name for the model
	 * @param	bool	database connection
	 * @return	void
	 */
	public function model($model, $name = '', $db_conn = FALSE)
	{
		// Delegate multiples
		if (is_array($model))
		{
			foreach ($model as $class)
			{
				$this->model($class);
			}
			return;
		}

		// Check for missing class
		if ($model === '')
		{
			return;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, ++$last_slash);

			// And the model name behind it
			$model = substr($model, $last_slash);
		}

		// Set name if not provided
		if (empty($name))
		{
			$name = $model;
		}

		// Check if already loaded
		if (in_array($name, $this->_ci_models, TRUE))
		{
			return;
		}

		// Check for name conflict
		if (isset($this->CI->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		// Load database if needed
		if ($db_conn !== FALSE && ! class_exists('CI_DB'))
		{
			if ($db_conn === TRUE)
			{
				$db_conn = '';
			}

			$this->database($db_conn, FALSE, TRUE);
		}

		// Load base class(es) if not already done
		if ( ! class_exists('CI_Model'))
		{
			$this->_ci_include('Model', 'core');
		}

		// Search MVC paths for model
		$model = strtolower($model);
		$file = 'models/'.$path.$model.'.php';
		foreach ($this->_ci_mvc_paths as $mod_path => $view_cascade)
		{
			// Check each path for filename
			if ( ! file_exists($mod_path.$file))
			{
				continue;
			}

			// Include source and instantiate object
			require_once($mod_path.$file);

			$model = ucfirst($model);
			$this->CI->$name = new $model();
			$this->_ci_models[] = $name;
			return;
		}

		// Couldn't find the model
		show_error('Unable to locate the model you have specified: '.$model);
	}

	// --------------------------------------------------------------------

	/**
	 * Database Loader
	 *
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable query builder (this allows us to override the config setting)
	 * @return	object
	 */
	public function database($params = '', $return = FALSE, $query_builder = NULL)
	{
		// Do we even need to load the database class?
		if (class_exists('CI_DB') && $return === FALSE && $query_builder === NULL && isset($this->CI->db) &&
		is_object($this->CI->db))
		{
			return FALSE;
		}

		require_once($this->_ci_base_path.'database/DB.php');

		if ($return === TRUE)
		{
			return DB($params, $query_builder);
		}

		// Initialize the db variable. Needed to prevent
		// reference errors with some configurations
		$this->CI->db = '';

		// Load the DB class
		$this->CI->db =& DB($params, $query_builder);
	}

	// --------------------------------------------------------------------

	/**
	 * Load the Utilities Class
	 *
	 * @return	string
	 */
	public function dbutil()
	{
		if ( ! class_exists('CI_DB'))
		{
			$this->database();
		}

		// for backwards compatibility, load dbforge so we can extend dbutils off it
		// this use is deprecated and strongly discouraged
		$this->dbforge();

		$driver = $this->CI->db->dbdriver;
		require_once($this->_ci_base_path.'database/DB_utility.php');
		require_once($this->_ci_base_path.'database/drivers/'.$driver.'/'.$driver.'_utility.php');
		$class = 'CI_DB_'.$driver.'_utility';

		$this->CI->dbutil = new $class();
	}

	// --------------------------------------------------------------------

	/**
	 * Load the Database Forge Class
	 *
	 * @return	string
	 */
	public function dbforge()
	{
		if ( ! class_exists('CI_DB'))
		{
			$this->database();
		}

		$driver = $this->CI->db->dbdriver;
		require_once($this->_ci_base_path.'database/DB_forge.php');
		require_once($this->_ci_base_path.'database/drivers/'.$driver.'/'.$driver.'_forge.php');
		$class = 'CI_DB_'.$driver.'_forge';

		$this->CI->dbforge = new $class();
	}

	// --------------------------------------------------------------------

	/**
	 * Load View
	 *
	 * This function is used to load a "view" file. It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it. In
	 *	some cases it's advantageous to be able to return data so that
	 *	a developer can process it in some way.
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	public function view($view, $vars = array(), $return = FALSE)
	{
		return $this->_ci_load(array(
			'_ci_view' => $view,
			'_ci_vars' => $this->_ci_object_to_array($vars),
			'_ci_return' => $return
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Load File
	 *
	 * This is a generic file loader
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public function file($path, $return = FALSE)
	{
		return $this->_ci_load(array(
			'_ci_path' => $path,
			'_ci_return' => $return
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within
	 * the controller class and its "view" files.
	 *
	 * @param	array
	 * @param 	string
	 * @return	void
	 */
	public function vars($vars = array(), $val = '')
	{
		if ($val !== '' && is_string($vars))
		{
			$vars = array($vars => $val);
		}

		$vars = $this->_ci_object_to_array($vars);

		if (is_array($vars) && count($vars) > 0)
		{
			foreach ($vars as $key => $val)
			{
				$this->_ci_cached_vars[$key] = $val;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get Variable
	 *
	 * Check if a variable is set and retrieve it.
	 *
	 * @param	array
	 * @return	void
	 */
	public function get_var($key)
	{
		return isset($this->_ci_cached_vars[$key]) ? $this->_ci_cached_vars[$key] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Variables
	 *
	 * Retrieve all loaded variables
	 *
	 * @return	array
	 */
	public function get_vars()
	{
		return $this->_ci_cached_vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Load Helper
	 *
	 * This function loads the specified helper file.
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function helper($helpers = array())
	{
		// Delegate multiples
		if (is_array($helpers))
		{
			foreach ($helpers as $helper)
			{
				$this->helper($helper);
			}
			return;
		}

		// Prep filename
		$helper = strtolower(str_replace(array('.php', '_helper'), '', $helpers)).'_helper';

		// Check if already loaded
		if (isset($this->_ci_helpers[$helper]))
		{
			return;
		}

		// Include helper with any subclass extension
		if ($this->_ci_include($helper, 'helpers'))
		{
			// Mark as loaded and return
			$this->_ci_helpers[$helper] = TRUE;
			log_message('debug', 'Helper loaded: '.$helper);
			return;
		}

		// Unable to load the helper
		show_error('Unable to load the requested file: helpers/'.$helper.'.php');
	}

	// --------------------------------------------------------------------

	/**
	 * Load Helpers
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @param	array
	 * @return	void
	 */
	public function helpers($helpers = array())
	{
		$this->helper($helpers);
	}

	// --------------------------------------------------------------------

	/**
	 * Loads a language file
	 *
	 * @param	array
	 * @param	string
	 * @return	void
	 */
	public function language($file = array(), $lang = '')
	{
		if ( ! is_array($file))
		{
			$file = array($file);
		}

		foreach ($file as $langfile)
		{
			$this->CI->lang->load($langfile, $lang);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Loads a config file
	 *
	 * @param	string
	 * @param	bool
	 * @param 	bool
	 * @return	void
	 */
	public function config($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$this->CI->config->load($file, $use_sections, $fail_gracefully);
	}

	// --------------------------------------------------------------------

	/**
	 * Driver
	 *
	 * Loads a driver library
	 *
	 * @param	mixed	the name of the class or array of classes
	 * @param	mixed	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	public function driver($library = '', $params = NULL, $object_name = NULL)
	{
		// Delegate multiples
		if (is_array($library))
		{
			foreach ($library as $driver)
			{
				$this->driver($driver);
			}
			return;
		}

		if ($library === '')
		{
			return FALSE;
		}

		// We can save the loader some time since Drivers will *always* be in a subfolder,
		// and typically identically named to the library
		if ( ! strpos($library, '/'))
		{
			$library = ucfirst($library).'/'.$library;
		}

		return $this->library($library, $params, $object_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Add Package Path
	 *
	 * Prepends a parent path to the library, mvc, and config path arrays
	 *
	 * @param	string	path
	 * @param	bool	view cascade flag
	 * @return	void
	 */
	public function add_package_path($path, $view_cascade = TRUE)
	{
		// Resolve path
		$path = CodeIgniter::resolve_path($path);

		// Prepend path to library/helper paths
		array_unshift($this->_ci_library_paths, $path);

		// Add MVC path with view cascade param
		$this->_ci_mvc_paths = array_merge(array($path => $view_cascade), $this->_ci_mvc_paths);

		// Prepend config file path
		array_push($this->CI->config->_config_paths, $path);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Package Paths
	 *
	 * Return a list of all package paths, by default it will ignore $this->_ci_base_path.
	 *
	 * @param	boolean include base path flag
	 * @return	void
	 */
	public function get_package_paths($include_base = FALSE)
	{
		return $include_base === TRUE ? $this->_ci_library_paths : array_keys($this->_ci_mvc_paths);
	}

	// --------------------------------------------------------------------

	/**
	 * Remove Package Path
	 *
	 * Remove a path from the library, mvc, and config path arrays if it exists
	 * If no path is provided, the most recently added path is removed.
	 *
	 * @param	string	path
	 * @param	boolean remove from config path flag
	 * @return	void
	 */
	public function remove_package_path($path = '', $remove_config_path = TRUE)
	{
		if ($path === '')
		{
			// Shift last added path from each list
			array_shift($this->_ci_library_paths);
			array_shift($this->_ci_mvc_paths);
			if ($remove_config_path)
			{
				array_pop($this->CI->config->_config_paths);
			}
			return;
		}

		// Resolve path
		$path = CodeIgniter::resolve_path($path);

		// Prevent app path removal - it is a default for all lists
		if ($path == $this->_ci_app_path)
		{
			return;
		}

		// Unset from library/helper list unless base path
		if ($path != $this->_ci_base_path && ($key = array_search($path, $this->_ci_library_paths)) !== FALSE)
		{
			unset($this->_ci_library_paths[$key]);
		}

		// Unset path from MVC list
		if (isset($this->_ci_mvc_paths[$path]))
		{
			unset($this->_ci_mvc_paths[$path]);
		}

		// Unset path from config list
		if ($remove_config_path && ($key = array_search($path, $this->CI->config->_config_paths)) !== FALSE)
		{
			unset($this->CI->config->_config_paths[$key]);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Include a file from package paths
	 *
	 * This function includes a prefixed subclass file if found, and its base file
	 *
	 * @param	string	File name
	 * @param	string	Search directory
	 * @return	void
	 */
	protected function _ci_include($name, $dir)
	{
		// Get subclass prefix and build relative file name
		$pre = $this->CI->config->item('subclass_prefix');
		$file = $dir.'/'.$pre.$name.'.php';

		// Search all paths for subclass extension
		foreach ($this->_ci_library_paths as $path)
		{
			// Check each path for extension
			$path .= $file;
			if (file_exists($path))
			{
				// Extension found - require base file
				$base = $this->_ci_base_path.$dir.'/'.$name.'.php';
				if ( ! file_exists($base))
				{
					// No base for extension found
					return FALSE;
				}

				// Include extension followed by base, so extension overrides base functions
				// If this is for a base class, the order won't matter
				include_once($path);
				include_once($base);
				return TRUE;
			}
		}

		// Search all paths for the regular file
		$file = $dir.'/'.$name.'.php';
		foreach ($this->_ci_library_paths as $path)
		{
			// Check each path for base
			$path .= $file;
			if (file_exists($path))
			{
				// Include file
				include_once($path);
				return TRUE;
			}
		}

		// File not found
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * File/View Loader
	 *
	 * This function is used to load views and files.
	 * Variables are prefixed with _ci_ to avoid symbol collision with
	 * variables made available to view files
	 *
	 * @param	array
	 * @return	void
	 */
	protected function _ci_load($_ci_data)
	{
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val)
		{
			$$_ci_val = isset($_ci_data[$_ci_val]) ? $_ci_data[$_ci_val] : FALSE;
		}

		// Set the path to the requested file
		$_ci_exists = FALSE;
		if (is_string($_ci_path) && $_ci_path !== '')
		{
			// General file - extract name from path
			$parts = explode('/', $_ci_path);
			$_ci_file = end($parts);
			unset($parts);
			$_ci_exists = file_exists($_ci_path);
		}
		else
		{
			// View file - add extension as necessary
			$_ci_file = (pathinfo($_ci_view, PATHINFO_EXTENSION) === '') ? $_ci_view.'.php' : $_ci_view;

			// Check view path first
			if (file_exists($this->_ci_view_path.$_ci_file))
			{
				$_ci_path = $this->_ci_view_path.$_ci_file;
				$_ci_exists = TRUE;
			}
			else
			{
				// Search MVC package paths
				foreach ($this->_ci_mvc_paths as $_ci_mvc => $_ci_cascade)
				{
					if (file_exists($_ci_mvc.'views/'.$_ci_file))
					{
						// Set path, mark existing, and quit
						$_ci_path = $_ci_mvc.'views/'.$_ci_file;
						$_ci_exists = TRUE;
						break;
					}

					if ( ! $_ci_cascade)
					{
						// No cascade - stop looking
						break;
					}
				}
			}
		}

		// Verify file existence
		if ( ! $_ci_exists)
		{
			show_error('Unable to load the requested file: '.$_ci_file);
		}

		// This allows anything loaded using $this->load (libraries, models, etc.)
		// to become accessible from within the view or file
		foreach (get_object_vars($this->CI) as $_ci_key => $_ci_var)
		{
			if ( ! isset($this->$_ci_key))
			{
				$this->$_ci_key =& $this->CI->$_ci_key;
			}
		}

		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->CI->load->vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */
		if (is_array($_ci_vars))
		{
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);

		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be post-processed by
		 *	the output class. Why do we need post processing? For one thing,
		 *	in order to show the elapsed page load time. Unless we can
		 *	intercept the content right before it's sent to the browser and
		 *	then stop the timer it won't be accurate.
		 */
		ob_start();

		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		if ( ! is_php('5.4') && (bool) @ini_get('short_open_tag') === FALSE &&
		$this->CI->config->item('rewrite_short_tags') === TRUE)
		{
			echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>',
				str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else
		{
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name
		}

		log_message('debug', 'File loaded: '.$_ci_path);

		// Return the file data if requested
		if ($_ci_return === TRUE)
		{
			return @ob_get_clean();
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 */
		if (ob_get_level() > $this->_ci_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			$this->CI->output->append_output(@ob_get_clean());
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load class
	 *
	 * This function loads the requested class.
	 *
	 * @param	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	protected function _ci_load_class($class, $params = NULL, $object_name = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace('.php', '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		$subdir = '';
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, ++$last_slash);

			// Get the filename from the path
			$class = substr($class, $last_slash);

			// Check for match and driver base class
			if (strtolower(trim($subdir, '/')) == strtolower($class) && ! class_exists('CI_Driver_Library'))
			{
				// We aren't instantiating an object here, just making the base class available
				require $this->_ci_base_path.'libraries/Driver.php';
			}
		}

		// Is this a class extension request?
		$pre = $this->CI->config->item('subclass_prefix');
		foreach ($this->_ci_library_paths as $path)
		{
			// Try both upper- and lower-class in path subdirectory
			$path .= 'libraries/'.$subdir;
			foreach (array(ucfirst($class), strtolower($class)) as $class)
			{
				$subclass = $path.$pre.$class.'.php';
				if (file_exists($subclass))
				{
					// Found extension - require base class (in base path, no subdir, always capital)
					$baseclass = $this->_ci_base_path.'libraries/'.ucfirst($class).'.php';
					if ( ! file_exists($baseclass))
					{
						$msg = 'Unable to load the requested class: '.$class;
						log_message('error', $msg);
						show_error($msg);
					}

					// Safety: Was the class already loaded by a previous call?
					if (in_array($subclass, $this->_ci_loaded_files))
					{
						// Before we deem this to be a duplicate request, let's see
						// if a custom object name is being supplied. If so, we'll
						// return a new instance of the object
						if ( ! is_null($object_name) && ! isset($this->CI->$object_name))
						{
							return $this->_ci_init_class($class, $pre, $params, $object_name);
						}

						log_message('debug', $class.' class already loaded. Second attempt ignored.');
						return;
					}

					// Include base class followed by subclass for inheritance
					include_once($baseclass);
					include_once($subclass);
					$this->_ci_loaded_files[] = $subclass;

					return $this->_ci_init_class($class, $pre, $params, $object_name);
				}
			}
		}

		// Let's search for the requested library file and load it.
		foreach ($this->_ci_library_paths as $path)
		{
			// Try both upper- and lower-class in path subdirectory
			$path .= 'libraries/'.$subdir;
			foreach (array(ucfirst($class), strtolower($class)) as $class)
			{
				// Does the file exist? No? Bummer...
				$file = $path.$class.'.php';
				if ( ! file_exists($file))
				{
					continue;
				}

				// Safety: Was the class already loaded by a previous call?
				if (in_array($file, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied. If so, we'll
					// return a new instance of the object
					if ( ! is_null($object_name) && ! isset($this->CI->$object_name))
					{
						return $this->_ci_init_class($class, '', $params, $object_name);
					}

					log_message('debug', $class.' class already loaded. Second attempt ignored.');
					return;
				}

				// If this looks like a driver, make sure the base class is loaded
				if (strtolower($subdir) == strtolower($class).'/' && !class_exists('CI_Driver_Library'))
				{
					// We aren't instantiating an object here, that'll be done by the Library itself
					require $this->_ci_base_path.'libraries/Driver.php';
				}

				include_once($file);
				$this->_ci_loaded_files[] = $file;
				return $this->_ci_init_class($class, '', $params, $object_name);
			}
		}

		// One last attempt. Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir === '')
		{
			$path = strtolower($class).'/'.$class;
			return $this->_ci_load_class($path, $params, $object_name);
		}
		else if (ucfirst($subdir) != $subdir)
		{
			// Lowercase subdir failed - retry capitalized
			$path = ucfirst($subdir).$class;
			return $this->_ci_load_class($path, $params, $object_name);
		}

		// If we got this far we were unable to find the requested class.
		$msg = 'Unable to load the requested class: '.$class;
		log_message('error', $msg);
		show_error($msg);
	}

	// --------------------------------------------------------------------

	/**
	 * Instantiates a class
	 *
	 * @param	string	Class name
	 * @param	string	Class prefix
	 * @param	array	Optional configuration
	 * @param	string	Optional object name
	 * @return	void
	 */
	protected function _ci_init_class($class, $prefix = '', $config = NULL, $object_name = NULL)
	{
		// Do we need to check for configs?
		if ($config === NULL)
		{
			// See if there's a config file for the class
			$file = strtolower($class);
			$data = $this->CI->config->get($file.'.php', 'config');
			if (!is_array($data))
			{
				// Try uppercase
				$data = $this->CI->config->get(ucfirst($file).'.php', 'config');
			}

			// Set config if found
			if (is_array($data))
			{
				$config = $data;
			}
		}

		if ($prefix === '')
		{
			if (class_exists('CI_'.$class))
			{
				$name = 'CI_'.$class;
			}
			elseif (class_exists($this->CI->config->item('subclass_prefix').$class))
			{
				$name = $this->CI->config->item('subclass_prefix').$class;
			}
			else
			{
				$name = $class;
			}
		}
		else
		{
			$name = $prefix.$class;
		}

		// Is the class name valid?
		if ( ! class_exists($name))
		{
			log_message('error', 'Non-existent class: '.$name);
			show_error('Non-existent class: '.$name);
		}

		// Set the variable name we will assign the class to
		// Was a custom class name supplied? If so we'll use it
		$class = strtolower($class);

		if (is_null($object_name))
		{
			$classvar = isset($this->_ci_varmap[$class]) ? $this->_ci_varmap[$class] : $class;
		}
		else
		{
			$classvar = $object_name;
		}

		// Save the class name and object name
		$this->_ci_classes[$class] = $classvar;

		// Instantiate the class
		if ($config !== NULL)
		{
			$this->CI->$classvar = new $name($config);
		}
		else
		{
			$this->CI->$classvar = new $name();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits sub-systems,
	 * libraries, and helpers to be loaded automatically.
	 *
	 * This function is public, as it's called from CodeIgniter.php.
	 * However, there is no reason you should ever need to use it.
	 *
	 * @return	void
	 */
	public function _ci_autoloader()
	{
		$autoload = $this->CI->_autoload;
		unset($this->CI->_autoload);

		// Check for autoload array
		if ( ! is_array($autoload) OR empty($autoload))
		{
			return FALSE;
		}

		// Load helpers and languages
		foreach (array('helper', 'language') as $type)
		{
			if (isset($autoload[$type]) && count($autoload[$type]) > 0)
			{
				$this->$type($autoload[$type]);
			}
		}

		// Load libraries
		if (isset($autoload['libraries']))
		{
			// Load the database driver.
			$libs = (array)$autoload['libraries'];
			$key = array_search('database', $libs);
			if ($key !== FALSE)
			{
				$this->database();
				unset($libs[$key]);
			}

			// Load all other libraries
			$this->library($libs);
		}

		// Load drivers
		if (isset($autoload['drivers']))
		{
			$this->driver($autoload['drivers']);
		}

		// Load controllers
		if (isset($autoload['controller']))
		{
			// We have to "manually" feed multiples to controller(), since an array
			// is treated as a router stack instead of more than one controller
			foreach ((array)$autoload['controller'] as $uri)
			{
				$this->controller($uri);
			}
		}

		// Load models
		if (isset($autoload['model']))
		{
			$this->model($autoload['model']);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	protected function _ci_object_to_array($object)
	{
		return is_object($object) ? get_object_vars($object) : $object;
	}
}

/* End of file Loader.php */
/* Location: ./system/core/Loader.php */
