<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
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
 * CodeIgniter APC Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */

class CI_Cache_apc extends CI_Driver
{

	protected $_raw_mode = FALSE; 	// Store raw data, or array with metadata

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
		$data = apc_fetch($id);

		return $this->_raw_mode ? $data : (is_array($data) ? $data[0] : FALSE);
	}

	// ------------------------------------------------------------------------	
	
	/**
	 * Cache Save
	 *
	 * @param 	string		Unique Key
	 * @param 	mixed		Data to store
	 * @param 	int			Length of time (in seconds) to cache the data
	 *
	 * @return 	boolean		true on success/FALSE on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		return apc_store($id, $this->_raw_mode ? $data : array($data, time(), $ttl), FALSE, $ttl);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		unique identifier of the item in the cache
	 * @param 	boolean		true on success/FALSE on failure
	 */
	public function delete($id)
	{
		return apc_delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return 	boolean		FALSE on failure/true on success
	 */
	public function clean()
	{
		return apc_clear_cache('user');
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a counter in memcache
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		incremented value
	 */
	public function increment($id)
	{
		if($this->_raw_mode)
		{ 
			return apc_inc($id);
		}
		else
		{
			$value = $this->get($id);
			$metadata = $this->get_metadata($id);
	
			if(is_numeric($value))
			{
				if($this->save($id, ++$value, $metadata['ttl']))
				{
					return $value;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Decrement a counter in memcache
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		decremented value
	 */
	public function decrement($id)
	{
		if($this->_raw_mode)
		{
			return apc_dec($id);
		}
		else 
		{
			$value = $this->get($id);
			$metadata = $this->get_metadata($id);
	
			if(is_numeric($value))
			{
				if($this->save($id, --$value, $metadata['ttl']))
				{
					return $value;
				}
			}
		}
		
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param 	string		user/filehits
	 * @return 	mixed		array on success, FALSE on failure	
	 */
	 public function cache_info($type = NULL)
	 {
		 return apc_cache_info($type);
	 }

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		array on success/FALSE on failure
	 */
	public function get_metadata($id)
	{
		if($this->_raw_mode)
		{
			return array(); // we don't have any data for raw keys
		}

		$stored = apc_fetch($id);

		if (count($stored) !== 3)
		{
			return FALSE;
		}

		list($data, $time, $ttl) = $stored;

		return array(
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported()
	 *
	 * Check to see if APC is available on this system, bail if it isn't.
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('apc') OR ini_get('apc.enabled') != "1")
		{
			log_message('error', 'The APC PHP extension must be loaded to use APC Cache.');
			return FALSE;
		}
		
		return TRUE;
	}

	// ------------------------------------------------------------------------

	
}
// End Class

/* End of file Cache_apc.php */
/* Location: ./system/libraries/Cache/drivers/Cache_apc.php */
