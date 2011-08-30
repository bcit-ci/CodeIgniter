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
 * URI Class
 *
 * Parses URIs and determines routing
 * The base class, CI_CoreShare, is defined in CodeIgniter.php and allows
 * access to protected methods between CodeIgniter, Router, and URI.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	URI
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/uri.html
 */
class CI_URI extends CI_CoreShare {
	/**
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI			= NULL;

	/**
	 * Reference to Config object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $Config		= NULL;

	/**
	 * Enable query string config item
	 *
	 * @var	boolean
	 * @access	protected
	 */
	protected $enable_query	= FALSE;

	/**
	 * Permitted URI chars config item
	 *
	 * @var	string
	 * @access	protected
	 */
	protected $permit_chars	= '';

	/**
	 * List of cached uri segments
	 *
	 * @var array
	 * @access public
	 */
	protected $keyval		= array();

	/**
	 * List of uri segments
	 *
	 * @var array
	 * @access public
	 */
	protected $segments		= array();

	/**
	 * Re-indexed list of uri segments
	 * Starts at 1 instead of 0
	 *
	 * @var array
	 * @access public
	 */
	protected $rsegments	= array();

	/**
	 * Current uri string
	 *
	 * @var string
	 * @access public
	 */
	protected $uri_string	= '';

	/**
	 * Characters to be converted by _filter_uri
	 *
	 * @var	array
	 * @access	protected
	 */
	protected $bad_chars	= array('$',		'(',		')',		'%28',		'%29');

	/**
	 * Character conversions by _filter_uri
	 *
	 * @var	array
	 * @access	protected
	 */
	protected $good_chars	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');

	/**
	 * Constructor
	 *
	 * Simply globalizes the Router object. The front loads the Router class early
	 * on so it's not available normally as other classes are.
	 *
	 * @param	object	parent reference
	 */
	public function __construct(CodeIgniter $CI) {
		// Attach parent and Config references
		$this->CI =& $CI;
		$this->Config =& $CI->config;

		// Get commonly used config items
		$this->enable_query = $CI->config->item('enable_query_strings');
		$this->permit_chars = $CI->config->item('permitted_uri_chars');
		$CI->log_message('debug', 'URI Class Initialized');
	}

	/**
	 * Fetch a URI Segment
	 *
	 * This function returns the URI segment based on the number provided.
	 *
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	public function segment($index, $no_result = FALSE) {
		return (isset($this->segments[$index]) ? $this->segments[$index] : $no_result);
	}

	/**
	 * Fetch a URI "routed" Segment
	 *
	 * This function returns the re-routed URI segment (assuming routing rules are used)
	 * based on the number provided. If there is no routing this function returns the
	 * same result as $this->segment()
	 *
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	public function rsegment($index, $no_result = FALSE) {
		return (isset($this->rsegments[$index]) ? $this->rsegments[$index] : $no_result);
	}

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
	 * @param	integer	the	starting segment number
	 * @param	array	an	array of default values
	 * @return	array
	 */
	public function uri_to_assoc($index = 3, $default = array()) {
		return $this->_uri_to_assoc($index, $default, 'segment');
	}

	/**
	 * Identical to above only it uses the re-routed segment array
	 *
	 */
	public function ruri_to_assoc($index = 3, $default = array()) {
		return $this->_uri_to_assoc($index, $default, 'rsegment');
	}

	/**
	 * Generate a URI string from an associative array
	 *
	 *
	 * @param	array	an	associative array of key/values
	 * @return	array
	 */
	public function assoc_to_uri($array) {
		$temp = array();
		foreach ((array)$array as $key => $val) {
			$temp[] = $key;
			$temp[] = $val;
		}

		return implode('/', $temp);
	}

	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @param	integer
	 * @param	string
	 * @return	string
	 */
	public function slash_segment($index, $where = 'trailing') {
		return $this->_slash_segment($index, $where, 'segment');
	}

	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @param	integer
	 * @param	string
	 * @return	string
	 */
	public function slash_rsegment($index, $where = 'trailing') {
		return $this->_slash_segment($index, $where, 'rsegment');
	}

	/**
	 * Segment Array
	 *
	 * @return	array
	 */
	public function segment_array() {
		return $this->segments;
	}

	/**
	 * Routed Segment Array
	 *
	 * @return	array
	 */
	public function rsegment_array() {
		return $this->rsegments;
	}

	/**
	 * Total number of segments
	 *
	 * @return	integer
	 */
	public function total_segments() {
		return count($this->segments);
	}

	/**
	 * Total number of routed segments
	 *
	 * @return	integer
	 */
	public function total_rsegments() {
		return count($this->rsegments);
	}

