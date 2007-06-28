<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
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
 * @author		Rick Ellis
 * @category	Libraries
 * @link		http://www.codeigniter.com/user_guide/general/routing.html
 */
class CI_Router {

	var $config;
	var $uri_string		= '';
	var $segments		= array();
	var $rsegments		= array();
	var $routes 		= array();
	var $error_routes	= array();
	var $class			= '';
	var $method			= 'index';
	var $directory		= '';
	var $uri_protocol 	= 'auto';
	var $default_controller;
	var $scaffolding_request = FALSE; // Must be set to FALSE
	
	/**
	 * Constructor
	 *
	 * Runs the route mapping function.
	 */
	function CI_Router()
	{
		$this->config =& load_class('Config');
		$this->_set_route_mapping();
		log_message('debug', "Router Class Initialized");
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
	function _set_route_mapping()
	{		
		// Are query strings enabled in the config file?
		// If so, we're done since segment based URIs are not used with query strings.
		if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')]))
		{
			$this->set_class(trim($this->_filter_uri($_GET[$this->config->item('controller_trigger')])));

			if (isset($_GET[$this->config->item('function_trigger')]))
			{
				$this->set_method(trim($this->_filter_uri($_GET[$this->config->item('function_trigger')])));
			}
			
			return;
		}
		
		// Load the routes.php file.
		@include(APPPATH.'config/routes'.EXT);
		$this->routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
		unset($route);

		// Set the default controller so we can display it in the event
		// the URI doesn't correlated to a valid controller.
		$this->default_controller = ( ! isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);	
		
		// Fetch the complete URI string
		$this->uri_string = $this->_get_uri_string();
		
		// If the URI contains only a slash we'll kill it
		if ($this->uri_string == '/')
		{
			$this->uri_string = '';
		}
	
		// Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
		if ($this->uri_string == '')
		{
			if ($this->default_controller === FALSE)
			{
				show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
			}
		
			$this->set_class($this->default_controller);
			$this->set_method('index');

			log_message('debug', "No URI present. Default controller set.");
			return;
		}
		unset($this->routes['default_controller']);
		
		// Do we need to remove the suffix specified in the config file?
		if  ($this->config->item('url_suffix') != "")
		{
			$this->uri_string = preg_replace("|".preg_quote($this->config->item('url_suffix'))."$|", "", $this->uri_string);
		}
		
		// Explode the URI Segments. The individual segments will
		// be stored in the $this->segments array.	
		foreach(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val)
		{
			// Filter segments for security
			$val = trim($this->_filter_uri($val));
			
			if ($val != '')
				$this->segments[] = $val;
		}
		
		// Parse any custom routing that may exist
		$this->_parse_routes();		
		
		// Re-index the segment array so that it starts with 1 rather than 0
		$this->_reindex_segments();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Compile Segments
	 *
	 * This function takes an array of URI segments as
	 * input, and puts it into the $this->segments array.
	 * It also sets the current class/method
	 *
	 * @access	private
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function _compile_segments($segments = array())
	{	
		$segments = $this->_validate_segments($segments);
		
		if (count($segments) == 0)
		{
			return;
		}
						
		$this->set_class($segments[0]);
		
		if (isset($segments[1]))
		{
			// A scaffolding request. No funny business with the URL
			if ($this->routes['scaffolding_trigger'] == $segments[1] AND $segments[1] != '_ci_scaffolding')
			{
				$this->scaffolding_request = TRUE;
				unset($this->routes['scaffolding_trigger']);
			}
			else
			{
				// A standard method request
				$this->set_method($segments[1]);
			}
		}
		
		// Update our "routed" segment array to contain the segments.
		// Note: If there is no custom routing, this array will be
		// identical to $this->segments
		$this->rsegments = $segments;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */	
	function _validate_segments($segments)
	{
		// Does the requested controller exist in the root folder?
		if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
		{
			return $segments;
		}

		// Is the controller in a sub-folder?
		if (is_dir(APPPATH.'controllers/'.$segments[0]))
		{		
			// Set the directory and remove it from the segment array
			$this->set_directory($segments[0]);
			$segments = array_slice($segments, 1);
			
			if (count($segments) > 0)
			{
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
				{
					show_404();	
				}
			}
			else
			{
				$this->set_class($this->default_controller);
				$this->set_method('index');
			
				// Does the default controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
				{
					$this->directory = '';
					return array();
				}
			
			}
				
			return $segments;
		}
	
		// Can't find the requested controller...
		show_404();	
	}

	// --------------------------------------------------------------------	
	/**
	 * Re-index Segments
	 *
	 * This function re-indexes the $this->segment array so that it
	 * starts at 1 rather then 0.  Doing so makes it simpler to
	 * use functions like $this->uri->segment(n) since there is
	 * a 1:1 relationship between the segment array and the actual segments.
	 *
	 * @access	private
	 * @return	void
	 */	
	function _reindex_segments()
	{
		// Is the routed segment array different then the main segment array?
		$diff = (count(array_diff($this->rsegments, $this->segments)) == 0) ? FALSE : TRUE;
	
		$i = 1;
		foreach ($this->segments as $val)
		{
			$this->segments[$i++] = $val;
		}
		unset($this->segments[0]);
		
		if ($diff == FALSE)
		{
			$this->rsegments = $this->segments;
		}
		else
		{
			$i = 1;
			foreach ($this->rsegments as $val)
			{
				$this->rsegments[$i++] = $val;
			}
			unset($this->rsegments[0]);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the URI String
	 *
	 * @access	private
	 * @return	string
	 */	
	function _get_uri_string()
	{
		if (strtoupper($this->config->item('uri_protocol')) == 'AUTO')
		{
			// If the URL has a question mark then it's simplest to just
			// build the URI string from the zero index of the $_GET array.
			// This avoids having to deal with $_SERVER variables, which
			// can be unreliable in some environments
			if (is_array($_GET) AND count($_GET) == 1)
			{
				// Note: Due to a bug in current() that affects some versions
				// of PHP we can not pass function call directly into it
				$keys = array_keys($_GET);
				return current($keys);
			}
		
			// Is there a PATH_INFO variable?
			// Note: some servers seem to have trouble with getenv() so we'll test it two ways		
			$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');			
			if ($path != '' AND $path != "/".SELF)
			{
				return $path;
			}
					
			// No PATH_INFO?... What about QUERY_STRING?
			$path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');	
			if ($path != '')
			{
				return $path;
			}
			
			// No QUERY_STRING?... Maybe the ORIG_PATH_INFO variable exists?
			$path = (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO');	
			if ($path != '' AND $path != "/".SELF)
			{
				return $path;
			}

			// We've exhausted all our options...
			return '';
		}
		else
		{
			$uri = strtoupper($this->config->item('uri_protocol'));
			
			if ($uri == 'REQUEST_URI')
			{
				return $this->_parse_request_uri();
			}
			
			return (isset($_SERVER[$uri])) ? $_SERVER[$uri] : @getenv($uri);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Parse the REQUEST_URI
	 *
	 * Due to the way REQUEST_URI works it usually contains path info
	 * that makes it unusable as URI data.  We'll trim off the unnecessary
	 * data, hopefully arriving at a valid URI that we can use.
	 *
	 * @access	private
	 * @return	string
	 */	
	function _parse_request_uri()
	{
		if ( ! isset($_SERVER['REQUEST_URI']) OR $_SERVER['REQUEST_URI'] == '')
		{
			return '';
		}
		
		$request_uri = preg_replace("|/(.*)|", "\\1", str_replace("\\", "/", $_SERVER['REQUEST_URI']));

		if ($request_uri == '' OR $request_uri == SELF)
		{
			return '';
		}
		
		$fc_path = FCPATH;		
		if (strpos($request_uri, '?') !== FALSE)
		{
			$fc_path .= '?';
		}
		
		$parsed_uri = explode("/", $request_uri);
				
		$i = 0;
		foreach(explode("/", $fc_path) as $segment)
		{
			if (isset($parsed_uri[$i]) AND $segment == $parsed_uri[$i])
			{
				$i++;
			}
		}
		
		$parsed_uri = implode("/", array_slice($parsed_uri, $i));
		
		if ($parsed_uri != '')
		{
			$parsed_uri = '/'.$parsed_uri;
		}

		return $parsed_uri;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Filter segments for malicious characters
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */	
	function _filter_uri($str)
	{
		if ($this->config->item('permitted_uri_chars') != '')
		{
			if ( ! preg_match("|^[".preg_quote($this->config->item('permitted_uri_chars'))."]+$|i", $str))
			{
				exit('The URI you submitted has disallowed characters.');
			}
		}	
			return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Parse Routes
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @access	private
	 * @return	void
	 */
	function _parse_routes()
	{
		// Do we even have any custom routing to deal with?
		// There is a default scaffolding trigger, so we'll look just for 1
		if (count($this->routes) == 1)
		{
			$this->_compile_segments($this->segments);
			return;
		}

		// Turn the segment array into a URI string
		$uri = implode('/', $this->segments);
		$num = count($this->segments);

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]))
		{
			$this->_compile_segments(explode('/', $this->routes[$uri]));		
			return;
		}
				
		// Loop through the route array looking for wild-cards
		foreach (array_slice($this->routes, 1) as $key => $val)
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
			
				$this->_compile_segments(explode('/', $val));		
				return;
			}
		}
		
		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_compile_segments($this->segments);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set the class name
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_class($class)
	{
		$this->class = $class;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the current class
	 *
	 * @access	public
	 * @return	string
	 */	
	function fetch_class()
	{
		return $this->class;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Set the method name
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_method($method)
	{
		$this->method = $method;
	}

	// --------------------------------------------------------------------
	
	/**
	 *  Fetch the current method
	 *
	 * @access	public
	 * @return	string
	 */	
	function fetch_method()
	{
		if ($this->method == $this->fetch_class())
		{
			return 'index';
		}

		return $this->method;
	}

	// --------------------------------------------------------------------
	
	/**
	 *  Set the directory name
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_directory($dir)
	{
		$this->directory = $dir.'/';
	}

	// --------------------------------------------------------------------
	
	/**
	 *  Fetch the sub-directory (if any) that contains the requested controller class
	 *
	 * @access	public
	 * @return	string
	 */	
	function fetch_directory()
	{
		return $this->directory;
	}

}
// END Router Class
?>