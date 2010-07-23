<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Security Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/security_helper.html
 */

// ------------------------------------------------------------------------

/**
 * XSS Filtering
 *
 * @access	public
 * @param	string
 * @param	bool	whether or not the content is an image file
 * @return	string
 */	
if ( ! function_exists('xss_clean'))
{
	function xss_clean($str, $is_image = FALSE)
	{
		$CI =& get_instance();
		return $CI->security->xss_clean($str, $is_image);
	}
}

// ------------------------------------------------------------------------

/**
 * Sanitize Filename
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('sanitize_filename'))
{
	function sanitize_filename($filename)
	{
		$CI =& get_instance();
		return $CI->security->sanitize_filename($filename);
	}
}

// --------------------------------------------------------------------

/**
 * Hash encode a string
 *
 * This is simply an alias for do_hash()
 * dohash() is now deprecated
 */
if ( ! function_exists('dohash'))
{	
	function dohash($str, $type = 'sha1')
	{
		return do_hash($str, $type);
	}
}

// --------------------------------------------------------------------

/**
 * Hash encode a string
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('do_hash'))
{	
	function do_hash($str, $type = 'sha1')
	{
		if ($type == 'sha1')
		{
			if ( ! function_exists('sha1'))
			{
				if ( ! function_exists('mhash'))
				{	
					require_once(BASEPATH.'libraries/Sha1'.EXT);
					$SH = new CI_SHA;
					return $SH->generate($str);
				}
				else
				{
					return bin2hex(mhash(MHASH_SHA1, $str));
				}
			}
			else
			{
				return sha1($str);
			}	
		}
		else
		{
			return md5($str);
		}
	}
}
	
// ------------------------------------------------------------------------

/**
 * Strip Image Tags
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('strip_image_tags'))
{
	function strip_image_tags($str)
	{
		$str = preg_replace("#<img\s+.*?src\s*=\s*[\"'](.+?)[\"'].*?\>#", "\\1", $str);
		$str = preg_replace("#<img\s+.*?src\s*=\s*(.+?).*?\>#", "\\1", $str);
			
		return $str;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Convert PHP tags to entities
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('encode_php_tags'))
{
	function encode_php_tags($str)
	{
		return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}
}


/* End of file security_helper.php */
/* Location: ./system/helpers/security_helper.php */