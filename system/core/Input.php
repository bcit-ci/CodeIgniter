<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Input
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/input.html
 */
class CI_Input {

	var $ip_address				= FALSE;
	var $user_agent				= FALSE;
	var $_allow_get_array		= FALSE;
	var $_standardize_newlines	= TRUE;
	var $_enable_xss			= FALSE; // Set automatically based on config setting
	var $_enable_csrf			= FALSE; // Set automatically based on config setting


	/**
	* Constructor
	*
	* Sets whether to globally enable the XSS processing
	* and whether to allow the $_GET array
	*
	* @access	public
	*/
	function CI_Input()
	{
		log_message('debug', "Input Class Initialized");

		$this->_allow_get_array	= (config_item('enable_query_strings') === TRUE) ? TRUE : FALSE;
		$this->_enable_xss		= (config_item('global_xss_filtering') === TRUE) ? TRUE : FALSE;
		$this->_enable_csrf		= (config_item('csrf_protection') === TRUE) ? TRUE : FALSE;

		// Do we need to load the security class?
		if ($this->_enable_xss == TRUE OR $this->_enable_csrf == TRUE)
		{
			$this->security =& load_class('Security');
		}

		// Do we need the Unicode class?
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
	* This is a helper function to retrieve values from global arrays
	*
	* @access	private
	* @param	array
	* @param	string
	* @param	bool
	* @return	string
	*/
	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
	{
		if ( ! isset($array[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			$_security =& load_class('Security');
			return $_security->xss_clean($array[$index]);
		}

		return $array[$index];
	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the GET array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function get($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_GET, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the POST array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function post($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_POST, $index, $xss_clean);
	}


	// --------------------------------------------------------------------

	/**
	* Fetch an item from either the GET array or the POST
	*
	* @access	public
	* @param	string	The index key
	* @param	bool	XSS cleaning
	* @return	string
	*/
	function get_post($index = '', $xss_clean = FALSE)
	{
		if ( ! isset($_POST[$index]) )
		{
			return $this->get($index, $xss_clean);
		}
		else
		{
			return $this->post($index, $xss_clean);
		}
	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the COOKIE array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function cookie($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
	}

	// ------------------------------------------------------------------------

	/**
	* Set cookie
	*
	* Accepts six parameter, or you can submit an associative
	* array in the first parameter containing all the values.
	*
	* @access	public
	* @param	mixed
	* @param	string	the value of the cookie
	* @param	string	the number of seconds until expiration
	* @param	string	the cookie domain.  Usually:  .yourdomain.com
	* @param	string	the cookie path
	* @param	string	the cookie prefix
	* @return	void
	*/
	function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '')
	{
		if (is_array($name))
		{
			foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'name') as $item)
			{
				if (isset($name[$item]))
				{
					$$item = $name[$item];
				}
			}
		}

		if ($prefix == '' AND config_item('cookie_prefix') != '')
		{
			$prefix = config_item('cookie_prefix');
		}
		if ($domain == '' AND config_item('cookie_domain') != '')
		{
			$domain = config_item('cookie_domain');
		}
		if ($path == '/' AND config_item('cookie_path') != '/')
		{
			$path = config_item('cookie_path');
		}

		if ( ! is_numeric($expire))
		{
			$expire = time() - 86500;
		}
		else
		{
			if ($expire > 0)
			{
				$expire = time() + $expire;
			}
			else
			{
				$expire = 0;
			}
		}

		setcookie($prefix.$name, $value, $expire, $path, $domain, 0);
	}

	// --------------------------------------------------------------------

	/**
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
	}

	// --------------------------------------------------------------------

	/**
	* Fetch the IP Address
	*
	* @access	public
	* @return	string
	*/
	function ip_address()
	{
		if ($this->ip_address !== FALSE)
		{
			return $this->ip_address;
		}
		
		if (config_item('proxy_ips') != '' && $this->server('HTTP_X_FORWARDED_FOR') && $this->server('REMOTE_ADDR'))
		{
			$proxies = preg_split('/[\s,]/', config_item('proxy_ips'), -1, PREG_SPLIT_NO_EMPTY);
			$proxies = is_array($proxies) ? $proxies : array($proxies);

			$this->ip_address = in_array($_SERVER['REMOTE_ADDR'], $proxies) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		}
		elseif ($this->server('REMOTE_ADDR') AND $this->server('HTTP_CLIENT_IP'))
		{
			$this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($this->server('REMOTE_ADDR'))
		{
			$this->ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif ($this->server('HTTP_CLIENT_IP'))
		{
			$this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($this->server('HTTP_X_FORWARDED_FOR'))
		{
			$this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if ($this->ip_address === FALSE)
		{
			$this->ip_address = '0.0.0.0';
			return $this->ip_address;
		}

		if (strpos($this->ip_address, ',') !== FALSE)
		{
			$x = explode(',', $this->ip_address);
			$this->ip_address = trim(end($x));
		}

		if ( ! $this->valid_ip($this->ip_address))
		{
			$this->ip_address = '0.0.0.0';
		}

		return $this->ip_address;
	}

	// --------------------------------------------------------------------

	/**
	* Validate IP Address
	*
	* Updated version suggested by Geert De Deckere
	* 
	* @access	public
	* @param	string
	* @return	string
	*/
	function valid_ip($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) != 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if ($ip_segments[0][0] == '0')
		{
			return FALSE;
		}
		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be 
			// longer than 3 digits or greater then 255
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	* User Agent
	*
	* @access	public
	* @return	string
	*/
	function user_agent()
	{
		if ($this->user_agent !== FALSE)
		{
			return $this->user_agent;
		}

		$this->user_agent = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

		return $this->user_agent;
	}

	// --------------------------------------------------------------------

	/**
	* Sanitize Globals
	*
	* This function does the following:
	*
	* Unsets $_GET data (if query strings are not enabled)
	*
	* Unsets all globals if register_globals is enabled
	*
	* Standardizes newline characters to \n
	*
	* @access	private
	* @return	void
	*/
	function _sanitize_globals()
	{
		// It would be "wrong" to unset any of these GLOBALS.
		$protected = array('_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION', '_ENV', 'GLOBALS', 'HTTP_RAW_POST_DATA',
							'system_folder', 'application_folder', 'BM', 'EXT', 'CFG', 'URI', 'RTR', 'OUT', 'IN');

		// Unset globals for securiy. 
		// This is effectively the same as register_globals = off
		foreach (array($_GET, $_POST, $_COOKIE) as $global)
		{
			if ( ! is_array($global))
			{
				if ( ! in_array($global, $protected))
				{
					global $$global;
					$$global = NULL;
				}
			}
			else
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
		}

		// Is $_GET data allowed? If not we'll set the $_GET to an empty array
		if ($this->_allow_get_array == FALSE)
		{
			$_GET = array();
		}
		else
		{
			if (is_array($_GET) AND count($_GET) > 0)
			{
				foreach($_GET as $key => $val)
				{
					$_GET[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
				}
			}
		}

		// Clean $_POST Data
		if (is_array($_POST) AND count($_POST) > 0)
		{
			foreach($_POST as $key => $val)
			{
				$_POST[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
		}

		// Clean $_COOKIE Data
		if (is_array($_COOKIE) AND count($_COOKIE) > 0)
		{
			// Also get rid of specially treated cookies that might be set by a server
			// or silly application, that are of no use to a CI application anyway
			// but that when present will trip our 'Disallowed Key Characters' alarm
			// http://www.ietf.org/rfc/rfc2109.txt
			// note that the key names below are single quoted strings, and are not PHP variables
			unset($_COOKIE['$Version']);
			unset($_COOKIE['$Path']);
			unset($_COOKIE['$Domain']);

			foreach($_COOKIE as $key => $val)
			{
				$_COOKIE[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
		}

		// Sanitize PHP_SELF
		$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);


		// CSRF Protection check
		if ($this->_enable_csrf == TRUE)
		{
			$this->security->csrf_verify();
		}

		log_message('debug', "Global POST and COOKIE data sanitized");
	}

	// --------------------------------------------------------------------

	/**
	* Clean Input Data
	*
	* This is a helper function. It escapes data and
	* standardizes newline characters to \n
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	function _clean_input_data($str)
	{
		if (is_array($str))
		{
			$new_array = array();
			foreach ($str as $key => $val)
			{
				$new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
			return $new_array;
		}

		// We strip slashes if magic quotes is on to keep things consistent
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			$str = $this->uni->clean_string($str);
		}

		// Should we filter the input data?
		if ($this->_enable_xss === TRUE)
		{
			$str = $this->security->xss_clean($str);
		}

		// Standardize newlines if needed
		if ($this->_standardize_newlines == TRUE)
		{
			if (strpos($str, "\r") !== FALSE)
			{
				$str = str_replace(array("\r\n", "\r"), "\n", $str);
			}
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	* Clean Keys
	*
	* This is a helper function. To prevent malicious users
	* from trying to exploit keys we make sure that keys are
	* only named with alpha-numeric text and a few other items.
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	function _clean_input_keys($str)
	{
		if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
		{
			exit('Disallowed Key Characters.');
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			$str = $this->uni->clean_string($str);
		}

		return $str;
	}

}
// END Input class

/* End of file Input.php */
/* Location: ./system/core/Input.php */