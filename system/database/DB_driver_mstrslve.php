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
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Database Driver Class for master/slave mode
 *
 * This is the platform-independent master/slave mode DB implementation class.
 * It extends base DB implementation class. This class will not be called directly. 
 * Rather, the adapter class for the specific database will extend and instantiate it.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
abstract class CI_DB_driver_mstrslve extends CI_DB_driver_single {

	/**
	 * Databases credentials in master/slave mode
	 *
	 * @var	array
	 */
	public $cred;
	
	/**
	 * Master/slave mode flag
	 *
	 * @var	bool
	 */
	public $mstrslve          	= TRUE;
	
	/**
	 * Default database in master/slave mode when autoinit is used
	 *
	 * @var	string 'master'/'slave'
	 */
	public $db_deflt       		= 'slave';
	
	/**
	 * Connection ID master
	 *
	 * @var	object|resource
	 */
	public $conn_id_master		= FALSE;
	
	/**
	 * Connection ID slave
	 *
	 * @var	object|resource
	 */
	public $conn_id_slave		= FALSE;
	
	/**
	 * Force usage of particular database in master/slave mode
	 *
	 * @var	string|null
	 */
	protected $db_force   	    = NULL;
	
	/**
	 * Whether to clear forced usage of particular database in master/slave mode
	 * after each query
	 *
	 * @var	bool
	 */
	protected $db_force_clr     = TRUE;
	
	/**
	 * Active database in master/slave mode
	 *
	 * @var	string|null
	 */
	protected $dbactive      	= NULL;

	// --------------------------------------------------------------------
	
	/**
	 * Initialize database credentials when in master/slave setup
	 *
	 * @return	void
	 */
	private function _set_cred() 
	{
		// Handle autoinit in master/slave mode
		if ($this->dbactive === NULL) 
		{
			$this->dbactive = $this->db_deflt;
		}
		
		if (is_array($this->cred[$this->dbactive]))
		{
			foreach ($this->cred[$this->dbactive] as $key => $val)
			{
				$this->$key = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize Database Settings
	 *
	 * @return	bool
	 */
	public function initialize()
	{
		/* If an established connection is available, then there's
		 * no need to connect and select the database.
		 *
		 * Depending on the database driver, conn_id can be either
		 * boolean TRUE, a resource or an object.
		 */
		if ($this->conn_id)
		{
			return TRUE;
		}

		// ----------------------------------------------------------------
		
		// Set credentials first
		$this->_set_cred();
		
		// Connect to the database and set the connection ID
		$this->conn_id = $this->db_connect($this->pconnect);

		// No connection resource? Check if there is a failover else throw an error
		if ( ! $this->conn_id)
		{
			// Check if there is a failover set
			if ( ! empty($this->failover) && is_array($this->failover))
			{
				// Go over all the failovers
				foreach ($this->failover as $failover)
				{
					// Replace the current settings with those of the failover
					foreach ($failover as $key => $val)
					{
						$this->$key = $val;
					}

					// Try to connect
					$this->conn_id = $this->db_connect($this->pconnect);

					// If a connection is made break the foreach loop
					if ($this->conn_id)
					{
						break;
					}
				}
			}

			// We still don't have a connection?
			if ( ! $this->conn_id)
			{
				log_message('error', 'Unable to connect to the database');

				if ($this->db_debug)
				{
					$this->display_error('db_unable_to_connect');
				}

				return FALSE;
			}
		}

		// Now we set the character set and that's all
		return $this->db_set_charset($this->char_set);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Configure db params for master/slave setup
	 *
	 * @param	string	the sql query
	 * @return	void
	 */
	private function _config_master_slave($sql = '') 
	{	
		if ($this->db_force === 'master' OR ($this->db_force === NULL AND $this->is_write_type($sql) === TRUE))
		{
			if ($this->dbactive === 'slave') 
			{
				if(gettype($this->conn_id_slave) !== gettype($this->conn_id))
				{
					$this->conn_id_slave = $this->conn_id;
				}
				$this->conn_id = &$this->conn_id_master;
			}
			$this->dbactive = 'master';
			log_message('error', 'master ' . $sql);
		}
		else
		{
			if ($this->dbactive === 'master')
			{
				if(gettype($this->conn_id_slave) !== gettype($this->conn_id))
				{
					$this->conn_id_master = $this->conn_id;
				}
				$this->conn_id = &$this->conn_id_slave;
			}
			$this->dbactive = 'slave';
			log_message('error', 'slave ' . $sql);
		}
		
		if ( ! $this->conn_id)
		{
			$this->initialize();
		}
		
		// Clear database force if not explicitly set not to
		if ($this->db_force_clr === TRUE)
		{
			$this->db_force = NULL;
		}
	}
	
	/**
	 * Simple Query
	 * This is a simplified version of the query() function. Internally
	 * we only use it when running transaction commands since they do
	 * not require all the features of the main query() function.
	 *
	 * @param	string	the sql query
	 * @return	mixed
	 */
	public function simple_query($sql)
	{
		$this->_config_master_slave($sql);

		return $this->_execute($sql);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Force using specific database in master/slave mode
	 *
	 * @param	string which database to use
	 * @param	boolean toggle auto/manual database selection after the first query
	 * @return	void
	 */
	public function db_force($database = 'master', $db_force_clr = TRUE)
	{
		$this->db_force = $database;
		$this->db_force_clr = $db_force_clr;
		$this->_config_master_slave();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Clear force master
	 *
	 * @return	void
	 */
	public function db_force_clear()
	{
		$this->db_force = NULL;
		$this->db_force_clr = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	public function close()
	{
		$closed = 0;
		if (is_resource($this->conn_id_master) OR is_object($this->conn_id_master))
		{
			$this->_close($this->conn_id_master);
			$closed++;
		}
		$this->conn_id_master = FALSE;
		
		if (is_resource($this->conn_id_slave) OR is_object($this->conn_id_slave))
		{
			$this->_close($this->conn_id_slave);
			$closed++;
		}
		$this->conn_id_slave = FALSE;
		
		// If master and slave were closed, conn_id should not be closed
		if ($closed !== 2 AND (is_resource($this->conn_id) OR is_object($this->conn_id)))
		{
			$this->_close($this->conn_id);
		}
		$this->conn_id = FALSE;
	}

}

/* End of file DB_driver_mstrslve.php */
/* Location: ./system/database/DB_driver_mstrslve.php */