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
 * CodeIgniter Compatibility Helpers
 *
 * This helper contains some functions based on the PEAR PHP_Compat library
 * http://pear.php.net/package/PHP_Compat
 * 
 * The PEAR compat library is a little bloated and the code doesn't harmonize
 * well with CodeIgniter, so those functions have been refactored.
 * We cheat a little and use CI's _exception_handler() to output our own PHP errors
 * so that the behavior fully mimicks the PHP 5 counterparts.  -- Derek Jones
 * @PHP4
 * 
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/compatibility_helper.html
 */

// ------------------------------------------------------------------------

if ( ! defined('PHP_EOL'))
{
	define('PHP_EOL', (DIRECTORY_SEPARATOR == '/') ? "\n" : "\r\n");
} 

// ------------------------------------------------------------------------

/**
 * file_put_contents()
 *
 * Writes a string to a file
 * http://us.php.net/manual/en/function.file_put_contents.php
 * argument 4, $context, not supported
 *
 * @access	public
 * @param	string		file name
 * @param	mixed		data to be written
 * @param	int			flags
 * @return	int			length of written string
 */
if ( ! function_exists('file_put_contents'))
{
	function file_put_contents($filename, $data, $flags = NULL)
	{
		if (is_scalar($data))
		{
			settype($data, 'STRING');
		}

		if ( ! is_string($data) && ! is_array($data) && ! is_resource($data))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'file_put_contents(): the 2nd parameter should be either a string or an array', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}

		// read stream if given a stream resource
		if (is_resource($data))
		{
			if (get_resource_type($data) !== 'stream')
			{
				$backtrace = debug_backtrace();
				_exception_handler(E_USER_WARNING, 'file_put_contents(): supplied resource is not a valid stream resource', $backtrace[0]['file'], $backtrace[0]['line']);
				return FALSE;
			}

			$text = '';
			
			while ( ! feof($data))
			{
				$text .= fread($data, 4096);
			}
			
			$data = $text;
			unset($text);
		}
	
		// strings only please!
		if (is_array($data))
		{
			$data = implode('', $data);
		}

		// Set the appropriate mode
		if (($flags & 8) > 0) // 8 = FILE_APPEND flag
		{
			$mode = FOPEN_WRITE_CREATE;
		}
		else
		{
			$mode = FOPEN_WRITE_CREATE_DESTRUCTIVE;
		}
	
		// Check if we're using the include path
		if (($flags & 1) > 0) // 1 = FILE_USE_INCLUDE_PATH flag
		{
			$use_include_path = TRUE;
		}
		else
		{
			$use_include_path = FALSE;
		}
	
		$fp = @fopen($filename, $mode, $use_include_path);
	
		if ($fp === FALSE)
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'file_put_contents('.htmlentities($filename).') failed to open stream', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		if (($flags & LOCK_EX) > 0)
		{
			if ( ! flock($fp, LOCK_EX))
			{
				$backtrace = debug_backtrace();
				_exception_handler(E_USER_WARNING, 'file_put_contents('.htmlentities($filename).') unable to acquire an exclusive lock on file', $backtrace[0]['file'], $backtrace[0]['line']);
				return FALSE;
			}
		}
		
		// write it
		if (($written = @fwrite($fp, $data)) === FALSE)
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'file_put_contents('.htmlentities($filename).') failed to write to '.htmlentities($filename), $backtrace[0]['file'], $backtrace[0]['line']);
		}
	
		// Close the handle
		@fclose($fp);
	
		// Return length
		return $written;
	}
}

// ------------------------------------------------------------------------

/**
 * fputcsv()
 *
 * Format line as CSV and write to file pointer
 * http://us.php.net/manual/en/function.fputcsv.php
 *
 * @access	public
 * @param	resource	file pointer
 * @param	array		data to be written
 * @param	string		delimiter
 * @param	string		enclosure
 * @return	int			length of written string
 */
