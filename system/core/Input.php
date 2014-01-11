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
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Input
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/input.html
 */
class CI_Input {

	/**
	 * IP address of the current user
	 *
	 * @var	string
	 */
	public $ip_address = FALSE;

	/**
<<<<<<< develop
	 * User agent strin
=======
	 * User agent string
>>>>>>> local
	 *
	 * @var	string
	 */
	public $user_agent = FALSE;

	/**
	 * Allow GET array flag
<<<<<<< develop
	 *
	 * If set to FALSE, then $_GET will be set to an empty array.
	 *
=======
	 *
	 * If set to FALSE, then $_GET will be set to an empty array.
	 *
>>>>>>> local
	 * @var	bool
	 */
	protected $_allow_get_array = TRUE;

	/**
<<<<<<< develop
	 * Standartize new lines flag
	 *
	 * If set to TRUE, then newlines are standardized.
	 *
=======
	 * Standardize new lines flag
	 *
	 * If set to TRUE, then newlines are standardized.
	 *
>>>>>>> local
	 * @var	bool
	 */
	protected $_standardize_newlines = TRUE;

	/**
	 * Enable XSS flag
<<<<<<< develop
	 *
	 * Determines whether the XSS filter is always active when
	 * GET, POST or COOKIE data is encountered.
	 * Set automatically based on config setting.
	 *
=======
	 *
	 * Determines whether the XSS filter is always active when
	 * GET, POST or COOKIE data is encountered.
	 * Set automatically based on config setting.
	 *
>>>>>>> local
	 * @var	bool
	 */
	protected $_enable_xss = FALSE;

	/**
	 * Enable CSRF flag
	 *
	 * Enables a CSRF cookie token to be set.
	 * Set automatically based on config setting.
	 *
	 * @var	bool
	 */
	protected $_enable_csrf = FALSE;

