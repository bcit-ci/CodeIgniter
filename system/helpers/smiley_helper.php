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
 * CodeIgniter Smiley Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/smiley_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('smiley_js'))
{
	/**
	 * Smiley Javascript
	 *
	 * Returns the javascript required for the smiley insertion.  Optionally takes
	 * an array of aliases to loosely couple the smiley array to the view.
	 *
	 * @param	mixed	alias name or array of alias->field_id pairs
	 * @param	string	field_id if alias name was passed in
	 * @param	bool
	 * @return	array
	 */
	function smiley_js($alias = '', $field_id = '', $inline = TRUE)
	{
		static $do_setup = TRUE;
		$r = '';

		if ($alias !== '' && ! is_array($alias))
		{
			$alias = array($alias => $field_id);
		}

		if ($do_setup === TRUE)
		{
			$do_setup = FALSE;
			$m = array();

			if (is_array($alias))
			{
				foreach ($alias as $name => $id)
				{
					$m[] = '"'.$name.'" : "'.$id.'"';
				}
			}

			$m = '{'.implode(',', $m).'}';

			$r .= <<<EOF
			var smiley_map = {$m};

			function insert_smiley(smiley, field_id) {
				var el = document.getElementById(field_id), newStart;

				if ( ! el && smiley_map[field_id]) {
					el = document.getElementById(smiley_map[field_id]);

					if ( ! el)
						return false;
				}

				el.focus();
				smiley = " " + smiley;

				if ('selectionStart' in el) {
					newStart = el.selectionStart + smiley.length;

					el.value = el.value.substr(0, el.selectionStart) +
									smiley +
									el.value.substr(el.selectionEnd, el.value.length);
					el.setSelectionRange(newStart, newStart);
				}
				else if (document.selection) {
					document.selection.createRange().text = smiley;
				}
			}
EOF;
		}
		elseif (is_array($alias))
		{
			foreach ($alias as $name => $id)
			{
				$r .= 'smiley_map["'.$name.'"] = "'.$id."\";\n";
			}
		}

		return ($inline) ? '<script type="text/javascript" charset="utf-8">/*<![CDATA[ */'.$r.'// ]]></script>' : $r;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_clickable_smileys'))
{
	/**
	 * Get Clickable Smileys
	 *
	 * Returns an array of image tag links that can be clicked to be inserted
	 * into a form field.
	 *
	 * @param	string	the URL to the folder containing the smiley images
	 * @param	array
	 * @return	array
	 */
	function get_clickable_smileys($image_url, $alias = '')
	{
		// For backward compatibility with js_insert_smiley
		if (is_array($alias))
		{
			$smileys = $alias;
		}
		elseif (FALSE === ($smileys = _get_smiley_array()))
		{
			return FALSE;
		}

		// Add a trailing slash to the file path if needed
		$image_url = rtrim($image_url, '/').'/';

		$used = array();
		foreach ($smileys as $key => $val)
		{
			// Keep duplicates from being used, which can happen if the
			// mapping array contains multiple identical replacements. For example:
			// :-) and :) might be replaced with the same image so both smileys
			// will be in the array.
			if (isset($used[$smileys[$key][0]]))
			{
				continue;
			}

			$link[] = '<a href="javascript:void(0);" onclick="insert_smiley(\''.$key.'\', \''.$alias.'\')"><img src="'.$image_url.$smileys[$key][0].'" alt="'.$smileys[$key][3].'" style="width: '.$smileys[$key][1].'; height: '.$smileys[$key][2].'; border: 0;" /></a>';
			$used[$smileys[$key][0]] = TRUE;
		}

		return $link;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('parse_smileys'))
{
	/**
	 * Parse Smileys
	 *
	 * Takes a string as input and swaps any contained smileys for the actual image
	 *
	 * @param	string	the text to be parsed
	 * @param	string	the URL to the folder containing the smiley images
	 * @param	array
	 * @return	string
	 */
	function parse_smileys($str = '', $image_url = '', $smileys = NULL)
	{
		if ($image_url === '' OR ( ! is_array($smileys) && FALSE === ($smileys = _get_smiley_array())))
		{
			return $str;
		}

		// Add a trailing slash to the file path if needed
		$image_url = rtrim($image_url, '/').'/';

		foreach ($smileys as $key => $val)
		{
			$str = str_replace($key, '<img src="'.$image_url.$smileys[$key][0].'" alt="'.$smileys[$key][3].'" style="width: '.$smileys[$key][1].'; height: '.$smileys[$key][2].'; border: 0;" />', $str);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_get_smiley_array'))
{
	/**
	 * Get Smiley Array
	 *
	 * Fetches the config/smiley.php file
	 *
	 * @return	mixed
	 */
	function _get_smiley_array()
	{
		if (defined('ENVIRONMENT') && file_exists(APPPATH.'config/'.ENVIRONMENT.'/smileys.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/smileys.php');
		}
		elseif (file_exists(APPPATH.'config/smileys.php'))
		{
			include(APPPATH.'config/smileys.php');
		}

		return (isset($smileys) && is_array($smileys)) ? $smileys : FALSE;
	}
}

/* End of file smiley_helper.php */
/* Location: ./system/helpers/smiley_helper.php */