	/**
	 * Fetch the entire URI string
	 *
	 * @return	string
	 */
	public function uri_string() {
		return $this->uri_string;
	}

	/**
	 * Fetch the entire Re-routed URI string
	 *
	 * @return	string
	 */
	public function ruri_string() {
		return '/'.implode('/', $this->rsegment_array());
	}

	/**
	 * Load the URI and return it as an array of segments
	 *
	 * This function establishes the URI string.
	 * The Router object calls this protected function via CI_CoreShare.
	 *
	 * @access	protected
	 * @return	array	uri segments
	 */
	protected function _load_uri() {
		// Check for enable_query_string
		if ($this->_parse_query_string()) {
			// Query strings enabled and parsed - return segments
			return $this->segments;
		}

		// Find URI according to configured protocol
		$proto = strtoupper($this->Config->item('uri_protocol'));
		if ($proto == 'AUTO') {
			if (defined('STDIN')) {
				// Request came from the command line
				$uri = $this->_parse_cli_args();
			}
			else if ($path = $this->_detect_uri()) {
				// The REQUEST_URI will work in most situations
				$uri = $path;
			}
			else {
				// Is there a PATH_INFO variable?
				// Note: some servers seem to have trouble with getenv() so we'll test it two ways
				$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
				if (trim($path, '/') != '' && $path != '/'.SELF) {
					$uri = $path;
				}
				else {
					// No PATH_INFO?... What about QUERY_STRING?
					$path = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
					if (trim($path, '/') != '') {
						$uri = $path;
					}
					else if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '') {
						// As a last ditch effort use the $_GET array
						$uri = key($_GET);
					}
					else {
						// We've exhausted all our options...
						$this->uri_string = '';
						return array();
					}
				}
			}
		}
		else if ($proto == 'REQUEST_URI') {
			$uri = $this->_detect_uri();
		}
		else if ($proto == 'CLI') {
			$uri = $this->_parse_cli_args();
		}
		else {
			$uri = (isset($_SERVER[$proto])) ? $_SERVER[$proto] : @getenv($proto);
		}

		// Remove any suffix
		$suffix = $this->Config->item('url_suffix');
		if ($suffix != '') {
			$uri = preg_replace('|'.preg_quote($suffix).'$|', '', $uri);
		}

		// Filter out control characters
		$uri = $this->CI->remove_invisible_characters($uri, FALSE);

		// Set URI string - if the URI contains only a slash we'll kill it
		$this->uri_string = ($uri == '/') ? '' : $uri;

		// Break URI into segments
		foreach (explode('/', preg_replace('|/*(.+?)/*$|', '\\1', $this->uri_string)) as $val) {
			// Filter segments for security
			$val = trim($this->_filter_uri($val));

			if ($val != '') {
				$this->segments[] = $val;
			}
		}

