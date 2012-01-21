<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package	  CodeIgniter
 * @author	  ExpressionEngine Dev Team
 * @copyright Copyright (c) 2006 - 2011 EllisLab, Inc.
 * @license	  http://codeigniter.com/user_guide/license.html
 * @link	  http://codeigniter.com
 * @since	  Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Redis Caching Class
 *
 * @package	   CodeIgniter
 * @subpackage Libraries
 * @category   Core
 * @author	   Anton Lindqvist <anton@qvister.se>
 * @link
 */
class CI_Cache_redis extends CI_Driver
{

	/**
	 * Default config
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	private static $_default_config = array(
		'host' => '127.0.0.1',
		'port' => 6379,
		'timeout' => 0
	);

	/**
	 * Redis connection
	 *
	 * @access private
	 * @var Redis
	 */
	private $_redis;

	/**
	 * Class destructor
	 *
	 * Closes the connection to Redis if present.
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		if ($this->_redis)
        {
			$this->_redis->close();
		}
	}

	/**
	 * Get cache
	 *
	 * @access public
	 * @param string $key Cache key identifier
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->_redis->get($key);
	}

	/**
	 * Save cache
	 *
	 * @access public
	 * @param string  $key	 Cache key identifier
	 * @param mixed	  $value Data to save
	 * @param integer $ttl	 Time to live
	 * @return boolean
	 */
	public function save($key, $value, $ttl = NULL)
	{
		return ($ttl)
			? $this->_redis->setex($key, $ttl, $value)
			: $this->_redis->set($key, $value);
	}

	/**
	 * Delete from cache
	 *
	 * @access public
	 * @param string $key Cache key
	 * @return boolean
	 */
	public function delete($key)
	{
		return ($this->_redis->delete($key) === 1);
	}

	/**
	 * Clean cache
	 *
	 * @access public
	 * @return boolean
	 * @see Redis::flushDB()
	 */
	public function clean()
	{
		return $this->_redis->flushDB();
	}

	/**
	 * Get cache driver info
	 *
	 * @access public
	 * @param string $type Not supported in Redis. Only included in order to offer a
	 *					   consistent cache API.
	 * @return array
	 * @see Redis::info()
	 */
	public function cache_info($type = NULL)
	{
		return $this->_redis->info();
	}

	/**
	 * Get cache metadata
	 *
	 * @access public
	 * @param string $key Cache key
	 * @return array
	 */
	public function get_metadata($key)
	{
		$value = $this->get($key);

		if ($value)
        {
			return array(
				'expire' => time() + $this->_redis->ttl($key),
				'data' => $value
			);
		}
	}

	/**
	 * Check if Redis driver is supported
	 *
	 * @access public
	 * @return boolean
	 */
	public function is_supported()
	{
		if (extension_loaded('redis'))
        {
			$this->_setup_redis();

			return TRUE;
		}
		else
		{
			log_message(
				'error',
				'The Redis extension must be loaded to use Redis cache.'
			);

			return FALSE;
		}

	}

	/**
	 * Setup Redis config and connection
	 *
	 * Loads Redis config file if present. Will halt execution if a Redis connection
	 * can't be established.
	 *
	 * @access private
	 * @return void
	 * @see Redis::connect()
	 */
	private function _setup_redis()
	{
		$config = array();
		$CI =& get_instance();

		if ($CI->config->load('redis', TRUE, TRUE))
        {
			$config += $CI->config->item('redis');
		}

		$config = array_merge(self::$_default_config, $config);

		$this->_redis = new Redis();

		try
		{
			$this->_redis->connect($config['host'], $config['port'], $config['timeout']);
		}
		catch (RedisException $e)
		{
			show_error('Redis connection refused. ' . $e->getMessage());
		}
	}

}
// End Class

/* End of file Cache_redis.php */
/* Location: ./system/libraries/Cache/drivers/Cache_redis.php */