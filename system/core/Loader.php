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
 * Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Loader
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class CI_Loader {
	// All these are set automatically. Don't mess with them.
	/**
	 * CodeIgniter core
	 *
	 * @var		object
	 * @access	protected
	 */
	protected $CI;

	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var		int
	 * @access	protected
	 */
	protected $_ci_ob_level;

	/**
	 * Autoload config array
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_autoload;

	/**
	 * List of paths to load libraries/helpers from
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_library_paths	= array();

	/**
	 * List of paths to load models/viewers/controllers from
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_mvc_paths		= array();

	/**
	 * List of loaded base classes
	 * Set by the controller class
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_base_classes		= array(); // Set by the controller class

	/**
	 * List of cached variables
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_cached_vars		= array();

	/**
	 * List of loaded classes
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_classes			= array();

	/**
	 * List of loaded files
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_loaded_files		= array();

	/*
	 * List of loaded controllers
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_controllers		= array();

	/*
	 * List of loaded models
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_models			= array();

	/**
	 * List of loaded helpers
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_helpers			= array();

	/**
	 * List of class name mappings
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $_ci_varmap			= array('unit_test' => 'unit', 'user_agent' => 'agent');

	/**
	 * Constructor
	 *
	 * Sets default package paths, gets the initial output buffering level,
	 * and autoloads additional paths and config files
	 */
	public function __construct()
	{
		// Attach parent reference
		$this->CI =& CodeIgniter::instance();

		$this->_ci_ob_level = ob_get_level();
		$this->_ci_library_paths = array(APPPATH, BASEPATH);
		$this->_ci_mvc_paths = array(APPPATH => TRUE);

		// Fetch autoloader array
		$autoload = $this->CI->config->get('autoload.php', 'autoload');
		if (is_array($autoload))
		{
			// Save config for ci_autoload
			$this->_ci_autoload = $autoload;

			// Autoload packages
			if (isset($autoload['packages']))
			{
				foreach ($autoload['packages'] as $package_path)
				{
					$this->add_package_path($package_path);
				}
			}

			// Load any custom config files
			if (count($autoload['config']) > 0)
			{
				foreach ($autoload['config'] as $key => $val)
				{
					$this->CI->config->load($val);
				}
			}
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
		if (isset($this->_ci_classes[$class]))
		{
			return $this->_ci_classes[$class];
		}

		return FALSE;
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

		if ($library == '' OR isset($this->_base_classes[$library]))
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
	 * @access	public
	 * @param	string	the name of the class
	 * @param	string	name for the controller
	 * @param	bool	FALSE to skip calling controller method
	 * @param	bool	TRUE to return output (depends on $call == TRUE)
	 * @return	mixed	Output if $return, TRUE on success, otherwise FALSE
	 */
	public function controller($route, $name = NULL, $call = TRUE, $return = FALSE)
	{
		// Check for missing class
		if (empty($route))
		{
			return FALSE;
		}

		// Get instance and establish segment stack
		if (is_array($route))
		{
			// Assume segments have been pre-parsed by CI_Router::validate_route() - make sure there's 4
			if (count($route) < 4)
			{
				return FALSE;
			}
		}
		else
		{
			// Call validate_route() to break URI into segments
			$route = $this->CI->router->validate_route(explode('/', $route));
			if ($route === FALSE)
			{
				return FALSE;
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
				// Locate base class
				foreach ($this->_ci_library_paths as $lib_path)
				{
					$file = $lib_path.'core/Controller.php';
					if (file_exists($file))
					{
						// Include class source
						include $file;
						break;
					}
				}

				// Check for subclass
				$pre = $this->CI->config->item('subclass_prefix');
				if ( ! empty($pre))
				{
					// Locate subclass
					foreach ($this->_ci_mvc_paths as $mvc_path => $cascade)
					{
						$file = $mvc_path.'core/'.$pre.'Controller.php';
						if (file_exists($file))
						{
							// Include class source
							include($file);
							break;
						}
					}
				}
			}

			// Include source and instantiate object
			include($path.'controllers/'.$subdir.strtolower($class).'.php');
			$classnm = ucfirst($class);
			$this->CI->$name = new $classnm();

			// Mark as loaded
			$this->_ci_controllers[] = $name;
		}

		// Call method unless disabled
		if ($call)
		{
			return $this->CI->call_controller($class, $method, $route, $name, $return);
		}

		return TRUE;
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
			foreach ($model as $babe)
			{
				$this->model($babe);
			}
			return;
		}

		// Check for missing class
		if ($model == '')
		{
			return;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, $last_slash + 1);

			// And the model name behind it
			$model = substr($model, $last_slash + 1);
		}

		// Set name if not provided
		if ($name == '')
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
		if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
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
			// Locate base class
			foreach ($this->_ci_library_paths as $path)
			{
				$file = $path.'core/Model.php';
				if (file_exists($file))
				{
					// Include class source
					include($file);
					break;
				}
			}

			// Check for subclass
			$pre = $this->CI->config->item('subclass_prefix');
			if (!empty($pre))
			{
				// Locate subclass
				foreach ($this->_ci_mvc_paths as $path => $cascade)
				{
					$file = $path.'core/'.$pre.'Model.php';
					if (file_exists($file))
					{
						// Include class source
						include($file);
						break;
					}
				}
			}
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
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */
	public function database($params = '', $return = FALSE, $active_record = NULL)
	{
		// Do we even need to load the database class?
		if (class_exists('CI_DB') && $return == FALSE && $active_record == NULL && isset($this->CI->db) &&
		is_object($this->CI->db))
		{
			return FALSE;
		}

		require_once(BASEPATH.'database/DB.php');

		if ($return === TRUE)
		{
			return DB($params, $active_record);
		}

		// Initialize the db variable. Needed to prevent
		// reference errors with some configurations
		$this->CI->db = '';

		// Load the DB class
		$this->CI->db =& DB($params, $active_record);
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
		require_once(BASEPATH.'database/DB_utility.php');
		require_once(BASEPATH.'database/drivers/'.$driver.'/'.$driver.'_utility.php');
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
		require_once(BASEPATH.'database/DB_forge.php');
		require_once(BASEPATH.'database/drivers/'.$driver.'/'.$driver.'_forge.php');
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
	 * some cases it's advantageous to be able to return data so that
	 * a developer can process it in some way.
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	public function view($view, $vars = array(), $return = FALSE)
	{
		return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
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
		return $this->_ci_load(array('_ci_path' => $path, '_ci_return' => $return));
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
		if ($val != '' AND is_string($vars))
		{
			$vars = array($vars => $val);
		}

		$vars = $this->_ci_object_to_array($vars);

		if (is_array($vars) AND count($vars) > 0)
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

		// Is this a helper extension request?
		$file = 'helpers/'.config_item('subclass_prefix').$helper.'.php';
		foreach ($this->_ci_library_paths as $path)
		{
			// Check each path for extension
			$ext_helper = $path.$file;
			if (file_exists($ext_helper))
			{
				// Extension found - require base class
				$base_helper = BASEPATH.'helpers/'.$helper.'.php';
				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: helpers/'.$helper.'.php');
				}

				// Include extension followed by base, so extension overrides base functions
				include_once($ext_helper);
				include_once($base_helper);

				// Mark as loaded and return
				$this->_ci_helpers[$helper] = TRUE;
				log_message('debug', 'Helper loaded: '.$helper);
				return;
			}
		}

		// Try to load the helper
		$file = 'helpers/'.$helper.'.php';
		foreach ($this->_ci_library_paths as $path)
		{
			// Check each path for helper
			if (file_exists($path.$file))
			{
				// Include helper
				include_once($path.$file);

				// Mark as loaded and return
				$this->_ci_helpers[$helper] = TRUE;
				log_message('debug', 'Helper loaded: '.$helper);
				return;
			}
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
	 * @param	string	the name of the class
	 * @param	mixed	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	public function driver($library = '', $params = NULL, $object_name = NULL)
	{
		if ( ! class_exists('CI_Driver_Library'))
		{
			// We aren't instantiating an object here, that'll be done by the Library itself
			require BASEPATH.'libraries/Driver.php';
		}

		if ($library == '')
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
	 * @param	boolean	view cascade flag
	 * @return	void
	 */
	public function add_package_path($path, $view_cascade = TRUE)
	{
		// Resolve path
		$path = $this->_ci_resolve_path($path);

		// Prepend path to library/helper paths
		array_unshift($this->_ci_library_paths, $path);

		// Add MVC path with view cascade param
		$this->_ci_mvc_paths = array($path => $view_cascade) + $this->_ci_mvc_paths;

		// Prepend config file path
		array_unshift($this->CI->config->_config_paths, $path);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Package Paths
	 *
	 * Return a list of all package paths, by default it will ignore BASEPATH.
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
		if ($path == '')
		{
			// Shift last added path from each list
			array_shift($this->_ci_library_paths);
			array_shift($this->_ci_mvc_paths);
			if ($remove_config_path)
			{
				array_shift($this->CI->config->_config_paths);
			}
			return;
		}

		// Resolve path
		$path = $this->_ci_resolve_path($path);

		// Prevent app path removal - it is a default for all lists
		if ($path == APPPATH)
		{
			return;
		}

		// Unset from library/helper list unless base path
		if ($path != BASEPATH && ($key = array_search($path, $this->_ci_library_paths)) !== FALSE)
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
	 * Resolves package path
	 *
	 * This function is used to identify absolute paths in the filesystem and include path
	 *
	 * @access	protected
	 * @param	string	initial path
	 * @return	string	resolved path
	 */
	protected function _ci_resolve_path($path)
	{
		// Assert trailing slash
		$path = rtrim($path, '/\\').'/';

		// See if path exists as-is
		if (file_exists($path))
		{
			return $path;
		}

		// Strip any leading slash and pair with include directories
		$dir = ltrim($path, "/\\");
		foreach (explode(PATH_SEPARATOR, get_include_path()) as $include)
		{
			$include = rtrim($include, "/\\");
			if (file_exists($include.'/'.$dir))
			{
				// Found include path - clean up and return
				return $include.'/'.$dir;
			}
		}

		// If we got here, it's not a real path - just return as-is
		return $path;
	}

	// --------------------------------------------------------------------

	/**
	 * Loader
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
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}

		$file_exists = FALSE;

		// Set the path to the requested file
		if ($_ci_path != '')
		{
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		else
		{
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.'.php' : $_ci_view;

			foreach ($this->_ci_mvc_paths as $view_file => $cascade)
			{
				if (file_exists($view_file.'views/'.$_ci_file))
				{
					$_ci_path = $view_file.'views/'.$_ci_file;
					$file_exists = TRUE;
					break;
				}

				if ( ! $cascade)
				{
					break;
				}
			}
		}

		if ( ! $file_exists && ! file_exists($_ci_path))
		{
			show_error('Unable to load the requested file: '.$_ci_file);
		}

		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.

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
		 * You can either set variables using the dedicated $this->load_vars()
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
		 * 2. So that the final rendered template can be
		 * post-processed by the output class. Why do we
		 * need post processing? For one thing, in order to
		 * show the elapsed page load time. Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();

		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.

		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
		{
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else
		{
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name
		}

		log_message('debug', 'File loaded: '.$_ci_path);

		// Return the file data if requested
		if ($_ci_return === TRUE)
		{
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */
		if (ob_get_level() > $this->_ci_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			$this->CI->output->append_output(ob_get_contents());
			@ob_end_clean();
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
			$subdir = substr($class, 0, $last_slash + 1);

			// Get the filename from the path
			$class = substr($class, $last_slash + 1);
		}

		// We'll test for both lowercase and capitalized versions of the file name
		foreach (array(ucfirst($class), strtolower($class)) as $class)
		{
			$pre = config_item('subclass_prefix');
			$file = 'libraries/'.$subdir.$pre.$class.'.php';
			foreach ($this->_ci_library_paths as $path)
			{
				// Is this a class extension request?
				$subclass = $path.$file;
				if (file_exists($subclass))
				{
					// Found extension - require base class
					$baseclass = BASEPATH.'libraries/'.ucfirst($class).'.php';
					if ( ! file_exists($baseclass))
					{
						log_message('error', 'Unable to load the requested class: '.$class);
						show_error('Unable to load the requested class: '.$class);
					}

					// Safety: Was the class already loaded by a previous call?
					if (in_array($subclass, $this->_ci_loaded_files))
					{
						// Before we deem this to be a duplicate request, let's see
						// if a custom object name is being supplied. If so, we'll
						// return a new instance of the object
						if ( ! is_null($object_name))
						{
							if ( ! isset($this->CI->$object_name))
							{
								return $this->_ci_init_class($class, $pre, $params, $object_name);
							}
						}

						$is_duplicate = TRUE;
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

			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;
			foreach ($this->_ci_library_paths as $path)
			{
				$filepath = $path.'libraries/'.$subdir.$class.'.php';

				// Does the file exist? No? Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}

				// Safety: Was the class already loaded by a previous call?
				if (in_array($filepath, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied. If so, we'll
					// return a new instance of the object
					if ( ! is_null($object_name))
					{
						if ( ! isset($this->CI->$object_name))
						{
							return $this->_ci_init_class($class, '', $params, $object_name);
						}
					}

					$is_duplicate = TRUE;
					log_message('debug', $class.' class already loaded. Second attempt ignored.');
					return;
				}

                // If this looks like a driver, make sure the base class is loaded
                if (strtolower($subdir) == strtolower($class).'/' && !class_exists('CI_Driver_Library'))
                {
                    // We aren't instantiating an object here, that'll be done by the Library itself
                    require BASEPATH.'libraries/Driver.php';
                }

				include_once($filepath);
				$this->_ci_loaded_files[] = $filepath;
				return $this->_ci_init_class($class, '', $params, $object_name);
			}
		} // END FOREACH

		// One last attempt. Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir == '')
		{
			$path = strtolower($class).'/'.$class;
			return $this->_ci_load_class($path, $params);
		}

		// If we got this far we were unable to find the requested class.
		// We do not issue errors if the load call failed due to a duplicate request
		if ($is_duplicate == FALSE)
		{
			log_message('error', 'Unable to load the requested class: '.$class);
			show_error('Unable to load the requested class: '.$class);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Instantiates a class
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @param	string	an optional object name
	 * @return	null
	 */
	protected function _ci_init_class($class, $prefix = '', $config = FALSE, $object_name = NULL)
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

		if ($prefix == '')
		{
			if (class_exists('CI_'.$class))
			{
				$name = 'CI_'.$class;
			}
			elseif (class_exists(config_item('subclass_prefix').$class))
			{
				$name = config_item('subclass_prefix').$class;
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
			show_error('Non-existent class: '.$class);
		}

		// Set the variable name we will assign the class to
		// Was a custom class name supplied? If so we'll use it
		$class = strtolower($class);

		if (is_null($object_name))
		{
			$classvar = ( ! isset($this->_ci_varmap[$class])) ? $class : $this->_ci_varmap[$class];
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
	 * However, there is no reason you should ever needs to use it.
	 *
	 * @param	array
	 * @return	void
	 */
	public function ci_autoloader()
	{
		// Set base classes to prevent overwriting core modules
		$this->_base_classes =& is_loaded();

		// Check for autoload array
		if ( ! isset($this->_ci_autoload))
		{
			return FALSE;
		}

		// Autoload helpers and languages
		foreach (array('helper', 'language') as $type)
		{
			if (isset($this->_ci_autoload[$type]) AND count($this->_ci_autoload[$type]) > 0)
			{
				$this->$type($this->_ci_autoload[$type]);
			}
		}

		// A little tweak to remain backward compatible
		// The $this->_ci_autoload['core'] item was deprecated
		if ( ! isset($this->_ci_autoload['libraries']) AND isset($this->_ci_autoload['core']))
		{
			$this->_ci_autoload['libraries'] = $this->_ci_autoload['core'];
		}

		// Load libraries
		if (isset($this->_ci_autoload['libraries']) AND count($this->_ci_autoload['libraries']) > 0)
		{
			// Load the database driver.
			if (in_array('database', $this->_ci_autoload['libraries']))
			{
				$this->database();
				$this->_ci_autoload['libraries'] = array_diff($this->_ci_autoload['libraries'], array('database'));
			}

			// Load all other libraries
			foreach ($this->_ci_autoload['libraries'] as $item)
			{
				$this->library($item);
			}
		}

		// Autoload controllers
		if (isset($this->_ci_autoload['controller']))
		{
			$controller = $this->_ci_autoload['controller'];
			if ( ! is_array($controller))
			{
				$controller = array($controller);
			}

			// We have to "manually" feed multiples to controller(), since an array
			// is treated as a router stack instead of more than one controller
			foreach ($controller as $uri)
			{
				$this->controller($uri);
			}
		}

		// Autoload models
		if (isset($this->_ci_autoload['model']))
		{
			$this->model($this->_ci_autoload['model']);
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
		return (is_object($object)) ? get_object_vars($object) : $object;
	}
}

/* End of file Loader.php */
/* Location: ./system/core/Loader.php */