	/**
	 * List of all HTTP request headers
	 *
	 * @var array
	 */
	protected $headers = array();

<<<<<<< HEAD
=======
	/**
	 * Input stream data
	 *
	 * Parsed from php://input at runtime
	 *
	 * @see	CI_Input::input_stream()
	 * @var	array
	 */
	protected $_input_stream = NULL;

<<<<<<< develop
=======
>>>>>>> upstream/develop
>>>>>>> local
	/**
	 * Class constructor
	 *
	 * Determines whether to globally enable the XSS processing
	 * and whether to allow the $_GET array.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		log_message('debug', 'Input Class Initialized');

<<<<<<< develop
		$this->_allow_get_array	= (config_item('allow_get_array') === TRUE);
		$this->_enable_xss	= (config_item('global_xss_filtering') === TRUE);
		$this->_enable_csrf	= (config_item('csrf_protection') === TRUE);
=======
		$this->_allow_get_array		= (config_item('allow_get_array') === TRUE);
		$this->_enable_xss		= (config_item('global_xss_filtering') === TRUE);
		$this->_enable_csrf		= (config_item('csrf_protection') === TRUE);
		$this->_sandardize_newlines	= (bool) config_item('standardize_newlines');
>>>>>>> local

		global $SEC;
		$this->security =& $SEC;

		// Do we need the UTF-8 class?
		if (UTF8_ENABLED === TRUE)
		{
			global $UNI;
			$this->uni =& $UNI;
		}

		// Sanitize global arrays
		$this->_sanitize_globals();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch from array
	 *
	 * Internal method used to retrieve values from global arrays.
	 *
	 * @param	array	&$array		$_GET, $_POST, $_COOKIE, $_SERVER, etc.
	 * @param	string	$index		Index for item to be fetched from $array
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
<<<<<<< develop
	protected function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
=======
	protected function _fetch_from_array(&$array, $index = '', $xss_clean = NULL)
>>>>>>> local
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		if (isset($array[$index]))
		{
<<<<<<< develop
			return NULL;
=======
			$value = $array[$index];
>>>>>>> local
		}
		elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) // Does the index contain array notation
		{
			$value = $array;
			for ($i = 0; $i < $count; $i++)
			{
				$key = trim($matches[0][$i], '[]');
				if ($key === '') // Empty notation will return the value as array
				{
					break;
				}

				if (isset($value[$key]))
				{
					$value = $value[$key];
				}
				else
				{
					return NULL;
				}
			}
		}
		else
		{
			return NULL;
		}

		return ($xss_clean === TRUE)
			? $this->security->xss_clean($value)
			: $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the GET array
	 *
	 * @param	string	$index		Index for item to be fetched from $_GET
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
<<<<<<< develop
	public function get($index = NULL, $xss_clean = FALSE)
=======
	public function get($index = NULL, $xss_clean = NULL)
>>>>>>> local
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		// Check if a field has been provided
		if ($index === NULL)
		{
			if (empty($_GET))
			{
				return array();
			}

			$get = array();

			// loop through the full _GET array
			foreach (array_keys($_GET) as $key)
			{
				$get[$key] = $this->_fetch_from_array($_GET, $key, $xss_clean);
			}
			return $get;
		}

		return $this->_fetch_from_array($_GET, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the POST array
	 *
	 * @param	string	$index		Index for item to be fetched from $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
<<<<<<< develop
	public function post($index = NULL, $xss_clean = FALSE)
=======
	public function post($index = NULL, $xss_clean = NULL)
>>>>>>> local
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		// Check if a field has been provided
		if ($index === NULL)
		{
			if (empty($_POST))
			{
				return array();
			}

			$post = array();

			// Loop through the full _POST array and return it
			foreach (array_keys($_POST) as $key)
			{
				$post[$key] = $this->_fetch_from_array($_POST, $key, $xss_clean);
			}
			return $post;
		}

		return $this->_fetch_from_array($_POST, $index, $xss_clean);
	}

<<<<<<< develop
	// --------------------------------------------------------------------

	/**
	 * Fetch an item from POST data with fallback to GET
	 *
	 * @param	string	$index		Index for item to be fetched from $_POST or $_GET
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function get_post($index = '', $xss_clean = FALSE)
	{
		return isset($_POST[$index])
			? $this->post($index, $xss_clean)
			: $this->get($index, $xss_clean);
=======
	// --------------------------------------------------------------------

	/**
	 * Fetch an item from POST data with fallback to GET
	 *
	 * @param	string	$index		Index for item to be fetched from $_POST or $_GET
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function post_get($index = '', $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		return isset($_POST[$index])
			? $this->post($index, $xss_clean)
			: $this->get($index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from GET data with fallback to POST
	 *
	 * @param	string	$index		Index for item to be fetched from $_GET or $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function get_post($index = '', $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		return isset($_GET[$index])
			? $this->get($index, $xss_clean)
			: $this->post($index, $xss_clean);
>>>>>>> local
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @param	string	$index		Index for item to be fetched from $_COOKIE
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
<<<<<<< develop
	public function cookie($index = '', $xss_clean = FALSE)
=======
	public function cookie($index = '', $xss_clean = NULL)
>>>>>>> local
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the SERVER array
	 *
	 * @param	string	$index		Index for item to be fetched from $_SERVER
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
<<<<<<< develop
	public function server($index = '', $xss_clean = FALSE)
	{
=======
	public function server($index = '', $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

>>>>>>> local
		return $this->_fetch_from_array($_SERVER, $index, $xss_clean);
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch an item from the php://input stream
	 *
	 * Useful when you need to access PUT, DELETE or PATCH request data.
	 *
	 * @param	string	$index		Index for item to be fetched
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
<<<<<<< develop
	public function input_stream($index = '', $xss_clean = FALSE)
	{
=======
	public function input_stream($index = '', $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

>>>>>>> local
		// The input stream can only be read once, so we'll need to check
		// if we have already done that first.
		if (is_array($this->_input_stream))
		{
			return $this->_fetch_from_array($this->_input_stream, $index, $xss_clean);
		}

		// Parse the input stream in our cache var
		parse_str(file_get_contents('php://input'), $this->_input_stream);
		if ( ! is_array($this->_input_stream))
		{
			$this->_input_stream = array();
			return NULL;
		}

		return $this->_fetch_from_array($this->_input_stream, $index, $xss_clean);
	}

	// ------------------------------------------------------------------------

	/**
	 * Set cookie
	 *
	 * Accepts an arbitrary number of parameters (up to 7) or an associative
	 * array in the first parameter containing all the values.
	 *
	 * @param	string|mixed[]	$name		Cookie name or an array containing parameters
	 * @param	string		$value		Cookie value
	 * @param	int		$expire		Cookie expiration time in seconds
	 * @param	string		$domain		Cookie domain (e.g.: '.yourdomain.com')
	 * @param	string		$path		Cookie path (default: '/')
	 * @param	string		$prefix		Cookie name prefix
	 * @param	bool		$secure		Whether to only transfer cookies via SSL
	 * @param	bool		$httponly	Whether to only makes the cookie accessible via HTTP (no javascript)
	 * @return	void
	 */
<<<<<<< develop
	public function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
=======
	public function set_cookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
>>>>>>> local
	{
		if (is_array($name))
		{
			// always leave 'name' in last place, as the loop will break otherwise, due to $$item
			foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name') as $item)
			{
				if (isset($name[$item]))
				{
					$$item = $name[$item];
				}
			}
		}

		if ($prefix === '' && config_item('cookie_prefix') !== '')
		{
			$prefix = config_item('cookie_prefix');
		}

		if ($domain == '' && config_item('cookie_domain') != '')
		{
			$domain = config_item('cookie_domain');
		}

		if ($path === '/' && config_item('cookie_path') !== '/')
		{
			$path = config_item('cookie_path');
		}

		if ($secure === FALSE && config_item('cookie_secure') !== FALSE)
		{
			$secure = config_item('cookie_secure');
		}

		if ($httponly === FALSE && config_item('cookie_httponly') !== FALSE)
		{
			$httponly = config_item('cookie_httponly');
		}

		if ( ! is_numeric($expire))
		{
			$expire = time() - 86500;
		}
		else
		{
			$expire = ($expire > 0) ? time() + $expire : 0;
		}

		setcookie($prefix.$name, $value, $expire, $path, $domain, $secure, $httponly);
<<<<<<< develop
=======
	}

