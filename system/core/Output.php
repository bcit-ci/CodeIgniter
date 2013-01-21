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
 * Output Class
 *
 * Responsible for sending final output to the browser.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Output
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/output.html
 */
class CI_Output {

	/**
	 * Final output string
	 *
	 * @var	string
	 */
	public $final_output;

	/**
	 * Cache expiration time
	 *
	 * @var	int
	 */
	public $cache_expiration = 0;

	/**
	 * List of server headers
	 *
	 * @var	array
	 */
	public $headers =	array();

	/**
	 * List of mime types
	 *
	 * @var	array
	 */
	public $mimes =		array();

	/**
	 * Mime-type for the current page
	 *
	 * @var	string
	 */
	protected $mime_type	= 'text/html';

	/**
	 * Enable Profiler flag
	 *
	 * @var	bool
	 */
	public $enable_profiler = FALSE;

	/**
	 * zLib output compression flag
	 *
	 * @var	bool
	 */
	protected $_zlib_oc =		FALSE;

	/**
	 * List of profiler sections
	 *
	 * @var	array
	 */
	protected $_profiler_sections =	array();

	/**
	 * Parse markers flag
	 *
	 * Whether or not to parse variables like {elapsed_time} and {memory_usage}.
	 *
	 * @var	bool
	 */
	public $parse_exec_vars =	TRUE;

