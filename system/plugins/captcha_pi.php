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

/*
Instructions:

Load the plugin using:

 	$this->load->plugin('captcha');

Once loaded you can generate a captcha like this:
	
	$vals = array(
					'word'		 => 'Random word',
					'img_path'	 => './captcha/',
					'img_url'	 => 'http://example.com/captcha/',
					'font_path'	 => './system/fonts/texb.ttf',
					'img_width'	 => '150',
					'img_height' => 30,
					'expiration' => 7200
				);
	
	$cap = create_captcha($vals);
	echo $cap['image'];
	

NOTES:
	
	The captcha function requires the GD image library.
	
	Only the img_path and img_url are required.
	
	If a "word" is not supplied, the function will generate a random
	ASCII string.  You might put together your own word library that
	you can draw randomly from.
	
	If you do not specify a path to a TRUE TYPE font, the native ugly GD
	font will be used.
	
	The "captcha" folder must be writable (666, or 777)
	
	The "expiration" (in seconds) signifies how long an image will
	remain in the captcha folder before it will be deleted.  The default
	is two hours.

RETURNED DATA

The create_captcha() function returns an associative array with this data:

  [array]
  (
	'image' => IMAGE TAG
	'time'	=> TIMESTAMP (in microtime)
	'word'	=> CAPTCHA WORD
  )

The "image" is the actual image tag:
<img src="http://example.com/captcha/12345.jpg" width="140" height="50" />

The "time" is the micro timestamp used as the image name without the file
extension.  It will be a number like this:  1139612155.3422

The "word" is the word that appears in the captcha image, which if not
supplied to the function, will be a random string.


ADDING A DATABASE

In order for the captcha function to prevent someone from posting, you will need
to add the information returned from create_captcha() function to your database.
Then, when the data from the form is submitted by the user you will need to verify
that the data exists in the database and has not expired.

Here is a table prototype:

	CREATE TABLE captcha (
	 captcha_id bigint(13) unsigned NOT NULL auto_increment,
	 captcha_time int(10) unsigned NOT NULL,
	 ip_address varchar(16) default '0' NOT NULL,
	 word varchar(20) NOT NULL,
	 PRIMARY KEY `captcha_id` (`captcha_id`),
	 KEY `word` (`word`)
	)


Here is an example of usage with a DB.

On the page where the captcha will be shown you'll have something like this:

	$this->load->plugin('captcha');
	$vals = array(
					'img_path'	 => './captcha/',
					'img_url'	 => 'http://example.com/captcha/'
				);
	
	$cap = create_captcha($vals);

	$data = array(
					'captcha_id'	=> '',
					'captcha_time'	=> $cap['time'],
					'ip_address'	=> $this->input->ip_address(),
					'word'			=> $cap['word']
				);

	$query = $this->db->insert_string('captcha', $data);
	$this->db->query($query);
		
	echo 'Submit the word you see below:';
	echo $cap['image'];
	echo '<input type="text" name="captcha" value="" />';


Then, on the page that accepts the submission you'll have something like this:

	// First, delete old captchas
	$expiration = time()-7200; // Two hour limit
	$DB->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);		

	// Then see if a captcha exists:
	$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND date > ?";
	$binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
	$query = $this->db->query($sql, $binds);
	$row = $query->row();

	if ($row->count == 0)
	{
		echo "You must submit the word that appears in the image";
	}

*/


	
/**
|==========================================================
| Create Captcha
|==========================================================
|
*/
function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
{		
	$defaults = array('word' => '', 'img_path' => '', 'img_url' => '', 'img_width' => '150', 'img_height' => '30', 'font_path' => '', 'expiration' => 7200);		
	
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
	
	if ($img_path == '' OR $img_url == '')
	{
		return FALSE;
	}

	if ( ! @is_dir($img_path))
	{
		return FALSE;
	}
	
	if ( ! is_really_writable($img_path))
	{
		return FALSE;
	}
			
	if ( ! extension_loaded('gd'))
	{
		return FALSE;
	}		
	
	// -----------------------------------
	// Remove old images	
	// -----------------------------------
			
	list($usec, $sec) = explode(" ", microtime());
	$now = ((float)$usec + (float)$sec);
			
	$current_dir = @opendir($img_path);
	
	while($filename = @readdir($current_dir))
	{
		if ($filename != "." and $filename != ".." and $filename != "index.html")
		{
			$name = str_replace(".jpg", "", $filename);
		
			if (($name + $expiration) < $now)
			{
				@unlink($img_path.$filename);
			}
		}
	}
	
	@closedir($current_dir);

	// -----------------------------------
	// Do we have a "word" yet?
	// -----------------------------------
	
   if ($word == '')
   {
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$str = '';
		for ($i = 0; $i < 8; $i++)
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
	
	$bg_color		= imagecolorallocate ($im, 255, 255, 255);
	$border_color	= imagecolorallocate ($im, 153, 102, 102);
	$text_color		= imagecolorallocate ($im, 204, 153, 153);
	$grid_color		= imagecolorallocate($im, 255, 182, 182);
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
		$font_size = 5;
		$x = rand(0, $img_width/($length/3));
		$y = 0;
	}
	else
	{
		$font_size	= 16;
		$x = rand(0, $img_width/($length/1.5));
		$y = $font_size+2;
	}

	for ($i = 0; $i < strlen($word); $i++)
	{
		if ($use_font == FALSE)
		{
			$y = rand(0 , $img_height/2);
			imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
			$x += ($font_size*2);
		}
		else
		{		
			$y = rand($img_height/2, $img_height-3);
			imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, substr($word, $i, 1));
			$x += $font_size;
		}
	}
	

	// -----------------------------------
	//  Create the border
	// -----------------------------------

	imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);		

	// -----------------------------------
	//  Generate the image
	// -----------------------------------
	
	$img_name = $now.'.jpg';

	ImageJPEG($im, $img_path.$img_name);
	
	$img = "<img src=\"$img_url$img_name\" width=\"$img_width\" height=\"$img_height\" style=\"border:0;\" alt=\" \" />";
	
	ImageDestroy($im);
		
	return array('word' => $word, 'time' => $now, 'image' => $img);
}


/* End of file captcha_pi.php */
/* Location: ./system/plugins/captcha_pi.php */