	// --------------------------------------------------------------------

	/**
<<<<<<< HEAD
	* Fetch an item from the SERVER array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function server($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_SERVER, $index, $xss_clean);
>>>>>>> local
	}

	// --------------------------------------------------------------------

	/**
<<<<<<< develop
=======
	* Fetch the IP Address
	*
	* @return	string
	*/
=======
>>>>>>> local
	 * Fetch the IP Address
	 *
	 * Determines and validates the visitor's IP address.
	 *
	 * @return	string	IP address
	 */
<<<<<<< develop
=======
>>>>>>> upstream/develop
>>>>>>> local
	public function ip_address()
	{
		if ($this->ip_address !== FALSE)
		{
			return $this->ip_address;
		}

		$proxy_ips = config_item('proxy_ips');
<<<<<<< develop
		if ( ! empty($proxy_ips) && ! is_array($proxy_ips))
=======
<<<<<<< HEAD
		if ( ! empty($proxy_ips))
>>>>>>> local
		{
			$proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
		}

		$this->ip_address = $this->server('REMOTE_ADDR');

		if ($proxy_ips)
		{
			foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header)
			{
				if (($spoof = $this->server($header)) !== NULL)
				{
					// Some proxies typically list the whole chain of IP
					// addresses through which the client has reached us.
					// e.g. client_ip, proxy_ip1, proxy_ip2, etc.
					sscanf($spoof, '%[^,]', $spoof);

					if ( ! $this->valid_ip($spoof))
					{
						$spoof = NULL;
					}
					else
					{
						break;
					}
				}
			}

			if ($spoof)
			{
				for ($i = 0, $c = count($proxy_ips); $i < $c; $i++)
				{
					// Check if we have an IP address or a subnet
					if (strpos($proxy_ips[$i], '/') === FALSE)
					{
						// An IP address (and not a subnet) is specified.
						// We can compare right away.
						if ($proxy_ips[$i] === $this->ip_address)
						{
							$this->ip_address = $spoof;
							break;
						}

						continue;
					}

					// We have a subnet ... now the heavy lifting begins
					isset($separator) OR $separator = $this->valid_ip($this->ip_address, 'ipv6') ? ':' : '.';

					// If the proxy entry doesn't match the IP protocol - skip it
					if (strpos($proxy_ips[$i], $separator) === FALSE)
					{
						continue;
					}

					// Convert the REMOTE_ADDR IP address to binary, if needed
					if ( ! isset($ip, $sprintf))
					{
						if ($separator === ':')
						{
							// Make sure we're have the "full" IPv6 format
							$ip = explode(':',
								str_replace('::',
									str_repeat(':', 9 - substr_count($this->ip_address, ':')),
									$this->ip_address
								)
							);

							for ($i = 0; $i < 8; $i++)
							{
								$ip[$i] = intval($ip[$i], 16);
							}

							$sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
						}
						else
						{
							$ip = explode('.', $this->ip_address);
							$sprintf = '%08b%08b%08b%08b';
						}

						$ip = vsprintf($sprintf, $ip);
					}

					// Split the netmask length off the network address
					sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);

<<<<<<< develop
=======
			return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
=======
		if ( ! empty($proxy_ips) && ! is_array($proxy_ips))
		{
			$proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
		}

		$this->ip_address = $this->server('REMOTE_ADDR');

		if ($proxy_ips)
		{
			foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header)
			{
				if (($spoof = $this->server($header)) !== NULL)
				{
					// Some proxies typically list the whole chain of IP
					// addresses through which the client has reached us.
					// e.g. client_ip, proxy_ip1, proxy_ip2, etc.
					sscanf($spoof, '%[^,]', $spoof);

					if ( ! $this->valid_ip($spoof))
					{
						$spoof = NULL;
					}
					else
					{
						break;
					}
				}
			}

			if ($spoof)
			{
				for ($i = 0, $c = count($proxy_ips); $i < $c; $i++)
				{
					// Check if we have an IP address or a subnet
					if (strpos($proxy_ips[$i], '/') === FALSE)
					{
						// An IP address (and not a subnet) is specified.
						// We can compare right away.
						if ($proxy_ips[$i] === $this->ip_address)
						{
							$this->ip_address = $spoof;
							break;
						}

						continue;
					}

					// We have a subnet ... now the heavy lifting begins
					isset($separator) OR $separator = $this->valid_ip($this->ip_address, 'ipv6') ? ':' : '.';

					// If the proxy entry doesn't match the IP protocol - skip it
					if (strpos($proxy_ips[$i], $separator) === FALSE)
					{
						continue;
					}

					// Convert the REMOTE_ADDR IP address to binary, if needed
					if ( ! isset($ip, $sprintf))
					{
						if ($separator === ':')
						{
							// Make sure we're have the "full" IPv6 format
							$ip = explode(':',
								str_replace('::',
									str_repeat(':', 9 - substr_count($this->ip_address, ':')),
									$this->ip_address
								)
							);

							for ($i = 0; $i < 8; $i++)
							{
								$ip[$i] = intval($ip[$i], 16);
							}

							$sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
						}
						else
						{
							$ip = explode('.', $this->ip_address);
							$sprintf = '%08b%08b%08b%08b';
						}

						$ip = vsprintf($sprintf, $ip);
					}

					// Split the netmask length off the network address
					sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);

>>>>>>> local
					// Again, an IPv6 address is most likely in a compressed form
					if ($separator === ':')
					{
						$netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
						for ($i = 0; $i < 8; $i++)
						{
							$netaddr[$i] = intval($netaddr[$i], 16);
						}
					}
					else
					{
						$netaddr = explode('.', $netaddr);
					}
<<<<<<< develop

					// Convert to binary and finally compare
					if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0)
					{
						$this->ip_address = $spoof;
						break;
					}
				}
=======

					// Convert to binary and finally compare
					if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0)
					{
						$this->ip_address = $spoof;
						break;
					}
				}
			}
