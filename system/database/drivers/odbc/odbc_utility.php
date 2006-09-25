<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ODBC Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/database/
 */
class CI_DB_odbc_utility extends CI_DB_utility {


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
		if ($this->db_debug)
		{
			return $this->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */
	function drop_database($name)
	{
		// ODBC has no "drop database" command since it's 
		// designed to connect to an existing database		
		if ($this->db_debug)
		{
			return $this->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * List databases
	 *
	 * @access	public
	 * @return	bool
	 */
	function list_databases()
	{
		// Not sure if ODBC lets you list all databases...	
		if ($this->db_debug)
		{
			return $this->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @access	public
	 * @return	bool
	 */
	function drop_table($table)
	{
		// Not a supported ODBC feature	
		if ($this->db_debug)
		{
			return $this->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @access	public
	 * @return	string
	 */
	function _version()
	{
		return "SELECT version() AS ver";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access	public
	 * @return	string
	 */
	function _show_tables()
	{
		return "SHOW TABLES FROM `".$this->db->database."`";		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Show columnn query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function _show_columns($table = '')
	{
		return "SHOW COLUMNS FROM ".$this->db->_escape_table($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	object
	 */
	function _field_data($table)
	{
		return "SELECT TOP 1 FROM ".$this->db->_escape_table($table);
	}



}

?>