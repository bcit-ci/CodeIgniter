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
 * CodeIgniter CAPTCHA Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/captcha_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('create_captcha'))
{
	/**
	 * Create CAPTCHA
	 *
	 * @param	array	array of data for the CAPTCHA
	 * @return	string
	 */
	function create_captcha($data = '')
	{
		$defaults = array('word' => '', 'img_width' => 150, 'img_height' => 30, 'font_path' => '', 'random_str_length' => '5', 'border' => TRUE);

		foreach ($defaults as $key => $val)
		{
			if ( ! is_array($data))
			{
				if ( ! isset($$key) OR $$key == '')
				{
					$$key = $val;
				}
			}
			else
			{
				$$key = ( ! isset($data[$key])) ? $val : $data[$key];
			}
		}



		if ( ! extension_loaded('gd'))
		{
			return FALSE;
		}

		
		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------

	   if ($word == '')
	   {
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$str = '';
			for ($i = 0; $i < $random_str_length; $i++)
			{
				$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
			}

			$word = $str;
	   }

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------

		$length	= strlen($word);
		$angle	= ($length >= 6) ? rand(-($length-6), ($length-6)) : 0;
		$x_axis	= rand(6, (360/$length)-16);
		$y_axis = ($angle >= 0 ) ? rand($img_height, $img_width) : rand(6, $img_height);

		// -----------------------------------
		// Create image
		// -----------------------------------

		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		if (function_exists('imagecreatetruecolor'))
		{
			$im = imagecreatetruecolor($img_width, $img_height);
		}
		else
		{
			$im = imagecreate($img_width, $img_height);
		}

		// -----------------------------------
		//  Assign colors
		// -----------------------------------
		
		/* RAND */
		$red = rand(50, 100);
		$green = rand(50, 100);
		$blue = rand(50, 100);

		$bg_color		= imagecolorallocate($im, 255, 255, 255);
		$border_color	= imagecolorallocate($im, $red, $green, $blue);
		$text_color		= imagecolorallocate($im, $red+30, $green+30, $blue+30);
		$grid_color		= imagecolorallocate($im, $red+60, $green+60, $blue+60);
		$shadow_color	= imagecolorallocate($im, 255, 240, 240);
		
		

		// -----------------------------------
		//  Create the rectangle
		// -----------------------------------

		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------

		$theta		= 1;
		$thetac		= 7;
		$radius		= 16;
		$circles	= 20;
		$points		= 32;

		for ($i = 0; $i < ($circles * $points) - 1; $i++)
		{
			$theta = $theta + $thetac;
			$rad = $radius * ($i / $points );
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta = $theta + $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta )) + $y_axis;
			imageline($im, $x, $y, $x1, $y1, $grid_color);
			$theta = $theta - $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------

		$use_font = ($font_path != '' AND file_exists($font_path) AND function_exists('imagettftext')) ? TRUE : FALSE;

		if ($use_font == FALSE)
		{
			$font_size = 7;
			$x = rand(1, $img_width-(($length*$font_size)*2));
			$y = 0;
		}
		else
		{
			$font_size	= 12;
			$x = rand(1, $img_width-($length*$font_size));
			$y = $font_size+2;
		}

		for ($i = 0; $i < strlen($word); $i++)
		{
			if ($use_font == FALSE)
			{
				$y = rand(1 , $img_height-($font_size*3));
				imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
				$x += ($font_size*2);
			}
			else
			{
				$y = rand($font_size , $img_height-($font_size/3));
				imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, substr($word, $i, 1));
				$x += $font_size;
			}
		}


		// -----------------------------------
		//  Create the border
		// -----------------------------------

		if ($border == TRUE)
		{
			imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);
		}

		// -----------------------------------
		//  Generate the image
		// -----------------------------------
		
		header("Content-type: image/jpeg");
		ImageJPEG($im);

		ImageDestroy($im);

		return $word;
	}
}

/* End of file captcha_helper.php */
/* Location: ./system/helpers/captcha_helper.php */