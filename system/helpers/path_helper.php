<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright		Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright		Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Path Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/xml_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Set Realpath
 *
 * @access	public
 * @param	string
 * @param	bool	checks to see if the path exists
 * @return	string
 */
if ( ! function_exists('set_realpath'))
{
	function set_realpath($path, $check_existance = FALSE)
	{
		// Security check to make sure the path is NOT a URL.  No remote file inclusion!
		if (preg_match("#^(http:\/\/|https:\/\/|www\.|ftp|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})#i", $path))
		{
			show_error('The path you submitted must be a local server path, not a URL');
		}

		// Resolve the path
		if (function_exists('realpath') AND @realpath($path) !== FALSE)
		{
			$path = realpath($path).'/';
		}

		// Add a trailing slash
		$path = preg_replace("#([^/])/*$#", "\\1/", $path);

		// Make sure the path exists
		if ($check_existance == TRUE)
		{
			if ( ! is_dir($path))
			{
				show_error('Not a valid path: '.$path);
			}
		}

		return $path;
	}
}


/* End of file path_helper.php */
/* Location: ./system/helpers/path_helper.php */