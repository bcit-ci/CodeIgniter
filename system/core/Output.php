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
 * bundled with this package in the files license.txt / license.rst. It is
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
 * Output Class
 *
 * Responsible for sending final output to browser
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Output
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/output.html
 */
class CI_Output {
	/**
	 * CodeIgniter core
	 *
	 * @var		object
	 */
	protected $CI;

	/**
	 * Current output string
	 *
	 * Protected to prevent corruption
	 * Accessible as string via __get for backward compatibility
	 *
	 * @var		array
	 */
	protected $final_output			= array('');

	/**
	 * Cache expiration time
	 *
	 * @var		int
	 */
	public $cache_expiration		= 0;

	/**
	 * List of server headers
	 *
	 * @var		array
	 */
	public $headers					= array();

	/**
	 * List of mime types
	 *
	 * @var		array
	 */
	public $mimes					= NULL;

	/**
	 * Mime-type for the current page
	 *
	 * @var string
	 */
	protected $mime_type			= 'text/html';

	/**
	 * Determines whether profiler is enabled
	 *
	 * @var		bool
	 */
	public $enable_profiler			= FALSE;

	/**
	 * Determines if output compression is enabled
	 *
	 * @var		bool
	 */
	protected $_zlib_oc				= FALSE;

	/**
	 * List of profiler sections
	 *
	 * @var		array
	 */
	protected $_profiler_sections	= array();

	/**
	 * Whether or not to parse variables like {elapsed_time} and {memory_usage}
	 *
	 * @var		bool
	 */
	public $parse_exec_vars			= TRUE;

	/**
	 * Set up Output class
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// Get parent reference
		$this->CI =& get_instance();

		$this->_zlib_oc = (bool) @ini_get('zlib.output_compression');

		log_message('debug', 'Output Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Output
	 *
	 * Returns the current output string
	 *
	 * @param	bool	Get all flag
	 * @return	string
	 */
	public function get_output($all = FALSE)
	{
		return ($all ? implode($this->final_output) : end($this->final_output));
	}

	// --------------------------------------------------------------------

