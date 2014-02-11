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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

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

if ( ! function_exists('force_download'))
{
	/**
	 * Force Download
	 *
	 * Generates headers that force a download to happen
	 *
	 * @param	string	filename
	 * @param	mixed	the data to be downloaded
	 * @param	bool	whether to try and send the actual file MIME type
	 * @return	void
	 */
	function force_download($filename = '', $data = '', $set_mime = FALSE)
	{
		if ($filename === '' OR $data === '')
		{
			return FALSE;
		}
		elseif ($data === NULL)
		{
			if (@is_file($filename) && ($filesize = @filesize($filename)) !== FALSE)
			{
				$filepath = $filename;
				$filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
				$filename = end($filename);
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			$filesize = strlen($data);
		}

		// Set the default MIME type to send
		$mime = 'application/octet-stream';

		$x = explode('.', $filename);
		$extension = end($x);

		if ($set_mime === TRUE)
		{
			if (count($x) === 1 OR $extension === '')
			{
				/* If we're going to detect the MIME type,
				 * we'll need a file extension.
				 */
				return FALSE;
			}

			// Load the mime types
			$mimes =& get_mimes();

			// Only change the default MIME if we can find one
			if (isset($mimes[$extension]))
			{
				$mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
			}
		}

		/* It was reported that browsers on Android 2.1 (and possibly older as well)
		 * need to have the filename extension upper-cased in order to be able to
		 * download it.
		 *
		 * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
		 */
		if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT']))
		{
			$x[count($x) - 1] = strtoupper($extension);
			$filename = implode('.', $x);
		}

		if ($data === NULL && ($fp = @fopen($filepath, 'rb')) === FALSE)
		{
			return FALSE;
		}

		// Clean output buffer
		if (ob_get_level() !== 0 && @ob_end_clean() === FALSE)
		{
			@ob_clean();
		}

		// Generate the server headers
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.$filesize);

		// Internet Explorer-specific headers
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
		{
			header('Cache-Control: no-cache, no-store, must-revalidate');
		}

		header('Pragma: no-cache');

		// If we have raw data - just dump it
		if ($data !== NULL)
		{
			exit($data);
		}

		// Flush 1MB chunks of data
		while ( ! feof($fp) && ($data = fread($fp, 1048576)) !== FALSE)
		{
			echo $data;
		}

		fclose($fp);
		exit;
	}
}

/* End of file download_helper.php */
/* Location: ./system/helpers/download_helper.php */