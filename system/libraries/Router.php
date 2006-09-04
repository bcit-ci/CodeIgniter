<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
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
	var $routes 		= array();
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
		$this->config =& _load_class('CI_Config');
		$this->_set_route_mapping();
		log_message('debug', "Router Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the route mapping
	 *
	 * This function determies what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @access	private
	 * @return	void
	 */
	function _set_route_mapping()
	{		
		// Are query strings enabled? If so we're done...
		if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')]))
		{
			$this->set_class($_GET[$this->config->item('controller_trigger')]);

			if (isset($_GET[$this->config->item('function_trigger')]))
			{
				$this->set_method($_GET[$this->config->item('function_trigger')]);
			}
			
			return;
		}

		// Load the routes.php file
		include_once(APPPATH.'config/routes'.EXT);
		$this->routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
		unset($route);

		// Set the default controller	
		$this->default_controller = ( ! isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);

		// Fetch the URI string Depending on the server, 
		// the URI will be available in one of two globals
		if ($this->config->item('uri_protocol') == 'auto')
		{
			$path_info = getenv('PATH_INFO');
			if ($path_info != '' AND $path_info != "/".SELF)
			{
				$this->uri_string = $path_info;
			}
			else
			{
				$path_info = getenv('ORIG_PATH_INFO');
				if ($path_info != '' AND $path_info != "/".SELF)
				{
					$this->uri_string = $path_info;
				}
				else
				{
					$this->uri_string = getenv('QUERY_STRING');
				}
			}
		}
		else
		{
			$this->uri_string = getenv(strtoupper($this->config->item('uri_protocol')));		
		}
		
	
		// Is there a URI string? If not, the default controller specified 
		// by the admin in the "routes" file will be shown.
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
		$i = 1;
		foreach ($this->segments as $val)
		{
			$this->segments[$i++] = $val;
		}
		unset($this->segments['0']);
	}
	// END _set_route_mapping()
	
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
						
		$this->set_class($segments['0']);
		
		if (isset($segments['1']))
		{
			// A scaffolding request. No funny business with the URL
			if ($this->routes['scaffolding_trigger'] == $segments['1'] AND $segments['1'] != '_ci_scaffolding')
			{
				$this->scaffolding_request = TRUE;
				unset($this->routes['scaffolding_trigger']);
			}
			else
			{
				// A standard method request
				$this->set_method($segments['1']);
			}
		}
	}
	// END _compile_segments()
	
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
		if (file_exists(APPPATH.'controllers/'.$segments['0'].EXT))
		{
			return $segments;
		}

		// Is the controller in a sub-folder?
		if (is_dir(APPPATH.'controllers/'.$segments['0']))
		{		
			// Set the directory and remove it from the segment array
			$this->set_directory($segments['0']);
			$segments = array_slice($segments, 1);
			
			if (count($segments) > 0)
			{
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments['0'].EXT))
				{
					show_404();	
				}
			}
			else
			{
				$this->set_class($this->default_controller);
				$this->set_method('index');
				$this->directory = '';
				return array();
			}
				
			return $segments;
		}
	
		// Can't find the requested controller...
		show_404();	
	}
	// END _validate_segments()
	
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
				exit('The URI you submitted has disallowed characters: '.$str);
			}
		}	
			return $str;
	}
	// END _filter_uri()
	
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
		if (count($this->routes) == 0)
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
				
		// Loop through the route array looking for wildcards
		foreach (array_slice($this->routes, 1) as $key => $val)
		{						
			// Convert wildcards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
			
			// Does the RegEx match?
			if (preg_match('#^'.preg_quote($key).'$#', $uri))
			{			
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.preg_quote($key).'$#', $val, $uri);
				}
			
				$this->_compile_segments(explode('/', $val));		
				return;
			}
		}
		
		// If we got this far it means we didn't encounter a 
		// matching route so we'll set the site default route
		$this->_compile_segments($this->segments);
	}
	// END set_method()

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
	// END set_class()
	
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
	// END fetch_class()
	
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
	// END set_method()

	// --------------------------------------------------------------------
	
	/**
	 *  Fetch the current method
	 *
	 * @access	public
	 * @return	string
	 */	
	function fetch_method()
	{
		return $this->method;
	}
	// END fetch_method()

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
	// END set_directory()

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
	// END fetch_directory()

}
// END Router Class
?>