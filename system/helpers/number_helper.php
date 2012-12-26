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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Number Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/number_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('byte_format'))
{
	/**
	 * Formats a numbers as bytes, based on size, and adds the appropriate suffix
	 *
	 * @param	mixed	will be cast as int
	 * @param	int
	 * @return	string
	 */
	function byte_format($num, $precision = 1)
	{
		$CI =& get_instance();
		$CI->lang->load('number');
		
		$kb = 1024;			// Kilobyte
		$mb = 1024 * $kb;	// Megabyte
		$gb = 1024 * $mb;	// Gigabyte
		$tb = 1024 * $gb;	// Terabyte
		$pb = 1024 * $tb;	// Pettabyte
		$eb = 1024 * $pb;	// Exabyte
		$zb = 1024 * $eb;	// Zettabyte
		$yb = 1024 * $zb;	// Yottabyte
		$bb = 1024 * $yb;	// Brontobyte

		if ($num >= $bb)
		{
			$num = round($num / $bb, $precision);
			$unit = $CI->lang->line('brontobyte_abbr');
		}
		elseif ($num >= $yb)
		{
			$num = round($num / $yb, $precision);
			$unit = $CI->lang->line('yottabyte_abbr');
		}
		elseif ($num >= $zb)
		{
			$num = round($num / $zb, $precision);
			$unit = $CI->lang->line('zettabyte_abbr');
		}
		elseif ($num >= $eb)
		{
			$num = round($num / $eb, $precision);
			$unit = $CI->lang->line('exabyte_abbr');
		}
		elseif ($num >= $pb)
		{
			$num = round($num / $pb, $precision);
			$unit = $CI->lang->line('pettabyte_abbr');
		}
		elseif ($num >= $tb)
		{
			$num = round($num / $tb, $precision);
			$unit = $CI->lang->line('terabyte_abbr');
		}
		elseif ($num >= $gb)
		{
			$num = round($num / $gb, $precision);
			$unit = $CI->lang->line('gigabyte_abbr');
		}
		elseif ($num >= $mb)
		{
			$num = round($num / $mb, $precision);
			$unit = $CI->lang->line('megabyte_abbr');
		}
		elseif ($num >= $kb)
		{
			$num = round($num / $kb, $precision);
			$unit = $CI->lang->line('kilobyte_abbr');
		}
		else
		{
			$unit = $CI->lang->line('bytes');
			return number_format($num).' '.$unit;
		}

		return number_format($num, $precision).' '.$unit;
	}
}

/* End of file number_helper.php */
/* Location: ./system/helpers/number_helper.php */
