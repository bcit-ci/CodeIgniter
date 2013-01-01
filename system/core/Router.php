<?php
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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Router Class
 *
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/general/routing.html
 */
class CI_Router {

	/**
	 * CI_Config class object
	 *
	 * @var	object
	 */
	public $config;

	/**
	 * List of routes
	 *
	 * @var	array
	 */
	public $routes =	array();

	/**
	 * Current class name
	 *
	 * @var	string
	 */
	public $class =		'';

	/**
	 * Current method name
	 *
	 * @var	string
	 */
	public $method =	'index';

	/**
	 * Sub-directory that contains the requested controller class
	 *
	 * @var	string
	 */
	public $directory =	'';

	/**
	 * Default controller (and method if specific)
	 *
	 * @var	string
	 */
	public $default_controller;

	/**
	 * Class constructor
	 *
	 * Runs the route mapping function.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->config =& load_class('Config', 'core');
		$this->uri =& load_class('URI', 'core');
		log_message('debug', 'Router Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Set route mapping
	 *
	 * Determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @return	void
	 */
	public function _set_routing()
	{
		// Are query strings enabled in the config file? Normally CI doesn't utilize query strings
		// since URI segments are more search-engine friendly, but they can optionally be used.
		// If this feature is enabled, we will gather the directory/class/method a little differently
		$segments = array();
		if ($this->config->item('enable_query_strings') === TRUE
			&& ! empty($_GET[$this->config->item('controller_trigger')])
			&& is_string($_GET[$this->config->item('controller_trigger')])
		)
		{
			if (isset($_GET[$this->config->item('directory_trigger')]) && is_string($_GET[$this->config->item('directory_trigger')]))
			{
				$this->set_directory(trim($this->uri->_filter_uri($_GET[$this->config->item('directory_trigger')])));
				$segments[] = $this->fetch_directory();
			}

			$this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));
			$segments[] = $this->fetch_class();

