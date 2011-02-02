<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Smiley Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/smiley_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Smiley Javascript
 *
 * Returns the javascript required for the smiley insertion.  Optionally takes
 * an array of aliases to loosely couple the smiley array to the view.
 *
 * @access	public
 * @param	mixed	alias name or array of alias->field_id pairs
 * @param	string	field_id if alias name was passed in
 * @return	array
 */
if ( ! function_exists('smiley_js'))
{
	function smiley_js($alias = '', $field_id = '', $inline = TRUE)
	{
		static $do_setup = TRUE;

		$r = '';

		if ($alias != '' && ! is_array($alias))
		{
			$alias = array($alias => $field_id);
		}

		if ($do_setup === TRUE)
		{
				$do_setup = FALSE;

				$m = array();

				if (is_array($alias))
				{
					foreach($alias as $name => $id)
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
		else
		{
			if (is_array($alias))
			{
				foreach($alias as $name => $id)
				{
					$r .= 'smiley_map["'.$name.'"] = "'.$id.'";'."\n";
				}
			}
		}

		if ($inline)
		{
			return '<script type="text/javascript" charset="utf-8">/*<![CDATA[ */'.$r.'// ]]></script>';
		}
		else
		{
			return $r;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Get Clickable Smileys
 *
 * Returns an array of image tag links that can be clicked to be inserted
 * into a form field.
 *
 * @access	public
 * @param	string	the URL to the folder containing the smiley images
 * @return	array
 */
if ( ! function_exists('get_clickable_smileys'))
{
	function get_clickable_smileys($image_url, $alias = '', $smileys = NULL)
	{
		// For backward compatibility with js_insert_smiley

		if (is_array($alias))
		{
			$smileys = $alias;
		}

		if ( ! is_array($smileys))
		{
			if (FALSE === ($smileys = _get_smiley_array()))
			{
				return $smileys;
			}
		}

		// Add a trailing slash to the file path if needed
		$image_url = rtrim($image_url, '/').'/';

		$used = array();
		foreach ($smileys as $key => $val)
		{
			// Keep duplicates from being used, which can happen if the
			// mapping array contains multiple identical replacements.  For example:
			// :-) and :) might be replaced with the same image so both smileys
			// will be in the array.
			if (isset($used[$smileys[$key][0]]))
			{
				continue;
			}

			$link[] = "<a href=\"javascript:void(0);\" onclick=\"insert_smiley('".$key."', '".$alias."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\" /></a>";

			$used[$smileys[$key][0]] = TRUE;
		}

		return $link;
	}
}

// ------------------------------------------------------------------------

/**
 * Parse Smileys
 *
 * Takes a string as input and swaps any contained smileys for the actual image
 *
 * @access	public
 * @param	string	the text to be parsed
 * @param	string	the URL to the folder containing the smiley images
 * @return	string
 */
if ( ! function_exists('parse_smileys'))
{
	function parse_smileys($str = '', $image_url = '', $smileys = NULL)
	{
		if ($image_url == '')
		{
			return $str;
		}

		if ( ! is_array($smileys))
		{
			if (FALSE === ($smileys = _get_smiley_array()))
			{
				return $str;
			}
		}

		// Add a trailing slash to the file path if needed
		$image_url = preg_replace("/(.+?)\/*$/", "\\1/",  $image_url);

		foreach ($smileys as $key => $val)
		{
			$str = str_replace($key, "<img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\" />", $str);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Get Smiley Array
 *
 * Fetches the config/smiley.php file
 *
 * @access	private
 * @return	mixed
 */
if ( ! function_exists('_get_smiley_array'))
{
	function _get_smiley_array()
	{
		if ( ! file_exists(APPPATH.'config/smileys'.EXT))
		{
			return FALSE;
		}

		include(APPPATH.'config/smileys'.EXT);

		if ( ! isset($smileys) OR ! is_array($smileys))
		{
			return FALSE;
		}

		return $smileys;
	}
}

// ------------------------------------------------------------------------

/**
 * JS Insert Smiley
 *
 * Generates the javascript function needed to insert smileys into a form field
 *
 * DEPRECATED as of version 1.7.2, use smiley_js instead
 *
 * @access	public
 * @param	string	form name
 * @param	string	field name
 * @return	string
 */
if ( ! function_exists('js_insert_smiley'))
{
	function js_insert_smiley($form_name = '', $form_field = '')
	{
		return <<<EOF
<script type="text/javascript">
	function insert_smiley(smiley)
	{
		document.{$form_name}.{$form_field}.value += " " + smiley;
	}
</script>
EOF;
	}
}


/* End of file smiley_helper.php */
/* Location: ./system/helpers/smiley_helper.php */