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
 * Database Cache Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_Cache {

	var $obj;

	/**
	 * Constructor
	 *
	 * Grabs the CI super object instance so we can access it.
	 *
	 */	
	function CI_DB_Cache()
	{
		// Assign the main CI object to $this->obj
		// and load the file helper since we use it a lot
		$this->obj =& get_instance();
		$this->obj->load->helper('file');	
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache Directory Path
	 *
	 * @access	public
	 * @param	string	the path to the cache directory
	 * @return	bool
	 */		
	function check_path($path = '')
	{
		if ($path == '')
		{
			if ($this->obj->db->cachedir == '')
			{
				return $this->obj->db->cache_off();
			}
		
			$path = $this->obj->db->cachedir;
		}
	
		// Add a trailing slash to the path if needed
		$path = preg_replace("/(.+?)\/*$/", "\\1/",  $path);
	
		if ( ! is_dir($path) OR ! is_writable($path))
		{
			if ($this->obj->db->db_debug)
			{
				return $this->obj->db->display_error('db_invalid_cache_path');
			}
			
			// If the path is wrong we'll turn off caching
			return $this->obj->db->cache_off();
		}
		
		$this->obj->db->cachedir = $path;
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Retreive a cached query
	 *
	 * The URI being requested will become the name of the cache sub-folder.
	 * An MD5 hash of the SQL statement will become the cache file name
	 *
	 * @access	public
	 * @return	string
	 */
	function read($sql)
	{
		if ( ! $this->check_path())
		{
			return $this->obj->db->cache_off();
		}
	
		$uri  = ($this->obj->uri->segment(1) == FALSE) ? 'base'  : $this->obj->uri->segment(2);
		$uri .= ($this->obj->uri->segment(2) == FALSE) ? 'index' : $this->obj->uri->segment(2);
		
		$filepath = md5($uri).'/'.md5($sql);
		
		if (FALSE === ($cachedata = read_file($this->obj->db->cachedir.$filepath)))
		{	
			return FALSE;
		}
		
		return unserialize($cachedata);			
	}	

	// --------------------------------------------------------------------

	/**
	 * Write a query to a cache file
	 *
	 * @access	public
	 * @return	bool
	 */
	function write($sql, $object)
	{
		if ( ! $this->check_path())
		{
			return $this->obj->db->cache_off();
		}

		$uri  = ($this->obj->uri->segment(1) == FALSE) ? 'base'  : $this->obj->uri->segment(2);
		$uri .= ($this->obj->uri->segment(2) == FALSE) ? 'index' : $this->obj->uri->segment(2);
		
		$dir_path = $this->obj->db->cachedir.md5($uri).'/';
		
		$filename = md5($sql);
	
		if ( ! @is_dir($dir_path))
		{
			if ( ! @mkdir($dir_path, 0777))
			{
				return FALSE;
			}
			
			@chmod($dir_path, 0777);			
		}
		
		if (write_file($dir_path.$filename, serialize($object)) === FALSE)
		{
			return FALSE;
		}
		
		@chmod($dir_path.$filename, 0777);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete cache files within a particular directory
	 *
	 * @access	public
	 * @return	bool
	 */
	function delete()
	{
		$uri  = ($this->obj->uri->segment(1) == FALSE) ? 'base'  : $this->obj->uri->segment(2);
		$uri .= ($this->obj->uri->segment(2) == FALSE) ? 'index' : $this->obj->uri->segment(2);
		
		$dir_path = $this->obj->db->cachedir.md5($uri).'/';
		
		delete_files($dir_path, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete all existing cache files
	 *
	 * @access	public
	 * @return	bool
	 */
	function delete_all()
	{
		delete_files($this->obj->db->cachedir, TRUE);
	}

}

?>