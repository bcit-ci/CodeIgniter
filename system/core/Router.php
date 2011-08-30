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
 * Router Class
 *
 * Parses URIs and determines routing
 * The base class, CI_CoreShare, is defined in CodeIgniter.php and allows
 * access to protected methods between CodeIgniter, Router, and URI.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Libraries
 * @link		http://codeigniter.com/user_guide/general/routing.html
 */
class CI_Router extends CI_CoreShare {
	/**
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI			= NULL;

	/**
	 * List of routes
	 *
	 * @var array
	 * @access	protected
	 */
	protected $routes		= array();

	/**
	 * Default controller (and method if specific)
	 *
	 * @var string
	 * @access public
	 */
	protected $default_controller;

	/**
	 * Route information stack - see indexes below
	 *
	 * @var array
	 * @access	protected
	 */
	protected $route_stack	= array('', '', '', '');

	// Segment stack indexes
	const SEG_PATH = 0;
	const SEG_SUBDIR = 1;
	const SEG_CLASS = 2;
	const SEG_METHOD = 3;
	const SEG_ARGS = 4;

	/**
	 * Constructor
	 *
	 * Runs the route mapping function.
	 *
	 * @param	object	parent reference
	 */
	public function __construct(CodeIgniter $CI)
	{
		// Attach parent reference
		$this->CI =& $CI;
		$CI->log_message('debug', 'Router Class Initialized');
	}

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
	 * @param	mixed	route URI string or array of route segments
	 * @return	mixed	FALSE if route doesn't exist, otherwise array of 4+ segments
	 */
	public function validate_route($route)
	{
		// If we don't have any segments, the default will have to do
		if (empty($route))
		{
			$route = $this->_default_segments();
			if (empty($route))
			{
				// No default - fail
				return FALSE;
			}
		}

		// Explode route if not already segmented
		if (!is_array($route))
		{
			$route = explode('/', $route);
		}

		// Prepare case options
		$name = strtolower($route[0]);
		$names = array(ucfirst($name), $name);

		// Search paths for controller
		foreach ($this->CI->get_package_paths() as $path)
		{
			// Append subdirectory
			$file_path = $path.'controllers/';

			foreach ($names as $name)
			{
				// Does the requested controller exist in the base folder?
				if (file_exists($file_path.$name.'.php'))
				{
					// Found it - append method if missing
					if (!isset($route[1]))
					{
						$route[] = 'index';
					}

					// Prepend path and empty directory and return
					return array_merge(array($path, ''), $route);
				}

				// Is the controller in a sub-folder?
				if (is_dir($file_path.$name))
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
					if (file_exists($file_path.$name.$class.'.php'))
					{
						// Found it - assemble segments
						if (!isset($route[1]))
						{
							$route[] = $class;
						}
						if (!isset($route[2]))
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
		}

		// If we got here, no valid route was found
		return FALSE;
	}

	/**
	 * Set the package path
	 *
	 * @param	string	package path
	 * @return	void
	 */
	public function set_path($path)
	{
		$this->route_stack[self::SEG_PATH] = $path;
	}

	/**
	 * Fetch the current package path
	 *
	 * @return	string	package path
	 */
	public function fetch_path()
	{
		return $this->route_stack[self::SEG_PATH];
	}

	/**
	 * Set the directory name
	 *
	 * @param	string	directory name
	 * @return	void
	 */
	public function set_directory($dir)
	{
		$this->route_stack[self::SEG_SUBDIR] = $dir == '' ? '' : str_replace(array('/', '.'), '', $dir).'/';
	}

	/**
	 * Fetch the sub-directory (if any) that contains the requested controller class
	 *
	 * @return	string	directory name
	 */
	public function fetch_directory()
	{
		return $this->route_stack[self::SEG_SUBDIR];
	}

	/**
	 * Set the class name
	 *
	 * @param	string	class name
	 * @return	void
	 */
	public function set_class($class)
	{
		$this->route_stack[self::SEG_CLASS] = str_replace(array('/', '.'), '', $class);
	}

	/**
	 * Fetch the current class
	 *
	 * @return	string	class name
	 */
	public function fetch_class()
	{
		return $this->route_stack[self::SEG_CLASS];
	}

	/**
	 * Set the method name
	 *
	 * @param	string	method name
	 * @return	void
	 */
	public function set_method($method)
	{
		$this->route_stack[self::SEG_METHOD] = ($method == '' ? 'index' : $method);
	}

	/**
	 * Fetch the current method
	 *
	 * @return	string	method name
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

	/**
	 * Fetch the current route stack
	 *
	 * @return	array	route stack
	 */
	public function fetch_route()
	{
		return $this->route_stack;
	}

	/**
	 * Get error route
	 *
	 * Identifies error override route, if defined, and validates it.
	 *
	 * @param	string	error template name ('general', '404', 'php')
	 * @return	mixed	FALSE if route doesn't exist, otherwise array of 4+ segments
	 */
	public function get_error_route($template)
	{
		// Select route
		$route = ($template == 'general' ? 'error' : $template).'_override';

		// See if override is defined
		if (empty($this->routes[$route]))
		{
			// No override to apply
			return FALSE;
		}

		// Return validated override path
		return $this->validate_route($this->routes[$route]);
	}

	/**
	 * Set the route mapping
	 *
	 * This function determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 * The CodeIgniter object calls this protected method via CI_CoreShare.
	 *
	 * @access	protected
	 * @param	array	route overrides
	 * @return	void
	 */
	protected function _set_routing($overrides)
	{
		// Force overrides to array
		if (!is_array($overrides))
		{
			$overrides = array();
		}

		// Load the routes.php file.
		$route = CodeIgniter::get_config('routes.php', 'route');
		if (is_array($route))
		{
			$this->routes = $route;
		}

		// Set the default controller so we can display it in the event
		// the URI doesn't correlate to a valid controller.
		$this->default_controller = (isset($this->routes['default_controller']) &&
			$this->routes['default_controller'] != '') ? strtolower($this->routes['default_controller']) : FALSE;

		// Fetch the complete URI string and turn the segment array into a URI string
		$segments = $this->_call_core($this->CI->uri, '_load_uri');
		$uri = implode('/', $segments);

		// Is there a literal route match? If so we're done
		if (isset($this->routes[$uri]))
		{
			return $this->_set_route($this->routes[$uri], $overrides);
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
				if (strpos($val, '$') !== FALSE && strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				return $this->_set_route($val, $overrides);
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_route($segments, $overrides);
	}

	/**
	 * Set the Route
	 *
	 * This helper function takes an array of URI segments as input, and sets the current class/method
	 * It should only be called internally
	 *
	 * @access	protected
	 * @param	mixed	route URI string or array of route segments
	 * @param	array	routing	overrides
	 * @return	void
	 */
	protected function _set_route($route, array $overrides)
	{
		// Save original request in case of 404
		$uri = is_array($route) ? implode('/', $route) : $route;

		// Determine if route is valid
		$route = $this->validate_route($route);
		if ($route === FALSE)
		{
			// Invalid request - show a 404
			throw new CI_ShowError('The page you requested was not found.', '404 Page Not Found', 404,
				'404 Page Not Found --> '.$uri, 'error_404');
		}

		// Set route stack and process
		$this->route_stack = $route;
		if (isset($overrides['directory']))
		{
			// Override directory
			$this->set_directory($overrides['directory']);
		}
		else
		{
			// Clean directory entry
			$this->set_directory($route[self::SEG_SUBDIR]);
		}
		if (isset($overrides['controller']))
		{
			// Override class
			$this->set_class($overrides['controller']);
		}
		else
		{
			// Clean class entry
			$this->set_class($route[self::SEG_CLASS]);
		}
		if (isset($overrides['function']))
		{
			// Override method
			$this->set_method($overrides['function']);
		}

		// Update our "routed" segment array to contain the segments without the path or directory.
		// Note: If there is no custom routing, this array will be identical to URI->segments
		$this->_call_core($this->CI->uri, '_routed', array_slice($route, self::SEG_CLASS));
	}

	/**
	 * Get segments of default controller
	 *
	 * This helper function breaks the default controller, if any, into segments
	 * It should only be called internally
	 *
	 * @access	protected
	 * @return	array	array of segments
	 */
	protected function _default_segments()
	{
		// Check for default controller
		if (empty($this->default_controller))
		{
			// Return empty array
			return array();
		}

		// Break out default controller
		$default = explode('/', $this->default_controller);
		if (!isset($default[1]))
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
