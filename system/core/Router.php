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
 * Router Class
 *
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Libraries
 * @link		http://codeigniter.com/user_guide/general/routing.html
 */
class CI_Router {
	/**
	 * CodeIgniter core
	 *
	 * @var		object
	 * @access	protected
	 */
	protected $CI;

	/**
	 * List of routes
	 *
	 * @var		array
	 * @access	public
	 */
	protected $routes;

	/**
	 * Stack of route data
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $route_stack;

	/**
	 * Default controller (and method if specific)
	 *
	 * @var		string
	 * @access	public
	 */
	protected $default_controller;

	/**
	 * Route stack index constants
	 */
	const SEG_PATH = 0;
	const SEG_SUBDIR = 1;
	const SEG_CLASS = 2;
	const SEG_METHOD = 3;
	const SEG_ARGS = 4;

	/**
	 * Constructor
	 *
	 * Runs the route mapping function.
	 */
	public function __construct()
	{
		$this->CI =& CodeIgniter::instance();
		$this->route_stack = array('', '', '', '');
		log_message('debug', 'Router Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Set the route mapping
	 *
	 * This function determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @access	private
	 * @return	void
	 */
	public function _set_routing()
	{
		// Load the routes.php file.
		$route = $this->CI->config->get('routes.php', 'route');

		// Set routes
		$this->routes = is_array($route) ? $route : array();
		unset($route);

		// Set the default controller so we can display it in the event
		// the URI doesn't correlate to a valid controller.
		$this->default_controller = (isset($this->routes['default_controller']) &&
			$this->routes['default_controller'] != '') ? strtolower($this->routes['default_controller']) : FALSE;

		// Are query strings enabled in the config file? Normally CI doesn't utilize query strings
		// since URI segments are more search-engine friendly, but they can optionally be used.
		// If this feature is enabled, we will gather the directory/class/method a little differently
		$uri =& $this->CI->uri;
		$config =& $this->CI->config;
		$ctl_trigger = $config->item('controller_trigger');
		if ($config->item('enable_query_strings') === TRUE && isset($_GET[$ctl_trigger]))
		{
			$segments = array();

			// Add directory segment if provided
			$dir_trigger = $config->item('directory_trigger');
			if (isset($_GET[$dir_trigger]))
			{
				$segments[] = trim($uri->_filter_uri($_GET[$dir_trigger]));
			}

			// Add controller segment - this was qualified above
			$class = trim($uri->_filter_uri($_GET[$ctl_trigger]));
			$segments[] = $class;

			// Add function segment if provided
			$fun_trigger = $config->item('function_trigger');
			if (isset($_GET[$fun_trigger]))
			{
				$segments[] = trim($uri->_filter_uri($_GET[$fun_trigger]));
			}

			// Determine if segments point to a valid route
			$route = $this->validate_route($segments);
			if ($route === FALSE)
			{
				// Invalid request - show a 404
				show_404($class);
			}

			// Set route stack and clean directory and class
			$this->route_stack = $route;
			$this->set_directory($route[self::SEG_SUBDIR]);
			$this->set_class($route[self::SEG_CLASS]);
			return;
		}

		// Fetch the complete URI string
		$uri->_fetch_uri_string();

		// Do we need to remove the URL suffix?
		$uri->_remove_url_suffix();

		// Compile the segments into an array
		$uri->_explode_segments();

		// Parse any custom routing that may exist
		// The default route will be applied if valid and necessary
		$this->_parse_routes();
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Route
	 *
	 * This function takes an array of URI segments as
	 * input, and sets the current class/method
	 *
	 * @access	protected
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	protected function _set_request($segments = array())
	{
		// Determine if segments point to a valid route
		$route = $this->validate_route($segments);
		if ($route === FALSE)
		{
			// Invalid request - show a 404
			$page = isset($segments[0]) ? $segments[0] : '';
			show_404($page);
		}

		// Set route stack and clean directory and class
		$this->route_stack = $route;
		$this->set_directory($route[self::SEG_SUBDIR]);
		$this->set_class($route[self::SEG_CLASS]);

		// Update our "routed" segment array to contain the segments without the path or directory.
		// Note: If there is no custom routing, this array will be
		// identical to $this->CI->uri->segments
		$this->CI->uri->rsegments = array_slice($route, self::SEG_CLASS);

		// Re-index the segment array so that it starts with 1 rather than 0
		$this->CI->uri->_reindex_segments();
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the supplied segments.
	 *
	 * This function attempts to determine the path to the controller.
	 * On success, a complete array of at least 4 segments is returned:
	 *	array(
	 *		0 => $path,		// Package path where Controller found
	 *		1 => $subdir,	// Subdirectory, which may be ''
	 *		2 => $class,	// Validated Controller class
	 *		3 => $method,	// Method, which may be 'index'
	 *		...				// Any remaining segments
	 *	);
	 *
	 * @access	public
	 * @param	array	route segments
	 * @return	mixed	FALSE if route doesn't exist, otherwise array of 4+ segments
	 */
	public function validate_route($route)
	{
		// If we don't have any segments, the default will have to do
		if (count($route) == 0)
		{
			$route = $this->_default_segments();
			if (empty($route)) {
				// No default - fail
				return FALSE;
			}
		}

		// Explode route if not already segmented
		if (!is_array($route)) {
			$route = explode('/', $route);
		}

		// Search paths for controller
		foreach ($this->CI->load->get_package_paths() as $path)
		{
			// Does the requested controller exist in the base folder?
			if (file_exists($path.'controllers/'.$route[0].'.php'))
			{
				// Found it - append method if missing
				if ( ! isset($route[1]))
				{
					$route[] = 'index';
				}

				// Prepend path and empty directory and return
				return array_merge(array($path, ''), $route);
			}

			// Is the controller in a sub-folder?
			if (is_dir($path.'controllers/'.$route[0]))
			{
				// Found a sub-folder - is there a controller name?
				if (isset($route[1]))
				{
					// Yes - get class and method
					$class = $route[1];
					$method = isset($route[2]) ? $route[2] : 'index';
				}
				else
				{
					// Get default controller segments
					$default = $this->_default_segments();
					if (empty($default))
					{
						// No default controller to apply - carry on
						unset($default);
						continue;
					}

					// Get class and method
					$class = array_unshift($default);
					$method = array_unshift($default);
				}

				// Does the requested controller exist in the sub-folder?
				if (file_exists($path.'controllers/'.$route[0].$class.'.php'))
				{
					// Found it - assemble segments
					if ( ! isset($route[1]))
					{
						$route[] = $class;
					}
					if ( ! isset($route[2]))
					{
						$route[] = $method;
					}
					if (isset($default) && count($default) > 0)
					{
						$route = array_merge($route, $default);
					}

					// Prepend path and return
					array_unshift($route, $path);
					return $route;
				}
			}
		}

		// If we got here, no valid route was found
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse Routes
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _parse_routes()
	{
		// Turn the segment array into a URI string
		$segments = $this->CI->uri->segments;
		$uri = implode('/', $segments);

		// Is there a literal match? If so we're done
		if (isset($this->routes[$uri]))
		{
			return $this->_set_request(explode('/', $this->routes[$uri]));
		}

		// Loop through the route array looking for wild-cards
		foreach ($this->routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri))
			{
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				return $this->_set_request(explode('/', $val));
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_request($segments);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the class name
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_class($class)
	{
		$this->route_stack[self::SEG_CLASS] = str_replace(array('/', '.'), '', $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current class
	 *
	 * @return	string
	 */
	public function fetch_class()
	{
		return $this->route_stack[self::SEG_CLASS];
	}

	// --------------------------------------------------------------------

	/**
	 * Set the method name
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_method($method)
	{
		$this->route_stack[self::SEG_METHOD] = $method;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current method
	 *
	 * @return	string
	 */
	public function fetch_method()
	{
		$method = $this->route_stack[self::SEG_METHOD];
		if ($method == $this->fetch_class())
		{
			return 'index';
		}

		return $method;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the directory name
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_directory($dir)
	{
		$this->route_stack[self::SEG_SUBDIR] = $dir == '' ? '' : str_replace(array('/', '.'), '', $dir).'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the sub-directory (if any) that contains the requested controller class
	 *
	 * @return	string
	 */
	public function fetch_directory()
	{
		return $this->route_stack[self::SEG_SUBDIR];
	}

	// --------------------------------------------------------------------

	/**
	 * Set the package path
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_path($path)
	{
		$this->route_stack[self::SEG_PATH] = $path;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current package path
	 *
	 * @return	string
	 */
	public function fetch_path()
	{
		return $this->route_stack[self::SEG_PATH];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current route stack
	 *
	 * @return	array
	 */
	public function fetch_route()
	{
		return $this->route_stack;
	}

	// --------------------------------------------------------------------

	/**
	 * Get error route
	 *
	 * Identifies the 404 or error override route, if defined, and validates it.
	 *
	 * @param	boolean	TRUE for 404 route
	 * @return	mixed	FALSE if route doesn't exist, otherwise array of 4+ segments
	 */
	public function get_error_route($is404 = FALSE) {
		// Select route
		$route = ($is404 ? '404' : 'error').'_override';

		// See if 404_override is defined
		if (empty($this->routes[$route])) {
			// No override to apply
			return FALSE;
		}

		// Return validated override path
		return $this->validate_route($this->routes[$route]);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the controller overrides
	 *
	 * @access	private
	 * @param	array
	 * @return	null
	 */
	public function _set_overrides($routing)
	{
		if ( ! is_array($routing))
		{
			return;
		}

		if (isset($routing['directory']))
		{
			$this->set_directory($routing['directory']);
		}

		if (isset($routing['controller']) AND $routing['controller'] != '')
		{
			$this->set_class($routing['controller']);
		}

		if (isset($routing['function']))
		{
			$routing['function'] = ($routing['function'] == '') ? 'index' : $routing['function'];
			$this->set_method($routing['function']);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get segments of default controller
	 *
	 * @access	protected
	 * @return	array	array of segments
	 */
	protected function _default_segments()
	{
		// Check for default controller
		if ($this->default_controller === FALSE)
		{
			// Return empty array
			return array();
		}

		// Break out default controller
		$default = explode('/', $this->default_controller);
		if ( ! isset($default[1]))
		{
			// Add default method
			$default[] = 'index';
		}

		return $default;
	}
}
// END Router Class

/* End of file Router.php */
/* Location: ./system/core/Router.php */
