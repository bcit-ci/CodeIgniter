<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Rest Controller
 *
 * A fully RESTful server implementation for CodeIgniter using one library, one config file and one controller.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Phil Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			https://github.com/philsturgeon/codeigniter-restserver
 * @version 		2.6.2
 */
abstract class REST_Controller extends CI_Controller
{
	/**
	 * This defines the rest format.
	 *
	 * Must be overridden it in a controller so that it is set.
	 *
	 * @var string|null
	 */
	protected $rest_format = NULL;

	/**
	 * Defines the list of method properties such as limit, log and level
	 *
	 * @var array
	 */
	protected $methods = array();

	/**
	 * List of allowed HTTP methods
	 *
	 * @var array
	 */
	protected $allowed_http_methods = array('get', 'delete', 'post', 'put');

	/**
	 * General request data and information.
	 * Stores accept, language, body, headers, etc.
	 *
	 * @var object
	 */
	protected $request = NULL;

	/**
	 * What is gonna happen in output?
	 *
	 * @var object
	 */
	protected $response = NULL;

	/**
	 * Stores DB, keys, key level, etc
	 *
	 * @var object
	 */
	protected $rest = NULL;

	/**
	 * The arguments for the GET request method
	 *
	 * @var array
	 */
	protected $_get_args = array();

	/**
	 * The arguments for the POST request method
	 *
	 * @var array
	 */
	protected $_post_args = array();

	/**
	 * The arguments for the PUT request method
	 *
	 * @var array
	 */
	protected $_put_args = array();

	/**
	 * The arguments for the DELETE request method
	 *
	 * @var array
	 */
	protected $_delete_args = array();

	/**
	 * The arguments from GET, POST, PUT, DELETE request methods combined.
	 *
	 * @var array
	 */
	protected $_args = array();

	/**
	 * If the request is allowed based on the API key provided.
	 *
	 * @var boolean
	 */
	protected $_allow = TRUE;

	/**
	 * Determines if output compression is enabled
	 *
	 * @var boolean
	 */
	protected $_zlib_oc = FALSE;

	/**
	 * The LDAP Distinguished Name of the User post authentication
	 *
	 * @var string
	*/
	protected $_user_ldap_dn = '';

	/**
	 * List all supported methods, the first will be the default format
	 *
	 * @var array
	 */
	protected $_supported_formats = array(
		'xml' => 'application/xml',
		'json' => 'application/json',
		'jsonp' => 'application/javascript',
		'serialized' => 'application/vnd.php.serialized',
		'php' => 'text/plain',
		'html' => 'text/html',
		'csv' => 'application/csv'
	);

	/**
	 * Developers can extend this class and add a check in here.
	 */
	protected function early_checks()
	{

	}

