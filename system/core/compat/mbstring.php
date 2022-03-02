<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 3.0.0
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
 * @link		https://codeigniter.com/userguide3/
 * @link		https://secure.php.net/mbstring
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
	 * @link	https://secure.php.net/mb_strlen
	 * @param	string	$str
	 * @param	string	$encoding
	 * @return	int
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
	 * @link	https://secure.php.net/mb_strpos
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
	 * @link	https://secure.php.net/mb_substr
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
