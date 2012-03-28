<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * SQL Relay Forge Class
 *
 * @category	Database
 * @author		jjang9b
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlrelay_forge extends CI_DB_forge {

    /**
     * Each is matched to a DB Forge Class.
     */

	function __construct()
	{
		// Assign the main database object to $this->db
        $CI =& get_instance();
        $this->db =& $CI->db;

		require_once(BASEPATH.'database/drivers/'.$this->db->dbcase.'/'.$this->db->dbcase.'_forge.php');
		$CI_DB_each_forge = "CI_DB_".$this->db->dbcase."_forge";
		$this->CI_sqlrelay_forge = new $CI_DB_each_forge(); 	
	}

	// --------------------------------------------------------------------

	/**
	 * Create database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */

	function _create_database($name)
	{
		return $this->CI_sqlrelay_forge->_create_database($name);	
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
		return $this->CI_sqlrelay_forge->_drop_database($name);	
	}

	// --------------------------------------------------------------------

	/**
	 * Create Table
	 *
	 * @access	private
	 * @param	string	the table name
	 * @param	array	the fields
	 * @param	mixed	primary key(s)
	 * @param	mixed	key(s)
	 * @param	boolean	should 'IF NOT EXISTS' be added to the SQL
	 * @return	bool
	 */

	function _create_table($table, $fields, $primary_keys, $keys, $if_not_exists)
	{
		return $this->CI_sqlrelay_forge->_create_table($table, $fields, $primary_keys, $keys, $if_not_exists);
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
		return $this->CI_sqlrelay_forge->_drop_table($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Alter table query
	 *
	 * Generates a platform-specific query so that a table can be altered
	 * Called by add_column(), drop_column(), and column_alter(),
	 *
	 * @access	private
	 * @param	string	the ALTER type (ADD, DROP, CHANGE)
	 * @param	string	the column name
	 * @param	string	the table name
	 * @param	string	the column definition
	 * @param	string	the default value
	 * @param	boolean	should 'NOT NULL' be added
	 * @param	string	the field after which we should add the new field
	 * @return	object
	 */

	function _alter_table($alter_type, $table, $column_name, $column_definition = '', $default_value = '', $null = '', $after_field = '')
	{
		return $this->CI_sqlrelay_forge->_alter_table($alter_type, $table, $column_name, $column_definition, $default_value, $nulil, $after_field);	
	}

	// --------------------------------------------------------------------

	/**
	 * Rename a table
	 *
	 * Generates a platform-specific query so that a table can be renamed
	 *
	 * @access	private
	 * @param	string	the old table name
	 * @param	string	the new table name
	 * @return	string
	 */

	function _rename_table($table_name, $new_table_name)
	{
		return $this->CI_sqlrelay_forge->_rename_table($table_name, $new_table_name);
	}

}

/* End of file sqlrelay_forge.php */
/* Location: ./system/database/drivers/sqlrelay/sqlrelay_forge.php */
