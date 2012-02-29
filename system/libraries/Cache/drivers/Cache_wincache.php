<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		Mike Murkovic
 * @copyright
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Wincache Caching Class
 *
 * Read more about Wincache functions here:
 * http://www.php.net/manual/en/ref.wincache.php
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		Mike Murkovic
 * @link		
 */

class CI_Cache_wincache extends CI_Driver {

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
		if ($data = wincache_ucache_get($id))
        {
            return $data;
        }
        return FALSE;
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
		return wincache_ucache_set($id, $data, $ttl);
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
		return wincache_ucache_delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return 	boolean		false on failure/true on success
	 */
	public function clean()
	{
		return wincache_ucache_clear();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @return 	mixed		array on success, false on failure
	 */
	 public function cache_info()
	 {
		 return wincache_ucache_info(true);
	 }

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		array on success/false on failure
	 */
	public function get_metadata($id)
	{
        if ($stored = wincache_ucache_info(false, $id))
        {
            $age = $stored['ucache_entries'][1]['age_seconds'];
            $ttl = $stored['ucache_entries'][1]['ttl_seconds'];
            $hitcount = $stored['ucache_entries'][1]['hitcount'];

            return array(
                'expire'    => $ttl - $age,
                'hitcount'  => $hitcount,
                'age'       => $age,
                'ttl'       => $ttl
            );
        }
        return false;
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported()
	 *
	 * Check to see if WinCache is available on this system, bail if it isn't.
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('wincache') )
		{
            log_message('error', 'The Wincache PHP extension must be loaded to use Wincache Cache.');
            return FALSE;
		}
		
		return TRUE;
	}

	// ------------------------------------------------------------------------

	
}
// End Class

/* End of file Cache_wincache.php */
/* Location: ./system/libraries/Cache/drivers/Cache_wincache.php */
