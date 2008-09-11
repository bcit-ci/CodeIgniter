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
 * CodeIgniter Typography Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/typography_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Convert newlines to HTML line breaks except within PRE tags
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('nl2br_except_pre'))
{
	function nl2br_except_pre($str)
	{
		$ex = explode("pre>",$str);
		$ct = count($ex);
	
		$newstr = "";
		for ($i = 0; $i < $ct; $i++)
		{
			if (($i % 2) == 0)
			{
				$newstr .= nl2br($ex[$i]);
			}
			else
			{
				$newstr .= $ex[$i];
			}
		
			if ($ct - 1 != $i)
				$newstr .= "pre>";
		}
	
		return $newstr;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Auto Typography Wrapper Function
 *
 *
 * @access	public
 * @param	string
 * @param	bool	whether to allow javascript event handlers
 * @param	bool	whether to reduce multiple instances of double newlines to two
 * @return	string
 */
if ( ! function_exists('auto_typography'))
{
	function auto_typography($str, $allow_event_handlers = FALSE, $reduce_empty_lines = FALSE)
	{
		$CI =& get_instance();
	
		$CI->load->library('typography');
		
		$CI->typography->allow_js_event_handlers($allow_event_handlers);
		$CI->typography->reduce_empty_lines($reduce_empty_lines);
		
		return $CI->typography->convert($str);
	}
}

/* End of file typography_helper.php */
/* Location: ./system/helpers/typography_helper.php */