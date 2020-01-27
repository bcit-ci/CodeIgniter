<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Memcache Driver (Using php-memcache instead of php-memcached)
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev | Andrei de Oliveira Mosman
 * @link	https://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_memcache_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * Memcache instance
	 *
	 * @var	Memcache
	 */
	protected $_memcache;

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

		if (empty($this->_config['save_path']))
		{
			log_message('error', 'Session: No Memcached save path configured.');
		}

		if ($this->_config['match_ip'] === TRUE)
		{
			$this->_key_prefix .= $_SERVER['REMOTE_ADDR'].':';
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 *
	 * Sanitizes save_path and initializes connections.
	 *
	 * @param	string	$save_path	Server path(s)
	 * @param	string	$name		Session cookie name, unused
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		$this->_memcache = new Memcache();
        $server_list = array();

		if ( ! preg_match_all('#,?([^,:]+)\:(\d{1,5})(?:\:(\d+))?#', $this->_config['save_path'], $matches, PREG_SET_ORDER))
		{
			$this->_memcache = NULL;
			log_message('error', 'Session: Invalid Memcached save path format: '.$this->_config['save_path']);
			return $this->_failure;
		}

		foreach ($matches as $match)
		{
			// If Memcached already has this server (or if the port is invalid), skip it
			if (in_array($match[1].':'.$match[2], $server_list, TRUE))
			{
				log_message('debug', 'Session: Memcached server pool already has '.$match[1].':'.$match[2]);
				continue;
			}

			if ( ! $this->_memcache->addServer($match[1], $match[2], true, isset($match[3]) ? $match[3] : 1))
			{
				log_message('error', 'Could not add '.$match[1].':'.$match[2].' to Memcached server pool.');
			}
			else
			{
				$server_list[] = $match[1].':'.$match[2];
			}
		}

		if (empty($server_list))
		{
			log_message('error', 'Session: Memcached server pool is empty.');
			return $this->_failure;
		}

		$this->php5_validate_id();

		return $this->_success;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
		if (isset($this->_memcache) && $this->_get_lock($session_id))
		{
			// Needed by write() to detect session_regenerate_id() calls
			$this->_session_id = $session_id;

			$session_data = (string) $this->_memcache->get($this->_key_prefix.$session_id);
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 *
	 * Writes (create / update) session data
	 *
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		if ( ! isset($this->_memcache, $this->_lock_key))
		{
			return $this->_failure;
		}
		// Was the ID regenerated?
		elseif ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return $this->_failure;
			}

			$this->_fingerprint = md5('');
			$this->_session_id = $session_id;
		}

		$key = $this->_key_prefix.$session_id;

		$this->_memcache->replace($this->_lock_key, time(), false, 300);
		if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
		{
			if ($this->_memcache->set($key, $session_data, false, $this->_config['expiration']))
			{
				$this->_fingerprint = $fingerprint;
				return $this->_success;
			}

			return $this->_failure;
		}
		elseif (
            $this->_memcache->replace($key, $session_data, false, $this->_config['expiration'])
			OR $this->_memcache->set($key, $session_data, false, $this->_config['expiration'])
		)
		{
			return $this->_success;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Releases locks and closes connection.
	 *
	 * @return	bool
	 */
	public function close()
	{
		if (isset($this->_memcache))
		{
			$this->_release_lock();
			if ( ! $this->_memcache->close())
			{
				return $this->_failure;
			}

			$this->_memcache = NULL;
			return $this->_success;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 *
	 * Destroys the current session.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		if (isset($this->_memcache, $this->_lock_key))
		{
			$this->_memcache->delete($this->_key_prefix.$session_id);
			$this->_cookie_destroy();
			return $this->_success;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 *
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		// Not necessary, Memcached takes care of that.
		return $this->_success;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate ID
	 *
	 * Checks whether a session ID record exists server-side,
	 * to enforce session.use_strict_mode.
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function validateSessionId($id)
	{
		//$this->_memcached->get($this->_key_prefix.$id);
        //return ($this->_memcached->getResultCode() === Memcached::RES_SUCCESS);
        return($this->_memcache->get($this->_key_prefix.$id)!=false);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 *
	 * Acquires an (emulated) lock.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		// PHP 7 reuses the SessionHandler object on regeneration,
		// so we need to check here if the lock key is for the
		// correct session ID.
		if ($this->_lock_key === $this->_key_prefix.$session_id.':lock')
		{
			if ( ! $this->_memcache->replace($this->_lock_key, time(), false, 300))
			{
                return($this->_memcache->add($this->_lock_key, time(), false, 300));
                /**
				return ($this->_memcached->getResultCode() === Memcached::RES_NOTFOUND)
					? $this->_memcached->add($this->_lock_key, time(), 300)
                    : FALSE;
                 */
			}

			return TRUE;
		}

		// 30 attempts to obtain a lock, in case another request already has it
		$lock_key = $this->_key_prefix.$session_id.':lock';
		$attempt = 0;
		do
		{
			if ($this->_memcache->get($lock_key))
			{
				sleep(1);
				continue;
			}

			$method = ($this->_memcache->get($lock_key)==false) ? 'add' : 'set';
			if ( ! $this->_memcache->$method($lock_key, time(), false, 300))
			{
				log_message('error', 'Session: Error while trying to obtain lock for '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}
		while (++$attempt < 30);

		if ($attempt === 30)
		{
			log_message('error', 'Session: Unable to obtain lock for '.$this->_key_prefix.$session_id.' after 30 attempts, aborting.');
			return FALSE;
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 *
	 * Releases a previously acquired lock
	 *
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if (isset($this->_memcache, $this->_lock_key) && $this->_lock)
		{
            if ( ! $this->_memcache->delete($this->_lock_key) )
			{
				log_message('error', 'Session: Error while trying to free lock for '.$this->_lock_key);
				return FALSE;
			}

			$this->_lock_key = NULL;
			$this->_lock = FALSE;
		}

		return TRUE;
	}
}
