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
 * CodeIgniter Inflector Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/directory_helper.html
 */


// --------------------------------------------------------------------

/**
 * Singular
 *
 * Takes a plural word and makes it singular
 *
 * @access	public
 * @param	string
 * @return	str
 */
if ( ! function_exists('singular'))
{
	function singular($str)
	{
		$result = strval($str);

		$singular_rules = array(
			'/(matr)ices$/'         => '\1ix',
			'/(vert|ind)ices$/'     => '\1ex',
			'/^(ox)en/'             => '\1',
			'/(alias)es$/'          => '\1',
			'/([octop|vir])i$/'     => '\1us',
			'/(cris|ax|test)es$/'   => '\1is',
			'/(shoe)s$/'            => '\1',
			'/(o)es$/'              => '\1',
			'/(bus|campus)es$/'     => '\1',
			'/([m|l])ice$/'         => '\1ouse',
			'/(x|ch|ss|sh)es$/'     => '\1',
			'/(m)ovies$/'           => '\1\2ovie',
			'/(s)eries$/'           => '\1\2eries',
			'/([^aeiouy]|qu)ies$/'  => '\1y',
			'/([lr])ves$/'          => '\1f',
			'/(tive)s$/'            => '\1',
			'/(hive)s$/'            => '\1',
			'/([^f])ves$/'          => '\1fe',
			'/(^analy)ses$/'        => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'            => '\1um',
			'/(p)eople$/'           => '\1\2erson',
			'/(m)en$/'              => '\1an',
			'/(s)tatuses$/'         => '\1\2tatus',
			'/(c)hildren$/'         => '\1\2hild',
			'/(n)ews$/'             => '\1\2ews',
			'/([^u])s$/'            => '\1',
		);

		foreach ($singular_rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}
}

// --------------------------------------------------------------------

/**
 * Plural
 *
 * Takes a singular word and makes it plural
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	str
 */
if ( ! function_exists('plural'))
{
	function plural($str, $force = FALSE)
	{
		$result = strval($str);

		$plural_rules = array(
			'/^(ox)$/'                 => '\1\2en',     // ox
			'/([m|l])ouse$/'           => '\1ice',      // mouse, louse
			'/(matr|vert|ind)ix|ex$/'  => '\1ices',     // matrix, vertex, index
			'/(x|ch|ss|sh)$/'          => '\1es',       // search, switch, fix, box, process, address
			'/([^aeiouy]|qu)y$/'       => '\1ies',      // query, ability, agency
			'/(hive)$/'                => '\1s',        // archive, hive
			'/(?:([^f])fe|([lr])f)$/'  => '\1\2ves',    // half, safe, wife
			'/sis$/'                   => 'ses',        // basis, diagnosis
			'/([ti])um$/'              => '\1a',        // datum, medium
			'/(p)erson$/'              => '\1eople',    // person, salesperson
			'/(m)an$/'                 => '\1en',       // man, woman, spokesman
			'/(c)hild$/'               => '\1hildren',  // child
			'/(buffal|tomat)o$/'       => '\1\2oes',    // buffalo, tomato
			'/(bu|campu)s$/'           => '\1\2ses',    // bus, campus
			'/(alias|status|virus)/'   => '\1es',       // alias
			'/(octop)us$/'             => '\1i',        // octopus
			'/(ax|cris|test)is$/'      => '\1es',       // axis, crisis
			'/s$/'                     => 's',          // no change (compatibility)
			'/$/'                      => 's',
		);

		foreach ($plural_rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}
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
if ( ! function_exists('camelize'))
{
	function camelize($str)
	{
		$str = 'x'.strtolower(trim($str));
		$str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
		return substr(str_replace(' ', '', $str), 1);
	}
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
if ( ! function_exists('underscore'))
{
	function underscore($str)
	{
		return preg_replace('/[\s]+/', '_', strtolower(trim($str)));
	}
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
if ( ! function_exists('humanize'))
{
	function humanize($str)
	{
		return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($str))));
	}
}

// --------------------------------------------------------------------

/**
 * Pluralize
 *
 * Takes a singular word and a count, and returns the word in the sigular or plural form based on the count
 * Optionally, will return the string including the count supplied appended to the start
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	bool
 * @return	str
 */

if ( !function_exists( 'resolve_case' ) )
{
	function resolve_case($word, $count, $include_count = false)
	{
		if ($count == 1)
		{
			$return_value = singular($word);
		}
		else
		{
			$return_value = plural($word);
		}

		if ( $include_count == true)
		{
			$return_value = $count . ' ' . $return_value;
		}

		return $return_value;
	}
}

/* End of file inflector_helper.php */
/* Location: ./system/helpers/inflector_helper.php */