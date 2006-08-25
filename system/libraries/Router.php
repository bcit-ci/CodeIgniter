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
		switch ($this->config->item('uri_protocol'))
		{
			case 'path_info'	: $this->uri_string = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');	
				break;
			case 'query_string' : $this->uri_string = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING'); 
				break;
			default : 
						$path_info = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
						
						if ($path_info != '' AND $path_info != "/".SELF)
						{
							$this->uri_string = $path_info;
						}
						else
						{
							$this->uri_string = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
						}
				break;
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
		
		// Do we need to remove the suffix specified in the config file?
		if  ($this->config->item('url_suffix') != "")
		{
			$this->uri_string = preg_replace("|".preg_quote($this->config->item('url_suffix'))."$|", "", $this->uri_string);
		}

		// Explode the URI Segments. The individual segments will
		// be stored in the $this->segments array.
		$this->_compile_segments(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)));


		// Remap the class/method if a route exists
		unset($this->routes['default_controller']);
		
		if (count($this->routes) > 0)
		{
			$this->_parse_routes();
		}
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
	function _compile_segments($segs, $route = FALSE)
	{		
		$segments = array();
	
		$i = 1;
		foreach($segs as $val)
		{
			$val = trim($this->_filter_uri($val));
			
			if ($val != '')
				$segments[$i++] = $val;
		}
		
		$this->set_class($segments['1']);
		
		if (isset($segments['2']))
		{
			// A scaffolding request. No funny business with the URL
			if ($this->routes['scaffolding_trigger'] == $segments['2'] AND $segments['2'] != '_ci_scaffolding')
			{
				$this->scaffolding_request = TRUE;
				unset($this->routes['scaffolding_trigger']);
			}
			else
			{
				// A standard method request
				$this->set_method($segments['2']);
			}
		}
		
		if ($route == FALSE)
		{
			$this->segments = $segments;
		}
		
		unset($segments);
	}
	// END _compile_segments()
	
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
		 if ( ! preg_match("/^[a-z0-9~\s\%\.:_-]+$/i", $str))
		 { 
			exit('The URI you submitted has disallowed characters: '.$str);
		 }
		 
		 return $str;
	}
	// END _filter_uri()
	
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
	// END _filter_uri()
	
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
	// END _filter_uri()
	
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
	// END set_method()
	
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
		// Turn the segment array into a URI string
		$uri = implode('/', $this->segments);
		$num = count($this->segments);

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]))
		{
			$this->_compile_segments(explode('/', $this->routes[$uri]), TRUE);		
			return;
		}
		
		// Loop through the route array looking for wildcards
		foreach ($this->routes as $key => $val)
		{
			if (count(explode('/', $key)) != $num)
				continue;
		
			if (preg_match("|".str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key))."$|", $uri))
			{
				$this->_compile_segments(explode('/', $val), TRUE);		
				break;
			}
		}	
	}
	// END set_method()
}
// END Router Class
?>