		// Return segments
		return $this->segments;
	}

	/**
	 * Set the routed URI Segments and reindex.
	 *
	 * This function sets the routed segments and re-index the segment arrays so that they start
	 * at 1 rather than 0. Doing so makes it simpler to use functions like segment(n) since there
	 * is a 1:1 relationship between the segment array and the actual segments.
	 * The Router object calls this protected function via CI_CoreShare.
	 *
	 * @access	protected
	 * @param	array	routed segments
	 * @return	void
	 */
	protected function _routed(array $rsegments) {
		// Set routed segments
		$this->rsegments = $rsegments;

		// Reindex both segment stacks
		array_unshift($this->segments, NULL);
		array_unshift($this->rsegments, NULL);
		unset($this->segments[0]);
		unset($this->rsegments[0]);
	}

	/**
	 * Filter segments for malicious characters
	 *
	 * This helper function filters URI segments.
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _filter_uri($str) {
		// Apply permitted characters
		if ($str != '' && $this->permit_chars != '' && $this->enable_query == FALSE) {
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote()
			// is to maintain backwards compatibility as many are unaware of how characters in the
			// permitted_uri_chars will be parsed as a regex pattern
			if (!preg_match('|^['.str_replace(array('\\-', '\-'), '-',
			preg_quote($this->permit_chars, '-')).']+$|i', $str)) {
				throw new CI_ShowError('The URI you submitted has disallowed characters.', '', 400);
			}
		}

		// Convert programatic characters to entities
		return str_replace($this->bad_chars, $this->good_chars, $str);
	}

	/**
	 * Detects the URI
	 *
	 * This helper function will detect the URI automatically and fix the query string
	 * if necessary. It should only be called internally.
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _detect_uri() {
		// Get the request URI
		$uri = $this->_get_request_uri();

		// This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strncmp($uri, '?/', 2) === 0) {
			$uri = substr($uri, 2);
		}
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1])) {
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}
		else {
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri == '/' || empty($uri)) {
			return '/';
		}

		$uri = parse_url($uri, PHP_URL_PATH);

		// Do some final cleaning of the URI and return it
		return str_replace(array('//', '../'), '/', trim($uri, '/'));
	}

	/**
	 * Detects the query string if supported
	 *
	 * This helper function checks for query string support and parses the query string
	 * if necessary. It should only be called internally.
	 *
	 * @return	boolean	TRUE if query string supported and parsed, otherwise FALSE
	 */
	protected function _parse_query_string() {
		// Are query strings enabled in the config file? Normally CI doesn't utilize query strings
		// since URI segments are more search-engine friendly, but they can optionally be used.
		$ctl_trigger = $this->Config->item('controller_trigger');
		if ($this->enable_query === TRUE && isset($_GET[$ctl_trigger])) {
			// Add directory segment if provided
			$dir_trigger = $this->Config->item('directory_trigger');
			if (isset($_GET[$dir_trigger])) {
				$this->segments[] = trim($this->_filter_uri($_GET[$dir_trigger]));
			}

			// Add controller segment - this was qualified above
			$this->segments[] = trim($this->_filter_uri($_GET[$ctl_trigger]));

			// Add function segment if provided
			$fun_trigger = $this->Config->item('function_trigger');
			if (isset($_GET[$fun_trigger])) {
				$this->segments[] = trim($this->_filter_uri($_GET[$fun_trigger]));
			}

			// Set query string and return parsed
			$this->uri_string = $this->_get_request_uri();
			return TRUE;
		}

		// Return no query string support
		return FALSE;
	}

	/**
	 * Parse cli arguments
	 *
	 * This helper function takes each command line argument and assumes it is a URI segment.
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _parse_cli_args() {
		$args = array_slice($_SERVER['argv'], 1);

		return $args ? '/' . implode('/', $args) : '';
	}

	/**
	 * Get the request URI
	 *
	 * This helper function gets the request URI with the script name removed.
	 * It should only be called internally.
	 *
	 * @return	string	request URI
	 */
	protected function _get_request_uri() {
		// Make sure request uri and script name are available
		if (!isset($_SERVER['REQUEST_URI']) || !isset($_SERVER['SCRIPT_NAME'])) {
			return '';
		}

		// Get the request URI without the leading script name
		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		else if (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}
		return $uri;
	}

	/**
	 * Generate a key value pair from the URI string or Re-routed URI string
	 *
	 * This helper function supports uri_to_assoc() and ruri_to_assoc().
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @param	int	the starting segment number
	 * @param	array	an array of default values
	 * @param	string	which array we should use
	 * @return	array
	 */
	protected function _uri_to_assoc($index = 3, $default = array(), $which = 'segment') {
		if ($which == 'segment') {
			$total_segments = 'total_segments';
			$segment_array = 'segment_array';
		}
		else {
			$total_segments = 'total_rsegments';
			$segment_array = 'rsegment_array';
		}

		if (!is_numeric($index)) {
			return $default;
		}

		if (isset($this->keyval[$index])) {
			return $this->keyval[$index];
		}

		if ($this->$total_segments() < $index) {
			if (count($default) == 0) {
				return array();
			}

			$retval = array();
			foreach ($default as $val) {
				$retval[$val] = FALSE;
			}
			return $retval;
		}

		$segments = array_slice($this->$segment_array(), ($index - 1));

		$i = 0;
		$lastval = '';
		$retval = array();
		foreach ($segments as $seg) {
			if ($i % 2) {
				$retval[$lastval] = $seg;
			}
			else {
				$retval[$seg] = FALSE;
				$lastval = $seg;
			}

			$i++;
		}

		if (count($default) > 0) {
			foreach ($default as $val) {
				if (!array_key_exists($val, $retval)) {
					$retval[$val] = FALSE;
				}
			}
		}

		// Cache the array for reuse
		$this->keyval[$index] = $retval;
		return $retval;
	}

	/**
	 * Fetch a URI Segment and add a trailing slash - helper function
	 *
	 * This helper function supports slash_segment() and slash_rsegment().
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @param	integer
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _slash_segment($index, $where = 'trailing', $which = 'segment') {
		$leading	= '/';
		$trailing	= '/';

		if ($where == 'trailing') {
			$leading	= '';
		}
		else if ($where == 'leading') {
			$trailing	= '';
		}

		return $leading.$this->$which($index).$trailing;
	}
}
// END URI Class

/* End of file URI.php */
/* Location: ./system/core/URI.php */
