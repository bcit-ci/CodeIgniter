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
 * CodeIgniter String Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/string_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Trim Slashes
 *
 * Removes any leading/trailing slashes from a string:
 *
 * /this/that/theother/
 *
 * becomes:
 *
 * this/that/theother
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('trim_slashes'))
{
	function trim_slashes($str)
	{
		return trim($str, '/');
	}
}

// ------------------------------------------------------------------------

/**
 * Strip Slashes
 *
 * Removes slashes contained in a string or in an array
 *
 * @access	public
 * @param	mixed	string or array
 * @return	mixed	string or array
 */
if ( ! function_exists('strip_slashes'))
{
	function strip_slashes($str)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = strip_slashes($val);
			}
		}
		else
		{
			$str = stripslashes($str);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Strip Quotes
 *
 * Removes single and double quotes from a string
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('strip_quotes'))
{
	function strip_quotes($str)
	{
		return str_replace(array('"', "'"), '', $str);
	}
}

// ------------------------------------------------------------------------

/**
 * Quotes to Entities
 *
 * Converts single and double quotes to entities
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('quotes_to_entities'))
{
	function quotes_to_entities($str)
	{
		return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $str);
	}
}

// ------------------------------------------------------------------------

/**
 * Reduce Double Slashes
 *
 * Converts double slashes in a string to a single slash,
 * except those found in http://
 *
 * http://www.some-site.com//index.php
 *
 * becomes:
 *
 * http://www.some-site.com/index.php
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('reduce_double_slashes'))
{
	function reduce_double_slashes($str)
	{
		return preg_replace("#(^|[^:])//+#", "\\1/", $str);
	}
}

// ------------------------------------------------------------------------

/**
 * Reduce Multiples
 *
 * Reduces multiple instances of a particular character.  Example:
 *
 * Fred, Bill,, Joe, Jimmy
 *
 * becomes:
 *
 * Fred, Bill, Joe, Jimmy
 *
 * @access	public
 * @param	string
 * @param	string	the character you wish to reduce
 * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
 * @return	string
 */
if ( ! function_exists('reduce_multiples'))
{
	function reduce_multiples($str, $character = ',', $trim = FALSE)
	{
		$str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);

		if ($trim === TRUE)
		{
			$str = trim($str, $character);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Create a Random String
 *
 * Useful for generating passwords or hashes.
 *
 * @access	public
 * @param	string	type of random string.  basic, alpha, alunum, numeric, nozero, unique, md5, encrypt and sha1
 * @param	integer	number of characters
 * @return	string
 */
if ( ! function_exists('random_string'))
{
	function random_string($type = 'alnum', $len = 8)
	{
		switch($type)
		{
			case 'basic'	: return mt_rand();
				break;
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			case 'alpha'	:

					switch ($type)
					{
						case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'numeric'	:	$pool = '0123456789';
							break;
						case 'nozero'	:	$pool = '123456789';
							break;
					}
					
					$str = substr(str_shuffle(str_repeat($pool, ceil($len/strlen($pool)))),0,$len);
					
					return $str;
				break;
			case 'unique'	:
			case 'md5'		:

						return md5(uniqid(mt_rand()));
				break;
			case 'encrypt'	:
			case 'sha1'	:

						$CI =& get_instance();
						$CI->load->helper('security');

						return do_hash(uniqid(mt_rand(), TRUE), 'sha1');
				break;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Add's _1 to a string or increment the ending number to allow _2, _3, etc
 *
 * @param   string  $str  required
 * @param   string  $separator  What should the duplicate number be appended with
 * @param   string  $first  Which number should be used for the first dupe increment
 * @return  string
 */
function increment_string($str, $separator = '_', $first = 1)
{
	preg_match('/(.+)'.$separator.'([0-9]+)$/', $str, $match);

	return isset($match[2]) ? $match[1].$separator.($match[2] + 1) : $str.$separator.$first;
}

// ------------------------------------------------------------------------

/**
 * Alternator
 *
 * Allows strings to be alternated.  See docs...
 *
 * @access	public
 * @param	string (as many parameters as needed)
 * @return	string
 */
if ( ! function_exists('alternator'))
{
	function alternator()
	{
		static $i;

		if (func_num_args() == 0)
		{
			$i = 0;
			return '';
		}
		$args = func_get_args();
		return $args[($i++ % count($args))];
	}
}

// ------------------------------------------------------------------------

/**
 * Repeater function
 *
 * @access	public
 * @param	string
 * @param	integer	number of repeats
 * @return	string
 */
if ( ! function_exists('repeater'))
{
	function repeater($data, $num = 1)
	{
		return (($num > 0) ? str_repeat($data, $num) : '');
	}
}


/* End of file string_helper.php */
/* Location: ./system/helpers/string_helper.php */