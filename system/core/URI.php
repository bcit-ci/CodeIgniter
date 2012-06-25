<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * URI Class
 *
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	URI
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/uri.html
 */
class CI_URI {

	/**
	 * List of cached uri segments
	 *
	 * @var array
	 */
	public $keyval =	array();

	/**
	 * Current uri string
	 *
	 * @var string
	 */
	public $uri_string;

	/**
	 * List of uri segments
	 *
	 * @var array
	 */
	public $segments =	array();

	/**
	 * Re-indexed list of uri segments
	 * Starts at 1 instead of 0
	 *
	 * @var array
	 */
	public $rsegments =	array();

	/**
	 * Constructor
	 *
	 * Simply globalizes the $RTR object. The front
	 * loads the Router class early on so it's not available
	 * normally as other classes are.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->config =& load_class('Config', 'core');
		log_message('debug', 'URI Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the URI String
	 *
	 * Called by CI_Router
	 *
	 * @return	void
	 */
	public function _fetch_uri_string()
	{
		if (strtoupper($this->config->item('uri_protocol')) === 'AUTO')
		{
			// Is the request coming from the command line?
			if ($this->_is_cli_request())
			{
				$this->_set_uri_string($this->_parse_cli_args());
				return;
			}

			// Let's try the REQUEST_URI first, this will work in most situations
			if ($uri = $this->_detect_uri())
			{
				$this->_set_uri_string($uri);
				return;
			}

			// Is there a PATH_INFO variable?
			// Note: some servers seem to have trouble with getenv() so we'll test it two ways
			$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
			if (trim($path, '/') !== '' && $path !== '/'.SELF)
			{
				$this->_set_uri_string($path);
				return;
			}

			// No PATH_INFO?... What about QUERY_STRING?
			$path = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
			if (trim($path, '/') !== '')
			{
				$this->_set_uri_string($path);
				return;
			}

			// As a last ditch effort lets try using the $_GET array
			if (is_array($_GET) && count($_GET) === 1 && trim(key($_GET), '/') !== '')
			{
				$this->_set_uri_string(key($_GET));
				return;
			}

			// We've exhausted all our options...
			$this->uri_string = '';
			return;
		}

		$uri = strtoupper($this->config->item('uri_protocol'));

		if ($uri === 'REQUEST_URI')
		{
			$this->_set_uri_string($this->_detect_uri());
			return;
		}
		elseif ($uri === 'CLI')
		{
			$this->_set_uri_string($this->_parse_cli_args());
			return;
		}

		$path = isset($_SERVER[$uri]) ? $_SERVER[$uri] : @getenv($uri);
		$this->_set_uri_string($path);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the URI String
	 *
	 * @param 	string
	 * @return	void
	 */
	protected function _set_uri_string($str)
	{
		// Filter out control characters
		$str = remove_invisible_characters($str, FALSE);

		// If the URI contains only a slash we'll kill it
		$this->uri_string = ($str === '/') ? '' : $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Detects the URI
	 *
	 * This function will detect the URI automatically
	 * and fix the query string if necessary.
	 *
	 * @return	string
	 */
	protected function _detect_uri()
	{
		if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
		{
			return '';
		}

		if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0)
		{
			$uri = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($_SERVER['REQUEST_URI'], dirname($_SERVER['SCRIPT_NAME'])) === 0)
		{
			$uri = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}
		else
		{
			$uri = $_SERVER['REQUEST_URI'];
		}

		// This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strpos($uri, '?/') === 0)
		{
			$uri = substr($uri, 2);
		}

		$parts = explode('?', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1]))
		{
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}
		else
		{
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri === '/' OR empty($uri))
		{
			return '/';
		}

		$uri = parse_url('pseudo://hostname/'.$uri, PHP_URL_PATH);