	/**
	 * Class constructor
	 *
	 * Determines whether zLib output compression will be used.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->_zlib_oc = (bool) @ini_get('zlib.output_compression');

		// Get mime types for later
		$this->mimes =& get_mimes();

		log_message('debug', 'Output Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Output
	 *
	 * Returns the current output string.
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
	 * Sets the output string.
	 *
	 * @param	string	$output	Output data
	 * @return	CI_Output
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
	 * Appends data onto the output string.
	 *
	 * @param	string	$output	Data to append
	 * @return	CI_Output
	 */
	public function append_output($output)
	{
		if (empty($this->final_output))
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
	 * Lets you set a server header which will be sent with the final output.
	 *
	 * Note: If a file is cached, headers will not be sent.
	 * @todo	We need to figure out how to permit headers to be cached.
	 *
	 * @param	string	$header		Header
	 * @param	bool	$replace	Whether to replace the old header value, if already set
	 * @return	CI_Output
	 */
	public function set_header($header, $replace = TRUE)
	{
		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ($this->_zlib_oc && strncasecmp($header, 'content-length', 14) === 0)
		{
			return $this;
		}

		$this->headers[] = array($header, $replace);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Content-Type Header
	 *
	 * @param	string	$mime_type	Extension of the file we're outputting
	 * @param	string	$charset	Character set (default: NULL)
	 * @return	CI_Output
	 */
	public function set_content_type($mime_type, $charset = NULL)
	{
		if (strpos($mime_type, '/') === FALSE)
		{
			$extension = ltrim($mime_type, '.');

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
			$charset = config_item('charset');
		}

		$header = 'Content-Type: '.$mime_type
			.(empty($charset) ? NULL : '; charset='.$charset);

		$this->headers[] = array($header, TRUE);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Current Content-Type Header
	 *
	 * @return	string	'text/html', if not already set
	 */
	public function get_content_type()
	{
		for ($i = 0, $c = count($this->headers); $i < $c; $i++)
		{
			if (sscanf($this->headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1)
			{
				return $content_type;
			}
		}

		return 'text/html';
	}

	// --------------------------------------------------------------------

	/**
	 * Get Header
	 *
	 * @param	string	$header_name
	 * @return	string
	 */
	public function get_header($header)
	{
		// Combine headers already sent with our batched headers
		$headers = array_merge(
			// We only need [x][0] from our multi-dimensional array
			array_map('array_shift', $this->headers),
			headers_list()
		);

		if (empty($headers) OR empty($header))
		{
			return NULL;
		}

		for ($i = 0, $c = count($headers); $i < $c; $i++)
		{
			if (strncasecmp($header, $headers[$i], $l = strlen($header)) === 0)
			{
				return trim(substr($headers[$i], $l+1));
			}
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Set HTTP Status Header
	 *
	 * As of version 1.7.2, this is an alias for common function
	 * set_status_header().
	 *
	 * @param	int	$code	Status code (default: 200)
	 * @param	string	$text	Optional message
	 * @return	CI_Output
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
	 * @param	bool	$val	TRUE to enable or FALSE to disable
	 * @return	CI_Output
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
	 * Allows override of default/config settings for
	 * Profiler section display.
	 *
	 * @param	array	$sections	Profiler sections
	 * @return	CI_Output
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
	 * @param	int	$time	Cache expiration time in seconds
	 * @return	CI_Output
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
	 * Processes sends the sends finalized output data to the browser along
	 * with any server headers and profile data. It also stops benchmark
	 * timers so the page rendering speed and memory usage can be shown.
	 *
	 * Note: All "view" data is automatically put into $this->final_output
	 *	 by controller class.
	 *
	 * @uses	CI_Output::$final_output
	 * @param	string	$output	Output data override
	 * @return	void
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
		if ($output === '')
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

		// Do we need to write a cache file? Only if the controller does not have its
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
			$memory	= round(memory_get_usage() / 1024 / 1024, 2).'MB';

			$output = str_replace(array('{elapsed_time}', '{memory_usage}'), array($elapsed, $memory), $output);
		}

		// --------------------------------------------------------------------

		// Is compression requested?
		if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc === FALSE
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
			return;
		}

		// --------------------------------------------------------------------

		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler === TRUE)
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
	 * Write Cache
	 *
	 * @param	string	$output	Output data to cache
	 * @return	void
	 */
	public function _write_cache($output)
	{
		$CI =& get_instance();
		$path = $CI->config->item('cache_path');
		$cache_path = ($path === '') ? APPPATH.'cache/' : $path;

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

		// Put together our serialized info.
		$cache_info = serialize(array(
			'expire'	=> $expire,
			'headers'	=> $this->headers
		));

		if (flock($fp, LOCK_EX))
		{
			fwrite($fp, $cache_info.'ENDCI--->'.$output);
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
	 * Update/serve cached output
	 *
	 * @uses	CI_Config
	 * @uses	CI_URI
	 *
	 * @param	object	&$CFG	CI_Config class instance
	 * @param	object	&$URI	CI_URI class instance
	 * @return	bool	TRUE on success or FALSE on failure
	 */
	public function _display_cache(&$CFG, &$URI)
	{
		$cache_path = ($CFG->item('cache_path') === '') ? APPPATH.'cache/' : $CFG->item('cache_path');

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

		// Look for embedded serialized file info.
		if ( ! preg_match('/^(.*)ENDCI--->/', $cache, $match))
		{
			return FALSE;
		}

		$cache_info = unserialize($match[1]);
		$expire = $cache_info['expire'];

		$last_modified = filemtime($cache_path);

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

		// Add headers from cache file.
		foreach ($cache_info['headers'] as $header)
		{
			$this->set_header($header[0], $header[1]);
		}

		// Display the cache
		$this->_display(substr($cache, strlen($match[0])));
		log_message('debug', 'Cache file is current. Sending it to browser.');
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete cache
	 *
	 * @param	string	$uri	URI string
	 * @return	bool
	 */
	public function delete_cache($uri = '')
	{
		$CI =& get_instance();
		$cache_path = $CI->config->item('cache_path');
		if ($cache_path === '')
		{
			$cache_path = APPPATH.'cache/';
		}

		if ( ! is_dir($cache_path))
		{
			log_message('error', 'Unable to find cache path: '.$cache_path);
			return FALSE;
		}

		if (empty($uri))
		{
			$uri = $CI->uri->uri_string();
		}

		$cache_path .= md5($CI->config->item('base_url').$CI->config->item('index_page').$uri);

		if ( ! @unlink($cache_path))
		{
			log_message('error', 'Unable to delete cache file for '.$uri);
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache Header
	 *
	 * Set the HTTP headers to match the server-side file cache settings
	 * in order to reduce bandwidth.
	 *
	 * @param	int	$last_modified	Timestamp of when the page was last modified
	 * @param	int	$expiration	Timestamp of when should the requested page expire from cache
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
	 * Minify
	 *
	 * Reduce excessive size of HTML/CSS/JavaScript content.
	 *
	 * @param	string	$output	Output to minify
	 * @param	string	$type	Output content MIME type
	 * @return	string	Minified output
	 */
	public function minify($output, $type = 'text/html')
	{
		switch ($type)
		{
			case 'text/html':

				if (($size_before = strlen($output)) === 0)
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
					$output = str_replace($s, $this->_minify_script_style($s, TRUE), $output);
				}

				// Minify the javascript in <script> tags.
				foreach ($javascript_clean[0] as $s)
				{
					$javascript_mini[] = $this->_minify_script_style($s, TRUE);
				}

				// Replace multiple spaces with a single space.
				$output = preg_replace('!\s{2,}!', ' ', $output);

				// Remove comments (non-MSIE conditionals)
				$output = preg_replace('{\s*<!--[^\[<>].*(?<!!)-->\s*}msU', '', $output);

				// Remove spaces around block-level elements.
				$output = preg_replace('/\s*(<\/?(html|head|title|meta|script|link|style|body|table|thead|tbody|tfoot|tr|th|td|h[1-6]|div|p|br)[^>]*>)\s*/is', '$1', $output);

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
			case 'text/javascript':

				$output = $this->_minify_script_style($output);

			break;

			default: break;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Minify Style and Script
	 *
	 * Reduce excessive size of CSS/JavaScript content.  To remove spaces this
	 * script walks the string as an array and determines if the pointer is inside
	 * a string created by single quotes or double quotes.  spaces inside those
	 * strings are not stripped.  Opening and closing tags are severed from
	 * the string initially and saved without stripping whitespace to preserve
	 * the tags and any associated properties if tags are present
	 *
	 * Minification logic/workflow is similar to methods used by Douglas Crockford
	 * in JSMIN. http://www.crockford.com/javascript/jsmin.html
	 *
	 * KNOWN ISSUE: ending a line with a closing parenthesis ')' and no semicolon
	 * where there should be one will break the Javascript. New lines after a
	 * closing parenthesis are not recognized by the script. For best results
	 * be sure to terminate lines with a semicolon when appropriate.
	 *
	 * @param	string	$output		Output to minify
	 * @param	bool	$has_tags	Specify if the output has style or script tags
	 * @return	string	Minified output
	 */
	protected function _minify_script_style($output, $has_tags = FALSE)
	{
		// We only need this if there are tags in the file
		if ($has_tags === TRUE)
		{
			// Remove opening tag and save for later
			$pos = strpos($output, '>') + 1;
			$open_tag = substr($output, 0, $pos);
			$output = substr_replace($output, '', 0, $pos);

			// Remove closing tag and save it for later
			$end_pos = strlen($output);
			$pos = strpos($output, '</');
			$closing_tag = substr($output, $pos, $end_pos);
			$output = substr_replace($output, '', $pos);
		}

		// Remove CSS comments
		$output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!i', '', $output);

		// Remove spaces around curly brackets, colons,
		// semi-colons, parenthesis, commas
		$output = preg_replace('!\s*(:|;|,|}|{|\(|\))\s*!i', '$1', $output);

		// Replace tabs with spaces
		// Replace carriage returns & multiple new lines with single new line
		// and trim any leading or trailing whitespace
		$output = trim(preg_replace(array('/\t+/', '/\r/', '/\n+/'), array(' ', "\n", "\n"), $output));

		// Remove spaces when safe to do so.
		$in_string = $in_dstring = $prev = FALSE;
		$array_output = str_split($output);
		foreach ($array_output as $key => $value)
		{
			if ($in_string === FALSE && $in_dstring === FALSE)
			{
				if ($value === ' ')
				{
					// Get the next element in the array for comparisons
					$next = $array_output[$key + 1];

					// Strip spaces preceded/followed by a non-ASCII character
					// or not preceded/followed by an alphanumeric
					// or not preceded/followed \ $ and _
					if ((preg_match('/^[\x20-\x7f]*$/D', $next) OR preg_match('/^[\x20-\x7f]*$/D', $prev))
						&& ( ! ctype_alnum($next) OR ! ctype_alnum($prev))
						&& ! in_array($next, array('\\', '_', '$'), TRUE)
						&& ! in_array($prev, array('\\', '_', '$'), TRUE)
					)
					{
						unset($array_output[$key]);
					}
				}
				else
				{
					// Save this value as previous for the next iteration
					// if it is not a blank space
					$prev = $value;
				}
			}

			if ($value === "'")
			{
				$in_string = ! $in_string;
			}
			elseif ($value === '"')
			{
				$in_dstring = ! $in_dstring;
			}
		}

		// Put the string back together after spaces have been stripped
		$output = implode($array_output);

		// Remove new line characters unless previous or next character is
		// printable or Non-ASCII
		preg_match_all('/[\n]/', $output, $lf, PREG_OFFSET_CAPTURE);
		$removed_lf = 0;
		foreach ($lf as $feed_position)
		{
			foreach ($feed_position as $position)
			{
				$position = $position[1] - $removed_lf;
				$next = $output[$position + 1];
				$prev = $output[$position - 1];
				if ( ! ctype_print($next) && ! ctype_print($prev)
					&& ! preg_match('/^[\x20-\x7f]*$/D', $next)
					&& ! preg_match('/^[\x20-\x7f]*$/D', $prev)
				)
				{
					$output = substr_replace($output, '', $position, 1);
					$removed_lf++;
				}
			}
		}

		// Put the opening and closing tags back if applicable
		return isset($open_tag)
			? $open_tag.$output.$closing_tag
			: $output;
	}

}

/* End of file Output.php */
/* Location: ./system/core/Output.php */