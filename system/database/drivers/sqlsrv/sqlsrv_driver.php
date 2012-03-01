<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
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
 * @since		Version 1.0
 * @filesource
 */

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
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlsrv_driver extends CI_DB {

	public $dbdriver = 'sqlsrv';

	// The character used for escaping
	protected $_escape_char = '';

	// clause and character used for LIKE escape sequences
	protected $_like_escape_str = ' ESCAPE \'%s\' ';
	protected $_like_escape_chr = '!';

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	protected $_count_string = 'SELECT COUNT(*) AS ';
	protected $_random_keyword = ' NEWID()';

	/**
	 * Non-persistent database connection
	 *
	 * @return	resource
	 */
	public function db_connect($pooling = TRUE)
	{
		// Check for a UTF-8 charset being passed as CI's default 'utf8'.
		$character_set = (0 === strcasecmp('utf8', $this->char_set)) ? 'UTF-8' : $this->char_set;

		$connection = array(
			'UID'			=> empty($this->username) ? '' : $this->username,
			'PWD'			=> empty($this->password) ? '' : $this->password,
			'Database'		=> $this->database,
			'ConnectionPooling'	=> $pooling ? 1 : 0,
			'CharacterSet'		=> $character_set,
			'ReturnDatesAsStrings'	=> 1
		);

		// If the username and password are both empty, assume this is a
		// 'Windows Authentication Mode' connection.
		if (empty($connection['UID']) && empty($connection['PWD']))
		{
			unset($connection['UID'], $connection['PWD']);
		}

		return sqlsrv_connect($this->hostname, $connection);
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @return	resource
	 */
	public function db_pconnect()
	{
		return $this->db_connect(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 *
	 * @return	void
	 */
	public function reconnect()
	{
		// Not supported in MSSQL
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @param	string	database name
	 * @return	bool
	 */
	public function db_select($database = '')
	{
		if ($database === '')
		{
			$database = $this->database;
		}

		if ($this->_execute('USE '.$database))
		{
			$this->database = $database;
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	an SQL query
	 * @return	resource
	 */
	protected function _execute($sql)
	{
		return sqlsrv_query($this->conn_id, $this->_prep_query($sql), NULL,
					array('Scrollable' => SQLSRV_CURSOR_STATIC, 'SendStreamParamsAtExec' => TRUE));
	}

	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @param	string	an SQL query
	 * @return	string
	 */
	protected function _prep_query($sql)
	{
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	public function trans_begin($test_mode = FALSE)
	{
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ( ! $this->trans_enabled OR $this->_trans_depth > 0)
		{
			return TRUE;
		}

		// Reset the transaction failure flag.
		// If the $test_mode flag is set to TRUE transactions will be rolled back
		// even if the queries produce a successful result.
		$this->_trans_failure = ($test_mode === TRUE);

		return sqlsrv_begin_transaction($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	public function trans_commit()
	{
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ( ! $this->trans_enabled OR $this->_trans_depth > 0)
		{
			return TRUE;
		}

		return sqlsrv_commit($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	public function trans_rollback()
	{
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ( ! $this->trans_enabled OR $this->_trans_depth > 0)
		{
			return TRUE;
		}

		return sqlsrv_rollback($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @param	string
	 * @param	bool	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	public function escape_str($str, $like = FALSE)
	{
		// Escape single quotes
		return str_replace("'", "''", $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return @sqlrv_rows_affected($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	* Insert ID
	*
	* Returns the last id created in the Identity column.
	*
	* @return	int
	*/
	public function insert_id()
	{
		$query = $this->query('SELECT @@IDENTITY AS insert_id');
		$query = $query->row();
		return $query->insert_id;
	}

	// --------------------------------------------------------------------

	/**
	* Version number query string
	*
	* @return	string
	*/
	protected function _version()
	{
		$info = sqlsrv_server_info($this->conn_id);
		return $info['SQLServerVersion'];
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @param	string
	 * @return	int
	 */
	public function count_all($table = '')
	{
		if ($table == '')
		{
			return 0;
		}

		$query = $this->query('SELECT COUNT(*) AS numrows FROM '.$this->dbprefix.$table);
		if ($query->num_rows() == 0)
		{
			return 0;
		}

		$query = $query->row();
		$this->_reset_select();
		return (int) $query->numrows;
	}

	// --------------------------------------------------------------------

	/**
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		return "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
	}

	// --------------------------------------------------------------------

	/**
	 * List column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return "SELECT * FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '".$this->_escape_table($table)."'";
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @param	string	the table name
	 * @return	object
	 */
	protected function _field_data($table)
	{
		return 'SELECT TOP 1 * FROM '.$this->_escape_table($table);
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @return	string
	 */
	protected function _error_message()
	{
		$error = sqlsrv_errors();
		if ( ! is_array($error))
		{
			return '';
		}

		$error = array_shift($error);
		return isset($error['message']) ? $error['message'] : '';
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @return	string
	 */
	protected function _error_number()
	{
		$error = sqlsrv_errors();
		if ( ! is_array($error))
		{
			return '';
		}
		elseif (isset($error['SQLSTATE']))
		{
			return isset($error['code']) ? $error['SQLSTATE'].'/'.$error['code'] : $error['SQLSTATE'];
		}
		elseif (isset($error['code']))
		{
			return $error['code'];
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Escape Table Name
	 *
	 * This function adds backticks if the table name has a period
	 * in it. Some DBs will get cranky unless periods are escaped
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _escape_table($table)
	{
		return $table;
	}

	/**
	 * Escape the SQL Identifiers
	 *
	 * This function escapes column and table names
	 *
	 * @param	string
	 * @return	string
	 */
	public function _escape_identifiers($item)
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
	 * @param	array
	 * @return	string
	 */
	protected function _from_tables($tables)
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
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _insert($table, $keys, $values)
	{
		return 'INSERT INTO '.$this->_escape_table($table).' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @param	array	the orderby clause
	 * @param	array	the limit clause
	 * @return	string
	 */
	protected function _update($table, $values, $where)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		return 'UPDATE '.$this->_escape_table($table).' SET '.implode(', ', $valstr).' WHERE '.implode(' ', $where);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 * If the database does not support the truncate() command
	 * This function maps to "DELETE FROM table"
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return 'TRUNCATE '.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @param	string	the limit clause
	 * @return	string
	 */
	protected function _delete($table, $where)
	{
		return 'DELETE FROM '.$this->_escape_table($table).' WHERE '.implode(' ', $where);
	}

	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	the sql query string
	 * @param	integer	the number of rows to limit the query to
	 * @param	integer	the offset value
	 * @return	string
	 */
	protected function _limit($sql, $limit, $offset)
	{
		return preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 TOP '.($limit + $offset).' ', $sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @param	resource
	 * @return	void
	 */
	protected function _close($conn_id)
	{
		@sqlsrv_close($conn_id);
	}

}

/* End of file sqlsrv_driver.php */
/* Location: ./system/database/drivers/sqlsrv/sqlsrv_driver.php */
