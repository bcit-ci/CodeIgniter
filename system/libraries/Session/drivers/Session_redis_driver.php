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
 * @author		Andrey Andreev
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Redis Driver
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_redis_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * Save path
	 *
	 * @var	string
	 */
	protected $_save_path;

	/**
	 * phpRedis instance
	 *
	 * @var	resource
	 */
	protected $_redis;

	/**
	 * Key prefix
	 *
	 * @var	string
	 */
	protected $_key_prefix = 'ci_session:';

	/**
	 * Lock key
	 *
	 * @var	string
	 */
	protected $_lock_key;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);

		if (empty($this->_save_path))
		{
			log_message('error', 'Session: No Redis save path configured.');
		}
		elseif (preg_match('#(?:tcp://)?([^:]+)(?:\:(\d+))?(\?.+)?#', $this->_save_path, $matches))
		{
			$this->_save_path = array(
				'host' => $matches[1],
				'port' => empty($matches[2]) ? NULL : $matches[2],
				'timeout' => NULL
			);
			
			if ( ! empty($matches[3]))
			{
				$this->_save_path = array_merge($this->_save_path, array(
					'password' => preg_match('#auth=([^\s&]+)#', $matches[3], $match) ? $match[1] : NULL,
					'database' => preg_match('#database=(\d+)#', $matches[3], $match) ? (int) $match[1] : NULL,
					'timeout' => preg_match('#timeout=(\d+\.\d+)#', $matches[3], $match) ? (float) $match[1] : NULL
				));
				
				preg_match('#prefix=([^\s&]+)#', $matches[3], $match) && $this->_key_prefix = $match[1];
			}
		}
		else
		{
			log_message('error', 'Session: Invalid Redis save path format: '.$this->_save_path);
		}

		if ($this->_match_ip === TRUE)
		{
			$this->_key_prefix .= $_SERVER['REMOTE_ADDR'].':';
		}
	}

	// ------------------------------------------------------------------------

	public function open($save_path, $name)
	{
		if (empty($this->_save_path))
		{
			return FALSE;
		}

		$redis = new Redis();
		if ( ! $redis->connect($this->_save_path['host'], $this->_save_path['port'], $this->_save_path['timeout']))
		{
			log_message('error', 'Session: Unable to connect to Redis with the configured settings.');
		}
		elseif (isset($this->_save_path['password']) && ! $redis->auth($this->_save_path['password']))
		{
			log_message('error', 'Session: Unable to authenticate to Redis instance.');
		}
		elseif (isset($this->_save_path['database']) && ! $redis->select($this->_save_path['database']))
		{
			log_message('error', 'Session: Unable to select Redis database with index '.$this->_save_path['database']);
		}
		else
		{
			$this->_redis = $redis;
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	public function read($session_id)
	{
		if (isset($this->_redis) && $this->_get_lock($session_id))
		{
			$session_data = (string) $this->_redis->get($this->_key_prefix.$session_id);
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return FALSE;
	}

	public function write($session_id, $session_data)
	{
		if (isset($this->_redis, $this->_lock_key))
		{
			$this->_redis->setTimeout($this->_lock_key, 5);
			if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
			{
				if ($this->_redis->set($this->_key_prefix.$session_id, $session_data, $this->_expiration))
				{
					$this->_fingerprint = $fingerprint;
					return TRUE;
				}

				return FALSE;
			}

			return $this->_redis->setTimeout($this->_key_prefix.$session_id, $this->_expiration);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	public function close()
	{
		if (isset($this->_redis))
		{
			try {
				if ($this->_redis->ping() === '+PONG')
				{
					isset($this->_lock_key) && $this->_redis->delete($this->_lock_key);
					if ( ! $this->_redis->close())
					{
						return FALSE;
					}
				}
			}
			catch (RedisException $e)
			{
				log_message('error', 'Session: Got RedisException on close(): '.$e->getMessage());
			}

			$this->_redis = NULL;
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	public function destroy($session_id)
	{
		if (isset($this->_redis, $this->_lock_key))
		{
			if ($this->_redis->delete($this->_key_prefix.$session_id) !== 1)
			{
				log_message('debug', 'Session: Redis::delete() expected to return 1, got '.var_export($result, TRUE).' instead.');
			}

			return ($this->_cookie_destroy() && $this->close());
		}

		return $this->close();
	}

	// ------------------------------------------------------------------------

	public function gc($maxlifetime)
	{
		// TODO: keys()/getKeys() is said to be performance-intensive,
		// although it supports patterns (*, [charlist] at the very least).
		// scan() seems to be recommended, but requires redis 2.8
		// Not sure if we need any of these though, as we set keys with expire times
		return TRUE;
	}

	// ------------------------------------------------------------------------

	protected function _get_lock($session_id)
	{
		if (isset($this->_lock_key))
		{
			return $this->_redis->setTimeout($this->_lock_key, 5);
		}

		$lock_key = $this->_key_prefix.$session_id.':lock';
		if (($ttl = $this->_redis->ttl($lock_key)) < 1)
		{
			if ( ! $this->_redis->setex($lock_key, 5, time()))
			{
				log_message('error', 'Session: Error while trying to obtain lock for '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;

			if ($ttl === -1)
			{
				log_message('debug', 'Session: Lock for '.$this->_key_prefix.$session_id.' had no TTL, overriding.');
			}

			$this->_lock = TRUE;
			return TRUE;
		}

		// Another process has the lock, we'll try to wait for it to free itself ...
		$attempt = 0;
		while ($attempt++ < 5)
		{
			usleep(($ttl * 1000000) - 20000);
			if (($ttl = $this->_redis->ttl($lock_key)) > 0)
			{
				continue;
			}

			if ( ! $this->_redis->setex($lock_key, 5, time()))
			{
				log_message('error', 'Session: Error while trying to obtain lock for '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}

		if ($attempt === 5)
		{
			log_message('error', 'Session: Unable to obtain lock for '.$this->_key_prefix.$session_id.' after 5 attempts, aborting.');
			return FALSE;
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	protected function _release_lock()
	{
		if (isset($this->_redis, $this->_lock_key) && $this->_lock)
		{
			if ( ! $this->_redis->delete($this->_lock_key))
			{
				log_message('error', 'Session: Error while trying to free lock for '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = NULL;
			$this->_lock = FALSE;
		}

		return TRUE;
	}

}

/* End of file Session_redis_driver.php */
/* Location: ./system/libraries/Session/drivers/Session_redis_driver.php */