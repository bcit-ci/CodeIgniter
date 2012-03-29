<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
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
 * CodeIgniter Memcache Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */

class CI_Cache_memcache extends CI_Driver
{

	private $_memcache; 		// Holds the memcache object
	
	protected $_raw_mode = FALSE; 	// Store raw data, or array with metadata
	protected $_memcache_conf 	= array(
					'default' => array(
						'default_host'       => '127.0.0.1', 
						'default_port'       => 11211,
						'default_persistent' => FALSE,
						'default_weight'     => 1
					)
				);
	
	// ------------------------------------------------------------------------	
	
	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/FALSE on failure
	 */
	public function get($id)
	{
		$data = $this->_memcache->get($id);
		
		return $this->_raw_mode ? $data : (is_array($data) ? $data[0] : FALSE);
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Save
	 *
	 * @param 	string		unique identifier
	 * @param 	mixed		data being cached
	 * @param 	int			time to live
	 * @return 	boolean 	true on success, FALSE on failure
	 */
	public function save($id, $data, $ttl = 60)
	{	
		if (get_class($this->_memcached) == 'Memcached')
		{
			return $this->_memcache->set($id, $this->_raw_mode ? $data : array($data, time(), $ttl), FALSE, $ttl);
		}
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		key to be deleted.
	 * @return 	boolean 	true on success, FALSE on failure
	 */
	public function delete($id)
	{
		return $this->_memcache->delete($id);
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Clean the Cache
	 *
	 * @return 	boolean		FALSE on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcache->flush();
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
		$value = $this->get($id);
		
		if(is_numeric($value))
		{
			if($this->_raw_mode)
			{
				return $this->_memcache->increment($id);
			}
			else
			{
				$metadata = $this->get_metadata($id);
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
		$value = $this->get($id);
		
		if(is_numeric($value))
		{
			if($this->_raw_mode)
			{
				return $this->_memcache->decrement($id);
			}
			else
			{
				$metadata = $this->get_metadata($id);
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
	 * @param 	null		type disabled
	 * @return 	mixed 		array on success, FALSE on failure
	 */
	public function cache_info($type = NULL)
	{
		return $this->_memcache->getStats();
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
		if($this->_raw_mode)
		{
			return array(); // we don't have any data for raw keys
		}
		
		$stored = $this->_memcache->get($id, MEMCACHE_COMPRESSED);
		
		if (count($stored) !== 3)
		{
			return FALSE;
		}
		
		list ($value, $time, $ttl) = $stored;
		
		return array(
			'expire' => $time + $ttl, 
			'mtime' => $time, 
			'data' => $data
		);
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Setup memcache.
	 */
	private function _setup_memcache()
	{
		// Try to load memcache server info from the config file.
		$CI = & get_instance();

		if ($CI->config->load('memcache', TRUE, TRUE))
		{
			if (is_array($CI->config->config['memcache']))
			{
				$this->_memcache_conf = NULL;
				
				foreach ($CI->config->config['memcache'] as $name => $conf)
				{
					$this->_memcache_conf[$name] = $conf;
				}
			}
		}

		if (class_exists('Memcache'))
		{
			$this->_memcache = new Memcache();
		}
		else
		{
			log_message('error', 'Failed to create object for Memcache Cache; extension not loaded?');

			return FALSE;
		}
		
		foreach ($this->_memcache_conf as $name => $cache_server)
		{
			if (! array_key_exists('hostname', $cache_server))
			{
				$cache_server['hostname'] = $this->_memcache_conf['default']['default_host'];
			}
			
			if (! array_key_exists('port', $cache_server))
			{
				$cache_server['port'] = $this->_memcache_conf['default']['default_port'];
			}
			
			if (! array_key_exists('persistent', $cache_server))
			{
				$cache_server['persistent'] = $this->_memcache_conf['default']['default_persistent'];
			}

			if (! array_key_exists('weight', $cache_server))
			{
				$cache_server['weight'] = $this->_memcache_conf['default']['default_weight'];
			}
			
			$this->_memcache->addServer(
				$cache_server['hostname'],
				$cache_server['port'],
				$cache_server['persistent'],
				$cache_server['weight']
			);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Is supported
	 *
	 * Returns FALSE if memcache is not supported on the system.
	 * If it is, we setup the memcache object & return TRUE
	 */
	public function is_supported()
	{
		if (! extension_loaded('memcache'))
		{
			log_message('error', 'The memcache Extension must be loaded to use memcache Cache.');
			
			return FALSE;
		}
		
		$this->_setup_memcache();
		return TRUE;
	}

	// ------------------------------------------------------------------------


}
// End Class

/* End of file Cache_memcache.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcache.php */
