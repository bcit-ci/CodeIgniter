<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Compatibility Functions
 *
 * Function overrides for older versions of PHP or PHP environments missing
 * certain extensions / libraries
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Compatibility Functions
 * @author		ExpressionEngine Development Team
 * @link		http://codeigniter.com/user_guide/
 */

// ------------------------------------------------------------------------

/*
 * PHP versions prior to 5.0 don't support the E_STRICT constant
 * so we need to explicitly define it otherwise the Exception class 
 * will generate errors when running under PHP 4
 *
 */
if ( ! defined('E_STRICT'))
{
	define('E_STRICT', 2048);
}

/**
 * ctype_digit()
 *
 * Determines if a string is comprised only of digits
 * http://us.php.net/manual/en/function.ctype_digit.php
 *
 * @access	public
 * @param	string
 * @return	bool
 */
if ( ! function_exists('ctype_digit'))
{
	function ctype_digit($str)
	{
		if ( ! is_string($str) OR $str == '')
		{
			return FALSE;
		}
		
		return ! preg_match('/[^0-9]/', $str);
	}	
}

// --------------------------------------------------------------------

/**
 * ctype_alnum()
 *
 * Determines if a string is comprised of only alphanumeric characters
 * http://us.php.net/manual/en/function.ctype-alnum.php
 *
 * @access	public
 * @param	string
 * @return	bool
 */
if ( ! function_exists('ctype_alnum'))
{
	function ctype_alnum($str)
	{
		if ( ! is_string($str) OR $str == '')
		{
			return FALSE;
		}
		
		return ! preg_match('/[^0-9a-z]/i', $str);
	}	
}

// --------------------------------------------------------------------


/* End of file Compat.php */
/* Location: ./system/codeigniter/Compat.php */