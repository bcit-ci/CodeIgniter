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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Driver Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
abstract class CI_Session_driver implements SessionHandlerInterface {

	// WARNING! Setting default values to properties will
	// prevent using the configuration file values.

	/**
	 * Expiration time
	 *
	 * @var	int
	 */
	protected $_expiration;

	/**
	 * Cookie name
	 *
	 * @var	string
	 */
	protected $_cookie_name;

	/**
	 * Cookie domain
	 *
	 * @var	string
	 */
	protected $_cookie_domain;

	/**
	 * Cookie path
	 *
	 * @var	string
	 */
	protected $_cookie_path;

	/**
	 * Cookie secure flag
	 *
	 * @var	bool
	 */
	protected $_cookie_secure;

	/**
	 * Cookie HTTP-only flag
	 *
	 * @var	bool
	 */
	protected $_cookie_httponly;

	/**
	 * Match IP addresses flag
	 *
	 * @var	bool
	 */
	protected $_match_ip;

	/**
	 * Data fingerprint
	 *
	 * @var	bool
	 */
	protected $_fingerprint;

	/**
	 * Lock placeholder
	 *
	 * @var	mixed
	 */
	protected $_lock = FALSE;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct($params)
	{
		foreach ($params as $key => &$value)
		{
			$key = (strncmp($key, 'sess_', 5) === 0)
				? substr($key, 4)
				: '_'.$key;

			property_exists($this, $key) && $this->$key = $value;
		}

		isset($this->_expiration) OR $this->_expiration = (int) config_item('sess_expiration');
		isset($this->_cookie_name) OR $this->_cookie_name = config_item('sess_cookie_name');
		isset($this->_cookie_domain) OR $this->_cookie_domain = config_item('cookie_domain');
		isset($this->_cookie_path) OR $this->_cookie_path = config_item('cookie_path');
		isset($this->_cookie_secure) OR $this->_cookie_secure = config_item('cookie_secure');
		isset($this->_cookie_httponly) OR $this->_cookie_httponly = config_item('cookie_httponly');
		isset($this->_match_ip) OR $this->_match_ip = config_item('sess_match_ip');

		// Pass our configuration to php.ini, when appropriate
		ini_set('session.name', $this->_cookie_name);
		isset($this->_cookie_domain) && ini_set('session.cookie_domain', $this->_cookie_domain);
		isset($this->_cookie_path) && ini_set('session.cookie_path', $this->_cookie_path);
		isset($this->_cookie_secure) && ini_set('session.cookie_secure', $this->_cookie_secure);
		isset($this->_cookie_httponly) && ini_set('session.cookie_httponly', $this->_cookie_httponly);

		if ($this->_expiration)
		{
			ini_set('session.gc_maxlifetime', $this->_expiration);
			ini_set('session.cookie_lifetime', $this->_expiration);
		}
		// BC workaround for setting cookie lifetime
		elseif (config_item('sess_expire_on_close'))
		{
			ini_set('session.cookie_lifetime', 0);
		}

		// Security is king
		ini_set('session.use_trans_id', 0);
		ini_set('session.use_strict_mode', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.hash_function', 1);
		ini_set('session.hash_bits_per_character', 4);

		// Work-around for PHP bug #66827 (https://bugs.php.net/bug.php?id=66827)
		//
		// The session ID sanitizer doesn't check for the value type and blindly does
		// an implicit cast to string, which triggers an 'Array to string' E_NOTICE.
		if (isset($_COOKIE[$this->_cookie_name]) && ! is_string($_COOKIE[$this->_cookie_name]))
		{
			unset($_COOKIE[$this->_cookie_name]);
		}

/*
		Need to test if this is necessary for a custom driver or if it's only
		relevant to PHP's own files handler.

		https://bugs.php.net/bug.php?id=65475
		do this after session is started:
		if (is_php('5.5.2') && ! is_php('5.5.4'))
		{
			$session_id = session_id();
			if ($_COOKIE[$this->_cookie_name] !== $session_id && file_exists(teh file))
			{
				unlink(<teh file>);
			}

			setcookie(
				$this->_cookie_name,
				$session_id,
				$this->_expiration
					? time() + $this->_expiration
					: 0,
				$this->_cookie_path,
				$this->_cookie_domain,
				$this->_cookie_secure,
				$this->_cookie_httponly
			);
		}
*/
	}

	// ------------------------------------------------------------------------

	protected function _cookie_destroy()
	{
		return setcookie(
			$this->_cookie_name,
			NULL,
			1,
			$this->_cookie_path,
			$this->_cookie_domain,
			$this->_cookie_secure,
			$this->_cookie_httponly
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 *
	 * A default locking mechanism via semaphores, if ext/sysvsem is available.
	 *
	 * Drivers will usually override this and only fallback to it if no other
	 * locking mechanism is available.
	 *
	 * @param	string	$session_id
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		if ( ! extension_loaded('sysvsem'))
		{
			$this->_lock = TRUE;
			return TRUE;
		}

		if (($this->_lock = sem_get($session_id.($this->_match_ip ? '_'.$_SERVER['REMOTE_ADDR'] : ''), 1, 0644)) === FALSE)
		{
			return FALSE;
		}

		if ( ! sem_acquire($this->_lock))
		{
			sem_remove($this->_lock);
			$this->_lock = FALSE;
			return FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 *
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if (extension_loaded('sysvsem') && $this->_lock)
		{
			sem_release($this->_lock);
			sem_remove($this->_lock);
			$this->_lock = FALSE;
		}

		return TRUE;
	}

}

/* End of file Session_driver.php */
/* Location: ./system/libraries/Session/Session_driver.php */