	/**
	 * Constructor function
	 * @todo Document more please.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// init objects
		$this->request = new stdClass();
		$this->response = new stdClass();
		$this->rest = new stdClass();

		$this->_zlib_oc = @ini_get('zlib.output_compression');

		// Lets grab the config and get ready to party
		$this->load->config('rest');

		// let's learn about the request
		$this->request = new stdClass();

		// Is it over SSL?
		$this->request->ssl = $this->_detect_ssl();

		// How is this request being made? POST, DELETE, GET, PUT?
		$this->request->method = $this->_detect_method();

		// Create argument container, if nonexistent
		if ( ! isset($this->{'_'.$this->request->method.'_args'}))
		{
			$this->{'_'.$this->request->method.'_args'} = array();
		}

		// Set up our GET variables
		$this->_get_args = array_merge($this->_get_args, $this->uri->ruri_to_assoc());

		// This library is bundled with REST_Controller 2.5+, but will eventually be part of CodeIgniter itself
		$this->load->library('format');

		// Try to find a format for the request (means we have a request body)
		$this->request->format = $this->_detect_input_format();

		// Some Methods cant have a body
		$this->request->body = NULL;

		$this->{'_parse_' . $this->request->method}();

		// Now we know all about our request, let's try and parse the body if it exists
		if ($this->request->format and $this->request->body)
		{
			$this->request->body = $this->format->factory($this->request->body, $this->request->format)->to_array();
			// Assign payload arguments to proper method container
			$this->{'_'.$this->request->method.'_args'} = $this->request->body;
		}

		// Merge both for one mega-args variable
		$this->_args = array_merge($this->_get_args, $this->_put_args, $this->_post_args, $this->_delete_args, $this->{'_'.$this->request->method.'_args'});

		// Which format should the data be returned in?
		$this->response = new stdClass();
		$this->response->format = $this->_detect_output_format();

		// Which format should the data be returned in?
		$this->response->lang = $this->_detect_lang();

		// Developers can extend this class and add a check in here
		$this->early_checks();

		// Check if there is a specific auth type for the current class/method
		$this->auth_override = $this->_auth_override_check();

		// When there is no specific override for the current class/method, use the default auth value set in the config
		if ($this->auth_override !== TRUE)
		{
			if ($this->config->item('rest_auth') == 'basic')
			{
				$this->_prepare_basic_auth();
			}
			elseif ($this->config->item('rest_auth') == 'digest')
			{
				$this->_prepare_digest_auth();
			}
			elseif ($this->config->item('rest_ip_whitelist_enabled'))
			{
				$this->_check_whitelist_auth();
			}
		}

		$this->rest = new StdClass();
		// Load DB if its enabled
		if (config_item('rest_database_group') AND (config_item('rest_enable_keys') OR config_item('rest_enable_logging')))
		{
			$this->rest->db = $this->load->database(config_item('rest_database_group'), TRUE);
		}

		// Use whatever database is in use (isset returns false)
		elseif (property_exists($this, "db"))
		{
			$this->rest->db = $this->db;
		}

		// Checking for keys? GET TO WORK!
		if (config_item('rest_enable_keys'))
		{
			$this->_allow = $this->_detect_api_key();
		}

		// only allow ajax requests
		if ( ! $this->input->is_ajax_request() AND config_item('rest_ajax_only'))
		{
			$this->response(array('status' => false, 'error' => 'Only AJAX requests are accepted.'), 505);
		}
	}

	/**
	 * Remap
	 *
	 * Requests are not made to methods directly, the request will be for
	 * an "object". This simply maps the object and method to the correct
	 * Controller method.
	 *
	 * @param string $object_called
	 * @param array $arguments The arguments passed to the controller method.
	 */
	public function _remap($object_called, $arguments)
	{
		// Should we answer if not over SSL?
		if (config_item('force_https') AND !$this->_detect_ssl())
		{
			$this->response(array('status' => false, 'error' => 'Unsupported protocol'), 403);
		}

		$pattern = '/^(.*)\.('.implode('|', array_keys($this->_supported_formats)).')$/';
		if (preg_match($pattern, $object_called, $matches))
		{
			$object_called = $matches[1];
		}

		$controller_method = $object_called.'_'.$this->request->method;

		// Do we want to log this method (if allowed by config)?
		$log_method = !(isset($this->methods[$controller_method]['log']) AND $this->methods[$controller_method]['log'] == FALSE);

		// Use keys for this method?
		$use_key = ! (isset($this->methods[$controller_method]['key']) AND $this->methods[$controller_method]['key'] == FALSE);

		// Get that useless shitty key out of here
		if (config_item('rest_enable_keys') AND $use_key AND $this->_allow === FALSE)
		{
			if (config_item('rest_enable_logging') AND $log_method)
			{
				$this->_log_request();
			}

			$this->response(array('status' => false, 'error' => 'Invalid API Key.'), 403);
		}

		// Sure it exists, but can they do anything with it?
		if ( ! method_exists($this, $controller_method))
		{
			$this->response(array('status' => false, 'error' => 'Unknown method.'), 404);
		}

		// Doing key related stuff? Can only do it if they have a key right?
		if (config_item('rest_enable_keys') AND !empty($this->rest->key))
		{
			// Check the limit
			if (config_item('rest_enable_limits') AND !$this->_check_limit($controller_method))
			{
				$this->response(array('status' => false, 'error' => 'This API key has reached the hourly limit for this method.'), 401);
			}

			// If no level is set use 0, they probably aren't using permissions
			$level = isset($this->methods[$controller_method]['level']) ? $this->methods[$controller_method]['level'] : 0;

			// If no level is set, or it is lower than/equal to the key's level
			$authorized = $level <= $this->rest->level;

			// IM TELLIN!
			if (config_item('rest_enable_logging') AND $log_method)
			{
				$this->_log_request($authorized);
			}

			// They don't have good enough perms
			$authorized OR $this->response(array('status' => false, 'error' => 'This API key does not have enough permissions.'), 401);
		}

		// No key stuff, but record that stuff is happening
		else if (config_item('rest_enable_logging') AND $log_method)
		{
			$this->_log_request($authorized = TRUE);
		}

		// And...... GO!
		$this->_fire_method(array($this, $controller_method), $arguments);
	}

