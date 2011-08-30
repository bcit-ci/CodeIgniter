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
 * Output Class
 *
 * Responsible for sending final output to browser
 * The base class, CI_CoreShare, is defined in CodeIgniter.php and allows
 * Loader access to protected loading methods in CodeIgniter.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Output
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/output.html
 */
class CI_Output extends CI_CoreShare {
	/**
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI				= NULL;

	/**
	 * Current output string
	 *
	 * @var string
	 * @access 	protected
	 */
	protected $final_output;

	/**
	 * Cache expiration time
	 *
	 * @var int
	 * @access 	protected
	 */
	protected $cache_expiration	= 0;

	/**
	 * List of server headers
	 *
	 * @var array
	 * @access 	protected
	 */
	protected $headers			= array();

	/**
	 * List of mime types
	 *
	 * @var array
	 * @access 	protected
	 */
	protected $mime_types		= array();

	/**
	 * Determines wether profiler is enabled
	 *
	 * @var book
	 * @access 	protected
	 */
	protected $enable_profiler	= FALSE;

	/**
	 * Determines if output compression is enabled
	 *
	 * @var bool
	 * @access 	protected
	 */
	protected $_zlib_oc			= FALSE;

	/**
	 * List of profiler sections
	 *
	 * @var array
	 * @access 	protected
	 */
	protected $_profiler_sects = array();

	/**
	 * Whether or not to parse variables like {elapsed_time} and {memory_usage}
	 *
	 * @var bool
	 * @access 	protected
	 */
	protected $parse_exec_vars	= TRUE;

	/**
	 * Constructor
	 *
	 * @param	object	parent reference
	 */
	public function __construct(CodeIgniter $CI) {
		// Attach parent reference
		$this->CI =& $CI;

		// Get compression state
		$this->zlib_oc = @ini_get('zlib.output_compression');

		// Get mime types for later
		$mimes = CodeIgniter::get_config('mimes.php', 'mimes');
		if (is_array($mimes)) {
			$this->mime_types = $mimes;
		}

		$CI->log_message('debug', 'Output Class Initialized');
	}

	/**
	 * Get Output
	 *
	 * Returns the current output string
	 *
	 * @return	string
	 */
	public function get_output() {
		return $this->final_output;
	}

	/**
	 * Set Output
	 *
	 * Sets the output string
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_output($output) {
		$this->final_output = $output;
		return $this;
	}

	/**
	 * Append Output
	 *
	 * Appends data onto the output string
	 *
	 * @param	string
	 * @return	void
	 */
	public function append_output($output) {
		$this->final_output .= $output;
		return $this;
	}

	/**
	 * Set Header
	 *
	 * Lets you set a server header which will be outputted with the final display.
	 *
	 * Note: If a file is cached, headers will not be sent. We need to figure out
	 * how to permit header data to be saved with the cache data...
	 *
	 * @param	string
	 * @param 	bool
	 * @return	void
	 */
	public function set_header($header, $replace = TRUE) {
		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ($this->zlib_oc && strncasecmp($header, 'content-length', 14) == 0) {
			return;
		}

		$this->headers[] = array($header, $replace);

		return $this;
	}

	/**
	 * Set Content Type Header
	 *
	 * @param	string	extension of the file we're outputting
	 * @return	void
	 */
	public function set_content_type($mime_type) {
		if (strpos($mime_type, '/') === FALSE) {
			$extension = ltrim($mime_type, '.');

			// Is this extension supported?
			if (isset($this->mime_types[$extension])) {
				$mime_type =& $this->mime_types[$extension];

				if (is_array($mime_type)) {
					$mime_type = current($mime_type);
				}
			}
		}

		$this->headers[] = array('Content-Type: '.$mime_type, TRUE);

		return $this;
	}

