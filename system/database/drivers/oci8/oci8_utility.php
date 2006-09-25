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
 * Oracle Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_oci8_utility extends CI_DB_utility {


	/**
	 * Create database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */
	function _create_database($name)
	{
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
		return FALSE;
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
		return FALSE;
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
		return FALSE;
	}

	// --------------------------------------------------------------------

    /**
     * Version number query string
     *
     * @access  public
     * @return  string
     */
    function _version()
    {
        return ociserverversion($this->conn_id);
    }

    // --------------------------------------------------------------------

    /**
     * Show table query
     *
     * Generates a platform-specific query string so that the table names can be fetched
     *
     * @access  public
     * @return  string
     */
    function _show_tables()
    {
        return "select TABLE_NAME FROM ALL_TABLES";
    }

    // --------------------------------------------------------------------

    /**
     * Show columnn query
     *
     * Generates a platform-specific query string so that the column names can be fetched
     *
     * @access  public
     * @param   string  the table name
     * @return  string
     */
    function _show_columns($table = '')
    {
        return "SELECT COLUMN_NAME FROM all_tab_columns WHERE table_name = '$table'";
    }

    // --------------------------------------------------------------------

    /**
     * Field data query
     *
     * Generates a platform-specific query so that the column data can be retrieved
     *
     * @access  public
     * @param   string  the table name
     * @return  object
     */
    function _field_data($table)
    {
		return "SELECT * FROM ".$this->db->_escape_table($table)." where rownum = 1";
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
		return FALSE; // Is this supported in Oracle?
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
		return return FALSE; // Is this supported in Oracle?
	}

}

?>