		// Do some final cleaning of the URI and return it
		return str_replace(array('//', '../'), '/', trim($uri, '/'));
	}

	// --------------------------------------------------------------------

	/**
	 * Is cli Request?
	 *
	 * Duplicate of function from the Input class to test to see if a request was made from the command line
	 *
	 * @return 	bool
	 */
	protected function _is_cli_request()
	{
		return (php_sapi_name() === 'cli') OR defined('STDIN');
	}

	// --------------------------------------------------------------------

	/**
	 * Parse cli arguments
	 *
	 * Take each command line argument and assume it is a URI segment.
	 *
	 * @return	string
	 */
	protected function _parse_cli_args()
	{
		$args = array_slice($_SERVER['argv'], 1);
		return $args ? '/'.implode('/', $args) : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Filter segments for malicious characters
	 *
	 * Called by CI_Router
	 *
	 * @param	string
	 * @return	string
	 */
	public function _filter_uri($str)
	{
		if ($str !== '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') === FALSE)
		{
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
			// compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
			if ( ! preg_match('|^['.str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')).']+$|i', urldecode($str)))
			{
				show_error('The URI you submitted has disallowed characters.', 400);
			}
		}

		// Convert programatic characters to entities and return
		return str_replace(
					array('$',     '(',     ')',     '%28',   '%29'), // Bad
					array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;'), // Good
					$str);
	}

	// --------------------------------------------------------------------

	/**
	 * Remove the suffix from the URL if needed
	 *
	 * Called by CI_Router
	 *
	 * @return	void
	 */
	public function _remove_url_suffix()
	{
		$suffix = (string) $this->config->item('url_suffix');

		if ($suffix !== '' && ($offset = strrpos($this->uri_string, $suffix)) !== FALSE)
		{
			$this->uri_string = substr_replace($this->uri_string, '', $offset, strlen($suffix));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Explode the URI Segments. The individual segments will
	 * be stored in the $this->segments array.
	 *
	 * Called by CI_Router
	 *
	 * @return	void
	 */
	public function _explode_segments()
	{
		foreach (explode('/', preg_replace('|/*(.+?)/*$|', '\\1', $this->uri_string)) as $val)
		{
			// Filter segments for security
			$val = trim($this->_filter_uri($val));

			if ($val !== '')
			{
				$this->segments[] = $val;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Re-index Segments
	 *
	 * This function re-indexes the $this->segment array so that it
	 * starts at 1 rather than 0. Doing so makes it simpler to
	 * use functions like $this->uri->segment(n) since there is
	 * a 1:1 relationship between the segment array and the actual segments.
	 *
	 * Called by CI_Router
	 *
	 * @return	void
	 */
	public function _reindex_segments()
	{
		array_unshift($this->segments, NULL);
		array_unshift($this->rsegments, NULL);
		unset($this->segments[0]);
		unset($this->rsegments[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment
	 *
	 * This function returns the URI segment based on the number provided.
	 *
	 * @param	int
	 * @param	mixed
	 * @return	string
	 */
	public function segment($n, $no_result = NULL)
	{
		return isset($this->segments[$n]) ? $this->segments[$n] : $no_result;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI "routed" Segment
	 *
	 * This function returns the re-routed URI segment (assuming routing rules are used)
	 * based on the number provided. If there is no routing this function returns the
	 * same result as $this->segment()
	 *
	 * @param	int
	 * @param	mixed
	 * @return	string
	 */
	public function rsegment($n, $no_result = NULL)
	{
		return isset($this->rsegments[$n]) ? $this->rsegments[$n] : $no_result;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a key value pair from the URI string
	 *
	 * This function generates and associative array of URI data starting
	 * at the supplied segment. For example, if this is your URI:
	 *
	 *	example.com/user/search/name/joe/location/UK/gender/male
	 *
	 * You can use this function to generate an array with this prototype:
	 *
	 * array (
	 *			name => joe
	 *			location => UK
	 *			gender => male
	 *		 )
	 *
	 * @param	int	the starting segment number
	 * @param	array	an array of default values
	 * @return	array
	 */
	public function uri_to_assoc($n = 3, $default = array())
	{
		return $this->_uri_to_assoc($n, $default, 'segment');
	}

	// --------------------------------------------------------------------

	/**
	 * Identical to above only it uses the re-routed segment array
	 *
	 * @param 	int	the starting segment number
	 * @param 	array	an array of default values
	 * @return 	array
	 */
	public function ruri_to_assoc($n = 3, $default = array())
	{
		return $this->_uri_to_assoc($n, $default, 'rsegment');
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a key value pair from the URI string or Re-routed URI string
	 *
	 * @param	int	the starting segment number
	 * @param	array	an array of default values
	 * @param	string	which array we should use
	 * @return	array
	 */
	protected function _uri_to_assoc($n = 3, $default = array(), $which = 'segment')
	{
		if ( ! is_numeric($n))
		{
			return $default;
		}

		if (isset($this->keyval[$n]))
		{
			return $this->keyval[$n];
		}

		if ($which === 'segment')
		{
			$total_segments = 'total_segments';
			$segment_array = 'segment_array';
		}
		else
		{
			$total_segments = 'total_rsegments';
			$segment_array = 'rsegment_array';
		}

		if ($this->$total_segments() < $n)
		{
			return (count($default) === 0)
				? array()
				: array_fill_keys($default, NULL);
		}

		$segments = array_slice($this->$segment_array(), ($n - 1));
		$i = 0;
		$lastval = '';
		$retval  = array();
		foreach ($segments as $seg)
		{
			if ($i % 2)
			{
				$retval[$lastval] = $seg;
			}
			else
			{
				$retval[$seg] = NULL;
				$lastval = $seg;
			}

			$i++;
		}

		if (count($default) > 0)
		{
			foreach ($default as $val)
			{
				if ( ! array_key_exists($val, $retval))
				{
					$retval[$val] = NULL;
				}
			}
		}

		// Cache the array for reuse
		$this->keyval[$n] = $retval;
		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a URI string from an associative array
	 *
	 * @param	array	an associative array of key/values
	 * @return	array
	 */
	public function assoc_to_uri($array)
	{
		$temp = array();
		foreach ((array) $array as $key => $val)
		{
			$temp[] = $key;
			$temp[] = $val;
		}

		return implode('/', $temp);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @param	int
	 * @param	string
	 * @return	string
	 */
	public function slash_segment($n, $where = 'trailing')
	{
		return $this->_slash_segment($n, $where, 'segment');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @param	int
	 * @param	string
	 * @return	string
	 */
	public function slash_rsegment($n, $where = 'trailing')
	{
		return $this->_slash_segment($n, $where, 'rsegment');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment and add a trailing slash - helper function
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _slash_segment($n, $where = 'trailing', $which = 'segment')
	{
		$leading = $trailing = '/';

		if ($where === 'trailing')
		{
			$leading	= '';
		}
		elseif ($where === 'leading')
		{
			$trailing	= '';
		}

		return $leading.$this->$which($n).$trailing;
	}

	// --------------------------------------------------------------------

	/**
	 * Segment Array
	 *
	 * @return	array
	 */
	public function segment_array()
	{
		return $this->segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Routed Segment Array
	 *
	 * @return	array
	 */
	public function rsegment_array()
	{
		return $this->rsegments;
	}

	// --------------------------------------------------------------------

	/**
	 * Total number of segments
	 *
	 * @return	int
	 */
	public function total_segments()
	{
		return count($this->segments);
	}

	// --------------------------------------------------------------------

	/**
	 * Total number of routed segments
	 *
	 * @return	int
	 */
	public function total_rsegments()
	{
		return count($this->rsegments);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the entire URI string
	 *
	 * @return	string
	 */
	public function uri_string()
	{
		return $this->uri_string;
	}


	// --------------------------------------------------------------------

	/**
	 * Fetch the entire Re-routed URI string
	 *
	 * @return	string
	 */
	public function ruri_string()
	{
		return implode('/', $this->rsegment_array());
	}

}

/* End of file URI.php */
/* Location: ./system/core/URI.php */