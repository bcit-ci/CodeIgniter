<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 2.1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * SQL Server specific methods for the PDO driver
 */
class CI_SQLSrv_PDO_Driver extends CI_DB_pdo_driver{

	/**
	 * Initialize the parent driver class
	 *
	 * @param	array
	 */
	public function __construct($params)
	{
		parent::__construct($params);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Establish the database connection
	 */
	public function connect()
	{
		// Create the connection dsn
		if ( ! empty($this->dsn))
		{
			$dsn = $this->dsn;
		}
		else
		{		
			$dsn = "{$this->pdodriver}:Server={$this->hostname}";
	
			if ( ! empty($this->port))
			{
				$dsn .= ','.$this->port;
			}
			
			$dsn .= ';Database='.$this->database;
			
			// if the driver is dblib, the connection string is more similar to other drivers
			if (empty($this->dsn) && $this->pdodriver == 'dblib')
			{
				$dsn = "dblib:host={$this->hostname}";
				
				if ( ! empty($this->port))
				{
					$dsn .= ':'.$this->port;
				}
				
				$dsn .= ';dbname='.$this->database;
			}
		}
	
		// Connecting...
		try 
		{
			$this->conn_id = new PDO($dsn, $this->username, $this->password, $this->options);
		} 
		catch (PDOException $e) 
		{
			if ($this->db_debug && empty($this->failover))
			{
				$this->display_error($e->getMessage(), '', TRUE);
			}
			return FALSE;
		}
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Manipulate the query string for the current database
	 *
	 * @param	string	sql string
	 * @return	string
	 */
	public function _prep_query($sql)
	{
		$sql = preg_replace('`"(.*)"`imx', '[\1]', $sql);
		return str_replace('"."', '].[', $sql);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Override for insert_id method
	 *
	 * @param	string	name of generator
	 * @return	string
	 */
	public function _insert_id($name = NULL)
	{
		if ($name === NULL && $this->version() >= '8.1')
		{
			$query = $this->query('SELECT @@IDENTITY AS insert_id');
			$query = $query->row();
			return $query->ins_id;
		}
		
		return $this->conn_id->lastInsertId($name);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * SQL string to list the tables in the database
	 *
	 * @return	string
	 */
	public function _list_tables()
	{
		return "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	public function _field_data($table)
	{
		return 'SELECT TOP 1 * FROM '.$this->_from_tables($table);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @access	public
	 * @param	string	the sql query string
	 * @param	integer	the number of rows to limit the query to
	 * @param	integer	the offset value
	 * @return	string
	 */
	public function _limit($sql, $limit, $offset)
	{
		return preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 TOP '.($limit + $offset).' ', $sql);
	}

}

/* End of file sqlsrv.php */
/* Location: ./system/database/drivers/pdo/sub_drivers/sqlsrv.php */