			if ( ! empty($_GET[$this->config->item('function_trigger')]) && is_string($_GET[$this->config->item('function_trigger')]))
			{
				$this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
				$segments[] = $this->fetch_method();
			}
		}

		// Load the routes.php file.
		if (defined('ENVIRONMENT') && is_file(APPPATH.'config/'.ENVIRONMENT.'/routes.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/routes.php');
		}
		elseif (is_file(APPPATH.'config/routes.php'))
		{
			include(APPPATH.'config/routes.php');
		}

		$this->routes = (empty($route) OR ! is_array($route)) ? array() : $route;
		unset($route);

		// Set the default controller so we can display it in the event
		// the URI doesn't correlated to a valid controller.
		$this->default_controller = empty($this->routes['default_controller']) ? FALSE : $this->routes['default_controller'];

		// Were there any query string segments? If so, we'll validate them and bail out since we're done.
		if (count($segments) > 0)
		{
			return $this->_validate_request($segments);
		}

		// Fetch the complete URI string
		$this->uri->_fetch_uri_string();

		// Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
		if ($this->uri->uri_string == '')
		{
			return $this->_set_default_controller();
		}

		$this->uri->_remove_url_suffix(); // Remove the URL suffix
		$this->uri->_explode_segments(); // Compile the segments into an array
		$this->_parse_routes(); // Parse any custom routing that may exist
		$this->uri->_reindex_segments(); // Re-index the segment array so that it starts with 1 rather than 0
	}

	// --------------------------------------------------------------------

	/**
	 * Set default controller
	 *
	 * @return	void
	 */
	protected function _set_default_controller()
	{
		if (empty($this->default_controller))
		{
			show_error('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
		}

		// Is the method being specified?
		if (sscanf($this->default_controller, '%[^/]/%s', $class, $method) !== 2)
		{
			$method = 'index';
		}

		$this->set_class($class);
		$this->set_method($method);
		$this->_set_request(array($class, $method));

		// re-index the routed segments array so it starts with 1 rather than 0
		$this->uri->_reindex_segments();

		log_message('debug', 'No URI present. Default controller set.');
	}

	// --------------------------------------------------------------------

	/**
	 * Set request route
	 *
	 * Takes an array of URI segments as input and sets the class/method
	 * to be called.
	 *
	 * @param	array	$segments	URI segments
	 * @return	void
	 */
	protected function _set_request($segments = array())
	{
		$segments = $this->_validate_request($segments);

		if (count($segments) === 0)
		{
			return $this->_set_default_controller();
		}

		$this->set_class($segments[0]);

		isset($segments[1]) OR $segments[1] = 'index';
		$this->set_method($segments[1]);

		// Update our "routed" segment array to contain the segments.
		// Note: If there is no custom routing, this array will be
		// identical to $this->uri->segments
		$this->uri->rsegments = $segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate request
	 *
	 * Attempts validate the URI request and determine the controller path.
	 *
	 * @param	array	$segments	URI segments
	 * @return	array	URI segments
	 */
	protected function _validate_request($segments)
	{
		if (count($segments) === 0)
		{
			return $segments;
		}

		$temp = str_replace('-', '_', $segments[0]);

		// Does the requested controller exist in the root folder?
		if (file_exists(APPPATH.'controllers/'.$temp.'.php'))
		{
			$segments[0] = $temp;
			empty($segments[1]) OR $segments[1] = str_replace('-', '_', $segments[1]);
			return $segments;
		}

		// Is the controller in a sub-folder?
		if (is_dir(APPPATH.'controllers/'.$segments[0]))
		{
			// Set the directory and remove it from the segment array
			$this->set_directory(array_shift($segments));
			if (count($segments) > 0)
			{
				$segments[0] = str_replace('-', '_', $segments[0]);
				empty($segments[1]) OR $segments[1] = str_replace('-', '_', $segments[1]);

				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].'.php'))
				{
					if ( ! empty($this->routes['404_override']))
					{
						$this->directory = '';
						return explode('/', $this->routes['404_override'], 2);
					}
					else
					{
						show_404($this->fetch_directory().$segments[0]);
					}
				}
			}
			else
			{
				// Is the method being specified in the route?
				$segments = explode('/', $this->default_controller);
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].'.php'))
				{
					$this->directory = '';
				}
			}

			return $segments;
		}

		// If we've gotten this far it means that the URI does not correlate to a valid
		// controller class. We will now see if there is an override
		if ( ! empty($this->routes['404_override']))
		{
			if (sscanf($this->routes['404_override'], '%[^/]/%s', $class, $method) !== 2)
			{
				$method = 'index';
			}

			return array($class, $method);
		}

		// Nothing else to do at this point but show a 404
		show_404($segments[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse Routes
	 *
	 * Matches any routes that may exist in the config/routes.php file
	 * against the URI to determine if the class/method need to be remapped.
	 *
	 * @return	void
	 */
	protected function _parse_routes()
	{
		// Turn the segment array into a URI string
		$uri = implode('/', $this->uri->segments);

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]) && is_string($this->routes[$uri]))
		{
			return $this->_set_request(explode('/', $this->routes[$uri]));
		}

		// Loop through the route array looking for wild-cards
		foreach ($this->routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $key);

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri, $matches))
			{
				// Are we using callbacks to process back-references?
				if ( ! is_string($val) && is_callable($val))
				{
					// Remove the original string from the matches array.
					array_shift($matches);

					// Get the match count.
					$match_count = count($matches);

					// Determine how many parameters the callback has.
					$reflection = new ReflectionFunction($val);
					$param_count = $reflection->getNumberOfParameters();

					// Are there more parameters than matches?
					if ($param_count > $match_count)
					{
						// Any params without matches will be set to an empty string.
						$matches = array_merge($matches, array_fill($match_count, $param_count - $match_count, ''));

						$match_count = $param_count;
					}

					// Get the parameters so we can use their default values.
					$params = $reflection->getParameters();

					for ($m = 0; $m < $match_count; $m++)
					{
						// Is the match empty and does a default value exist?
						if (empty($matches[$m]) && $params[$m]->isDefaultValueAvailable())
						{
							// Substitute the empty match for the default value.
							$matches[$m] = $params[$m]->getDefaultValue();
						}
					}

					// Execute the callback using the values in matches as its parameters.
					$val = call_user_func_array($val, $matches);
				}
				// Are we using the default routing method for back-references?
				elseif (strpos($val, '$') !== FALSE && strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				return $this->_set_request(explode('/', $val));
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_request($this->uri->segments);
	}

	// --------------------------------------------------------------------

	/**
	 * Set class name
	 *
	 * @param	string	$class	Class name
	 * @return	void
	 */
	public function set_class($class)
	{
		$this->class = str_replace(array('/', '.'), '', $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current class
	 *
	 * @return	string
	 */
	public function fetch_class()
	{
		return $this->class;
	}

	// --------------------------------------------------------------------

	/**
	 * Set method name
	 *
	 * @param	string	$method	Method name
	 * @return	void
	 */
	public function set_method($method)
	{
		$this->method = $method;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current method
	 *
	 * @return	string
	 */
	public function fetch_method()
	{
		return ($this->method === $this->fetch_class()) ? 'index' : $this->method;
	}

	// --------------------------------------------------------------------

	/**
	 * Set directory name
	 *
	 * @param	string	$dir	Directory name
	 * @return	void
	 */
	public function set_directory($dir)
	{
		$this->directory = str_replace(array('/', '.'), '', $dir).'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch directory
	 *
	 * Feches the sub-directory (if any) that contains the requested
	 * controller class.
	 *
	 * @return	string
	 */
	public function fetch_directory()
	{
		return $this->directory;
	}

	// --------------------------------------------------------------------

	/**
	 * Set controller overrides
	 *
	 * @param	array	$routing	Route overrides
	 * @return	void
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

		if ( ! empty($routing['controller']))
		{
			$this->set_class($routing['controller']);
		}

		if (isset($routing['function']))
		{
			$routing['function'] = empty($routing['function']) ? 'index' : $routing['function'];
			$this->set_method($routing['function']);
		}
	}

}

/* End of file Router.php */
/* Location: ./system/core/Router.php */