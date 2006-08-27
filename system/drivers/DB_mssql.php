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
 * MS SQL Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/database/
 */
class CI_DB_mssql extends CI_DB {

	/**
	 * Non-persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_connect()
	{
		return mssql_connect($this->hostname, $this->username, $this->password);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_pconnect()
	{
		return mssql_pconnect($this->hostname, $this->username, $this->password);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_select()
	{
		return @mssql_select_db($this->database, $this->conn_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @access	private called by the base class
	 * @param	string	an SQL query
	 * @return	resource
	 */	
	function execute($sql)
	{
		$sql = $this->_prep_query($sql);
		return @mssql_query($sql, $this->conn_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @access	private called by execute()
	 * @param	string	an SQL query
	 * @return	string
	 */	
    function _prep_query($sql)
    {
		return $sql;
    }
	
	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function escape_str($str)	
	{	
		// Escape single quotes
		return str_replace("'", "''", $str);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @access	public
	 * @param	resource
	 * @return	void
	 */
	function destroy($conn_id)
	{
		mssql_close($conn_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @access	public
	 * @return	integer
	 */
	function affected_rows()
	{
		return @mssql_rows_affected($this->conn_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @access	public
	 * @return	integer
	 */
	function insert_id()
	{
		// Not supported in MS SQL?
		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function count_all($table = '')
	{
		if ($table == '')
			return '0';
	
		$query = $this->query("SELECT COUNT(*) AS numrows FROM `".$this->dbprefix.$table."`");
		
		if ($query->num_rows() == 0)
			return '0';

		$row = $query->row();
		return $row->numrows;
	}
	
	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @access	public
	 * @return	string
	 */
	function error_message()
	{
		// Are errros even supported in MS SQL?
		return '';
	}
	
	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @access	public
	 * @return	integer
	 */
	function error_number()
	{
		// Are error numbers supported?
		return '';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Escape Table Name
	 *
	 * This function adds backticks if the table name has a period
	 * in it. Some DBs will get cranky unless periods are escaped
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function escape_table($table)
	{
		if (stristr($table, '.'))
		{
			$table = preg_replace("/\./", "`.`", $table);
		}
		
		return $table;
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
		$sql = "SELECT TOP 1 FROM ".$this->escape_table($table);
		$query = $this->query($sql);
		return $query->field_data();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	function _insert($table, $keys, $values)
	{	
		return "INSERT INTO ".$this->escape_table($table)." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @return	string
	 */
	function _update($table, $values, $where)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
	
		return "UPDATE ".$this->escape_table($table)." SET ".implode(', ', $valstr)." WHERE ".implode(" ", $where);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @return	string
	 */	
	function _delete($table, $where)
	{
		return "DELETE FROM ".$this->escape_table($table)." WHERE ".implode(" ", $where);
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
		return "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";		
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
		return "SELECT * FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '".$this->escape_table($table)."'";	
	}
	
	// --------------------------------------------------------------------

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
	function _limit($sql, $limit, $offset)
	{
		$i = $limit + $offset;
	
		return preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 TOP '.$i.' ', $sql);		
	}

}



/**
 * MS SQL Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/database/
 */
class CI_DB_mssql_result extends CI_DB_result {
	
	/**
	 * Number of rows in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_rows()
	{
		return @mssql_num_rows($this->result_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_fields()
	{
		return @mssql_num_fields($this->result_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @access	public
	 * @return	array
	 */
	function field_data()
	{
		$retval = array();
		while ($field = mssql_fetch_field($this->result_id))
		{	
			$F 				= new CI_DB_field();
			$F->name 		= $field->name;
			$F->type 		= $field->type;
			$F->max_length	= $field->max_length;
			$F->primary_key = 0;
			$F->default		= '';
			
			$retval[] = $F;
		}
		
		return $retval;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @access	private
	 * @return	array
	 */
	function _fetch_assoc()
	{
		return mssql_fetch_assoc($this->result_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @access	private
	 * @return	object
	 */
	function _fetch_object()
	{
		return mssql_fetch_object($this->result_id);
	}

}

?>