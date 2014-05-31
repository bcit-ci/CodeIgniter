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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PHP ext/mbstring compatibility package
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Compatibility
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/
 * @link		http://php.net/mbstring
 */

// ------------------------------------------------------------------------

if (MB_ENABLED === TRUE)
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_strlen'))
{
	/**
	 * mb_strlen()
	 *
	 * WARNING: This function WILL fall-back to strlen()
	 * if iconv is not available!
	 *
	 * @link	http://php.net/mb_strlen
	 * @param	string	$str
	 * @param	string	$encoding
	 * @return	string
	 */
	function mb_strlen($str, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			return iconv_strlen($str, isset($encoding) ? $encoding : config_item('charset'));
		}

		log_message('debug', 'Compatibility (mbstring): iconv_strlen() is not available, falling back to strlen().');
		return strlen($str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_strpos'))
{
	/**
	 * mb_strpos()
	 *
	 * WARNING: This function WILL fall-back to strpos()
	 * if iconv is not available!
	 *
	 * @link	http://php.net/mb_strpos()
	 * @param	string	$haystack
	 * @param	string	$needle
	 * @param	int	$offset
	 * @param	string	$encoding
	 * @return	mixed
	 */
	function mb_strpos($haystack, $needle, $offset = 0, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			return iconv_strpos($haystack, $needle, $offset, isset($encoding) ? $encoding : config_item('charset'));
		}

		log_message('debug', 'Compatibility (mbstring): iconv_strpos() is not available, falling back to strpos().');
		return strpos($haystack, $needle, $offset);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_substr'))
{
	/**
	 * mb_substr()
	 *
	 * WARNING: This function WILL fall-back to substr()
	 * if iconv is not available.
	 *
	 * @link	http://php.net/mb_substr
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int 	$length
	 * @param	string	$encoding
	 * @return	string
	 */
	function mb_substr($str, $start, $length = NULL, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			isset($encoding) OR $encoding = config_item('charset');
			return iconv_substr(
				$str,
				$start,
				isset($length) ? $length : iconv_strlen($str, $encoding), // NULL doesn't work
				$encoding
			);
		}

		log_message('debug', 'Compatibility (mbstring): iconv_substr() is not available, falling back to substr().');
		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}

/* End of file mbstring.php */
/* Location: ./system/core/compat/mbstring.php */