if ( ! function_exists('fputcsv'))
{
	function fputcsv($handle, $fields, $delimiter = ',', $enclosure = '"')
	{
		// Checking for a handle resource
		if ( ! is_resource($handle))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'fputcsv() expects parameter 1 to be stream resource, '.gettype($handle).' given', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		// OK, it is a resource, but is it a stream?
		if (get_resource_type($handle) !== 'stream')
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'fputcsv() expects parameter 1 to be stream resource, '.get_resource_type($handle).' given', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		// Checking for an array of fields
		if ( ! is_array($fields))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'fputcsv() expects parameter 2 to be array, '.gettype($fields).' given', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		// validate delimiter
		if (strlen($delimiter) > 1)
		{
			$delimiter = substr($delimiter, 0, 1);
			$backtrace = debug_backtrace();
			_exception_handler(E_NOTICE, 'fputcsv() delimiter must be one character long, "'.htmlentities($delimiter).'" used', $backtrace[0]['file'], $backtrace[0]['line']);
		}
	
		// validate enclosure
		if (strlen($enclosure) > 1)
		{
			$enclosure = substr($enclosure, 0, 1);
			$backtrace = debug_backtrace();
			_exception_handler(E_NOTICE, 'fputcsv() enclosure must be one character long, "'.htmlentities($enclosure).'" used', $backtrace[0]['file'], $backtrace[0]['line']);

		}
	
		$out = '';
	
		foreach ($fields as $cell)
		{
			$cell = str_replace($enclosure, $enclosure.$enclosure, $cell);

			if (strpos($cell, $delimiter) !== FALSE OR strpos($cell, $enclosure) !== FALSE OR strpos($cell, "\n") !== FALSE)
			{
				$out .= $enclosure.$cell.$enclosure.$delimiter;
			}
			else
			{
				$out .= $cell.$delimiter;
			}
		}
	
		$length = @fwrite($handle, substr($out, 0, -1)."\n");
	
		return $length;
	}
}

// ------------------------------------------------------------------------

/**
 * stripos()
 *
 * Find position of first occurrence of a case-insensitive string
 * http://us.php.net/manual/en/function.stripos.php
 *
 * @access	public
 * @param	string		haystack
 * @param	string		needle
 * @param	int			offset
 * @return	int			numeric position of the first occurrence of needle in the haystack
 */
if ( ! function_exists('stripos'))
{
	function stripos($haystack, $needle, $offset = NULL)
	{
		// Cast non string scalar values
		if (is_scalar($haystack))
		{
			settype($haystack, 'STRING');
		}
	
		if ( ! is_string($haystack))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'stripos() expects parameter 1 to be string, '.gettype($haystack).' given', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		if ( ! is_scalar($needle))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'stripos() needle is not a string or an integer in '.$backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		if (is_float($offset))
		{
			$offset = (int)$offset;
		}
	
		if ( ! is_int($offset) && ! is_bool($offset) && ! is_null($offset))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'stripos() expects parameter 3 to be long, '.gettype($offset).' given', $backtrace[0]['file'], $backtrace[0]['line']);
			return NULL;
		}
	
		return strpos(strtolower($haystack), strtolower($needle), $offset);
	}
}

// ------------------------------------------------------------------------

/**
 * str_ireplace()
 *
 * Find position of first occurrence of a case-insensitive string
 * http://us.php.net/manual/en/function.str-ireplace.php
 * (parameter 4, $count, is not supported as to do so in PHP 4 would make
 * it a required parameter)
 *
 * @access	public
 * @param	mixed		search
 * @param	mixed		replace
 * @param	mixed		subject
 * @return	int			numeric position of the first occurrence of needle in the haystack
 */
