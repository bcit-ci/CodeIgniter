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
 * CodeIgniter Session Database Driver
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_database_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * DB object
	 *
	 * @var	object
	 */
	protected $_db;

	/**
	 * DB table
	 *
	 * @var	string
	 */
	protected $_table;

	/**
	 * Session ID
	 *
	 * @var	string
	 */
	protected $_session_id;

	/**
	 * Row exists flag
	 *
	 * @var	bool
	 */
	protected $_row_exists = FALSE;

	/**
	 * Lock "driver" flag
	 *
	 * @var	string
	 */
	protected $_lock_driver;

	/**
	 * Lock status flag
	 *
	 * @var	bool
	 */
	protected $_lock = FALSE;

	/**
	 * Semaphore ID
	 *
	 * Used for locking if the database doesn't support advisory locks
	 *
	 * @var	resource
	 */
	protected $_sem;

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

		$CI =& get_instance();
		isset($CI->db) OR $CI->load->database();
		$this->_db =& $CI->db;

		if ( ! $this->_db instanceof CI_DB_query_builder)
		{
			throw new Exception('Query Builder not enabled for the configured database. Aborting.');
		}
		elseif ($this->_db->pconnect)
		{
			throw new Exception('Configured database connection is persistent. Aborting.');
		}

		$db_driver = $this->_db->dbdriver.(empty($this->_db->subdriver) ? '' : '_'.$this->_db->subdriver);
		if (strpos($db_driver, 'mysql') !== FALSE)
		{
			$this->_lock_type = 'mysql';
		}
		elseif (in_array($db_driver, array('postgre', 'pdo_pgsql'), TRUE))
		{
			$this->_lock_type = 'postgre';
		}
		elseif (extension_loaded('sysvsem'))
		{
			$this->_lock_type = 'semaphore';
		}

		isset($this->_table) OR $this->_table = config_item('sess_table_name');
	}

	// ------------------------------------------------------------------------

	public function open($save_path, $name)
	{
		return empty($this->_db->conn_id)
			? ( ! $this->_db->autoinit && $this->_db->db_connect())
			: TRUE;
	}

	// ------------------------------------------------------------------------

	public function read($session_id)
	{
		$this->_session_id = $session_id;
		if (($this->_lock = $this->_get_lock()) !== FALSE)
		{
			$this->_db
				->select('data')
				->from($this->_table)
				->where('id', $session_id);

			if ($this->_match_ip)
			{
				$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
			}

			if (($result = $this->_db->get()->row()) === NULL)
			{
				$this->_fingerprint = md5('');
				return '';
			}

			$this->_fingerprint = md5(rtrim($result->data));
			$this->_row_exists = TRUE;
			return $result->data;
		}

		$this->_fingerprint = md5('');
		return '';
	}

	public function write($session_id, $session_data)
	{
		if ($this->_lock === FALSE)
		{
			return FALSE;
		}

		if ($this->_row_exists === FALSE)
		{
			if ($this->_db->insert($this->_table, array('id' => $session_id, 'ip_address' => $_SERVER['REMOTE_ADDR'], 'timestamp' => time(), 'data' => $session_data)))
			{
				$this->_fingerprint = md5($session_data);
				return $this->_row_exists = TRUE;
			}

			return FALSE;
		}

		$this->_db->where('id', $session_id);
		if ($this->_match_ip)
		{
			$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
		}

		$update_data = ($this->_fingerprint === md5($session_data))
			? array('timestamp' => time())
			: array('timestamp' => time(), 'data' => $session_data);

		if ($this->_db->update($this->_table, $update_data))
		{
			$this->_fingerprint = md5($session_data);
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	public function close()
	{
		return ($this->_lock)
			? $this->_release_lock()
			: TRUE;
	}

	// ------------------------------------------------------------------------

	public function destroy($session_id)
	{
		if ($this->_lock)
		{
			$this->_db->where('id', $session_id);
			if ($this->_match_ip)
			{
				$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
			}

			return $this->_db->delete($this->_table)
				? ($this->close() && $this->_cookie_destroy())
				: FALSE;
		}

		return ($this->close() && $this->_cookie_destroy());
	}

	// ------------------------------------------------------------------------

	public function gc($maxlifetime)
	{
		return $this->_db->delete($this->_table, 'timestamp < '.(time() - $maxlifetime));
	}

	// ------------------------------------------------------------------------

	protected function _get_lock()
	{
		$arg = $this->_session_id
			.($this->_match_ip ? '_'.$_SERVER['REMOTE_ADDR'] : '');

		if ($this->_lock_driver === 'mysql')
		{
			return (bool) $this->_db
				->query("SELECT GET_LOCK('".$session_id."', 10) AS ci_session_lock")
				->row()
				->ci_session_lock;
		}
		elseif ($this->_lock_driver === 'postgre')
		{
			return (bool) $this->_db->simple_query('SELECT pg_advisory_lock('.$arg.')');
		}
		elseif ($this->_lock_driver === 'semaphore')
		{
			if (($this->_sem = sem_get($arg, 1, 0644)) === FALSE)
			{
				return FALSE;
			}

			if ( ! sem_acquire($this->_sem))
			{
				sem_remove($this->_sem);
				return FALSE;
			}

			return TRUE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	protected function _release_lock()
	{
		if ($this->_lock_driver === 'mysql')
		{
			$arg = $this->_session_id
				.($this->_match_ip ? '_'.$_SERVER['REMOTE_ADDR'] : '');

			return (bool) $this->_db
				->query("SELECT RELEASE_LOCK('".$arg."') AS ci_session_lock")
				->row()
				->ci_session_lock;
		}
		elseif ($this->_lock_driver === 'postgre')
		{
			$arg = "hashtext('".$this->_session_id."')"
				.($this->_match_ip ? ", hashtext('".$_SERVER['REMOTE_ADDR']."')" : '');

			return (bool) $this->_db->simple_query('SELECT pg_advisory_unlock('.$arg.')');
		}
		elseif ($this->_lock_driver === 'semaphore')
		{
			sem_release($this->_sem);
			sem_remove($this->_sem);
		}

		return TRUE;
	}

}

/* End of file Session_database_driver.php */
/* Location: ./system/libraries/Session/drivers/Session_database_driver.php */