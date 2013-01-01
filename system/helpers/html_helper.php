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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter HTML Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/html_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('heading'))
{
	/**
	 * Heading
	 *
	 * Generates an HTML heading tag.
	 *
	 * @param	string	content
	 * @param	int	heading level
	 * @param	string
	 * @return	string
	 */
	function heading($data = '', $h = '1', $attributes = '')
	{
		return '<h'.$h._stringify_attributes($attributes).'>'.$data.'</h'.$h.'>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ul'))
{
	/**
	 * Unordered List
	 *
	 * Generates an HTML unordered list from an single or multi-dimensional array.
	 *
	 * @param	array
	 * @param	mixed
	 * @return	string
	 */
	function ul($list, $attributes = '')
	{
		return _list('ul', $list, $attributes);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ol'))
{
	/**
	 * Ordered List
	 *
	 * Generates an HTML ordered list from an single or multi-dimensional array.
	 *
	 * @param	array
	 * @param	mixed
	 * @return	string
	 */
	function ol($list, $attributes = '')
	{
		return _list('ol', $list, $attributes);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_list'))
{
	/**
	 * Generates the list
	 *
	 * Generates an HTML ordered list from an single or multi-dimensional array.
	 *
	 * @param	string
	 * @param	mixed
	 * @param	mixed
	 * @param	int
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
		$out = str_repeat(' ', $depth);

		// Write the opening list tag
		$out .= '<'.$type._stringify_attributes($attributes).">\n";

		// Cycle through the list elements.  If an array is
		// encountered we will recursively call _list()

		static $_last_list_item = '';
		foreach ($list as $key => $val)
		{
			$_last_list_item = $key;

			$out .= str_repeat(' ', $depth + 2).'<li>';

			if ( ! is_array($val))
			{
				$out .= $val;
			}
			else
			{
				$out .= $_last_list_item."\n"._list($type, $val, '', $depth + 4).str_repeat(' ', $depth + 2);
			}

			$out .= "</li>\n";
		}

		// Set the indentation for the closing tag and apply it
		return $out.str_repeat(' ', $depth).'</'.$type.">\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('br'))
{
	/**
	 * Generates HTML BR tags based on number supplied
	 *
	 * @param	int	$count	Number of times to repeat the tag
	 * @return	string
	 */
	function br($count = 1)
	{
		return str_repeat('<br />', $count);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('img'))
{
	/**
	 * Image
	 *
	 * Generates an <img /> element
	 *
	 * @param	mixed
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function img($src = '', $index_page = FALSE, $attributes = '')
	{
		if ( ! is_array($src) )
		{
			$src = array('src' => $src);
		}

		// If there is no alt attribute defined, set it to an empty string
		if ( ! isset($src['alt']))
		{
			$src['alt'] = '';
		}

		$img = '<img';

		foreach ($src as $k => $v)
		{
			if ($k === 'src' && strpos($v, '://') === FALSE)
			{
				$CI =& get_instance();

				if ($index_page === TRUE)
				{
					$img .= ' src="'.$CI->config->site_url($v).'"';
				}
				else
				{
					$img .= ' src="'.$CI->config->slash_item('base_url').$v.'"';
				}
			}
			else
			{
				$img .= ' '.$k.'="'.$v.'"';
			}
		}

		return $img._stringify_attributes($attributes).' />';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('doctype'))
{
	/**
	 * Doctype
	 *
	 * Generates a page document type declaration
	 *
	 * Examples of valid options: html5, xhtml-11, xhtml-strict, xhtml-trans,
	 * xhtml-frame, html4-strict, html4-trans, and html4-frame.
	 * All values are saved in the doctypes config file.
	 *
	 * @param	string	type	The doctype to be generated
	 * @return	string
	 */
	function doctype($type = 'xhtml1-strict')
	{
		global $_doctypes;

		if ( ! is_array($_doctypes))
		{
			if (defined('ENVIRONMENT') && is_file(APPPATH.'config/'.ENVIRONMENT.'/doctypes.php'))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/doctypes.php');
			}
			elseif (is_file(APPPATH.'config/doctypes.php'))
			{
				include(APPPATH.'config/doctypes.php');
			}

			if ( ! is_array($_doctypes))
			{
				return FALSE;
			}
		}

		return isset($_doctypes[$type]) ? $_doctypes[$type] : FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('link_tag'))
{
	/**
	 * Link
	 *
	 * Generates link to a CSS file
	 *
	 * @param	mixed	stylesheet hrefs or an array
	 * @param	string	rel
	 * @param	string	type
	 * @param	string	title
	 * @param	string	media
	 * @param	bool	should index_page be added to the css path
	 * @return	string
	 */
	function link_tag($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = FALSE)
	{
		$CI =& get_instance();
		$link = '<link ';

		if (is_array($href))
		{
			foreach ($href as $k => $v)
			{
				if ($k === 'href' && strpos($v, '://') === FALSE)
				{
					if ($index_page === TRUE)
					{
						$link .= 'href="'.$CI->config->site_url($v).'" ';
					}
					else
					{
						$link .= 'href="'.$CI->config->slash_item('base_url').$v.'" ';
					}
				}
				else
				{
					$link .= $k.'="'.$v.'" ';
				}
			}
		}
		else
		{
			if (strpos($href, '://') !== FALSE)
			{
				$link .= 'href="'.$href.'" ';
			}
			elseif ($index_page === TRUE)
			{
				$link .= 'href="'.$CI->config->site_url($href).'" ';
			}
			else
			{
				$link .= 'href="'.$CI->config->slash_item('base_url').$href.'" ';
			}

			$link .= 'rel="'.$rel.'" type="'.$type.'" ';

			if ($media !== '')
			{
				$link .= 'media="'.$media.'" ';
			}

			if ($title !== '')
			{
				$link .= 'title="'.$title.'" ';
			}
		}

		return $link."/>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('meta'))
{
	/**
	 * Generates meta tags from an array of key/values
	 *
	 * @param	array
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function meta($name = '', $content = '', $type = 'name', $newline = "\n")
	{
		// Since we allow the data to be passes as a string, a simple array
		// or a multidimensional one, we need to do a little prepping.
		if ( ! is_array($name))
		{
			$name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));
		}
		elseif (isset($name['name']))
		{
			// Turn single array into multidimensional
			$name = array($name);
		}

		$str = '';
		foreach ($name as $meta)
		{
			$type		= ( ! isset($meta['type']) OR $meta['type'] === 'name') ? 'name' : 'http-equiv';
			$name		= isset($meta['name'])					? $meta['name'] : '';
			$content	= isset($meta['content'])				? $meta['content'] : '';
			$newline	= isset($meta['newline'])				? $meta['newline'] : "\n";

			$str .= '<meta '.$type.'="'.$name.'" content="'.$content.'" />'.$newline;
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('nbs'))
{
	/**
	 * Generates non-breaking space entities based on number supplied
	 *
	 * @param	int
	 * @return	string
	 */
	function nbs($num = 1)
	{
		return str_repeat('&nbsp;', $num);
	}
}

/* End of file html_helper.php */
/* Location: ./system/helpers/html_helper.php */