<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Code Igniter Inflector Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/directory_helper.html
 */


// --------------------------------------------------------------------

/**
 * Singular
 *
 * Takes a singular word and makes it plural
 *
 * @access	public
 * @param	string
 * @return	str
 */		
function singular($str)
{
	$str = strtolower(trim($str));
	$end = substr($str, -3);
	
	if ($end == 'ies')
	{
		$str = substr($str, 0, strlen($str)-3).'y';
	}
	else
	{
		$end = substr($str, -1);
		
		if ($end == 's')
		{
			$str = substr($str, 0, strlen($str)-1);
		}
	}
	
	return $str;
}

// --------------------------------------------------------------------

/**
 * Plural
 *
 * Takes a plural word and makes it singular
 *
 * @access	public
 * @param	string
 * @return	str
 */		
function plural($str)
{
	$str = strtolower(trim($str));
	$end = substr($str, -1);

	if ($end == 'y')
	{
		$str = substr($str, 0, strlen($str)-1).'ies';
	}
	elseif ($end != 's')
	{
		$str .= 's';
	}

	return $str;	
}

// --------------------------------------------------------------------

/**
 * Camelize
 *
 * Takes multiple words separated by spaces or underscores and camelizes them
 *
 * @access	public
 * @param	string
 * @return	str
 */		
function camelize($str)
{		
	$str = 'x'.strtolower(trim($str));
	$str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
	return substr(str_replace(' ', '', $str), 1);
}

// --------------------------------------------------------------------

/**
 * Underscore
 *
 * Takes multiple words separated by spaces and underscores them
 *
 * @access	public
 * @param	string
 * @return	str
 */		
function underscore($str)
{
	return preg_replace('/[\s]+/', '_', strtolower(trim($str)));
}

// --------------------------------------------------------------------

/**
 * Humanize
 *
 * Takes multiple words separated by underscores and changes them to spaces
 *
 * @access	public
 * @param	string
 * @return	str
 */		
function humanize($str)
{
	return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($str))));
}
	
?>