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
 * CodeIgniter Memcached Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */

class CI_Cache_file extends CI_Driver {

	protected $_cache_path;
	protected $_raw_mode = FALSE; 	// Store raw data, or array with metadata

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$CI =& get_instance();
		$CI->load->helper('file');
		
		$path = $CI->config->item('cache_path');
	
		$this->_cache_path = ($path == '') ? APPPATH.'cache/' : $path;
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/FALSE on failure
	 */
	public function get($id)
	{
		if ( ! file_exists($this->_cache_path.$id))
		{
			return FALSE;
		}
		
		$data = read_file($this->_cache_path.$id);
		$data = unserialize($data);
		
		if (time() >  $data['time'] + $data['ttl'])
		{
			unlink($this->_cache_path.$id);
			return FALSE;
		}
		
		return $this->_raw_mode ? $data : $data['data'];
	}

	// ------------------------------------------------------------------------

	/**
	 * Save into cache
	 *
	 * @param 	string		unique key
	 * @param 	mixed		data to store
	 * @param 	int			length of time (in seconds) the cache is valid 
	 *						- Default is 60 seconds
	 * @return 	boolean		true on success/FALSE on failure
	 */
	public function save($id, $data, $ttl = 60)
	{		
		$contents = $this->_raw_mode ? $data : array(
				'time'		=> time(),
				'ttl'		=> $ttl,			
				'data'		=> $data
			);
		
		if (write_file($this->_cache_path.$id, serialize($contents)))
		{
			@chmod($this->_cache_path.$id, 0777);
			return TRUE;			
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		unique identifier of item in cache
	 * @return 	boolean		true on success/FALSE on failure
	 */
	public function delete($id)
	{
		return unlink($this->_cache_path.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 *
	 * @return 	boolean		FALSE on failure/true on success
	 */	
	public function clean()
	{
		return delete_files($this->_cache_path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a counter in Cache
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		return value from child method
	 */
	public function increment($id)
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

		return FALSE;
	}
	
	/**
	 * Decrement a counter in Cache
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		return value from child method
	 */
	public function decrement($id)
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

		return FALSE;
	}

	// ------------------------------------------------------------------------
	/**
	 * Cache Info
	 *
	 * Not supported by file-based caching
	 *
	 * @param 	string	user/filehits
	 * @return 	mixed 	FALSE
	 */
	public function cache_info($type = NULL)
	{
		return get_dir_file_info($this->_cache_path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		FALSE on failure, array on success.
	 */
	public function get_metadata($id)
	{
		if ( ! file_exists($this->_cache_path.$id))
		{
			return FALSE;
		}
		
		$data = read_file($this->_cache_path.$id);		
		$data = unserialize($data);
		
		if (is_array($data))
		{
			$data = $data['data'];
			$mtime = filemtime($this->_cache_path.$id);

			if ( ! isset($data['ttl']))
			{
				return FALSE;
			}

			return array(
				'expire' 	=> $mtime + $data['ttl'],
				'mtime'		=> $mtime
			);
		}
		
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 *
	 * In the file driver, check to see that the cache directory is indeed writable
	 * 
	 * @return boolean
	 */
	public function is_supported()
	{
		return is_really_writable($this->_cache_path);
	}

	// ------------------------------------------------------------------------
}
// End Class

/* End of file Cache_file.php */
/* Location: ./system/libraries/Cache/drivers/Cache_file.php */