	/**
	 * Set HTTP Status Header
	 * moved to Common procedural functions in 1.7.2
	 *
	 * @param	int		the status code
	 * @param	string
	 * @return	void
	 */
	public function set_status_header($code = 200, $text = '') {
		// Define status codes
		$stati = array(
			200	=> 'OK',
			201	=> 'Created',
			202	=> 'Accepted',
			203	=> 'Non-Authoritative Information',
			204	=> 'No Content',
			205	=> 'Reset Content',
			206	=> 'Partial Content',

			300	=> 'Multiple Choices',
			301	=> 'Moved Permanently',
			302	=> 'Found',
			304	=> 'Not Modified',
			305	=> 'Use Proxy',
			307	=> 'Temporary Redirect',

			400	=> 'Bad Request',
			401	=> 'Unauthorized',
			403	=> 'Forbidden',
			404	=> 'Not Found',
			405	=> 'Method Not Allowed',
			406	=> 'Not Acceptable',
			407	=> 'Proxy Authentication Required',
			408	=> 'Request Timeout',
			409	=> 'Conflict',
			410	=> 'Gone',
			411	=> 'Length Required',
			412	=> 'Precondition Failed',
			413	=> 'Request Entity Too Large',
			414	=> 'Request-URI Too Long',
			415	=> 'Unsupported Media Type',
			416	=> 'Requested Range Not Satisfiable',
			417	=> 'Expectation Failed',

			500	=> 'Internal Server Error',
			501	=> 'Not Implemented',
			502	=> 'Bad Gateway',
			503	=> 'Service Unavailable',
			504	=> 'Gateway Timeout',
			505	=> 'HTTP Version Not Supported'
		);

		// Validate code
		if ($code == '' || ! is_numeric($code)) {
			throw new CI_ShowError('Status codes must be numeric');
		}

		// Load text if necessary
		if ($text == '') {
			if (isset($stati[$code])) {
				$text = $stati[$code];
			}
			else {
				throw new CI_ShowError('No status text available. Please check your status code number or '.
					'supply your own message text.');
			}
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
		if (substr(php_sapi_name(), 0, 3) == 'cgi') {
			header('Status: '.$code.' '.$text, TRUE);
		}
		else if ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0') {
			header($server_protocol.' '.$code.' '.$text, TRUE, $code);
		}
		else {
			header('HTTP/1.1 '.$code.' '.$text, TRUE, $code);
		}

		return $this;
	}

	/**
	 * Enable/disable Profiler
	 *
	 * @param	bool
	 * @return	void
	 */
	public function enable_profiler($val = TRUE) {
		$this->enable_profiler = (is_bool($val)) ? $val : TRUE;
		return $this;
	}

	/**
	 * Set Profiler Sections
	 *
	 * Allows override of default / config settings for Profiler section display
	 *
	 * @param	array
	 * @return	void
	 */
	public function setprofiler_sects($sections) {
		foreach ($sections as $section => $enable) {
			$this->profiler_sects[$section] = ($enable !== FALSE) ? TRUE : FALSE;
		}

		return $this;
	}

	/**
	 * Set Cache
	 *
	 * @param	integer
	 * @return	void
	 */
	public function cache($time) {
		$this->cache_expiration = is_numeric($time) ? $time : 0;
		return $this;
	}

	/**
	 * Display Output
	 *
	 * All "view" data is automatically put into this variable by the controller class:
	 *
	 * $this->final_output
	 *
	 * This function sends the finalized output data to the browser along
	 * with any server headers and profile data. It also stops the
	 * benchmark timer so the page rendering speed and memory usage can be shown.
	 * The CodeIgniter and Exception objects call this protected method via CI_CoreShare.
	 *
	 * @access	protected
	 * @param 	string
	 * @return	mixed
	 */
	protected function _display($output = '') {
		// Set the output data
		if ($output == '') {
			$output =& $this->final_output;
		}

		// Do we need to write a cache file? Only if the controller does not have its
		// own _output() method and we are not dealing with a cache file, which we
		// can determine by the existence of the $CI->routed object
		if ($this->cache_expiration > 0 && isset($this->CI->routed) &&
		!$this->CI->is_callable($this->CI->routed, '_output')) {
			$this->_write_cache($output);
		}

		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data
		$elapsed = isset($this->CI->benchmark) ?
			$this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end') : '';

		if ($this->parse_exec_vars === TRUE) {
			$memory	 = function_exists('memory_get_usage') ? round(memory_get_usage()/1024/1024, 2).'MB' : '0';
			$output = str_replace('{elapsed_time}', $elapsed, $output);
			$output = str_replace('{memory_usage}', $memory, $output);
		}

		// Is compression requested?
		if ($this->CI->config->item('compress_output') === TRUE && $this->zlib_oc == FALSE) {
			if (extension_loaded('zlib')) {
				if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
				strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
					ob_start('ob_gzhandler');
				}
			}
		}

		// Are there any server headers to send?
		if (count($this->headers) > 0) {
			foreach ($this->headers as $header) {
				@header($header[0], $header[1]);
			}
		}