>>>>>>> upstream/develop
		}

		if ($which !== 'ipv6' && $which !== 'ipv4')
		{
<<<<<<< HEAD
			if (strpos($ip, ':') !== FALSE)
			{
				$which = 'ipv6';
			}
			elseif (strpos($ip, '.') !== FALSE)
			{
				$which = 'ipv4';
			}
			else
			{
				return FALSE;
>>>>>>> local
			}
=======
			return $this->ip_address = '0.0.0.0';
>>>>>>> upstream/develop
		}

<<<<<<< develop
		if ( ! $this->valid_ip($this->ip_address))
=======
		$func = '_valid_'.$which;
		return $this->$func($ip);
	}

	// --------------------------------------------------------------------

	/**
<<<<<<< HEAD
	* Validate IPv4 Address
	*
	* Updated version suggested by Geert De Deckere
	*
	* @access	protected
	* @param	string
	* @return	bool
	*/
	protected function _valid_ipv4($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) !== 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if ($ip_segments[0][0] == '0')
>>>>>>> local
		{
			return $this->ip_address = '0.0.0.0';
		}

<<<<<<< develop
		return $this->ip_address;
=======
		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
			{
				return FALSE;
			}
=======
	 * Validate IP Address
	 *
	 * @param	string	$ip	IP address
	 * @param	string	$which	IP protocol: 'ipv4' or 'ipv6'
	 * @return	bool
	 */
	public function valid_ip($ip, $which = '')
	{
		switch (strtolower($which))
		{
			case 'ipv4':
				$which = FILTER_FLAG_IPV4;
				break;
			case 'ipv6':
				$which = FILTER_FLAG_IPV6;
				break;
			default:
				$which = NULL;
				break;
>>>>>>> upstream/develop
		}

		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
>>>>>>> local
	}

	// --------------------------------------------------------------------

	/**
<<<<<<< develop
	 * Validate IP Address
	 *
	 * @param	string	$ip	IP address
	 * @param	string	$which	IP protocol: 'ipv4' or 'ipv6'
	 * @return	bool
	 */
	public function valid_ip($ip, $which = '')
