<?php
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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

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
	protected $valid_drivers = array(
		'apc',
		'dummy',
		'file',
		'memcached',
		'redis',
		'wincache'
	);

	/**
	 * Reference to the driver
	 *
	 * @var mixed
	 */
	protected $_adapter = 'dummy';

	/**
	 * Fallback driver
	 *
	 * @var string
	 */
	protected $_backup_driver = 'dummy';

	/**
	 * Cache key prefix
	 *
	 * @var	string
	 */
	public $key_prefix = '';

	/**
	 * Constructor
	 *
	 * Initialize class properties based on the configuration array.
	 *
	 * @param	array	$config = array()
	 * @return	void
	 */
	public function __construct(array $config = array())
	{
		$CI =& get_instance();
		
		/*
		 * A list of config options that we need to check for
		 * 1. Adapter (adapter)
		 * 2. Backup driver (backup)
		 * 3. Key prefix (key_prefix)
		 */
		
		$adapter = isset($config['adapter']) ? $config['adapter'] : $CI->config->item('cache_adapter');
		
		// if $adapter returns a TRUE value, then set it to $this->_adapter
		if ($adapter)
		{
			$this->_adapter = $adapter;
		}
		 
		$backup = isset($config['backup']) ? $config['backup'] : $CI->config->item('cache_backup');
		if ($backup)
		{
			$this->_backup_driver = $backup;
		}
		
		$drivers = isset($config['valid_drivers']) ? $config['valid_drivers'] : $CI->config->item('cache_valid_drivers');
		if (is_array($drivers))
		{
			/*
			 * no need to do any extra processing to make sure user didn't input driver names already
			 * in the valid drivers array or if the user inserted duplicates because it won't hurt
			 * anything. Let's not do any busy work.
			 */
			$this->valid_drivers = array_merge($this->valid_drivers, $drivers);
		}
		
		$key_prefix = isset($config['key_prefix']) ? $config['key_prefix'] : $CI->config->item('cache_key_prefix');
		if (is_string($key_prefix))
		{
			$this->key_prefix = $key_prefix;
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
	 * @param	string	$id
	 * @return	mixed	value matching $id or FALSE on failure
	 */
	public function get($id)
	{
		return $this->{$this->_adapter}->get($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param	string	$id		Cache ID
	 * @param	mixed	$data		Data to store
	 * @param	int	$ttl = 60	Cache TTL (in seconds)
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		return $this->{$this->_adapter}->save($this->key_prefix.$id, $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function delete($id)
	{
		return $this->{$this->_adapter}->delete($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function clean()
	{
		return $this->{$this->_adapter}->clean();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param	string	$type = 'user'	user/filehits
	 * @return	mixed	array containing cache info on success OR FALSE on failure
	 */
	public function cache_info($type = 'user')
	{
		return $this->{$this->_adapter}->cache_info($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	string	$id	key to get cache metadata on
	 * @return	mixed	cache item metadata
	 */
	public function get_metadata($id)
	{
		return $this->{$this->_adapter}->get_metadata($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param	string	$driver	The driver to test
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
	
	/**
	 * Magic method to allow drivers to support more methods than 
	 * the default
	 *
	 * @param	string	$method method to call
	 * @param	string	$args array of args
	 */
	public function __call($method, $args)
	{
		if (method_exists($this->{$this->_adapter}, $property))
		{
			return call_user_func(array($this->{$this->_adapter}, $method), $args);
		}
		
		return NULL;
	}

}

/**
 * CI_Cache_driver Class
 *
 * Extend this class to make a new CI_Cache driver.
 * A CI_Cache_driver implements key/value caching that persists across requests.
 * To make a new driver, derive from (extend) CI_Cache_driver. Overload the initialize method to simulate a contructor.
 * Then just implement the abstract functions in a similar fashion to the other CI Caching drivers.
 * Put your driver in the libraries/Cache/drivers folder anywhere in the loader paths. This includes the
 * application directory, the system directory, or any path you add with $CI->load->add_package_path().
 * Your driver must be named CI_Cache_<name>, and your filename must be Cache_<name>.php,
 * preferably also capitalized. (e.g.: CI_Cache_foo in libraries/Cache/drivers/Cache_foo.php)
 * Then specify the driver by setting 'cache_driver' in your config file or as a parameter when loading the CI_Cache
 * object. (e.g.: $config['cache_driver'] = 'foo'; OR $CI->load->driver('cache', array('cache_driver' => 'foo')); )
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		EllisLab Dev Team
 */
abstract class CI_Cache_driver extends CI_Driver {

	/**
	 * CI Singleton
	 *
	 * @see	get_instance()
	 * @var	object
	 */
	protected $CI;

	// ------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * Gets the CI singleton, so that individual drivers
	 * don't have to do it separately.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Decorate
	 *
	 * Decorates the child with the parent driver lib's methods and properties
	 *
	 * @param	object	Parent library object
	 * @return	void
	 */
	public function decorate($parent)
	{
		// Call base class decorate first
		parent::decorate($parent);

		// Call initialize method now that driver has access to $this->_parent
		$this->initialize();
	}

	// ------------------------------------------------------------------------

	/**
	 * Initialize driver
	 *
	 * @return	void
	 */
	protected function initialize()
	{
		// Overload this method to implement initialization
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Look for a value in the cache. If it exists, return the data
	 * if not, return FALSE
	 *
	 * @param	string	$id
	 * @return	mixed	value matching $id or FALSE on failure
	 */
	abstract public function get($id);

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param	string	$id		Cache ID
	 * @param	mixed	$data		Data to store
	 * @param	int	$ttl = 60	Cache TTL (in seconds)
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	abstract public function save($id, $data, $ttl = 60);

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	abstract public function delete($id);

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	abstract public function clean();

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param	string	$type = 'user'	user/filehits
	 * @return	mixed	array containing cache info on success OR FALSE on failure
	 */
	abstract public function cache_info($type = 'user');
	
	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	string	$id	key to get cache metadata on
	 * @return	mixed	cache item metadata
	 */
	abstract public function get_metadata($id);

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param	string	$driver	The driver to test
	 * @return	array
	 */
	abstract public function is_supported();
}

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */