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
 * CodeIgniter HTML Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/html_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Heading
 *
 * Generates an HTML heading tag.  First param is the data.
 * Second param is the size of the heading tag.
 *
 * @access	public
 * @param	string
 * @param	integer
 * @return	string
 */	
function heading($data = '', $h = '1')
{
	return "<h".$h.">".$data."</h".$h.">";
}

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
	return _list('ul', $list, $attributes);
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
	return _list('ol', $list, $attributes);
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
function _list($type = 'ul', $list, $attributes = '', $depth = 0)
{
	// If an array wasn't submitted there's nothing to do...
	if ( ! is_array($list))
	{
		return $list;
	}
	
	// Set the indentation based on the depth
	$out = str_repeat(" ", $depth);
	
	// Were any attributes submitted?  If so generate a string
	if (is_array($attributes))
	{
		$atts = '';
		foreach ($attributes as $key => $val)
		{
			$atts .= ' ' . $key . '="' . $val . '"';
		}
		$attributes = $atts;
	}
	
	// Write the opening list tag
	$out .= "<".$type.$attributes.">\n";

	// Cycle through the list elements.  If an array is 
	// encountered we will recursively call _list()

	static $_last_list_item = '';
	foreach ($list as $key => $val)
	{	
		$_last_list_item = $key;

		$out .= str_repeat(" ", $depth + 2);
		$out .= "<li>";
		
		if ( ! is_array($val))
		{
			$out .= $val;
		}
		else
		{
			$out .= $_last_list_item."\n";
			$out .= _list($type, $val, '', $depth + 4);
			$out .= str_repeat(" ", $depth + 2);
		}

		$out .= "</li>\n";		
	}

	// Set the indentation for the closing tag
	$out .= str_repeat(" ", $depth);
	
	// Write the closing list tag
	$out .= "</".$type.">\n";

	return $out;
}
	
// ------------------------------------------------------------------------

/**
 * Generates HTML BR tags based on number supplied
 *
 * @access	public
 * @param	integer
 * @return	string
 */	
function br($num = 1)
{
	return str_repeat("<br />", $num);
}
	
// ------------------------------------------------------------------------

/**
 * Generates non-breaking space entities based on number supplied
 *
 * @access	public
 * @param	integer
 * @return	string
 */	
function nbs($num = 1)
{
	return str_repeat("&nbsp;", $num);
}

// ------------------------------------------------------------------------

/**
 * Generates meta tags from an array of key/values
 *
 * @access	public
 * @param	array
 * @return	string
 */	
function meta($meta = array(), $newline = "\n")
{
	$str = '';
	foreach ($meta as $key => $val)
	{
		$str .= '<meta http-equiv="'.$key.'" content="'.$val.'" />'.$newline;
	}

	return $str;
}




?>