	/**
	 * Fire Method
	 *
	 * Fires the designated controller method with the given arguments.
	 *
	 * @param array $method The controller method to fire
	 * @param array $args The arguments to pass to the controller method
	 */
	protected function _fire_method($method, $args)
	{
		call_user_func_array($method, $args);
	}

	/**
	 * Response
	 *
	 * Takes pure data and optionally a status code, then creates the response.
	 *
	 * @param array $data
	 * @param null|int $http_code
	 */
	public function response($data = array(), $http_code = null)
	{
		global $CFG;

		// If data is empty and not code provide, error and bail
		if (empty($data) && $http_code === null)
		{
			$http_code = 404;

			// create the output variable here in the case of $this->response(array());
			$output = NULL;
		}

		// If data is empty but http code provided, keep the output empty
		else if (empty($data) && is_numeric($http_code))
		{
			$output = NULL;
		}

		// Otherwise (if no data but 200 provided) or some data, carry on camping!
		else
		{
			// Is compression requested?
			if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc == FALSE)
			{
				if (extension_loaded('zlib'))
				{
					if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
					{
						ob_start('ob_gzhandler');
					}
				}
			}

			is_numeric($http_code) OR $http_code = 200;

			// If the format method exists, call and return the output in that format
			if (method_exists($this, '_format_'.$this->response->format))
			{
				// Set the correct format header
				header('Content-Type: '.$this->_supported_formats[$this->response->format]);

				$output = $this->{'_format_'.$this->response->format}($data);
			}

			// If the format method exists, call and return the output in that format
			elseif (method_exists($this->format, 'to_'.$this->response->format))
			{
				// Set the correct format header
				header('Content-Type: '.$this->_supported_formats[$this->response->format]);

				$output = $this->format->factory($data)->{'to_'.$this->response->format}();
			}

			// Format not supported, output directly
			else
			{
				$output = $data;
			}
		}

		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);

		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ( ! $this->_zlib_oc && ! $CFG->item('compress_output'))
		{
			header('Content-Length: ' . strlen($output));
		}

		exit($output);
	}

	/*
	 * Detect SSL use
	 *
	 * Detect whether SSL is being used or not
	 */
	protected function _detect_ssl()
	{
    		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");
	}


	/*
	 * Detect input format
	 *
	 * Detect which format the HTTP Body is provided in
	 */
	protected function _detect_input_format()
	{
		if ($this->input->server('CONTENT_TYPE'))
		{
			// Check all formats against the HTTP_ACCEPT header
			foreach ($this->_supported_formats as $format => $mime)
			{
				if (strpos($match = $this->input->server('CONTENT_TYPE'), ';'))
				{
					$match = current(explode(';', $match));
				}

				if ($match == $mime)
				{
					return $format;
				}
			}
		}

		return NULL;
	}

	/**
	 * Detect format
	 *
	 * Detect which format should be used to output the data.
	 *
	 * @return string The output format.
	 */
	protected function _detect_output_format()
	{
		$pattern = '/\.('.implode('|', array_keys($this->_supported_formats)).')$/';

		// Check if a file extension is used
		if (preg_match($pattern, $this->uri->uri_string(), $matches))
		{
			return $matches[1];
		}

		// Check if a file extension is used
		elseif ($this->_get_args AND !is_array(end($this->_get_args)) AND preg_match($pattern, end($this->_get_args), $matches))
		{
			// The key of the last argument
			$last_key = end(array_keys($this->_get_args));

			// Remove the extension from arguments too
			$this->_get_args[$last_key] = preg_replace($pattern, '', $this->_get_args[$last_key]);
			$this->_args[$last_key] = preg_replace($pattern, '', $this->_args[$last_key]);

			return $matches[1];
		}

		// A format has been passed as an argument in the URL and it is supported
		if (isset($this->_get_args['format']) AND array_key_exists($this->_get_args['format'], $this->_supported_formats))
		{
			return $this->_get_args['format'];
		}

		// Otherwise, check the HTTP_ACCEPT (if it exists and we are allowed)
		if ($this->config->item('rest_ignore_http_accept') === FALSE AND $this->input->server('HTTP_ACCEPT'))
		{
			// Check all formats against the HTTP_ACCEPT header
			foreach (array_keys($this->_supported_formats) as $format)
			{
				// Has this format been requested?
				if (strpos($this->input->server('HTTP_ACCEPT'), $format) !== FALSE)
				{
					// If not HTML or XML assume its right and send it on its way
					if ($format != 'html' AND $format != 'xml')
					{

						return $format;
					}

					// HTML or XML have shown up as a match
					else
					{
						// If it is truly HTML, it wont want any XML
						if ($format == 'html' AND strpos($this->input->server('HTTP_ACCEPT'), 'xml') === FALSE)
						{
							return $format;
						}

						// If it is truly XML, it wont want any HTML
						elseif ($format == 'xml' AND strpos($this->input->server('HTTP_ACCEPT'), 'html') === FALSE)
						{
							return $format;
						}
					}
				}
			}
		} // End HTTP_ACCEPT checking

		// Well, none of that has worked! Let's see if the controller has a default
		if ( ! empty($this->rest_format))
		{
			return $this->rest_format;
		}

		// Just use the default format
		return config_item('rest_default_format');
	}

	/**
	 * Detect method
	 *
	 * Detect which HTTP method is being used
	 *
	 * @return string
	 */
	protected function _detect_method()
	{
		$method = strtolower($this->input->server('REQUEST_METHOD'));

		if ($this->config->item('enable_emulate_request'))
		{
			if ($this->input->post('_method'))
			{
				$method = strtolower($this->input->post('_method'));
			}
			elseif ($this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE'))
			{
				$method = strtolower($this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE'));
			}
		}

		if (in_array($method, $this->allowed_http_methods) && method_exists($this, '_parse_' . $method))
		{
			return $method;
		}

		return 'get';
	}

	/**
	 * Detect API Key
	 *
	 * See if the user has provided an API key
	 *
	 * @return boolean
	 */
	protected function _detect_api_key()
	{
		// Get the api key name variable set in the rest config file
		$api_key_variable = config_item('rest_key_name');

		// Work out the name of the SERVER entry based on config
		$key_name = 'HTTP_'.strtoupper(str_replace('-', '_', $api_key_variable));

		$this->rest->key = NULL;
		$this->rest->level = NULL;
		$this->rest->user_id = NULL;
		$this->rest->ignore_limits = FALSE;

		// Find the key from server or arguments
		if (($key = isset($this->_args[$api_key_variable]) ? $this->_args[$api_key_variable] : $this->input->server($key_name)))
		{
			if ( ! ($row = $this->rest->db->where(config_item('rest_key_column'), $key)->get(config_item('rest_keys_table'))->row()))
			{
				return FALSE;
			}

			$this->rest->key = $row->{config_item('rest_key_column')};

			isset($row->user_id) AND $this->rest->user_id = $row->user_id;
			isset($row->level) AND $this->rest->level = $row->level;
			isset($row->ignore_limits) AND $this->rest->ignore_limits = $row->ignore_limits;

			/*
			 * If "is private key" is enabled, compare the ip address with the list
			 * of valid ip addresses stored in the database.
			 */
			if(!empty($row->is_private_key))
			{
				// Check for a list of valid ip addresses
				if(isset($row->ip_addresses))
				{
					// multiple ip addresses must be separated using a comma, explode and loop
					$list_ip_addresses = explode(",", $row->ip_addresses);
					$found_address = FALSE;

					foreach($list_ip_addresses as $ip_address)
					{
						if($this->input->ip_address() == trim($ip_address))
						{
							// there is a match, set the the value to true and break out of the loop
							$found_address = TRUE;
							break;
						}
					}

					return $found_address;
				}
				else
				{
					// There should be at least one IP address for this private key.
					return FALSE;
				}
			}

			return $row;
		}

		// No key has been sent
		return FALSE;
	}

	/**
	 * Detect language(s)
	 *
	 * What language do they want it in?
	 *
	 * @return null|string The language code.
	 */
	protected function _detect_lang()
	{
		if ( ! $lang = $this->input->server('HTTP_ACCEPT_LANGUAGE'))
		{
			return NULL;
		}

		// They might have sent a few, make it an array
		if (strpos($lang, ',') !== FALSE)
		{
			$langs = explode(',', $lang);

			$return_langs = array();
			$i = 1;
			foreach ($langs as $lang)
			{
				// Remove weight and strip space
				list($lang) = explode(';', $lang);
				$return_langs[] = trim($lang);
			}

			return $return_langs;
		}

		// Nope, just return the string
		return $lang;
	}

	/**
	 * Log request
	 *
	 * Record the entry for awesomeness purposes
	 *
	 * @param boolean $authorized
	 * @return object
	 */
	protected function _log_request($authorized = FALSE)
	{
		return $this->rest->db->insert(config_item('rest_logs_table'), array(
					'uri' => $this->uri->uri_string(),
					'method' => $this->request->method,
					'params' => $this->_args ? (config_item('rest_logs_json_params') ? json_encode($this->_args) : serialize($this->_args)) : null,
					'api_key' => isset($this->rest->key) ? $this->rest->key : '',
					'ip_address' => $this->input->ip_address(),
					'time' => function_exists('now') ? now() : time(),
					'authorized' => $authorized
				));
	}

	/**
	 * Limiting requests
	 *
	 * Check if the requests are coming in a tad too fast.
	 *
	 * @param string $controller_method The method being called.
	 * @return boolean
	 */
	protected function _check_limit($controller_method)
	{
		// They are special, or it might not even have a limit
		if ( ! empty($this->rest->ignore_limits) OR !isset($this->methods[$controller_method]['limit']))
		{
			// On your way sonny-jim.
			return TRUE;
		}

		// How many times can you get to this method an hour?
		$limit = $this->methods[$controller_method]['limit'];

		// Get data on a keys usage
		$result = $this->rest->db
				->where('uri', $this->uri->uri_string())
				->where('api_key', $this->rest->key)
				->get(config_item('rest_limits_table'))
				->row();

		// No calls yet, or been an hour since they called
		if ( ! $result OR $result->hour_started < time() - (60 * 60))
		{
			// Right, set one up from scratch
			$this->rest->db->insert(config_item('rest_limits_table'), array(
				'uri' => $this->uri->uri_string(),
				'api_key' => isset($this->rest->key) ? $this->rest->key : '',
				'count' => 1,
				'hour_started' => time()
			));
		}

		// They have called within the hour, so lets update
		else
		{
			// Your luck is out, you've called too many times!
			if ($result->count >= $limit)
			{
				return FALSE;
			}

			$this->rest->db
					->where('uri', $this->uri->uri_string())
					->where('api_key', $this->rest->key)
					->set('count', 'count + 1', FALSE)
					->update(config_item('rest_limits_table'));
		}

		return TRUE;
	}

	/**
	 * Auth override check
	 *
	 * Check if there is a specific auth type set for the current class/method
	 * being called.
	 *
	 * @return boolean
	 */
	protected function _auth_override_check()
	{

		// Assign the class/method auth type override array from the config
		$this->overrides_array = $this->config->item('auth_override_class_method');

		// Check to see if the override array is even populated, otherwise return false
		if (empty($this->overrides_array))
		{
			return false;
		}

		// Check to see if there's an override value set for the current class/method being called
		if (empty($this->overrides_array[$this->router->class][$this->router->method]))
		{
			return false;
		}

		// None auth override found, prepare nothing but send back a true override flag
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'none')
		{
			return true;
		}

		// Basic auth override found, prepare basic
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'basic')
		{
			$this->_prepare_basic_auth();
			return true;
		}

		// Digest auth override found, prepare digest
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'digest')
		{
			$this->_prepare_digest_auth();
			return true;
		}

		// Whitelist auth override found, check client's ip against config whitelist
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'whitelist')
		{
			$this->_check_whitelist_auth();
			return true;
		}

		// Return false when there is an override value set but it does not match
		// 'basic', 'digest', or 'none'. (the value was misspelled)
		return false;
	}

	/**
	 * Parse GET
	 */
	protected function _parse_get()
	{
		// Grab proper GET variables
		parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $get);

		// Merge both the URI segments and GET params
		$this->_get_args = array_merge($this->_get_args, $get);
	}

	/**
	 * Parse POST
	 */
	protected function _parse_post()
	{
		$this->_post_args = $_POST;

		$this->request->format and $this->request->body = file_get_contents('php://input');
	}

	/**
	 * Parse PUT
	 */
	protected function _parse_put()
	{
		// It might be a HTTP body
		if ($this->request->format)
		{
			$this->request->body = file_get_contents('php://input');
		}

		// If no file type is provided, this is probably just arguments
		else
		{
			parse_str(file_get_contents('php://input'), $this->_put_args);
		}
	}

	/**
	 * Parse DELETE
	 */
	protected function _parse_delete()
	{
		// Set up out DELETE variables (which shouldn't really exist, but sssh!)
		parse_str(file_get_contents('php://input'), $this->_delete_args);
	}

	// INPUT FUNCTION --------------------------------------------------------------

	/**
	 * Retrieve a value from the GET request arguments.
	 *
	 * @param string $key The key for the GET request argument to retrieve
	 * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
	 * @return string The GET argument value.
	 */
	public function get($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_get_args;
		}

		return array_key_exists($key, $this->_get_args) ? $this->_xss_clean($this->_get_args[$key], $xss_clean) : FALSE;
	}

	/**
	 * Retrieve a value from the POST request arguments.
	 *
	 * @param string $key The key for the POST request argument to retrieve
	 * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
	 * @return string The POST argument value.
	 */
	public function post($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_post_args;
		}

		return array_key_exists($key, $this->_post_args) ? $this->_xss_clean($this->_post_args[$key], $xss_clean) : FALSE;
	}

	/**
	 * Retrieve a value from the PUT request arguments.
	 *
	 * @param string $key The key for the PUT request argument to retrieve
	 * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
	 * @return string The PUT argument value.
	 */
	public function put($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_put_args;
		}

		return array_key_exists($key, $this->_put_args) ? $this->_xss_clean($this->_put_args[$key], $xss_clean) : FALSE;
	}

	/**
	 * Retrieve a value from the DELETE request arguments.
	 *
	 * @param string $key The key for the DELETE request argument to retrieve
	 * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
	 * @return string The DELETE argument value.
	 */
	public function delete($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_delete_args;
		}

		return array_key_exists($key, $this->_delete_args) ? $this->_xss_clean($this->_delete_args[$key], $xss_clean) : FALSE;
	}

	/**
	 * Process to protect from XSS attacks.
	 *
	 * @param string $val The input.
	 * @param boolean $process Do clean or note the input.
	 * @return string
	 */
	protected function _xss_clean($val, $process)
	{
		if (CI_VERSION < 2)
		{
			return $process ? $this->input->xss_clean($val) : $val;
		}

		return $process ? $this->security->xss_clean($val) : $val;
	}

	/**
	 * Retrieve the validation errors.
	 *
	 * @return array
	 */
	public function validation_errors()
	{
		$string = strip_tags($this->form_validation->error_string());

		return explode("\n", trim($string, "\n"));
	}

	// SECURITY FUNCTIONS ---------------------------------------------------------

	/**
	 * Perform LDAP Authentication
	 *
	 * @param string $username The username to validate
	 * @param string $password The password to validate
	 * @return boolean
	 */
	protected function _perform_ldap_auth($username = '', $password = NULL)
	{
		if (empty($username))
		{
			log_message('debug', 'LDAP Auth: failure, empty username');
			return false;
		}

		log_message('debug', 'LDAP Auth: Loading Config');

		$this->config->load('ldap.php', true);

		$ldaptimeout = $this->config->item('timeout', 'ldap');
		$ldaphost = $this->config->item('server', 'ldap');
		$ldapport = $this->config->item('port', 'ldap');
		$ldaprdn = $this->config->item('binduser', 'ldap');
		$ldappass = $this->config->item('bindpw', 'ldap');
		$ldapbasedn = $this->config->item('basedn', 'ldap');

		log_message('debug', 'LDAP Auth: Connect to ' . $ldaphost);

		$ldapconfig['authrealm'] = $this->config->item('domain', 'ldap');

		// connect to ldap server
		$ldapconn = ldap_connect($ldaphost, $ldapport);

		if ($ldapconn) {

			log_message('debug', 'Setting timeout to ' . $ldaptimeout . ' seconds');

			ldap_set_option($ldapconn, LDAP_OPT_NETWORK_TIMEOUT, $ldaptimeout);

			log_message('debug', 'LDAP Auth: Binding to ' . $ldaphost . ' with dn ' . $ldaprdn);

			// binding to ldap server
			$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

			// verify binding
			if ($ldapbind) {
				log_message('debug', 'LDAP Auth: bind successful');
			} else {
				log_message('error', 'LDAP Auth: bind unsuccessful');
				return false;
			}

		}

		// search for user
		if (($res_id = ldap_search( $ldapconn, $ldapbasedn, "uid=$username")) == false) {
			log_message('error', 'LDAP Auth: User ' . $username . ' not found in search');
			return false;
		}

		if (ldap_count_entries($ldapconn, $res_id) != 1) {
			log_message('error', 'LDAP Auth: failure, username ' . $username . 'found more than once');
			return false;
		}

		if (( $entry_id = ldap_first_entry($ldapconn, $res_id))== false) {
			log_message('error', 'LDAP Auth: failure, entry of searchresult could not be fetched');
			return false;
		}

		if (( $user_dn = ldap_get_dn($ldapconn, $entry_id)) == false) {
			log_message('error', 'LDAP Auth: failure, user-dn could not be fetched');
			return false;
		}

		// User found, could not authenticate as user
		if (($link_id = ldap_bind($ldapconn, $user_dn, $password)) == false) {
			log_message('error', 'LDAP Auth: failure, username/password did not match: ' . $user_dn);
			return false;
		}

		log_message('debug', 'LDAP Auth: Success ' . $user_dn . ' authenticated successfully');

		$this->_user_ldap_dn = $user_dn;
		ldap_close($ldapconn);
		return true;
	}

	/**
	 * Check if the user is logged in.
	 *
	 * @param string $username The user's name
	 * @param string $password The user's password
	 * @return boolean
	 */
	protected function _check_login($username = '', $password = NULL)
	{
		if (empty($username))
		{
			return FALSE;
		}

		$auth_source = strtolower($this->config->item('auth_source'));

		if ($auth_source == 'ldap')
		{
			log_message('debug', 'performing LDAP authentication for $username');
			return $this->_perform_ldap_auth($username, $password);
		}

		$valid_logins = $this->config->item('rest_valid_logins');

		if ( ! array_key_exists($username, $valid_logins))
		{
			return FALSE;
		}

		// If actually NULL (not empty string) then do not check it
		if ($password !== NULL AND $valid_logins[$username] != $password)
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @todo document this.
	 */
	protected function _prepare_basic_auth()
	{
		// If whitelist is enabled it has the first chance to kick them out
		if (config_item('rest_ip_whitelist_enabled'))
		{
			$this->_check_whitelist_auth();
		}

		$username = NULL;
		$password = NULL;

		// mod_php
		if ($this->input->server('PHP_AUTH_USER'))
		{
			$username = $this->input->server('PHP_AUTH_USER');
			$password = $this->input->server('PHP_AUTH_PW');
		}

		// most other servers
		elseif ($this->input->server('HTTP_AUTHENTICATION'))
		{
			if (strpos(strtolower($this->input->server('HTTP_AUTHENTICATION')), 'basic') === 0)
			{
				list($username, $password) = explode(':', base64_decode(substr($this->input->server('HTTP_AUTHORIZATION'), 6)));
			}
		}

		if ( ! $this->_check_login($username, $password))
		{
			$this->_force_login();
		}
	}

	/**
	 * @todo Document this.
	 */
	protected function _prepare_digest_auth()
	{
		// If whitelist is enabled it has the first chance to kick them out
		if (config_item('rest_ip_whitelist_enabled'))
		{
			$this->_check_whitelist_auth();
		}

		$uniqid = uniqid(""); // Empty argument for backward compatibility
		// We need to test which server authentication variable to use
		// because the PHP ISAPI module in IIS acts different from CGI
		if ($this->input->server('PHP_AUTH_DIGEST'))
		{
			$digest_string = $this->input->server('PHP_AUTH_DIGEST');
		}
		elseif ($this->input->server('HTTP_AUTHORIZATION'))
		{
			$digest_string = $this->input->server('HTTP_AUTHORIZATION');
		}
		else
		{
			$digest_string = "";
		}

		// The $_SESSION['error_prompted'] variable is used to ask the password
		// again if none given or if the user enters wrong auth information.
		if (empty($digest_string))
		{
			$this->_force_login($uniqid);
		}

		// We need to retrieve authentication informations from the $auth_data variable
		preg_match_all('@(username|nonce|uri|nc|cnonce|qop|response)=[\'"]?([^\'",]+)@', $digest_string, $matches);
		$digest = array_combine($matches[1], $matches[2]);

		if ( ! array_key_exists('username', $digest) OR !$this->_check_login($digest['username']))
		{
			$this->_force_login($uniqid);
		}

		$valid_logins = $this->config->item('rest_valid_logins');
		$valid_pass = $valid_logins[$digest['username']];

		// This is the valid response expected
		$A1 = md5($digest['username'].':'.$this->config->item('rest_realm').':'.$valid_pass);
		$A2 = md5(strtoupper($this->request->method).':'.$digest['uri']);
		$valid_response = md5($A1.':'.$digest['nonce'].':'.$digest['nc'].':'.$digest['cnonce'].':'.$digest['qop'].':'.$A2);

		if ($digest['response'] != $valid_response)
		{
			header('HTTP/1.0 401 Unauthorized');
			header('HTTP/1.1 401 Unauthorized');
			exit;
		}
	}

	/**
	 * Check if the client's ip is in the 'rest_ip_whitelist' config
	 */
	protected function _check_whitelist_auth()
	{
		$whitelist = explode(',', config_item('rest_ip_whitelist'));

		array_push($whitelist, '127.0.0.1', '0.0.0.0');

		foreach ($whitelist AS &$ip)
		{
			$ip = trim($ip);
		}

		if ( ! in_array($this->input->ip_address(), $whitelist))
		{
			$this->response(array('status' => false, 'error' => 'Not authorized'), 401);
		}
	}

	/**
	 * @todo Document this.
	 *
	 * @param string $nonce
	 */
	protected function _force_login($nonce = '')
	{
		if ($this->config->item('rest_auth') == 'basic')
		{
			header('WWW-Authenticate: Basic realm="'.$this->config->item('rest_realm').'"');
		}
		elseif ($this->config->item('rest_auth') == 'digest')
		{
			header('WWW-Authenticate: Digest realm="'.$this->config->item('rest_realm').'", qop="auth", nonce="'.$nonce.'", opaque="'.md5($this->config->item('rest_realm')).'"');
		}

		$this->response(array('status' => false, 'error' => 'Not authorized'), 401);
	}

	/**
	 * Force it into an array
	 *
	 * @param object|array $data
	 * @return array
	 */
	protected function _force_loopable($data)
	{
		// Force it to be something useful
		if ( ! is_array($data) AND !is_object($data))
		{
			$data = (array) $data;
		}

		return $data;
	}

	// FORMATING FUNCTIONS ---------------------------------------------------------
	// Many of these have been moved to the Format class for better separation, but these methods will be checked too

	/**
	 * Encode as JSONP
	 *
	 * @param array $data The input data.
	 * @return string The JSONP data string (loadable from Javascript).
	 */
	protected function _format_jsonp($data = array())
	{
		return $this->get('callback').'('.json_encode($data).')';
	}

}
