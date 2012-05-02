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
 * Cubrid specific methods for the PDO driver
 */
class CI_Cubrid_PDO_Driver extends CI_DB_pdo_driver {

	/**
	 * Initialize the parent driver class
	 *
	 * @param	array
	 */
	public function __construct($params)
	{
		parent::__construct($params)
		
		// Set the escape character to the silly backtick
		$this->_escape_char = '`';
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
			$dsn = "cubrid:host={$this->hostname};dbname={$this->database}";
				
			if ( ! empty($this->port))
			{
				$dsn .= ';port='.$this->port;
			}
		}
	
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
	 * @return	string
	 */
	public function _list_tables()
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

}

/* End of file cubrid.php */
/* Location: ./system/database/drivers/pdo/sub_drivers/cubird.php */