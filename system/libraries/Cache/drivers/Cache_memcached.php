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

class CI_Cache_memcached extends CI_Driver {

	private $_memcached;	// Holds the memcached object
	
	protected $_raw_mode = FALSE; 	// Store raw data, or array with metadata
	protected $_memcached_conf 	= array(
					'default' => array(
						'default_host'		=> '127.0.0.1',
						'default_port'		=> 11211,
						'default_weight'	=> 1
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
		$data = $this->_memcached->get($id);
		
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
			return $this->_memcached->set($id, $this->_raw_mode ? $data : array($data, time(), $ttl), FALSE, $ttl);
		}
		
		return FALSE;
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
		return $this->_memcached->delete($id);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Clean the Cache
	 *
	 * @return 	boolean		FALSE on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcached->flush();
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Increment a counter in memcached
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		return value from child method
	 */
	public function increment($id)
	{
		$value = $this->get($id);
		
		if(is_numeric($value))
		{
			if($this->_raw_mode)
			{
				return $this->_memcached->increment($id);
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
	 * Decrement a counter in memcached
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		return value from child method
	 */
	public function decrement($id)
	{
		$value = $this->get($id);
		
		if(is_numeric($value))
		{
			if($this->_raw_mode)
			{
				return $this->_memcached->decrement($id);
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
	 * @param 	null		type not supported in memcached
	 * @return 	mixed 		array on success, FALSE on failure
	 */
	public function cache_info($type = NULL)
	{
		return $this->_memcached->getStats();
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

		$stored = $this->_memcached->get($id);

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
	 * Setup memcached.
	 */
	private function _setup_memcached()
	{
		// Try to load memcached server info from the config file.
		$CI =& get_instance();
		if ($CI->config->load('memcached', TRUE, TRUE))
		{
			if (is_array($CI->config->config['memcached']))
			{
				$this->_memcache_conf = NULL;

				foreach ($CI->config->config['memcached'] as $name => $conf)
				{
					$this->_memcache_conf[$name] = $conf;
				}
			}
		}

		if (class_exists('Memcached'))
		{
			$this->_memcached = new Memcached();
		}
		else
		{
			log_message('error', 'Failed to create object for Memcached Cache; extension not loaded?');

			return FALSE;
		}
		
		$this->_memcached = new Memcached();

		foreach ($this->_memcached_conf as $name => $cache_server)
		{
			if ( ! array_key_exists('hostname', $cache_server))
			{
				$cache_server['hostname'] = $this->_memcached_conf['default']['default_host'];
			}
	
			if ( ! array_key_exists('port', $cache_server))
			{
				$cache_server['port'] = $this->_memcached_conf['default']['default_port'];
			}
	
			if ( ! array_key_exists('weight', $cache_server))
			{
				$cache_server['weight'] = $this->_memcached_conf['default']['default_weight'];
			}
			
			$this->_memcached->addServer(
				$cache_server['hostname'],
				$cache_server['port'],
				$cache_server['weight']
			);
		}
	}

	// ------------------------------------------------------------------------


	/**
	 * Is supported
	 *
	 * Returns FALSE if memcached is not supported on the system.
	 * If it is, we setup the memcached object & return TRUE
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('memcached'))
		{
			log_message('error', 'The Memcached Extension must be loaded to use Memcached Cache.');
			
			return FALSE;
		}
		
		$this->_setup_memcached();
		return TRUE;
	}

	// ------------------------------------------------------------------------

}
// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */