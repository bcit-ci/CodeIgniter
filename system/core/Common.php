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
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Common Functions
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

// ------------------------------------------------------------------------

if ( ! function_exists('is_php'))
{
	/**
	 * Determines if the current version of PHP is greater then the supplied value
	 *
	 * Since there are a few places where we conditionally test for PHP > 5.3
	 * we'll set a static variable.
	 *
	 * @param	string
	 * @return	bool	TRUE if the current version is $version or higher
	 */
	function is_php($version = '5.3.0')
	{
		static $_is_php;
		$version = (string) $version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) >= 0);
		}

		return $_is_php[$version];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_really_writable'))
{
	/**
	 * Tests for file writability
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @param	string
	 * @return	void
	 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR === '/' && (bool) @ini_get('safe_mode') === FALSE)
		{
			return is_writable($file);
		}

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));
			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('load_class'))
{
	/**
	 * Load class
	 *
	 * This function returns classes loaded in the core CodeIgniter object.
 	 * DEPRECATED - get object from CodeIgniter::instance() directly
	 * Core classes are loaded internally, and all others should call CI_Loader.
	 *
	 * @param	string	the class name being requested
	 * @param	string	the directory where the class should be found
	 * @param	string	the class name prefix
	 * @return	object
	 */
	function &load_class($class, $directory = 'libraries', $prefix = 'CI_')
	{
		// Get instance, lowercase class, and check
		$CI = CodeIgniter::instance();
		$class = strtolower($class);
		if (isset($CI->$class))
		{
			// Return loaded class
			return $CI->$class;
		}

		// Failed - return NULL
		$null = NULL;
		return $null;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('is_loaded'))
{
	/**
	 * Checks if a class is loaded
	 *
 	 * DEPRECATED - check object with CodeIgniter::instance() directly
	 *
	 * @param	string
	 * @return	array
	 */
	function &is_loaded($class = '')
	{
		// Get instance, lowercase class, and check
		$CI = CodeIgniter::instance();
		$class = strtolower($class);
		return isset($CI->$class);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_config'))
{
	/**
	 * Gets the main config.php file
	 *
	 * DEPRECATED - check with CodeIgniter::instance()->config directly, as
	 * Config is loaded before every class except Benchmark
	 *
	 * @param	array
	 * @return	array
	 */
	function &get_config($replace = array())
	{
		// Get instance and check for config
		$CI = CodeIgniter::instance();
		if ( ! isset($CI->config->config))
		{
			exit('The configuration file has not been loaded.');
		}

		// Get config array
		$config = $CI->config->config;

		// Are any values being dynamically replaced?
		if (count($replace) > 0)
		{
			foreach ($replace as $key => $val)
			{
				if (isset($config[$key]))
				{
					$config[$key] = $val;
				}
			}
		}

		return $config;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('config_item'))
{
	/**
	 * Returns the specified config item
	 *
	 * @param	string
	 * @return	mixed
	 */
	function config_item($item)
	{
		// Get instance and check for config
		$CI = CodeIgniter::instance();
		if ( ! isset($CI->config))
		{
			return FALSE;
		}

		// Return config item
		return $CI->config->item($item);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_mimes'))
{
	/**
	 * Returns the MIME types array from config/mimes.php
	 *
	 * @return	array
	 */
	function &get_mimes()
	{
		$CI = CodeIgniter::instance();
		return $CI->config->get('mimes.php');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('show_error'))
{
	/**
	 * Error Handler
	 *
	 * This function lets us invoke the exception class and
	 * display errors using the standard error template located
	 * in application/errors/errors.php
	 * This function will send the error page directly to the
	 * browser and exit.
	 *
	 * @param	string
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
	{
		// Get instance, load Exceptions and call show_error
		$CI = CodeIgniter::instance();
		$CI->load_core_class('Exceptions');
		$CI->exceptions->show_error($heading, $message, 'error_general', $status_code);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('show_404'))
{
	/**
	 * 404 Page Handler
	 *
	 * This function is similar to the show_error() function above
	 * However, instead of the standard error template it displays
	 * 404 errors.
	 *
	 * @param	string
	 * @param	bool
	 * @return	void
	 */
	function show_404($page = '', $log_error = TRUE)
	{
		// Get instance, load Exceptions and call show_404
		$CI = CodeIgniter::instance();
		$CI->load_core_class('Exceptions');
		$CI->exceptions->show_404($page, $log_error);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('log_message'))
{
	/**
	 * Error Logging Interface
	 *
	 * We use this as a simple mechanism to access the logging
	 * class and send messages to be logged.
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	void
	 */
	function log_message($level = 'error', $message, $php_error = FALSE)
	{
		// Get instance and call log_message
		$CI = CodeIgniter::instance();
		$CI->log_message($level, $message, $php_error);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_status_header'))
{
	/**
	 * Set HTTP Status Header
	 *
	 * @param	int	the status code
	 * @param	string
	 * @return	void
	 */
	function set_status_header($code = 200, $text = '')
	{
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
			303	=> 'See Other',
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
			422	=> 'Unprocessable Entity',

			500	=> 'Internal Server Error',
			501	=> 'Not Implemented',
			502	=> 'Bad Gateway',
			503	=> 'Service Unavailable',
			504	=> 'Gateway Timeout',
			505	=> 'HTTP Version Not Supported'
		);

		if (empty($code) OR ! is_numeric($code))
		{
			show_error('Status codes must be numeric', 500);
		}

		is_int($code) OR $code = (int) $code;

		if (empty($text))
		{
			if (isset($stati[$code]))
			{
				$text = $stati[$code];
			}
			else
			{
				show_error('No status text available. Please check your status code number or supply your own message text.', 500);
			}
		}

		$server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (strpos(php_sapi_name(), 'cgi') === 0)
		{
			header('Status: '.$code.' '.$text, TRUE);
		}
		elseif ($server_protocol === 'HTTP/1.0')
		{
			header('HTTP/1.0 '.$code.' '.$text, TRUE, $code);
		}
		else
		{
			header('HTTP/1.1 '.$code.' '.$text, TRUE, $code);
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('remove_invisible_characters'))
{
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('html_escape'))
{
	/**
	 * Returns HTML escaped variable
	 *
	 * @param	mixed
	 * @return	mixed
	 */
	function html_escape($var)
	{
		return is_array($var)
			? array_map('html_escape', $var)
			: htmlspecialchars($var, ENT_QUOTES, config_item('charset'));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_stringify_attributes'))
{
	/**
	 * Stringify attributes for use in HTML tags.
	 *
	 * Helper function used to convert a string, array, or object
	 * of attributes to a string.
	 *
	 * @param	mixed	string, array, object
	 * @param	bool
	 * @return	string
	 */
	function _stringify_attributes($attributes, $js = FALSE)
	{
		$atts = NULL;

		if (empty($attributes))
		{
			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		$attributes = (array) $attributes;

		foreach ($attributes as $key => $val)
		{
			$atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
		}

		return rtrim($atts, ',');
	}
}

/* End of file Common.php */
/* Location: ./system/core/Common.php */
