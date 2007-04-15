<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns FALSE (or whatever you specify as the default value.)
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */	
function element($item, $array, $default = FALSE)
{
	if ( ! isset($array[$item]) OR $array[$item] == "")
	{
		return $default;
	}

	return $array[$item];
}

// ------------------------------------------------------------------------

/**
 * Random Element - Takes an array as input and returns a random element
 *
 * @access	public
 * @param	array
 * @return	mixed	depends on what the array contains
 */	
function random_element($array)
{
	if ( ! is_array($array))
	{
		return $array;
	}
	return $array[array_rand($array)];
}


?>