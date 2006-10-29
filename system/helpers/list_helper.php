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
 * Code Igniter HTML Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/html_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Unordered List
 *
 * Generates an HTML unordered list from an single or multi-dimensional array.
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */	
function ul($list, $attributes = '')
{
	return _generate_list('ul', $list, $attributes);
}

// ------------------------------------------------------------------------

/**
 * Ordered List
 *
 * Generates an HTML ordered list from an single or multi-dimensional array.
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */	
function ol($list, $attributes = '')
{
	return _generate_list('ol', $list, $attributes);
}

// ------------------------------------------------------------------------

/**
 * Generates the list
 *
 * Generates an HTML ordered list from an single or multi-dimensional array.
 *
 * @access	private
 * @param	string
 * @param	mixed		
 * @param	mixed		
 * @param	intiger		
 * @return	string
 */	
function _generate_list($type = 'ul', $list, $attributes = '', $depth = 0)
{
	if ( ! is_array($list))
	{
		return $list;
	}

	$out = str_repeat(" ", $depth);
	
	$out .= "<".$type._set_attributes($attributes).">\n";

	foreach ($list as $item)
	{
		if (is_array($item))
		{
			$out .= _list($type, $item, '', $depth+4);
		}
		else
		{
			$out .= str_repeat(" ", $depth + 2);
			
			$out .= "<li>".$item."</li>\n";
		}
	}

	$out .= str_repeat(" ", $depth);
	
	$out .= "</".$type.">\n";

	return $out;
}

// ------------------------------------------------------------------------

/**
 * Generates the attribute string
 *
 * @access	private
 * @param	mixed
 * @return	string
 */	
function _set_attributes($attributes)
{
	if (is_string($attributes))
	{
		return $attributes;
	}

	$atts = '';
	foreach ($attributes as $key => $val)
	{
		$atts .= ' ' . $key . '="' . $val . '"';
	}

	return $atts;
}
?>