	/**
	 * Set Output
	 *
	 * Sets the output string
	 *
	 * @param	string	Output
	 * @param	bool	Overwrite all flag
	 * @return	void
	 */
	public function set_output($output, $all = FALSE)
	{
		// Check for all flag
		if ($all)
		{
			// Reset stack to one level with output
			$this->final_output = array($output);
		}
		else
		{
			// Set buffer contents for current buffer in stack
			// Note: stack_pop() prevents emptying the array, so count will always be >= 1
			$level = count($this->final_output) - 1;
			$this->final_output[$level] = $output;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Append Output
	 *
	 * Appends data onto the output string
	 *
	 * @param	string
	 * @return	void
	 */
	public function append_output($output)
	{
		// Append output to current buffer in stack
		// Note: stack_pop() prevents emptying the array, so count will always be >= 1
		$level = count($this->final_output) - 1;
		$this->final_output[$level] .= $output;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Stack Push
	 *
	 * Pushes a new output buffer onto the stack
	 *
	 * @access	public
	 * @param	string	Optional initial buffer contents
	 * @return	int		New stack depth
	 */
	public function stack_push($output = '')
	{
		// Add a buffer to the output stack
		$this->final_output[] = $output;
		return count($this->final_output);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Stack Level
	 *
	 * Returns number of buffer levels in final output stack
	 *
	 * @access	public
	 * @return	int		Stack depth
	 */
	public function stack_level()
	{
		// Just return count of buffers
		return count($this->final_output);
	}

	// --------------------------------------------------------------------

	/**
	 * Stack Pop
	 *
	 * Pops current output buffer off the stack and returns it
	 * Returns bottom buffer contents (without pop) if only one exists
	 *
	 * @access	public
	 * @return	string
	 */
	public function stack_pop()
	{
		if (count($this->final_output) > 1)
		{
			// Pop the topmost buffer and return it
			return array_pop($this->final_output);
		}

		// Nothing to pop - just return contents of bottom buffer
		return $this->final_output[0];
	}

	// --------------------------------------------------------------------

	/**
	 * Set Header
	 *
	 * Lets you set a server header which will be outputted with the final display.
	 *
	 * Note: If a file is cached, headers will not be sent. We need to figure out
	 * how to permit header data to be saved with the cache data...
	 *
	 * @param	string
	 * @param	bool
	 * @return	void
	 */
	public function set_header($header, $replace = TRUE)
	{
		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ($this->_zlib_oc && strncasecmp($header, 'content-length', 14) === 0)
		{
			return;
		}

		$this->headers[] = array($header, $replace);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Content Type Header
	 *
	 * @param	string	extension of the file we're outputting
	 * @return	void
	 */
	public function set_content_type($mime_type, $charset = NULL)
	{
		if (strpos($mime_type, '/') === FALSE)
		{
			$extension = ltrim($mime_type, '.');

			// Do we need to get mime types?
			if ($this->mimes === NULL)
			{
				$mimes = $this->CI->config->get('mimes.php');
				$this->mimes = is_array($mimes) ? $mimes : array();
			}

			// Is this extension supported?
			if (isset($this->mimes[$extension]))
			{
				$mime_type =& $this->mimes[$extension];

				if (is_array($mime_type))
				{
					$mime_type = current($mime_type);
				}
			}
		}

		$this->mime_type = $mime_type;

		if (empty($charset))
		{
			$charset = $this->CI->config->item('charset');
		}

		$header = 'Content-Type: '.$mime_type
			.(empty($charset) ? NULL : '; charset='.strtolower($charset));

		$this->headers[] = array($header, TRUE);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Current Content Type Header
	 *
	 * @return	string	'text/html', if not already set
	 */
	public function get_content_type()
	{
		for ($i = 0, $c = count($this->headers); $i < $c; $i++)
		{
			if (preg_match('/^Content-Type:\s(.+)$/', $this->headers[$i][0], $matches))
			{
				return $matches[1];
			}
		}

		return 'text/html';
	}

	// --------------------------------------------------------------------

	/**
	 * Set HTTP Status Header
	 * moved to Common procedural functions in 1.7.2
	 *
	 * @param	int	the status code
	 * @param	string
	 * @return	void
	 */
	public function set_status_header($code = 200, $text = '')
	{
		set_status_header($code, $text);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Enable/disable Profiler
	 *
	 * @param	bool
	 * @return	void
	 */
	public function enable_profiler($val = TRUE)
	{
		$this->enable_profiler = is_bool($val) ? $val : TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Profiler Sections
	 *
	 * Allows override of default / config settings for Profiler section display
	 *
	 * @param	array
	 * @return	void
	 */
	public function set_profiler_sections($sections)
	{
		if (isset($sections['query_toggle_count']))
		{
			$this->_profiler_sections['query_toggle_count'] = (int) $sections['query_toggle_count'];
			unset($sections['query_toggle_count']);
		}

		foreach ($sections as $section => $enable)
		{
			$this->_profiler_sections[$section] = ($enable !== FALSE);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache
	 *
	 * @param	int
	 * @return	void
	 */
	public function cache($time)
	{
		$this->cache_expiration = is_numeric($time) ? $time : 0;
		return $this;
	}

	// --------------------------------------------------------------------

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
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function _display($output = '')
	{
		// Set the output data
		if ($output === '')
		{
			// Collapse the output stack
			$output = implode($this->final_output);
		}

		// --------------------------------------------------------------------

		// Is minify requested?
		if ($this->CI->config->item('minify_output') === TRUE)
		{
			$output = $this->minify($output, $this->mime_type);
		}

		// --------------------------------------------------------------------

		// Do we need to write a cache file? Only if the controller does not have its
		// own _output() method and we are not dealing with a cache file, which we
		// can determine by the existence of the $this->CI->routed object
		$cached = ( ! isset($this->CI->routed));
		if ($this->cache_expiration > 0 && ! $cached && ! method_exists($this->CI->routed, '_output'))
		{
			$this->_write_cache($output);
		}

		// --------------------------------------------------------------------

		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data

		$elapsed = $this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');

		if ($this->parse_exec_vars === TRUE)
		{
			$memory	= round(memory_get_usage() / 1024 / 1024, 2).'MB';

			$output = str_replace(array('{elapsed_time}', '{memory_usage}'), array($elapsed, $memory), $output);
		}

		// --------------------------------------------------------------------

		// Is compression requested?
		if ($this->CI->config->item('compress_output') === TRUE && $this->_zlib_oc === FALSE
			&& extension_loaded('zlib')
			&& isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
		{
			ob_start('ob_gzhandler');
		}

		// --------------------------------------------------------------------

		// Are there any server headers to send?
		if (count($this->headers) > 0)
		{
			foreach ($this->headers as $header)
			{
				@header($header[0], $header[1]);
			}
		}

		// --------------------------------------------------------------------

		// Does the routed controller object exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if ($cached)
		{
			echo $output;
			log_message('debug', 'Final output sent to browser');
			log_message('debug', 'Total execution time: '.$elapsed);
			return TRUE;
		}

		// --------------------------------------------------------------------

		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler === TRUE)
		{
			$this->CI->load->library('profiler');
			if ( ! empty($this->_profiler_sections))
			{
				$this->CI->profiler->set_sections($this->_profiler_sections);
			}

			// If the output data contains closing </body> and </html> tags
			// we will remove them and add them back after we insert the profile data
			$output = preg_replace('|</body>.*?</html>|is', '', $output, -1, $count).$this->CI->profiler->run();
			if ($count > 0)
			{
				$output .= '</body></html>';
			}
		}

		// Does the controller contain a function named _output()?
		// If so send the output there. Otherwise, echo it.
		if (method_exists($this->CI->routed, '_output'))
		{
			$this->CI->routed->_output($output);
		}
		else
		{
			echo $output;	// Send it to the browser!
		}

		log_message('debug', 'Final output sent to browser');
		log_message('debug', 'Total execution time: '.$elapsed);
	}

	// --------------------------------------------------------------------

	/**
	 * Write a Cache File
	 *
	 * @param	string
	 * @return	void
	 */
	public function _write_cache($output)
	{
		$path = $this->CI->config->item('cache_path');
		$cache_path = ($path === '') ? APPPATH.'cache/' : $path;

		if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
		{
			log_message('error', 'Unable to write cache file: '.$cache_path);
			return;
		}

		$uri =	$this->CI->config->item('base_url').
				$this->CI->config->item('index_page').
				$this->CI->uri->uri_string();

		$cache_path .= md5($uri);

		if ( ! $fp = @fopen($cache_path, 'w'))
		{
			log_message('error', 'Unable to write cache file: '.$cache_path);
			return;
		}

		$expire = time() + ($this->cache_expiration * 60);

		if (flock($fp, LOCK_EX))
		{
			fwrite($fp, $expire.'TS--->'.$output);
			flock($fp, LOCK_UN);
		}
		else
		{
			log_message('error', 'Unable to secure a file lock for file at: '.$cache_path);
			return;
		}
		fclose($fp);
		@chmod($cache_path, FILE_WRITE_MODE);

		log_message('debug', 'Cache file written: '.$cache_path);

		// Send HTTP cache-control headers to browser to match file cache settings.
		$this->set_cache_header($_SERVER['REQUEST_TIME'], $expire);
	}

	// --------------------------------------------------------------------

	/**
	 * Update/serve a cached file
	 *
	 * @return	bool
	 */
	public function _display_cache()
	{
		$cache_path = $this->CI->config->item('cache_path');
		if ($cache_path === '')
		{
			$cache_path = APPPATH.'cache/';
		}

		// Build the file path. The file name is an MD5 hash of the full this->CI->uri
		$uri =	$this->CI->config->item('base_url').$this->CI->config->item('index_page').$this->CI->uri->uri_string;
		$filepath = $cache_path.md5($uri);

		if ( ! @file_exists($filepath) OR ! $fp = @fopen($filepath, 'r'))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$cache = (filesize($filepath) > 0) ? fread($fp, filesize($filepath)) : '';

		flock($fp, LOCK_UN);
		fclose($fp);

		// Strip out the embedded timestamp
		if ( ! preg_match('/^(\d+)TS--->/', $cache, $match))
		{
			return FALSE;
		}

		$last_modified = filemtime($cache_path);
		$expire = $match[1];

		// Has the file expired?
		if ($_SERVER['REQUEST_TIME'] >= $expire && is_really_writable($cache_path))
		{
			// If so we'll delete it.
			@unlink($filepath);
			log_message('debug', 'Cache file has expired. File deleted.');
			return FALSE;
		}

		// Send the HTTP cache control headers.
		$this->set_cache_header($last_modified, $expire);

		// Output the cache (displayed during CodeIgniter::finalize)
		$this->set_output(substr($cache, strlen($match[0])), TRUE);
		log_message('debug', 'Cache file is current. Sending it to browser.');
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the HTTP headers to match the server-side file cache settings
	 * in order to reduce bandwidth.
	 *
	 * @param	int	timestamp of when the page was last modified
	 * @param	int	timestamp of when should the requested page expire from cache
	 * @return	void
	 */
	public function set_cache_header($last_modified, $expiration)
	{
		$max_age = $expiration - $_SERVER['REQUEST_TIME'];

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $last_modified <= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			$this->set_status_header(304);
			exit;
		}
		else
		{
			header('Pragma: public');
			header('Cache-Control: max-age=' . $max_age . ', public');
			header('Expires: '.gmdate('D, d M Y H:i:s', $expiration).' GMT');
			header('Last-modified: '.gmdate('D, d M Y H:i:s', $last_modified).' GMT');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Reduce excessive size of HTML content.
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function minify($output, $type = 'text/html')
	{
		switch ($type)
		{
			case 'text/html':

				$size_before = strlen($output);

				if ($size_before === 0)
				{
					return '';
				}

				// Find all the <pre>,<code>,<textarea>, and <javascript> tags
				// We'll want to return them to this unprocessed state later.
				preg_match_all('{<pre.+</pre>}msU', $output, $pres_clean);
				preg_match_all('{<code.+</code>}msU', $output, $codes_clean);
				preg_match_all('{<textarea.+</textarea>}msU', $output, $textareas_clean);
				preg_match_all('{<script.+</script>}msU', $output, $javascript_clean);

				// Minify the CSS in all the <style> tags.
				preg_match_all('{<style.+</style>}msU', $output, $style_clean);
				foreach ($style_clean[0] as $s)
				{
					$output = str_replace($s, $this->minify($s, 'text/css'), $output);
				}

				// Minify the javascript in <script> tags.
				foreach ($javascript_clean[0] as $s)
				{
					$javascript_mini[] = $this->minify($s, 'text/javascript');
				}

				// Replace multiple spaces with a single space.
				$output = preg_replace('!\s{2,}!', ' ', $output);

				// Remove comments (non-MSIE conditionals)
				$output = preg_replace('{\s*<!--[^\[].*-->\s*}msU', '', $output);

				// Remove spaces around block-level elements.
				$output = preg_replace('/\s*(<\/?(html|head|title|meta|script|link|style|body|h[1-6]|div|p|br)[^>]*>)\s*/is', '$1', $output);

				// Replace mangled <pre> etc. tags with unprocessed ones.

				if ( ! empty($pres_clean))
				{
					preg_match_all('{<pre.+</pre>}msU', $output, $pres_messed);
					$output = str_replace($pres_messed[0], $pres_clean[0], $output);
				}

				if ( ! empty($codes_clean))
				{
					preg_match_all('{<code.+</code>}msU', $output, $codes_messed);
					$output = str_replace($codes_messed[0], $codes_clean[0], $output);
				}

				if ( ! empty($textareas_clean))
				{
					preg_match_all('{<textarea.+</textarea>}msU', $output, $textareas_messed);
					$output = str_replace($textareas_messed[0], $textareas_clean[0], $output);
				}

				if (isset($javascript_mini))
				{
					preg_match_all('{<script.+</script>}msU', $output, $javascript_messed);
					$output = str_replace($javascript_messed[0], $javascript_mini, $output);
				}

				$size_removed = $size_before - strlen($output);
				$savings_percent = round(($size_removed / $size_before * 100));

				log_message('debug', 'Minifier shaved '.($size_removed / 1000).'KB ('.$savings_percent.'%) off final HTML output.');

			break;

			case 'text/css':

				//Remove CSS comments
				$output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $output);

				// Remove spaces around curly brackets, colons,
				// semi-colons, parenthesis, commas
				$output = preg_replace('!\s*(:|;|,|}|{|\(|\))\s*!', '$1', $output);

			break;

			case 'text/javascript':

				// Currently leaves JavaScript untouched.
			break;

			default: break;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Get inaccessible property (final_output)
	 *
	 * @param	string	Property name
	 * @return	mixed	Collapsed output stack if final_output, otherwise void
	 */
	public function __get($name)
	{
		// Check for now-protected 'final_output'
		if ($name === 'final_output')
		{
			// Collapse the stack and return it
			return $this->get_output(TRUE);
		}
	}
}

/* End of file Output.php */
/* Location: ./system/core/Output.php */
