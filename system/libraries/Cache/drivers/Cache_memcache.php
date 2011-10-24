<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Ryan Neufeld <ryan@metroleap.com>
 * @copyright	CBSIMG
 * @link		http://cbsimg.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------


/**
 * CodeIgniter memcache Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */

class Cache_memcache extends CI_Driver
{

	private $_memcache; // Holds the memcache object

	
	protected $_memcache_conf = array(
		'default' => array(
			'default_host' => '127.0.0.1', 
			'default_port' => 11211));

	protected $_default_options = array(
		'default_host' => '127.0.0.1', 
		'default_port' => 11211);

	// ------------------------------------------------------------------------	
	

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */
	public function get($id)
	{
		$data = $this->_memcache->get($id);
		
		return (is_array($data)) ? $data[0] : FALSE;
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Save
	 *
	 * @param 	string		unique identifier
	 * @param 	mixed		data being cached
	 * @param 	int			time to live
	 * @return 	boolean 	true on success, false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{	
		return $this->_memcache->set($id, array($data, time(), $ttl), false, $ttl);
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		key to be deleted.
	 * @return 	boolean 	true on success, false on failure
	 */
	public function delete($id)
	{
		return $this->_memcache->delete($id);
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Clean the Cache
	 *
	 * @return 	boolean		false on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcache->flush();
	}

	// ------------------------------------------------------------------------
	

	/**
	 * Cache Info
	 *
	 * @param 	null		type disabled
	 * @return 	mixed 		array on success, false on failure
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
		$stored = $this->_memcache->get($id, MEMCACHE_COMPRESSED);
		
		if (count($stored) !== 3)
		{
			return FALSE;
		}
		
		list ($value, $time, $ttl) = $stored;
		
		return array(
			'expire' => $time + $ttl, 
			'mtime' => $time, 
			'data' => $data);
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
		$this->_memcache = new Memcache();
		
		foreach ($this->_memcache_conf as $name => $cache_server)
		{
			if (! array_key_exists('hostname', $cache_server))
			{
				$cache_server['hostname'] = $this->_default_options['default_host'];
			}
			
			if (! array_key_exists('port', $cache_server))
			{
				$cache_server['port'] = $this->_default_options['default_port'];
			}
			
			$this->_memcache->connect($cache_server['hostname'], $cache_server['port']);
		}
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