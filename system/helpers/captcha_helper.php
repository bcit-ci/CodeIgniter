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
<<<<<<< develop
	 * @param	array	array of data for the CAPTCHA
	 * @param	string	path to create the image in
	 * @param	string	URL to the CAPTCHA image folder
	 * @param	string	server path to font
=======
	 * @param	array	$data		data for the CAPTCHA
	 * @param	string	$img_path	path to create the image in
	 * @param	string	$img_url	URL to the CAPTCHA image folder
	 * @param	string	$font_path	server path to font
>>>>>>> local
	 * @return	string
	 */
	function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
	{
		$defaults = array(
			'word'		=> '',
			'img_path'	=> '',
			'img_url'	=> '',
			'img_width'	=> '150',
			'img_height'	=> '30',
			'font_path'	=> '',
			'expiration'	=> 7200,
			'word_length'	=> 8,
			'pool'		=> '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'colors'	=> array(
				'background'	=> array(255,255,255),
				'border'	=> array(153,102,102),
				'text'		=> array(204,153,153),
				'grid'		=> array(255,182,182)
			)
		);

		foreach ($defaults as $key => $val)
		{
			if ( ! is_array($data) && empty($$key))
			{
				$$key = $val;
			}
			else
			{
				$$key = isset($data[$key]) ? $data[$key] : $val;
			}
		}

		if ($img_path === '' OR $img_url === ''
			OR ! @is_dir($img_path) OR ! is_writeable($img_path)
			OR ! extension_loaded('gd'))
		{
			return FALSE;
		}

		// -----------------------------------
		// Remove old images
		// -----------------------------------

		$now = microtime(TRUE);

		$current_dir = @opendir($img_path);
		while ($filename = @readdir($current_dir))
		{
			if (substr($filename, -4) === '.jpg' && (str_replace('.jpg', '', $filename) + $expiration) < $now)
			{
				@unlink($img_path.$filename);
			}
		}

		@closedir($current_dir);

		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------

		if (empty($word))
		{
<<<<<<< develop
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$word = '';
			for ($i = 0, $mt_rand_max = strlen($pool) - 1; $i < 8; $i++)
=======
			$word = '';
			for ($i = 0, $mt_rand_max = strlen($pool) - 1; $i < $word_length; $i++)
>>>>>>> local
			{
				$word .= $pool[mt_rand(0, $mt_rand_max)];
			}
		}
		elseif ( ! is_string($word))
		{
			$word = (string) $word;
		}

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------
		$length	= strlen($word);
<<<<<<< develop
		$angle	= ($length >= 6) ? rand(-($length-6), ($length-6)) : 0;
		$x_axis	= rand(6, (360/$length)-16);
		$y_axis = ($angle >= 0) ? rand($img_height, $img_width) : rand(6, $img_height);
=======
		$angle	= ($length >= 6) ? mt_rand(-($length-6), ($length-6)) : 0;
		$x_axis	= mt_rand(6, (360/$length)-16);
		$y_axis = ($angle >= 0) ? mt_rand($img_height, $img_width) : mt_rand(6, $img_height);
>>>>>>> local

		// Create image
		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		$im = function_exists('imagecreatetruecolor')
			? imagecreatetruecolor($img_width, $img_height)
			: imagecreate($img_width, $img_height);

		// -----------------------------------
		//  Assign colors
<<<<<<< develop
		// -----------------------------------
		$bg_color	= imagecolorallocate($im, 255, 255, 255);
		$border_color	= imagecolorallocate($im, 153, 102, 102);
		$text_color	= imagecolorallocate($im, 204, 153, 153);
		$grid_color	= imagecolorallocate($im, 255, 182, 182);
		$shadow_color	= imagecolorallocate($im, 255, 240, 240);

		//  Create the rectangle
		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);
=======
		// ----------------------------------

		is_array($colors) OR $colors = $defaults['colors'];

		foreach (array_keys($defaults['colors']) as $key)
		{
			// Check for a possible missing value
			is_array($colors[$key]) OR $colors[$key] = $defaults['colors'][$key];
			$colors[$key] = imagecolorallocate($im, $colors[$key][0], $colors[$key][1], $colors[$key][2]);
		}

		// Create the rectangle
		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $colors['background']);
>>>>>>> local

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------
		$theta		= 1;
		$thetac		= 7;
		$radius		= 16;
		$circles	= 20;
		$points		= 32;

		for ($i = 0, $cp = ($circles * $points) - 1; $i < $cp; $i++)
		{
			$theta += $thetac;
			$rad = $radius * ($i / $points);
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta += $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta)) + $y_axis;
<<<<<<< develop
			imageline($im, $x, $y, $x1, $y1, $grid_color);
=======
			imageline($im, $x, $y, $x1, $y1, $colors['grid']);
>>>>>>> local
			$theta -= $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------

		$use_font = ($font_path !== '' && file_exists($font_path) && function_exists('imagettftext'));
		if ($use_font === FALSE)
		{
			$font_size = 5;
<<<<<<< develop
			$x = rand(0, $img_width / ($length / 3));
=======
			$x = mt_rand(0, $img_width / ($length / 3));
>>>>>>> local
			$y = 0;
		}
		else
		{
			$font_size = 16;
<<<<<<< develop
			$x = rand(0, $img_width / ($length / 1.5));
=======
			$x = mt_rand(0, $img_width / ($length / 1.5));
>>>>>>> local
			$y = $font_size + 2;
		}

		for ($i = 0; $i < $length; $i++)
		{
			if ($use_font === FALSE)
			{
<<<<<<< develop
				$y = rand(0 , $img_height / 2);
				imagestring($im, $font_size, $x, $y, $word[$i], $text_color);
=======
				$y = mt_rand(0 , $img_height / 2);
				imagestring($im, $font_size, $x, $y, $word[$i], $colors['text']);
>>>>>>> local
				$x += ($font_size * 2);
			}
			else
			{
<<<<<<< develop
				$y = rand($img_height / 2, $img_height - 3);
				imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, $word[$i]);
=======
				$y = mt_rand($img_height / 2, $img_height - 3);
				imagettftext($im, $font_size, $angle, $x, $y, $colors['text'], $font_path, $word[$i]);
>>>>>>> local
				$x += $font_size;
			}
		}

		// Create the border
<<<<<<< develop
		imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $border_color);
=======
		imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $colors['border']);
>>>>>>> local

		// -----------------------------------
		//  Generate the image
		// -----------------------------------
<<<<<<< develop
		$img_name = $now.'.jpg';
		ImageJPEG($im, $img_path.$img_name);
		$img = '<img src="'.$img_url.$img_name.'" style="width: '.$img_width.'; height: '.$img_height .'; border: 0;" alt=" " />';
=======
		$img_filename = $now.'.jpg';
		ImageJPEG($im, $img_path.$img_filename);
		$img = '<img src="'.$img_url.$img_filename.'" style="width: '.$img_width.'; height: '.$img_height .'; border: 0;" alt=" " />';
>>>>>>> local
		ImageDestroy($im);

		return array('word' => $word, 'time' => $now, 'image' => $img, 'filename' => $img_filename);
	}
}

/* End of file captcha_helper.php */
/* Location: ./system/helpers/captcha_helper.php */