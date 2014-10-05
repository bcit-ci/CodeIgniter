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

	protected $_config;

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
	public function __construct(&$params)
	{
		$this->_config =& $params;
	}

	// ------------------------------------------------------------------------

	protected function _cookie_destroy()
	{
		return setcookie(
			$this->_config['cookie_name'],
			NULL,
			1,
			$this->_config['cookie_path'],
			$this->_config['cookie_domain'],
			$this->_config['cookie_secure'],
			TRUE
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

		if (($this->_lock = sem_get($session_id.($this->_config['match_ip'] ? '_'.$_SERVER['REMOTE_ADDR'] : ''), 1, 0644)) === FALSE)
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