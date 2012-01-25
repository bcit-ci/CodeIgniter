<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
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

// ------------------------------------------------------------------------

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
	 * Current output string
	 *
	 * @var string
	 */
	protected $final_output;
	/**
	 * Cache expiration time
	 *
	 * @var int
	 */
	protected $cache_expiration	= 0;
	/**
	 * List of server headers
	 *
	 * @var array
	 */
	protected $headers			= array();
	/**
	 * List of mime types
	 *
	 * @var array
	 */
	protected $mime_types		= array();
	/**
	 * Mime-type for the current page
	 *
	 * @var string
	 */
	protected $mime_type		= 'text/html';
	/**
	 * Determines wether profiler is enabled
	 *
	 * @var book
	 */
	protected $enable_profiler	= FALSE;
	/**
	 * Determines if output compression is enabled
	 *
	 * @var bool
	 */
	protected $_zlib_oc			= FALSE;
	/**
	 * List of profiler sections
	 *
	 * @var array
	 */
	protected $_profiler_sections = array();
	/**
	 * Whether or not to parse variables like {elapsed_time} and {memory_usage}
	 *
	 * @var bool
	 */
	protected $parse_exec_vars	= TRUE;

	public function __construct()
	{
		$this->_zlib_oc = @ini_get('zlib.output_compression');

		// Get mime types for later
		if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
		{
			include APPPATH.'config/'.ENVIRONMENT.'/mimes.php';
		}
		else
		{
			include APPPATH.'config/mimes.php';
		}


		$this->mime_types = $mimes;
		log_message('debug', 'Output Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Output
	 *
	 * Returns the current output string
	 *
	 * @return	string
	 */
	public function get_output()
	{
		return $this->final_output;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Output
	 *
	 * Sets the output string
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_output($output)
	{
		$this->final_output = $output;
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
		if ($this->final_output == '')
		{
			$this->final_output = $output;
		}
		else
		{
			$this->final_output .= $output;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Header
	 *
	 * Lets you set a server header which will be outputted with the final display.
	 *
	 * Note:  If a file is cached, headers will not be sent.  We need to figure out
	 * how to permit header data to be saved with the cache data...
	 *
	 * @param	string
	 * @param 	bool
	 * @return	void
	 */
	public function set_header($header, $replace = TRUE)
	{
		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ($this->_zlib_oc && strncasecmp($header, 'content-length', 14) == 0)
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
	public function set_content_type($mime_type)
	{
		if (strpos($mime_type, '/') === FALSE)
		{
			$extension = ltrim($mime_type, '.');

			// Is this extension supported?
			if (isset($this->mime_types[$extension]))
			{
				$mime_type =& $this->mime_types[$extension];

				if (is_array($mime_type))
				{
					$mime_type = current($mime_type);
				}
			}
		}
		
		$this->mime_type = $mime_type;

		$header = 'Content-Type: '.$mime_type;

		$this->headers[] = array($header, TRUE);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set HTTP Status Header
	 * moved to Common procedural functions in 1.7.2
	 *
	 * @param	int		the status code
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
		$this->enable_profiler = (is_bool($val)) ? $val : TRUE;
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
		foreach ($sections as $section => $enable)
		{
			$this->_profiler_sections[$section] = ($enable !== FALSE) ? TRUE : FALSE;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache
	 *
	 * @param	integer
	 * @return	void
	 */
	public function cache($time)
	{
		$this->cache_expiration = ( ! is_numeric($time)) ? 0 : $time;
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
	 * with any server headers and profile data.  It also stops the
	 * benchmark timer so the page rendering speed and memory usage can be shown.
	 *
	 * @param 	string
	 * @return	mixed
	 */
	public function _display($output = '')
	{
		// Note:  We use globals because we can't use $CI =& get_instance()
		// since this function is sometimes called by the caching mechanism,
		// which happens before the CI super object is available.
		global $BM, $CFG;

		// Grab the super object if we can.
		if (class_exists('CI_Controller'))
		{
			$CI =& get_instance();
		}

		// --------------------------------------------------------------------

		// Set the output data
		if ($output == '')
		{
			$output =& $this->final_output;
		}
		
		// --------------------------------------------------------------------

		// Is minify requested?
		if ($CFG->item('minify_output') === TRUE)
		{
			$output = $this->minify($output, $this->mime_type);
		}


		// --------------------------------------------------------------------

		// Do we need to write a cache file?  Only if the controller does not have its
		// own _output() method and we are not dealing with a cache file, which we
		// can determine by the existence of the $CI object above
		if ($this->cache_expiration > 0 && isset($CI) && ! method_exists($CI, '_output'))
		{
			$this->_write_cache($output);
		}

		// --------------------------------------------------------------------

		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data

		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');

		if ($this->parse_exec_vars === TRUE)
		{
			$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';

			$output = str_replace(array('{elapsed_time}', '{memory_usage}'), array($elapsed, $memory), $output);
		}

		// --------------------------------------------------------------------

		// Is compression requested?
		if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc == FALSE
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

		// Does the $CI object exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if ( ! isset($CI))
		{
			echo $output;
			log_message('debug', 'Final output sent to browser');
			log_message('debug', 'Total execution time: '.$elapsed);
			return TRUE;
		}

		// --------------------------------------------------------------------

		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler == TRUE)
		{
			$CI->load->library('profiler');
			if ( ! empty($this->_profiler_sections))
			{
				$CI->profiler->set_sections($this->_profiler_sections);
			}

			// If the output data contains closing </body> and </html> tags
			// we will remove them and add them back after we insert the profile data
			$output = preg_replace('|</body>.*?</html>|is', '', $output, -1, $count).$CI->profiler->run();
			if ($count > 0)
			{
				$output .= '</body></html>';
			}
		}

		// Does the controller contain a function named _output()?
		// If so send the output there.  Otherwise, echo it.
		if (method_exists($CI, '_output'))
		{
			$CI->_output($output);
		}
		else
		{
			echo $output; // Send it to the browser!
		}

		log_message('debug', 'Final output sent to browser');
		log_message('debug', 'Total execution time: '.$elapsed);
	}

	// --------------------------------------------------------------------

	/**
	 * Write a Cache File
	 *
	 * @param 	string
	 * @return	void
	 */
	public function _write_cache($output)
	{
		$CI =& get_instance();
		$path = $CI->config->item('cache_path');
		$cache_path = ($path == '') ? APPPATH.'cache/' : $path;

		if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
		{
			log_message('error', 'Unable to write cache file: '.$cache_path);
			return;
		}

		$uri =	$CI->config->item('base_url').
				$CI->config->item('index_page').
				$CI->uri->uri_string();

		$cache_path .= md5($uri);

		if ( ! $fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))
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
	 * @param 	object	config class
	 * @param 	object	uri class
	 * @return	void
	 */
	public function _display_cache(&$CFG, &$URI)
	{
		$cache_path = ($CFG->item('cache_path') == '') ? APPPATH.'cache/' : $CFG->item('cache_path');

		// Build the file path. The file name is an MD5 hash of the full URI
		$uri =	$CFG->item('base_url').$CFG->item('index_page').$URI->uri_string;
		$filepath = $cache_path.md5($uri);

		if ( ! @file_exists($filepath) OR ! $fp = @fopen($filepath, FOPEN_READ))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$cache = (filesize($filepath) > 0) ? fread($fp, filesize($filepath)) : '';

		flock($fp, LOCK_UN);
		fclose($fp);

		// Strip out the embedded timestamp
		if ( ! preg_match('/(\d+TS--->)/', $cache, $match))
		{
			return FALSE;
		}

		$last_modified = filemtime($cache_path);
		$expire = trim(str_replace('TS--->', '', $match[1]));

		// Has the file expired?
		if ($_SERVER['REQUEST_TIME'] >= $expire && is_really_writable($cache_path))
		{
			// If so we'll delete it.
			@unlink($filepath);
			log_message('debug', 'Cache file has expired. File deleted.');
			return FALSE;
		}
		else
		{	
			// Or else send the HTTP cache control headers.
			$this->set_cache_header($last_modified, $expire);
		}

		// Display the cache
		$this->_display(str_replace($match[0], '', $cache));
		log_message('debug', 'Cache file is current. Sending it to browser.');
		return TRUE;
	}


	// --------------------------------------------------------------------
	/**
	 * Set the HTTP headers to match the server-side file cache settings
	 * in order to reduce bandwidth.
	 *
	 * @param 	int		timestamp of when the page was last modified
	 * @param 	int		timestamp of when should the requested page expire from cache
	 * @return	void
	 */
	public function set_cache_header($last_modified, $expiration)
	{		
        $max_age = $expiration - $_SERVER['REQUEST_TIME'];

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && ($last_modified <= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])))
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
	 * @param 	string
	 * @param 	string
	 * @return	string
	 */
	public function minify($output, $type='text/html')
	{
		switch ($type)
		{
			case 'text/html':

				$size_before = strlen($output);

				// Keep track of <pre> <code> and <textarea> tags as
				// they were before processing.
				// We'll want to return them to this state later.
				preg_match_all('{<pre.+</pre>}msU', $output, $pres_clean);
				preg_match_all('{<code.+</code>}msU', $output, $codes_clean);
				preg_match_all('{<textarea.+</textarea>}msU', $output, $textareas_clean);

				// Minify the CSS in all the <style> tags.
				preg_match_all('{<style.+</style>}msU', $output, $style_clean);
				foreach ($style_clean[0] as $s)
				{
					$output = str_replace($s, $this->minify($s,'text/css'), $output);
				}

				// Replace multiple spaces with a single space.
				$output = preg_replace('!\s{2,}!', "\n", $output);
				
				// Remove comments (non-MSIE conditionals)
				$output = preg_replace('{\s*<!--[^\[].*-->\s*}msU', '', $output);

				// Remove spaces around block-level elements.
				$output = preg_replace('{\s*(</?(html|head|title|meta|script|link|style|body|h[1-6]|div|p|br).*>)\s*}', '$1', $output);

				// Replace mangled <pre> etc. tags with unprocessed ones.
				preg_match_all('{<pre.+</pre>}msU', $output, $pres_messed);
				preg_match_all('{<code.+</code>}msU', $output, $codes_messed);
				preg_match_all('{<textarea.+</textarea>}msU', $output, $textareas_messed);
				$output = str_replace($pres_messed[0], $pres_clean[0], $output);
				$output = str_replace($codes_messed[0], $codes_clean[0], $output);
				$output = str_replace($textareas_messed[0], $textareas_clean[0], $output);
				
				$size_after = strlen($output);
				$savings_percent = round(100 - ($size_after / $size_before * 100));
				
				log_message('debug', 'Minifier shaved '.$savings_percent.'% off HTML output size.');

			break;
			
			
			case 'text/css':
			
				// Remove spaces around curly brackets, colons, and semi-colons
				$output = preg_replace('!\s*(:|;|}|{)\s*!', '$1', $output);
				
				// Replace spaces with line breaks to limit line lengths
				$output = preg_replace('!\s+!', "\n", $output);

			break;
		}
		
		return $output;
	}


}

/* End of file Output.php */
/* Location: ./system/core/Output.php */
