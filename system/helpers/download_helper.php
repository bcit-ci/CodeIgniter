<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

/**
 * CodeIgniter Download Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/download_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Force Download
 *
 * Generates headers that force a download to happen
 *
 * @access	public
 * @param	string	filename
 * @param	mixed	the data to be downloaded
 * @param	bool	wether to try and send the actual file MIME type
 * @param	bool	wether to make the download android compatible
 * @return	void
 */
if ( ! function_exists('force_download'))
{
	function force_download($filename = '', $data = '', $set_mime = FALSE, $check_android = FALSE)
	{
		if ($filename == '' OR $data == '')
		{
			return FALSE;
		}
		
		// Set the default MIME type to send
		$mime = 'application/octet-stream';

		if ($set_mime === TRUE)
		{
			/* If we're going to detect the MIME type,
			 * we'll need a file extension.
			 */
			if (FALSE === strpos($filename, '.'))
			{
				return FALSE;
			}

			$extension = explode('.', $filename);
			$extension = end($extension);

			// Load the mime types
			if (defined('ENVIRONMENT') && is_file(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/mimes.php');
			}
			elseif (is_file(APPPATH.'config/mimes.php'))
			{
				include(APPPATH.'config/mimes.php');
			}

			// Only change the default MIME if we can find one
			if (isset($mimes[$extension]))
			{
				$mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
			}
		}
		
		//Check for android
		if($check_android === TRUE) {
			//Initialize variables
			$is_mobile = FALSE;
			$mobile = 'Unknown';
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				$agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
			
			//If the user agent is set, check for android
			if( ! is_null($agent)) 
			{
				if (defined('ENVIRONMENT') && is_file(APPPATH.'config/'.ENVIRONMENT.'/user_agents.php'))
				{
					include(APPPATH.'config/'.ENVIRONMENT.'/user_agents.php');
				}
				elseif (is_file(APPPATH.'config/user_agents.php'))
				{
					include(APPPATH.'config/user_agents.php');
				}
				
				if (is_array($mobiles) AND count($mobiles) > 0)
				{
					foreach ($mobiles as $key => $val)
					{
						if (FALSE !== (strpos(strtolower($agent), $key)))
						{
							$is_mobile = TRUE;
							$mobile = $val;
						}
					}
				}
				
				$android = FALSE;
				if($is_mobile === TRUE) 
				{
					//Check for android
					$android = array_key_exists('android', $mobiles) AND $mobile === $mobiles[$key];
				}
				
				//Uppercase the extention
				if($android === TRUE) 
				{
					$exploded_filename = explode('.', $filename);
					$extention = strtoupper($exploded_filename[(count($exploded_filename) - 1)]);
					$filename = substr($filename, 0, -strlen($extention));
					$filename .= $extention;
				}
			}
		}

		// Generate the server headers
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.strlen($data));

		// Internet Explorer-specific headers
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
		{
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Pragma: no-cache');
		}

		exit($data);
	}
}

/* End of file download_helper.php */
/* Location: ./system/helpers/download_helper.php */