		// Does the routed controller object exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if (!isset($this->CI->routed)) {
			echo $output;
			$this->CI->log_message('debug', 'Final output sent to browser');
			if ($elapsed != '') {
				$this->CI->log_message('debug', 'Total execution time: '.$elapsed);
			}
			return TRUE;
		}

		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler == TRUE) {
			$this->CI->load->library('profiler');

			if (!empty($this->profiler_sects)) {
				$this->CI->profiler->set_sects($this->profiler_sects);
			}

			// If the output data contains closing </body> and </html> tags
			// we will remove them and add them back after we insert the profile data
			if (preg_match('|</body>.*?</html>|is', $output)) {
				$output = preg_replace('|</body>.*?</html>|is', '', $output).$this->CI->profiler->run().
					'</body></html>';
			}
			else {
				$output .= $this->CI->profiler->run();
			}
		}

		// Does the controller contain a function named _output()?
		// If so send the output there. Otherwise, echo it.
		if ($this->CI->is_callable($this->CI->routed, '_output')) {
			$this->CI->routed->_output($output);
		}
		else {
			echo $output; // Send it to the browser!
		}

		$this->CI->log_message('debug', 'Final output sent to browser');
		if ($elapsed != '') {
			$this->CI->log_message('debug', 'Total execution time: '.$elapsed);
		}
	}

	/**
	 * Update/serve a cached file
	 *
	 * This function checks for a cached copy of the request to deliver.
	 * The CodeIgniter object calls this protected method via CI_CoreShare.
	 *
	 * @access	protected
	 * @return	boolean	TRUE if cache displayed, otherwise FALSE
	 */
	protected function _display_cache() {
		$cache_path = ($this->CI->config->item('cache_path') == '') ? APPPATH.'cache/' :
			$this->CI->config->item('cache_path');

		// Build the file path. The file name is an MD5 hash of the full URI
		$uri =	$this->CI->config->item('base_url').$this->CI->config->item('index_page').$this->CI->uri->uri_string();
		$filepath = $cache_path.md5($uri);

		if ( ! @file_exists($filepath)) {
			return FALSE;
		}

		if ( ! $fp = @fopen($filepath, FOPEN_READ)) {
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$cache = '';
		if (filesize($filepath) > 0) {
			$cache = fread($fp, filesize($filepath));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		// Strip out the embedded timestamp
		if (!preg_match('/(\d+TS--->)/', $cache, $match)) {
			return FALSE;
		}

		// Has the file expired? If so we'll delete it.
		if (time() >= trim(str_replace('TS--->', '', $match['1']))) {
			if ($this->CI->is_really_writable($cache_path)) {
				@unlink($filepath);
				$this->CI->log_message('debug', 'Cache file has expired. File deleted');
				return FALSE;
			}
		}

		// Display the cache
		$this->_display(str_replace($match['0'], '', $cache));
		$this->CI->log_message('debug', 'Cache file is current. Sending it to browser.');
		return TRUE;
	}

	/**
	 * Write a Cache File
	 *
	 * This helper function writes a cache file.
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _write_cache($output) {
		$path = $this->CI->config->item('cache_path');

		$cache_path = ($path == '') ? APPPATH.'cache/' : $path;

		if (!is_dir($cache_path) || !$this->CI->is_really_writable($cache_path)) {
			$this->CI->log_message('error', 'Unable to write cache file: '.$cache_path);
			return;
		}

		$uri =	$this->CI->config->item('base_url').$this->CI->config->item('index_page').$this->CI->uri->uri_string();

		$cache_path .= md5($uri);

		if (!($fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))) {
			$this->CI->log_message('error', 'Unable to write cache file: '.$cache_path);
			return;
		}

		$expire = time() + ($this->cache_expiration * 60);

		if (flock($fp, LOCK_EX)) {
			fwrite($fp, $expire.'TS--->'.$output);
			flock($fp, LOCK_UN);
		}
		else {
			$this->CI->log_message('error', 'Unable to secure a file lock for file at: '.$cache_path);
			return;
		}
		fclose($fp);
		@chmod($cache_path, FILE_WRITE_MODE);

		$this->CI->log_message('debug', 'Cache file written: '.$cache_path);
	}
}
// END Output Class

/* End of file Output.php */
/* Location: ./system/core/Output.php */
