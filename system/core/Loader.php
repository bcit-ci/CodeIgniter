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

/**
 * Loader Class
 *
 * Loads resources (libraries, controllers, views, etc.) into CodeIgniter.
 * The base class, CI_CoreShare, is defined in CodeIgniter.php and allows
 * Loader access to protected loading methods in CodeIgniter.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Loader
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class CI_Loader extends CI_CoreShare {
	/**
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI			= NULL;

	/**
	 * List of cached variables
	 *
	 * @var array
	 * @access protected
	 */
	protected $cached_vars	= array();

	/**
	 * List of class name mappings
	 *
	 * @var array
	 * @access protected
	 */
	protected $varmap		= array('unit_test' => 'unit', 'user_agent' => 'agent');

	/**
	 * Constructor
	 *
	 * Sets default package paths, gets the initial output buffering level,
	 * and autoloads additional paths and config files
	 *
	 * @param	object	parent reference
	 */
	public function __construct(CodeIgniter $CI) {
		// Attach parent reference
		$this->CI =& $CI;
		$CI->log_message('debug', 'Loader Class Initialized');
	}

	/**
	 * Library loader
	 *
	 * This function lets users load and instantiate library classes.
	 *
	 * @param	mixed	the name of the class or an array of names
	 * @param	array	optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	public function library($class, array $params = NULL, $obj_name = NULL) {
		// Check for missing class
		if (empty($class)) {
			return FALSE;
		}

		// Delegate multiples
		if (is_array($class)) {
			foreach ($class as $class) {
				$this->library($class, $params);
			}

			return;
		}

		// Parse out the filename and path.
		$subdir = $this->_get_path($class);

		// Set object name if not provided
		if (is_null($obj_name)) {
			$obj_name = isset($this->varmap[$class]) ? $this->varmap[$class] : strtolower($class);
		}

		// Load object in core
		$this->_call_core($this->CI, '_load', 'library', $class, $obj_name, $params, $subdir);
	}

	/**
	 * Driver loader
	 *
	 * Loads a driver library
	 *
	 * @param	string	the name of the class
	 * @param	array	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	public function driver($class, array $params = NULL, $obj_name = NULL) {
		if (!class_exists('CI_Driver_Library')) {
			// we aren't instantiating an object here, that'll be done by the Library itself
			require BASEPATH.'libraries/Driver.php';
		}

		// We can save the loader some time since Drivers will *always* be in a subfolder,
		// and typically identically named to the library
		if (!strpos($class, '/')) {
			$class = ucfirst($class).'/'.$class;
		}

		return $this->library($class, $params, $obj_name);
	}

	/**
	 * Helper loader
	 *
	 * This function loads the specified helper file.
	 *
	 * @param	mixed	the name of the helper or an array of names
	 * @return	void
	 */
	public function helper($helper) {
		// Check for missing name
		if (empty($helper)) {
			return FALSE;
		}

		// Delegate multiples
		if (is_array($helper)) {
			foreach ($helper as $help) {
				$this->helper($help);
			}
			return;
		}

		// Parse out the filename and path and make sure _helper suffix is attached
		$subdir = $this->_get_path($helper);
		if (substr($helper, -7) != '_helper') {
			$helper .= '_helper';
		}

		// Load helper in core
		$this->_call_core($this->CI, '_load', 'helper', $helper, FALSE, NULL, $subdir);
	}

	/**
	 * Helpers loader
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @param	array
	 * @return	void
	 */
	public function helpers($helpers = array()) {
		$this->helper($helpers);
	}

	/**
	 * Controller Loader
	 *
	 * This function lets users load and instantiate (sub)controllers.
	 * It accepts the controller route as a string, or an array of
	 * segments already parsed by CI_Route::validate_route(), and automatically
	 * calls the specified method (or 'index'), unless $call is FALSE.
	 *
	 * @access	public
	 * @param	string	the URI route
	 * @param	string	object name for the controller
	 * @param	boolean	FALSE to skip calling controller method
	 * @return	boolean TRUE on success, otherwise FALSE
	 */
	public function controller($route, $obj_name = '', $call = TRUE) {
		// Check for missing route
		if (empty($route)) {
			return FALSE;
		}

		// Get instance and establish route stack
		if (is_array($route)) {
			// Assume segments have been pre-parsed by CI_Router::validate_route() - make sure there's 4
			if (count($route) < 4) {
				return FALSE;
			}
		}
		else {
			// Call validate_route() to break URI into segments
			$route = $this->CI->router->validate_route(explode('/', $route));
			if ($route === FALSE) {
				return FALSE;
			}
		}

		// Extract segment parts
		$path = array_shift($route);
		$subdir = array_shift($route);
		$class = array_shift($route);
		$method = array_shift($route);

		// Load object in core
		$this->_call_core($this->CI, '_load', 'controller', $class, $obj_name, NULL, $subdir, $path);

		// Check if call is disabled
		if ($call) {
			// Pass any remaining route segments as arguments to the call
			return $this->_call_core($this->CI, '_call_controller', $class, $method, $route, $obj_name);
		}

		return TRUE;
	}

	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @param	string	the name of the class
	 * @param	string	an optional object name
	 * @param	mixed	database connection name or TRUE to load default
	 * @return	void
	 */
	public function model($class, $obj_name = '', $db_conn = FALSE) {
		// Check for missing class
		if ($class == '') {
			return;
		}

		// Delegate multiples
		if (is_array($class)) {
			foreach ($class as $babe) {
				$this->model($babe);
			}
			return;
		}

		// Parse out the filename and path.
		$subdir = $this->_get_path($class);

		// Load database if needed
		if ($db_conn !== FALSE && !class_exists('CI_DB')) {
			if ($db_conn === TRUE) {
				$db_conn = '';
			}

			$this->database($db_conn, FALSE, TRUE);
		}

		// Load object in core
		$this->_call_core($this->CI, '_load', 'model', $class, $obj_name, NULL, $subdir);
	}

	/**
	 * View loader
	 *
	 * This function is used to load a "view" file.
	 * You can either set variables using the dedicated vars() function or
	 * via the second parameter of this function. We'll merge the two types and
	 * cache them so that views that are embedded within other views can have
	 * access to these variables.
	 *
	 * @param	string	view name
	 * @param	array	associative array of local variables for the view
	 * @param	bool	TRUE to return the output
	 * @return	mixed	output if $return is TRUE, otherwise void
	 */
	public function view($view, array $vars = array(), $return = FALSE) {
		// Append any vars to cache
		if (!empty($vars)) {
			$this->vars($vars);
		}

		// Run file in core context
		return $this->_call_core($this->CI, '_run_file', $view, TRUE, $return, $this->cached_vars);
	}

	/**
	 * Language file loader
	 *
	 * @param	mixed	file name or array of names
	 * @param	string	language name
	 * @return	void
	 */
	public function language($file, $lang = '') {
		// Force file to array
		if (!is_array($file)) {
			$file = array($file);
		}

		// Load each file via Lang
		foreach ($file as $langfile) {
			$this->CI->lang->load($langfile, $lang);
		}
	}

	/**
	 * Config file loader
	 *
	 * @param	mixed	file name or array of names
	 * @param	boolean	if configuration values should be loaded into their own section
	 * @param	boolean	TRUE if errors should just return FALSE, otherwise an error message is displayed
	 * @return	void
	 */
	public function config($file, $use_sections = FALSE, $fail_gracefully = FALSE) {
		// Force file to array
		if (!is_array($file)) {
			$file = array($file);
		}

		// Load each file via Config
		foreach ($file as $config) {
			$this->CI->config->load($config, $use_sections, $fail_gracefully);
		}
	}

	/**
	 * Database Loader
	 *
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */
	public function database($params = '', $return = FALSE, $active_record = NULL) {
		// Do we even need to load the database class?
		if (class_exists('CI_DB') && $return == FALSE && $active_record == NULL && isset($this->CI->db) &&
		is_object($this->CI->db)) {
			return FALSE;
		}

		require_once(BASEPATH.'database/DB.php');

		if ($return === TRUE) {
			return DB($params, $active_record);
		}

		// Initialize the db variable. Needed to prevent
		// reference errors with some configurations
		$this->CI->db = '';

		// Load the DB class
		$this->CI->db =& DB($params, $active_record);
	}

	/**
	 * Load the Utilities Class
	 *
	 * @return	void
	 */
	public function dbutil() {
		if (!class_exists('CI_DB')) {
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

	/**
	 * Load the Database Forge Class
	 *
	 * @return	void
	 */
	public function dbforge() {
		if (!class_exists('CI_DB')) {
			$this->database();
		}

		$driver = $this->CI->db->dbdriver;
		require_once(BASEPATH.'database/DB_forge.php');
		require_once(BASEPATH.'database/drivers/'.$driver.'/'.$driver.'_forge.php');
		$class = 'CI_DB_'.$driver.'_forge';

		$this->CI->dbforge = new $class();
	}

	/**
	 * User file loader
	 *
	 * This is a generic file loader
	 *
	 * @param	string	file path
	 * @param	bool	TRUE to return output
	 * @return	mixed	output if $return is TRUE, otherwise void
	 */
	public function file($path, $return = FALSE) {
		// Run file in core context
		return $this->_call_core($this->CI, '_run_file', $path, FALSE, $return);
	}

	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within
	 * the controller class and its "view" files.
	 *
	 * @param	mixed	variable name or array of vars
	 * @param	mixed	variable value
	 * @return	void
	 */
	public function vars($vars = array(), $val = NULL) {
		// Handle non-array arguments
		if ($val != NULL && is_string($vars)) {
			$vars = array($vars => $val);
		}
		else if (is_object($vars)) {
			$vars = get_object_vars($vars);
		}

		// Set values into cached vars
		if (is_array($vars) && count($vars) > 0) {
			foreach ($vars as $key => $val) {
				$this->cached_vars[$key] = $val;
			}
		}
	}

	/**
	 * Get Variable
	 *
	 * Check if a variable is set and retrieve it.
	 *
	 * @param	string	var key
	 * @return	mixed	var value
	 */
	public function get_var($key) {
		// Return cached variable or NULL
		return isset($this->cached_vars[$key]) ? $this->cached_vars[$key] : NULL;
	}

	/**
	 * Add Package Path
	 *
	 * Adds a package path to the list
	 *
	 * @param	string	path
	 * @param 	boolean view cascade flag
	 * @param	boolean	add to config path flag
	 * @return	void
	 */
	public function add_package_path($path, $view_cascade = TRUE, $add_config_path = TRUE) {
		// Pass arguments to core method
		$this->CI->add_package_path($path, $view_cascade, $add_config_path);
	}

	/**
	 * Remove Package Path
	 *
	 * Remove a package path from the list
	 * If no path is provided, the most recently added path is removed.
	 *
	 * @param	string	path
	 * @param	boolean remove from config path flag
	 * @return	void
	 */
	public function remove_package_path($path = '', $remove_config_path = TRUE) {
		// Pass arguments to core method
		$this->CI->remove_packate_path($path, $remove_config_path);
	}

	/**
	 * Get Package Paths
	 *
	 * Return a list of all package paths, by default it will ignore BASEPATH.
	 *
	 * @param	boolean include base path flag
	 * @return	void
	 */
	public function get_package_paths($include_base = FALSE) {
		// Pass arguments to core method
		return $this->CI->get_package_paths($include_base);
	}

	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits various
	 * resources to be loaded automatically.
	 * The CodeIgniter object calls this protected method via CI_CoreShare.
	 *
	 * @access	protected
	 * @param	array	autoload array
	 * @return	void
	 */
	protected function _autoloader($autoload) {
		// Autoload languages
		if (isset($autoload['language']) && count($autoload['language']) > 0) {
			$this->language($autoload['language']);
		}

		// A little tweak to remain backward compatible
		// The $autoload['core'] item was deprecated
		if (!isset($autoload['libraries']) && isset($autoload['core'])) {
			$autoload['libraries'] = $autoload['core'];
		}

		// Load libraries
		if (isset($autoload['libraries']) && count($autoload['libraries']) > 0) {
			// Load the database driver.
			if (in_array('database', $autoload['libraries'])) {
				$this->database();
				$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
			}

			// Load all other libraries
			foreach ($autoload['libraries'] as $item) {
				$this->library($item);
			}
		}

		// Autoload controllers and models
		foreach (array('helper', 'controller', 'model') as $type) {
			if (isset($autoload[$type])) {
				// Force item to array
				$items = is_array($autoload[$type]) ? $autoload[$type] : array($autoload[$type]);
				foreach ($items as $item) {
					$this->$type($item);
				}
			}
		}
	}

	/**
	 * Get path from filename
	 *
	 * This helper function separates dirname, if present, from file.
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @param	string	reference to filename (to be modified)
	 * @return	string	path name
	 */
	protected function _get_path(&$file) {
		// Get any leading dirname without a leading slash
		$path = dirname(ltrim($file, '/'));

		// Strip filename to basename
		$file = basename($file, '.php');

		// Return leading dirname, if any
		return ($path == '.') ? '' : $path.'/';
	}
}

/* End of file Loader.php */
/* Location: ./system/core/Loader.php */
