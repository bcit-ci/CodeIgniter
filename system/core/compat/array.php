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
 * PHP ext/standard/array compatibility package
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Compatibility
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/
 * @link		http://php.net/book.array
 */

// ------------------------------------------------------------------------

if (is_php('5.5'))
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_column'))
{
	/**
	 * array_column()
	 *
	 * @link	http://php.net/array_column
	 * @param	string	$array
	 * @param	mixed	$column_key
	 * @param	mixed	$index_key
	 * @return	array
	 */
	function array_column(array $array, $column_key, $index_key = NULL)
	{
		if ( ! in_array($type = gettype($column_key), array('integer', 'string', 'NULL'), TRUE))
		{
			if ($type === 'double')
			{
				$column_key = (int) $column_key;
			}
			elseif ($type === 'object' && method_exists($column_key, '__toString'))
			{
				$column_key = (string) $column_key;
			}
			else
			{
				trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
				return FALSE;
			}
		}

		if ( ! in_array($type = gettype($index_key), array('integer', 'string', 'NULL'), TRUE))
		{
			if ($type === 'double')
			{
				$index_key = (int) $index_key;
			}
			elseif ($type === 'object' && method_exists($index_key, '__toString'))
			{
				$index_key = (string) $index_key;
			}
			else
			{
				trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
				return FALSE;
			}
		}

		$result = array();
		foreach ($array as &$a)
		{
			if ($column_key === NULL)
			{
				$value = $a;
			}
			elseif (is_array($a) && array_key_exists($column_key, $a))
			{
				$value = $a[$column_key];
			}
			else
			{
				continue;
			}

			if ($index_key === NULL OR ! array_key_exists($index_key, $a))
			{
				$result[] = $value;
			}
			else
			{
				$result[$a[$index_key]] = $value;
			}
		}

		return $result;
	}
}

// ------------------------------------------------------------------------

if (is_php('5.3'))
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_replace'))
{
	/**
	 * array_replace()
	 *
	 * @link	http://php.net/array_replace
	 * @return	array
	 */
	function array_replace()
	{
		$arrays = func_get_args();

		if (($c = count($arrays)) === 0)
		{
			trigger_error('array_replace() expects at least 1 parameter, 0 given', E_USER_WARNING);
			return NULL;
		}
		elseif ($c === 1)
		{
			if ( ! is_array($arrays[0]))
			{
				trigger_error('array_replace(): Argument #1 is not an array', E_USER_WARNING);
				return NULL;
			}

			return $arrays[0];
		}

		$array = array_shift($arrays);
		$c--;

		for ($i = 0, $c = count($arrays); $i < $c; $i++)
		{
			if ( ! is_array($arrays[$i]))
			{
				trigger_error('array_replace(): Argument #'.($i + 2).' is not an array', E_USER_WARNING);
				return NULL;
			}
			elseif (empty($arrays[$i]))
			{
				continue;
			}

			foreach (array_keys($arrays[$i]) as $key)
			{
				$array[$key] = $arrays[$i][$key];
			}
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_replace_recursive'))
{
	/**
	 * array_replace_recursive()
	 *
	 * @link	http://php.net/array_replace_recursive
	 * @return	array
	 */
	function array_replace_recursive()
	{
		$arrays = func_get_args();

		if (($c = count($arrays)) === 0)
		{
			trigger_error('array_replace_recursive() expects at least 1 parameter, 0 given', E_USER_WARNING);
			return NULL;
		}
		elseif ($c === 1)
		{
			if ( ! is_array($arrays[0]))
			{
				trigger_error('array_replace_recursive(): Argument #1 is not an array', E_USER_WARNING);
				return NULL;
			}

			return $arrays[0];
		}

		$array = array_shift($arrays);
		$c--;

		for ($i = 0, $c = count($arrays); $i < $c; $i++)
		{
			if ( ! is_array($arrays[$i]))
			{
				trigger_error('array_replace_recursive(): Argument #'.($i + 2).' is not an array', E_USER_WARNING);
				return NULL;
			}
			elseif (empty($arrays[$i]))
			{
				continue;
			}

			foreach (array_keys($arrays[$i]) as $key)
			{
				$array[$key] = (is_array($arrays[$i][$key]) && isset($array[$key]) && is_array($array[$key]))
					? array_replace_recursive($array[$key], $arrays[$i][$key])
					: $arrays[$i][$key];
			}
		}

		return $array;
	}
}

/* End of file array.php */
/* Location: ./system/core/compat/array.php */