if ( ! function_exists('str_ireplace'))
{
	function str_ireplace($search, $replace, $subject)
	{
		// Nothing to do here
		if ($search === NULL OR $subject === NULL)
		{
			return $subject;
		}
	
		// Crazy arguments
		if (is_scalar($search) && is_array($replace))
		{
			$backtrace = debug_backtrace();

			if (is_object($replace))
			{
				show_error('Object of class '.get_class($replace).' could not be converted to string in '.$backtrace[0]['file'].' on line '.$backtrace[0]['line']);
			}
			else
			{
				_exception_handler(E_USER_NOTICE, 'Array to string conversion in '.$backtrace[0]['file'], $backtrace[0]['line']);
			}
		}
	
		// Searching for an array
		if (is_array($search))
		{
			// Replacing with an array
			if (is_array($replace))
			{
				$search = array_values($search);
				$replace = array_values($replace);

				if (count($search) >= count($replace))
				{
					$replace = array_pad($replace, count($search), '');
				}
				else
				{
					$replace = array_slice($replace, 0, count($search));
				}
			}
			else
			{
				// Replacing with a string all positions
				$replace = array_fill(0, count($search), $replace);
			}
		}
		else
		{
			//Searching for a string and replacing with a string.
			$search  = array((string)$search);
			$replace = array((string)$replace);
		}
		
		// Prepare the search array
		foreach ($search as $search_key => $search_value)
		{
			$search[$search_key] = '/'.preg_quote($search_value, '/').'/i';
		}
		
		// Prepare the replace array (escape backreferences)
		foreach ($replace as $k => $v)
		{
			$replace[$k] = str_replace(array(chr(92), '$'), array(chr(92).chr(92), '\$'), $v);
		}
	
		// do the replacement
		$result = preg_replace($search, $replace, (array)$subject);
	
		// Check if subject was initially a string and return it as a string
		if ( ! is_array($subject))
		{
			return current($result);
		}
	
		// Otherwise, just return the array
		return $result;
	}
}

// ------------------------------------------------------------------------

/**
 * http_build_query()
 *
 * Generate URL-encoded query string
 * http://us.php.net/manual/en/function.http-build-query.php
 *
 * @access	public
 * @param	array		form data
 * @param	string		numeric prefix
 * @param	string		argument separator
 * @return	string		URL-encoded string
 */
if ( ! function_exists('http_build_query'))
{
	function http_build_query($formdata, $numeric_prefix = NULL, $separator = NULL)
	{
		// Check the data
		if ( ! is_array($formdata) && ! is_object($formdata))
		{
			$backtrace = debug_backtrace();
			_exception_handler(E_USER_WARNING, 'http_build_query() Parameter 1 expected to be Array or Object. Incorrect value given', $backtrace[0]['file'], $backtrace[0]['line']);
			return FALSE;
		}
	
		// Cast it as array
		if (is_object($formdata))
		{
			$formdata = get_object_vars($formdata);
		}
	
		// If the array is empty, return NULL
		if (empty($formdata))
		{
			return NULL;
		}
	
		// Argument separator
		if ($separator === NULL)
		{
			$separator = ini_get('arg_separator.output');

			if (strlen($separator) == 0)
			{
				$separator = '&';
			}
		}
	
		// Start building the query
		$tmp = array();

		foreach ($formdata as $key => $val)
		{
			if ($val === NULL)
			{
				continue;
			}
	
			if (is_integer($key) && $numeric_prefix != NULL)
			{
				$key = $numeric_prefix.$key;
			}
	
			if (is_resource($val))
			{
				return NULL;
			}
			
			// hand it off to a recursive parser
			$tmp[] = _http_build_query_helper($key, $val, $separator);
		}
	
		return implode($separator, $tmp);
	}
	
	
	// Helper helper.  Remind anyone of college?
	// Required to handle recursion in nested arrays.
	// 
	// You could shave fractions of fractions of a second by moving where
	// the urlencoding takes place, but it's much less intuitive, and if
	// your application has 10,000 form fields, well, you have other problems ;)
	function _http_build_query_helper($key, $val, $separator = '&')
	{	
		if (is_scalar($val))
		{
			return urlencode($key).'='.urlencode($val);			
		}
		else
		{
			// arrays please
			if (is_object($val))
			{
				$val = get_object_vars($val);
			}
			
			foreach ($val as $k => $v)
			{
				$tmp[] = _http_build_query_helper($key.'['.$k.']', $v, $separator);
			}
		}
			
		return implode($separator, $tmp);
	}
}


/* End of file compatibility_helper.php */
/* Location: ./system/helpers/compatibility_helper.php */