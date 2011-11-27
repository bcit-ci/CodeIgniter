<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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