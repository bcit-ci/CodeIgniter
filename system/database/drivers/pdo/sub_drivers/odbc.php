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
 * Fallback Sub driver for un-implemented databases
 */
class CI_ODBC_PDO_Driver {

	protected $conn;
	protected $pdo;

	/**
	 * Save the connection object for later use
	 */
	public function __construct($pdo)
	{
		$this->pdo =& $pdo;
		
		// Create the connection dsn
		$dsn = ( ! empty($pdo->dsn)) 
			? 'odbc:'.$pdo->dsn
			: "odbc:";
			
		if ( ! empty($pdo->port))
		{
			$dsn .= ';port='.$pdo->port;
		}
	
		// Connecting...
		try 
		{
			$pdo->conn_id = new PDO($dsn, $pdo->username, $pdo->password, $pdo->options);
		} 
		catch (PDOException $e) 
		{
			if ($pdo->db_debug && empty($pdo->failover))
			{
				$pdo->display_error($e->getMessage(), '', TRUE);
			}

			return FALSE;
		}
		
		$this->conn =& $pdo->conn_id;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Manipulate the query string for the current database
	 *
	 * @param	string	sql string
	 * @return	string
	 */
	public function prep_query($sql)
	{
		return $sql;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * SQL string to list the tables in the database
	 *
	 * @return	string
	 */
	public function list_tables()
	{
		return FALSE;
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
	public function field_data($table)
	{
		return 'SELECT * FROM '.$table;
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
	public function limit($sql, $limit, $offset)
	{
		return $sql;
	}

}

/* End of file odbc.php */
/* Location: ./system/database/drivers/pdo/sub_drivers/odbc.php */