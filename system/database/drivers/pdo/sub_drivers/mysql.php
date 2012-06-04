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
class CI_mysql_PDO_Driver extends CI_DB_pdo_driver{

	/**
	 * Initialize the parent driver class
	 *
	 * @param	array
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		// Set the escape character to the silly backtick
		$this->_escape_char = '`';
		$this->_like_escape_str = '';
		$this->_like_escape_chr = '';

		// Refer : http://php.net/manual/en/ref.pdo-mysql.connection.php
		if ( ! is_php('5.3.6'))
		{
			$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$this->char_set} COLLATE '{$this->dbcollat}'";
		}
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
			$dsn = "mysql:host={$this->hostname};dbname={$this->database}";

			if ( ! empty($this->port))
			{
				$dsn .= ';port='.$this->port;
			}

			if ( ! empty($this->charset))
			{
				$dsn .= ';charset='.$this->charset;
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
	 * SQL string to list the tables in the database
	 *
	 * @param	bool
	 * @return	string
	 */
	public function _list_tables($prefix_limit = FALSE)
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
	public function _field_data($table)
	{
		return 'SELECT * FROM '.$this->_from_tables($table).' LIMIT 1';
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
	public function _truncate($table)
	{
		return 'TRUNCATE '.$this->_from_tables($table);
	}
}

/* End of file mysql.php */
/* Location: ./system/database/drivers/pdo/sub_drivers/mysql.php */