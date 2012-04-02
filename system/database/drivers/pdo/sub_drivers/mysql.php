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
 * MySQL specific methods for the PDO driver
 */
class CI_MySQL_PDO_Driver {

	protected $conn;
	protected $pdo;

	/**
	 * Save the connection object for later use
	 */
	public function __construct($pdo)
	{
		$this->pdo =& $pdo;
		
		// Set the escape character to the silly backtick
		$pdo->_escape_char = '`';
		
		// Refer : http://php.net/manual/en/ref.pdo-mysql.connection.php
		if ( ! is_php('5.3.6'))
		{
			$pdo->options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$pdo->char_set} COLLATE '{$pdo->dbcollat}'";
		}
		
		// Create the connection dsn
		$dsn = ( ! empty($pdo->dsn)) 
			? $pdo->dsn
			: "mysql:host={$pdo->hostname};dbname={$pdo->database}";
			
		if ( ! empty($pdo->port))
		{
			$dsn .= ';port='.$pdo->port;
		}
		
		if ( ! empty($pdo->charset))
		{
			$dsn .= ';charset='.$pdo->charset;
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
	 * SQL string to list the tables in the database
	 *
	 * @return	string
	 */
	public function list_tables()
	{
		return "SHOW TABLES";
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
		$offset = ($offset == 0) ? '' : $offset.', ';

		return $sql.'LIMIT '.$offset.$limit;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Return MySQL-specific truncate command
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	public function truncate($table)
	{
		return 'TRUNCATE '.$this->pdo->_from_tables($table);
	}
}

/* End of file mysql.php */
/* Location: ./system/database/drivers/pdo/sub_drivers/mysql.php */