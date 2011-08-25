<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * SQLSRV Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlsrv_driver extends CI_DB {

	var $dbdriver = 'sqlsrv';

	// The character used for escaping
	var $_escape_char = '';

	// clause and character used for LIKE escape sequences
	var $_like_escape_str = " ESCAPE '%s' ";
	var $_like_escape_chr = '!';

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	var $_count_string = "SELECT COUNT(*) AS ";
	var $_random_keyword = ' ASC'; // not currently supported

	/**
	 * Non-persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */
	function db_connect($pooling = false)
	{
		// Check for a UTF-8 charset being passed as CI's default 'utf8'.
		$character_set = (0 === strcasecmp('utf8', $this->char_set)) ? 'UTF-8' : $this->char_set;

		$connection = array(
			'UID'				=> empty($this->username) ? '' : $this->username,
			'PWD'				=> empty($this->password) ? '' : $this->password,
			'Database'			=> $this->database,
			'ConnectionPooling' => $pooling ? 1 : 0,
			'CharacterSet'		=> $character_set,
			'ReturnDatesAsStrings' => 1
		);
		
		// If the username and password are both empty, assume this is a 
		// 'Windows Authentication Mode' connection.
		if(empty($connection['UID']) && empty($connection['PWD'])) {
			unset($connection['UID'], $connection['PWD']);
		}

		return sqlsrv_connect($this->hostname, $connection);
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
		$this->db_connect(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 *
	 * @access	public
	 * @return	void
	 */
	function reconnect()
	{
		// not implemented in MSSQL
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
		return $this->_execute('USE ' . $this->database);
	}

	// --------------------------------------------------------------------

	/**
	 * Set client character set
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	resource
	 */
	function db_set_charset($charset, $collation)
	{
		// @todo - add support if needed
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @access	private called by the base class
	 * @param	string	an SQL query
	 * @return	resource
	 */
	function _execute($sql)
	{
		$sql = $this->_prep_query($sql);
		return sqlsrv_query($this->conn_id, $sql, null, array(
			'Scrollable'				=> SQLSRV_CURSOR_STATIC,
			'SendStreamParamsAtExec'	=> true
		));
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
	 * Begin Transaction
	 *
	 * @access	public
	 * @return	bool
	 */
	function trans_begin($test_mode = FALSE)
	{
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		// Reset the transaction failure flag.
		// If the $test_mode flag is set to TRUE transactions will be rolled back
		// even if the queries produce a successful result.
		$this->_trans_failure = ($test_mode === TRUE) ? TRUE : FALSE;

		return sqlsrv_begin_transaction($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @access	public
	 * @return	bool
	 */
	function trans_commit()
	{
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		return sqlsrv_commit($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @access	public
	 * @return	bool
	 */
	function trans_rollback()
	{
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		return sqlsrv_rollback($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	function escape_str($str, $like = FALSE)
	{
		// Escape single quotes
		return str_replace("'", "''", $str);
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
		return @sqlrv_rows_affected($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	* Insert ID
	*
	* Returns the last id created in the Identity column.
	*
	* @access public
	* @return integer
	*/
	function insert_id()
	{
		return $this->query('select @@IDENTITY as insert_id')->row('insert_id');
	}

	// --------------------------------------------------------------------

	/**
	* Parse major version
	*
	* Grabs the major version number from the
	* database server version string passed in.
	*
	* @access private
	* @param string $version
	* @return int16 major version number
	*/
	function _parse_major_version($version)
	{
		preg_match('/([0-9]+)\.([0-9]+)\.([0-9]+)/', $version, $ver_info);
		return $ver_info[1]; // return the major version b/c that's all we're interested in.
	}

	// --------------------------------------------------------------------

	/**
	* Version number query string
	*
	* @access public
	* @return string
	*/
	function _version()
	{
		$info = sqlsrv_server_info($this->conn_id);
		return sprintf("select '%s' as ver", $info['SQLServerVersion']);
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
	
		$query = $this->query("SELECT COUNT(*) AS numrows FROM " . $this->dbprefix . $table);
		
		if ($query->num_rows() == 0)
			return '0';

		$row = $query->row();
		$this->_reset_select();
		return $row->numrows;
	}

	// --------------------------------------------------------------------

	/**
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access	private
	 * @param	boolean
	 * @return	string
	 */
	function _list_tables($prefix_limit = FALSE)
	{
		return "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
	}

	// --------------------------------------------------------------------

	/**
	 * List column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access	private
	 * @param	string	the table name
	 * @return	string
	 */
	function _list_columns($table = '')
	{
		return "SELECT * FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '".$this->_escape_table($table)."'";
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
		return "SELECT TOP 1 * FROM " . $this->_escape_table($table);	
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @access	private
	 * @return	string
	 */
	function _error_message()
	{
		$error = array_shift(sqlsrv_errors());
		return !empty($error['message']) ? $error['message'] : null;
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @access	private
	 * @return	integer
	 */
	function _error_number()
	{
		$error = array_shift(sqlsrv_errors());
		return isset($error['SQLSTATE']) ? $error['SQLSTATE'] : null;
	}

	// --------------------------------------------------------------------

	/**
	 * Escape Table Name
	 *
	 * This function adds backticks if the table name has a period
	 * in it. Some DBs will get cranky unless periods are escaped
	 *
	 * @access	private
	 * @param	string	the table name
	 * @return	string
	 */
	function _escape_table($table)
	{
		return $table;
	}	


	/**
	 * Escape the SQL Identifiers
	 *
	 * This function escapes column and table names
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _escape_identifiers($item)
	{
		return $item;
	}

	// --------------------------------------------------------------------

	/**
	 * From Tables
	 *
	 * This function implicitly groups FROM tables so there is no confusion
	 * about operator precedence in harmony with SQL standards
	 *
	 * @access	public
	 * @param	type
	 * @return	type
	 */
	function _from_tables($tables)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}

		return implode(', ', $tables);
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
		return "INSERT INTO ".$this->_escape_table($table)." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
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
	 * @param	array	the orderby clause
	 * @param	array	the limit clause
	 * @return	string
	 */
	function _update($table, $values, $where)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
	
		return "UPDATE ".$this->_escape_table($table)." SET ".implode(', ', $valstr)." WHERE ".implode(" ", $where);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 * If the database does not support the truncate() command
	 * This function maps to "DELETE FROM table"
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function _truncate($table)
	{
		return "TRUNCATE ".$table;
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
	 * @param	string	the limit clause
	 * @return	string
	 */
	function _delete($table, $where)
	{
		return "DELETE FROM ".$this->_escape_table($table)." WHERE ".implode(" ", $where);
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

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @access	public
	 * @param	resource
	 * @return	void
	 */
	function _close($conn_id)
	{
		@sqlsrv_close($conn_id);
	}

}



/* End of file mssql_driver.php */
/* Location: ./system/database/drivers/mssql/mssql_driver.php */