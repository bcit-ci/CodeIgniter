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
 * @param	string	filename
 * @param	mixed	the data to be downloaded
 * @param	bool	wether to try and send the actual file MIME type
 * @return	void
 */
if ( ! function_exists('force_download'))
{
	function force_download($filename = '', $data = '')
	{
		$fileinfo = array();
		//Check if content already loaded or not
		if(is_array($filename)){
			//content not available
			if(!isset($filename['path'])){
				return FALSE;
			} else if(!file_exists($filename['path'])){
				return FALSE;
			}
			if(!isset($filename['name'])){
				$filename['name'] = end(explode(DIRECTORY_SEPARATOR, $filename['path']));
			}
			$filename['size'] = filesize($filename['path']);
			$filename['type'] = "path";
			$fileinfo = $filename;
		} else {
			//content available
			if ($filename == '' OR $data == '')
			{
				return FALSE;
			}
			$fileinfo['name'] = $filename;
			$fileinfo['size'] = strlen($data);
			$fileinfo['type'] = "var";
		}
		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($fileinfo['name'], '.'))
		{
			return FALSE;
		}

		// Grab the file extension
		$x = explode('.', $fileinfo['name']);
		$extension = end($x);

		// Load the mime types
		if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/mimes.php');
		}
		elseif (is_file(APPPATH.'config/mimes.php'))
		{
			include(APPPATH.'config/mimes.php');
		}
		
		// Clean output buffer
		ob_clean();

		// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension]))
		{
			$mime = 'application/octet-stream';
		}
		else
		{
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}
		// Generate the server headers
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$fileinfo['name'].'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".$fileinfo['size']);
		}
		else
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$fileinfo['name'].'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".$fileinfo['size']);
		}

		switch($fileinfo['type']){
			case "var":
				exit($data);
				break;
			case "path":
				$chunksize = 1*(1024*1024); // how many bytes per chunk
				$buffer = '';
				$handle = fopen($fileinfo['path'], 'rb');
				if ($handle === false) {
					return false;
				}
				while (!feof($handle)) {
					$buffer = fread($handle, $chunksize);
					print $buffer;
				}
				return fclose($handle);
				break;
		}
	}
}

/* End of file download_helper.php */
/* Location: ./system/helpers/download_helper.php */