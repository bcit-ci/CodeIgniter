<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2011 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter XCache Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		Slawomir Jasinski
 * @link		http://www.spidersoft.com.au/projects/xcache-driver-codeigniter/
 */

class CI_Cache_xcache extends CI_Driver {

	/**
	 * Get 
	 *
	 * Look for a value in the cache.  If it exists, return the data 
	 * if not, return FALSE
	 *
	 * @param 	string	
	 * @return 	mixed		value that is stored/FALSE on failure
	 */
	public function get($id)
	{
		$data = xcache_get($id);

		return (is_array($data)) ? empty($data) : FALSE;
	}

	// ------------------------------------------------------------------------	
	
	/**
	 * Cache Save
	 *
	 * @param 	string		Unique Key
	 * @param 	mixed		Data to store
	 * @param 	int			Length of time (in seconds) to cache the data
	 *
	 * @return 	boolean		true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		return xcache_set($id, $data, $ttl);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		unique identifier of the item in the cache
	 * @param 	boolean		true on success/false on failure
	 */
	public function delete($id)
	{
		return xcache_unset($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported()
	 *
	 * Check to see if XCache is available on this system, bail if it isn't.
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('xcache') OR ! function_exists('xcache_get'))
		{
			log_message('error', 'The XCache PHP extension must be loaded to use XCache.');
			return FALSE;
		}
		
		return TRUE;
	}

	// ------------------------------------------------------------------------

	
}
// End Class

/* End of file Cache_xcache.php */
/* Location: ./system/libraries/Cache/drivers/Cache_xcache.php */