<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ODBC Utility Class
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/database/
 */
class CI_DB_odbc_utility extends CI_DB_utility {

	/**
	 * List databases
	 *
	 * @access	private
	 * @return	bool
	 */
	function _list_databases()
	{
		// Not sure if ODBC lets you list all databases...	
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize table query
	 *
	 * Generates a platform-specific query so that a table can be optimized
	 *
	 * @access	private
	 * @param	string	the table name
	 * @return	object
	 */
	function _optimize_table($table)
	{
		// Not a supported ODBC feature	
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Repair table query
	 *
	 * Generates a platform-specific query so that a table can be repaired
	 *
	 * @access	private
	 * @param	string	the table name
	 * @return	object
	 */
	function _repair_table($table)
	{
		// Not a supported ODBC feature	
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * ODBC Export
	 *
	 * @access	private
	 * @param	array	Preferences
	 * @return	mixed
	 */
	function _backup($params = array())
	{
		// Currently unsupported
		return $this->db->display_error('db_unsuported_feature');
	}
	
	/**
	 *
	 * The functions below have been deprecated as of 1.6, and are only here for backwards
	 * compatibility.  They now reside in dbforge().  The use of dbutils for database manipulation
	 * is STRONGLY discouraged in favour if using dbforge.
	 *
	 */

	/**
	 * Create database
	 *
	 * @access	private
	 * @param	string	the database name
	 * @return	bool
	 */
	function _create_database()
	{
		// ODBC has no "create database" command since it's
		// designed to connect to an existing database
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @access	private
	 * @param	string	the database name
	 * @return	bool
	 */
	function _drop_database($name)
	{
		// ODBC has no "drop database" command since it's
		// designed to connect to an existing database		
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}
}

/* End of file odbc_utility.php */
/* Location: ./system/database/drivers/odbc/odbc_utility.php */