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
 * CodeIgniter Directory Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/directory_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Create a Directory Map
 *
 * Reads the specified directory and builds an array
 * representation of it.  Sub-folders contained with the
 * directory will be mapped as well.
 *
 * @access	public
 * @param	string	path to source
 * @param	bool	whether to limit the result to the top level only
 * @return	array
 */	
if ( ! function_exists('directory_map'))
{
	function directory_map($source_dir, $top_level_only = FALSE, $hidden = FALSE)
	{	
		if ($fp = @opendir($source_dir))
		{
			$source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;		
			$filedata = array();
			
			while (FALSE !== ($file = readdir($fp)))
			{
				if (($hidden == FALSE && strncmp($file, '.', 1) == 0) OR ($file == '.' OR $file == '..'))
				{
					continue;
				}
				
				if ($top_level_only == FALSE && @is_dir($source_dir.$file))
				{
					$temp_array = array();
				
					$temp_array = directory_map($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, $hidden);
				
					$filedata[$file] = $temp_array;
				}
				else
				{
					$filedata[] = $file;
				}
			}
			
			closedir($fp);
			return $filedata;
		}
		else
		{
			return FALSE;
		}
	}
}


/* End of file directory_helper.php */
/* Location: ./system/helpers/directory_helper.php */