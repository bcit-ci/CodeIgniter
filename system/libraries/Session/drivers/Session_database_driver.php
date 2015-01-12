<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Database Driver
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_database_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * DB object
	 *
	 * @var	object
	 */
	protected $_db;

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
	protected $_lock_driver = 'semaphore';

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
			$this->_lock_driver = 'mysql';
		}
		elseif (in_array($db_driver, array('postgre', 'pdo_pgsql'), TRUE))
		{
			$this->_lock_driver = 'postgre';
		}

		isset($this->_config['save_path']) OR $this->_config['save_path'] = config_item('sess_table_name');
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
		if ($this->_get_lock($session_id) !== FALSE)
		{
			// Needed by write() to detect session_regenerate_id() calls
			$this->_session_id = $session_id;

			$this->_db
				->select('data')
				->from($this->_config['save_path'])
				->where('id', $session_id);

			if ($this->_config['match_ip'])
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
		// Was the ID regenerated?
		if ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return FALSE;
			}

			$this->_row_exists = FALSE;
			$this->_session_id = $session_id;
		}
		elseif ($this->_lock === FALSE)
		{
			return FALSE;
		}

		if ($this->_row_exists === FALSE)
		{
			if ($this->_db->insert($this->_config['save_path'], array('id' => $session_id, 'ip_address' => $_SERVER['REMOTE_ADDR'], 'timestamp' => time(), 'data' => $session_data)))
			{
				$this->_fingerprint = md5($session_data);
				return $this->_row_exists = TRUE;
			}

			return FALSE;
		}

		$this->_db->where('id', $session_id);
		if ($this->_config['match_ip'])
		{
			$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
		}

		$update_data = ($this->_fingerprint === md5($session_data))
			? array('timestamp' => time())
			: array('timestamp' => time(), 'data' => $session_data);

		if ($this->_db->update($this->_config['save_path'], $update_data))
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
			if ($this->_config['match_ip'])
			{
				$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
			}

			return $this->_db->delete($this->_config['save_path'])
				? ($this->close() && $this->_cookie_destroy())
				: FALSE;
		}

		return ($this->close() && $this->_cookie_destroy());
	}

	// ------------------------------------------------------------------------

	public function gc($maxlifetime)
	{
		return $this->_db->delete($this->_config['save_path'], 'timestamp < '.(time() - $maxlifetime));
	}

	// ------------------------------------------------------------------------

	protected function _get_lock($session_id)
	{
		if ($this->_lock_driver === 'mysql')
		{
			$arg = $session_id.($this->_config['match_ip'] ? '_'.$_SERVER['REMOTE_ADDR'] : '');
			if ($this->_db->query("SELECT GET_LOCK('".$arg."', 10) AS ci_session_lock")->row()->ci_session_lock)
			{
				$this->_lock = $arg;
				return TRUE;
			}

			return FALSE;
		}
		elseif ($this->_lock_driver === 'postgre')
		{
			$arg = "hashtext('".$session_id."')".($this->_config['match_ip'] ? ", hashtext('".$_SERVER['REMOTE_ADDR']."')" : '');
			if ($this->_db->simple_query('SELECT pg_advisory_lock('.$arg.')'))
			{
				$this->_lock = $arg;
				return TRUE;
			}

			return FALSE;
		}

		return parent::_get_lock($session_id);
	}

	// ------------------------------------------------------------------------

	protected function _release_lock()
	{
		if ( ! $this->_lock)
		{
			return TRUE;
		}

		if ($this->_lock_driver === 'mysql')
		{
			if ($this->_db->query("SELECT RELEASE_LOCK('".$this->_lock."') AS ci_session_lock")->row()->ci_session_lock)
			{
				$this->_lock = FALSE;
				return TRUE;
			}

			return FALSE;
		}
		elseif ($this->_lock_driver === 'postgre')
		{
			if ($this->_db->simple_query('SELECT pg_advisory_unlock('.$this->_lock.')'))
			{
				$this->_lock = FALSE;
				return TRUE;
			}

			return FALSE;
		}

		return parent::_release_lock();
	}

}

/* End of file Session_database_driver.php */
/* Location: ./system/libraries/Session/drivers/Session_database_driver.php */