=======
<<<<<<< HEAD
	* Validate IPv6 Address
	*
	* @access	protected
	* @param	string
	* @return	bool
	*/
	protected function _valid_ipv6($str)
>>>>>>> local
	{
		switch (strtolower($which))
		{
			case 'ipv4':
				$which = FILTER_FLAG_IPV4;
				break;
			case 'ipv6':
				$which = FILTER_FLAG_IPV6;
				break;
			default:
				$which = NULL;
				break;
		}

		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
	}

	// --------------------------------------------------------------------

	/**
<<<<<<< develop
=======
	* User Agent
	*
	* @access	public
	* @return	string
	*/
	function user_agent()
=======
>>>>>>> local
	 * Fetch User Agent string
	 *
	 * @return	string|null	User Agent string or NULL if it doesn't exist
	 */
	public function user_agent()
<<<<<<< develop
=======
>>>>>>> upstream/develop
>>>>>>> local
	{
		if ($this->user_agent !== FALSE)
		{
			return $this->user_agent;
		}

		return $this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Sanitize Globals
	 *
	 * Internal method serving for the following purposes:
	 *
	 *	- Unsets $_GET data (if query strings are not enabled)
	 *	- Unsets all globals if register_globals is enabled
	 *	- Cleans POST, COOKIE and SERVER data
	 * 	- Standardizes newline characters to PHP_EOL
	 *
	 * @return	void
	 */
	protected function _sanitize_globals()
	{
		// It would be "wrong" to unset any of these GLOBALS.
		$protected = array(
			'_SERVER',
			'_GET',
			'_POST',
			'_FILES',
			'_REQUEST',
			'_SESSION',
			'_ENV',
			'GLOBALS',
			'HTTP_RAW_POST_DATA',
			'system_folder',
			'application_folder',
			'BM',
			'EXT',
			'CFG',
			'URI',
			'RTR',
			'OUT',
			'IN'
		);

		// Unset globals for security.
		// This is effectively the same as register_globals = off
		// PHP 5.4 no longer has the register_globals functionality.
		if ( ! is_php('5.4'))
		{
			foreach (array($_GET, $_POST, $_COOKIE) as $global)
			{
				if (is_array($global))
				{
					foreach ($global as $key => $val)
					{
						if ( ! in_array($key, $protected))
						{
							global $$key;
							$$key = NULL;
						}
					}
				}
				elseif ( ! in_array($global, $protected))
				{
					global $$global;
					$$global = NULL;
				}
			}
		}

		// Is $_GET data allowed? If not we'll set the $_GET to an empty array
		if ($this->_allow_get_array === FALSE)
		{
			$_GET = array();
		}
		elseif (is_array($_GET) && count($_GET) > 0)
		{
			foreach ($_GET as $key => $val)
			{
				$_GET[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
		}

		// Clean $_POST Data
		if (is_array($_POST) && count($_POST) > 0)
		{
			foreach ($_POST as $key => $val)
			{
				$_POST[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
		}

		// Clean $_COOKIE Data
		if (is_array($_COOKIE) && count($_COOKIE) > 0)
		{
			// Also get rid of specially treated cookies that might be set by a server
			// or silly application, that are of no use to a CI application anyway
			// but that when present will trip our 'Disallowed Key Characters' alarm
			// http://www.ietf.org/rfc/rfc2109.txt
			// note that the key names below are single quoted strings, and are not PHP variables
			unset(
				$_COOKIE['$Version'],
				$_COOKIE['$Path'],
				$_COOKIE['$Domain']
			);

			foreach ($_COOKIE as $key => $val)
			{
				if (($cookie_key = $this->_clean_input_keys($key)) !== FALSE)
				{
					$_COOKIE[$cookie_key] = $this->_clean_input_data($val);
				}
				else
				{
					unset($_COOKIE[$key]);
				}
			}
		}

		// Sanitize PHP_SELF
		$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);

<<<<<<< develop
		// CSRF Protection check
		if ($this->_enable_csrf === TRUE && ! $this->is_cli_request())
=======
<<<<<<< HEAD

		// CSRF Protection check on HTTP requests
		if ($this->_enable_csrf == TRUE && ! $this->is_cli_request())
=======
		// CSRF Protection check
		if ($this->_enable_csrf === TRUE && ! is_cli())
>>>>>>> upstream/develop
>>>>>>> local
		{
			$this->security->csrf_verify();
		}

<<<<<<< develop
		log_message('debug', 'Global POST and COOKIE data sanitized');
=======
		log_message('debug', 'Global POST, GET and COOKIE data sanitized');
>>>>>>> local
	}

	// --------------------------------------------------------------------

	/**
	 * Clean Input Data
	 *
	 * Internal method that aids in escaping data and
	 * standardizing newline characters to PHP_EOL.
	 *
	 * @param	string|string[]	$str	Input string(s)
	 * @return	string
	 */
	protected function _clean_input_data($str)
	{
		if (is_array($str))
		{
			$new_array = array();
			foreach (array_keys($str) as $key)
			{
				$new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($str[$key]);
			}
			return $new_array;
		}

		/* We strip slashes if magic quotes is on to keep things consistent

		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			 it will probably not exist in future versions at all.
		*/
		if ( ! is_php('5.4') && get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			$str = $this->uni->clean_string($str);
		}

		// Remove control characters
		$str = remove_invisible_characters($str, FALSE);

		// Standardize newlines if needed
		if ($this->_standardize_newlines === TRUE)
		{
			return preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $str);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Clean Keys
	 *
	 * Internal method that helps to prevent malicious users
	 * from trying to exploit keys we make sure that keys are
	 * only named with alpha-numeric text and a few other items.
	 *
	 * @param	string	$str	Input string
<<<<<<< develop
	 * @return	string
	 */
	protected function _clean_input_keys($str)
	{
		if ( ! preg_match('/^[a-z0-9:_\/|-]+$/i', $str))
		{
			set_status_header(503);
			exit('Disallowed Key Characters.');
=======
	 * @param	string	$fatal	Whether to terminate script exection
	 *				or to return FALSE if an invalid
	 *				key is encountered
	 * @return	string|bool
	 */
	protected function _clean_input_keys($str, $fatal = TRUE)
	{
		if ( ! preg_match('/^[a-z0-9:_\/|-]+$/i', $str))
		{
			if ($fatal === TRUE)
			{
				return FALSE;
			}
			else
			{
				set_status_header(503);
				echo 'Disallowed Key Characters.';
				exit(EXIT_USER_INPUT);
			}
>>>>>>> local
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			return $this->uni->clean_string($str);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Request Headers
	 *
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	array
	 */
	public function request_headers($xss_clean = FALSE)
	{
<<<<<<< develop
		// In Apache, you can simply call apache_request_headers()
		if (function_exists('apache_request_headers'))
=======
		// If header is already defined, return it immediately
		if ( ! empty($this->headers))
>>>>>>> local
		{
			return $this->headers;
		}
<<<<<<< develop
		else
		{
			$headers['Content-Type'] = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');

			foreach ($_SERVER as $key => $val)
			{
				if (sscanf($key, 'HTTP_%s', $header) === 1)
				{
					$headers[$header] = $this->_fetch_from_array($_SERVER, $key, $xss_clean);
				}
			}
=======

		// In Apache, you can simply call apache_request_headers()
		if (function_exists('apache_request_headers'))
		{
			return $this->headers = apache_request_headers();
>>>>>>> local
		}

		$this->headers['Content-Type'] = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');

		foreach ($_SERVER as $key => $val)
		{
<<<<<<< develop
			$key = str_replace(array('_', '-'), ' ', strtolower($key));
			$key = str_replace(' ', '-', ucwords($key));
=======
			if (sscanf($key, 'HTTP_%s', $header) === 1)
			{
				// take SOME_HEADER and turn it into Some-Header
				$header = str_replace('_', ' ', strtolower($header));
				$header = str_replace(' ', '-', ucwords($header));
>>>>>>> local

				$this->headers[$header] = $this->_fetch_from_array($_SERVER, $key, $xss_clean);
			}
		}

		return $this->headers;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Request Header
	 *
	 * Returns the value of a single member of the headers class member
	 *
	 * @param	string		$index		Header name
	 * @param	bool		$xss_clean	Whether to apply XSS filtering
	 * @return	string|bool	The requested header on success or FALSE on failure
	 */
	public function get_request_header($index, $xss_clean = FALSE)
	{
		if (empty($this->headers))
		{
			$this->request_headers();
		}

		if ( ! isset($this->headers[$index]))
		{
			return NULL;
		}

		return ($xss_clean === TRUE)
			? $this->security->xss_clean($this->headers[$index])
			: $this->headers[$index];
	}

	// --------------------------------------------------------------------

	/**
	 * Is AJAX request?
	 *
	 * Test to see if a request contains the HTTP_X_REQUESTED_WITH header.
	 *
	 * @return 	bool
	 */
	public function is_ajax_request()
	{
		return ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	// --------------------------------------------------------------------

	/**
	 * Is CLI request?
	 *
	 * Test to see if a request was made from the command line.
	 *
<<<<<<< HEAD
	 * @return 	bool
	 */
	public function is_cli_request()
	{
		return (php_sapi_name() === 'cli' OR defined('STDIN'));
=======
	 * @deprecated	3.0.0	Use is_cli() instead
	 * @return      bool
	 */
	public function is_cli_request()
	{
		return is_cli();
	}

	// --------------------------------------------------------------------

	/**
	 * Get Request Method
	 *
	 * Return the request method
	 *
	 * @param	bool	$upper	Whether to return in upper or lower case
	 *				(default: FALSE)
	 * @return 	string
	 */
	public function method($upper = FALSE)
	{
		return ($upper)
			? strtoupper($this->server('REQUEST_METHOD'))
			: strtolower($this->server('REQUEST_METHOD'));
>>>>>>> upstream/develop
	}

	// --------------------------------------------------------------------

	/**
	 * Get Request Method
	 *
	 * Return the request method
	 *
	 * @param	bool	$upper	Whether to return in upper or lower case
	 *				(default: FALSE)
	 * @return 	string
	 */
	public function method($upper = FALSE)
	{
		return ($upper)
			? strtoupper($this->server('REQUEST_METHOD'))
			: strtolower($this->server('REQUEST_METHOD'));
	}

}

/* End of file Input.php */
/* Location: ./system/core/Input.php */