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
 * Postgre Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_postgre_utility extends CI_DB_utility {

	
	/**
	 * Create database
	 *
	 * @access	private
	 * @param	string	the database name
	 * @return	bool
	 */
	function _create_database($name)
	{
		return "CREATE DATABASE ".$name;
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
		return "DROP DATABASE ".$name;
	}

	// --------------------------------------------------------------------

	/**
	 * List databases
	 *
	 * @access	private
	 * @return	bool
	 */
	function _list_databases()
	{
		return "SELECT datname FROM pg_database";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @access	private
	 * @return	bool
	 */
	function _drop_table($table)
	{
		return "DROP TABLE ".$this->db->_escape_table($name)." CASCADE";
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
		return "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";	
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
		return "SELECT column_name FROM information_schema.columns WHERE table_name ='".$this->db->_escape_table($table)."'"; 	
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
		return "SELECT * FROM ".$this->db->_escape_table($table)." LIMIT 1";
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
		return FALSE; // Is this supported in Postgre?
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
		return return FALSE; // Is this supported in Postgre?
	}


}

?>