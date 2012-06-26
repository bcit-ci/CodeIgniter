<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource
 */

/**
 * CodeIgniter Caching Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache extends CI_Driver_Library {

	/**
	 * Valid cache drivers
	 *
	 * @var array
	 */
	protected $valid_drivers 	= array(
		'cache_apc',
		'cache_dummy',
		'cache_file',
		'cache_memcached',
		'cache_redis',
		'cache_wincache'
	);

	/**
	 * Path of cache files (if file-based cache)
	 *
	 * @var string
	 */
	protected $_cache_path = NULL;

	/**
	 * Reference to the driver
	 *
	 * @var mixed
	 */
	protected $_adapter = 'dummy';

	/**
	 * Fallback driver
	 *
	 * @param string
	 */
	protected $_backup_driver = 'dummy';

	/**
	 * Constructor
	 *
	 * Initialize class properties based on the configuration array.
	 *
	 * @param	array
	 * @return	void
	 */
	public function __construct($config = array())
	{
		$default_config = array(
			'adapter',
			'memcached'
		);

		foreach ($default_config as $key)
		{
			if (isset($config[$key]))
			{
				$param = '_'.$key;

				$this->{$param} = $config[$key];
			}
		}

		if (isset($config['backup']))
		{
			if (in_array('cache_'.$config['backup'], $this->valid_drivers))
			{
				$this->_backup_driver = $config['backup'];
			}
		}

		// If the specified adapter isn't available, check the backup.
		if ( ! $this->is_supported($this->_adapter))
		{
			if ( ! $this->is_supported($this->_backup_driver))
			{
				// Backup isn't supported either. Default to 'Dummy' driver.
				log_message('error', 'Cache adapter "'.$this->_adapter.'" and backup "'.$this->_backup_driver.'" are both unavailable. Cache is now using "Dummy" adapter.');
				$this->_adapter = 'dummy';
			}
			else
			{
				// Backup is supported. Set it to primary.
				$this->_adapter = $this->_backup_driver;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Look for a value in the cache. If it exists, return the data
	 * if not, return FALSE
	 *
	 * @param	string
	 * @return	mixed	value that is stored/FALSE on failure
	 */
	public function get($id)
	{
		return $this->{$this->_adapter}->get($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param	string	Unique Key
	 * @param	mixed	Data to store
	 * @param	int	Length of time (in seconds) to cache the data
	 * @return	bool	true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		return $this->{$this->_adapter}->save($id, $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	mixed	unique identifier of the item in the cache
	 * @return	bool	true on success/false on failure
	 */
	public function delete($id)
	{
		return $this->{$this->_adapter}->delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		return $this->{$this->_adapter}->clean();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param	string	user/filehits
	 * @return	mixed	array on success, false on failure
	 */
	public function cache_info($type = 'user')
	{
		return $this->{$this->_adapter}->cache_info($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	mixed	key to get cache metadata on
	 * @return	mixed	return value from child method
	 */
	public function get_metadata($id)
	{
		return $this->{$this->_adapter}->get_metadata($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param	string	The driver to test.
	 * @return	array
	 */
	public function is_supported($driver)
	{
		static $support = array();

		if ( ! isset($support[$driver]))
		{
			$support[$driver] = $this->{$driver}->is_supported();
		}

		return $support[$driver];
	}

}

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */