<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Code Igniter File Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/file_helpers.html
 */

// ------------------------------------------------------------------------

/**
 * Read File
 *
 * Opens the file specfied in the path and returns it as a string.
 *
 * @access	public
 * @param	string	path to file
 * @return	string
 */	
function read_file($file)
{
	if ( ! file_exists($file))
	{
		return FALSE;
	}

	if ( ! $fp = @fopen($file, 'rb'))
	{
		return FALSE;
	}
		
	flock($fp, LOCK_SH);
	
	$data = '';
	if (filesize($file) > 0) 
	{
		$data = fread($fp, filesize($file)); 
	}

	flock($fp, LOCK_UN);
	fclose($fp); 

	return $data;
}
	
// ------------------------------------------------------------------------

/**
 * Write File
 *
 * Writes data to the file specified in the path. 
 * Creats a new file if non-existant.
 *
 * @access	public
 * @param	string	path to file
 * @param	string	file data
 * @return	bool
 */	
function write_file($path, $data, $mode = 'wb')
{
	if ( ! $fp = @fopen($path, $mode))
	{
		return FALSE;
	}
		
	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);	

	return TRUE;
}
	
// ------------------------------------------------------------------------

/**
 * Delete Files
 *
 * Deletes all files contained in the supplied directory path.
 * Files must be writable or owned by the system in order to be deleted.
 * If the second parameter is set to TRUE, any direcotries contained
 * within the supplied base directory will be nuked as well.
 *
 * @access	public
 * @param	string	path to file
 * @param	bool	whether to delete any directories found in the path
 * @return	bool
 */	
function delete_files($path, $del_dir = FALSE)
{	
	// Trim the trailing slahs
	$path = preg_replace("|^(.+?)/*$|", "\\1", $path);
			
	if ( ! $current_dir = @opendir($path))
		return;
	
	while(FALSE !== ($filename = @readdir($current_dir)))
	{ 
		if ($filename != "." and $filename != "..")
		{
			if (is_dir($path.'/'.$filename))
			{
				delete_files($path.'/'.$filename, $del_dir);
			}
			else
			{
				unlink($path.'/'.$filename);
			}
		}
	}
	@closedir($current_dir);
	
	if ($del_dir == TRUE)
	{
		@rmdir($path);
	}
}


?>