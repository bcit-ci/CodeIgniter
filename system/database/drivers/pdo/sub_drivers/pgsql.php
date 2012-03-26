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
 * Postgresql specific methods for the PDO driver
 */
class CI_PgSQL_PDO_Driver {

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
			? $pdo->dsn
			: "pgsql:host={$pdo->hostname};dbname={$pdo->database}";
		
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
	 * Override for insert_id method
	 *
	 * @param	string	name of generator
	 * @return	string
	 */
	public function insert_id($name = NULL)
	{
		if ($name === NULL && $this->version() >= '8.1')
		{
			$query = $this->query('SELECT LASTVAL() AS ins_id');
			$query = $query->row();
			return $query->ins_id;
		}
		
		return $this->conn->lastInsertId($name);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * SQL string to list the tables in the database
	 *
	 * @return	string
	 */
	public function list_tables()
	{
		return "SELECT * FROM information_schema.tables WHERE table_schema = 'public'";
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
		return 'SELECT * FROM '.$table.' LIMIT 1';
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
		$sql .= 'LIMIT '.$limit;
		$sql .= ($offset > 0) ? ' OFFSET '.$offset : '';
			
		return $sql;
	}

}

/* End of file pgsql.php */
/* Location: ./system/database/drivers/pdo/sub_drivers/pgsql.php */