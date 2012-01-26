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
 * SQLite Database Adapter Class
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
class CI_DB_sqlite_driver extends CI_DB {

	public $dbdriver = 'sqlite';

	// The character used to escape with - not needed for SQLite
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
	protected $_random_keyword = ' Random()'; // database specific random keyword

	/**
	 * Non-persistent database connection
	 *
	 * @return	resource
	 */
	public function db_connect()
	{
		if ( ! $conn_id = @sqlite_open($this->database, FILE_WRITE_MODE, $error))
		{
			log_message('error', $error);

			if ($this->db_debug)
			{
				$this->display_error($error, '', TRUE);
			}

			return FALSE;
		}

		return $conn_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @return	resource
	 */
	public function db_pconnect()
	{
		if ( ! $conn_id = @sqlite_popen($this->database, FILE_WRITE_MODE, $error))
		{
			log_message('error', $error);

			if ($this->db_debug)
			{
				$this->display_error($error, '', TRUE);
			}

			return FALSE;
		}

		return $conn_id;
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
		// Not supported in SQLite
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @return	bool
	 */
	public function db_select()
	{
		// Not needed, in SQLite every pseudo-connection is a database
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set client character set
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function db_set_charset($charset, $collation)
	{
		// Not supported
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @return	string
	 */
	public function _version()
	{
		return sqlite_libversion();
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
		$sql = $this->_prep_query($sql);

		if ( ! preg_match('/^(SELECT|EXPLAIN).+$/i', ltrim($sql)))
		{
			return @sqlite_exec($this->conn_id, $sql);
		}

		return @sqlite_query($this->conn_id, $sql);
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

		$this->simple_query('BEGIN TRANSACTION');
		return TRUE;
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

		$this->simple_query('COMMIT');
		return TRUE;
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

		$this->simple_query('ROLLBACK');
		return TRUE;
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
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->escape_str($val, $like);
			}

			return $str;
		}

		$str = sqlite_escape_string($str);

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(array('%', '_', $this->_like_escape_chr),
						array($this->_like_escape_chr.'%', $this->_like_escape_chr.'_', $this->_like_escape_chr.$this->_like_escape_chr),
						$str);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return sqlite_changes($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insert_id()
	{
		return @sqlite_last_insert_rowid($this->conn_id);
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

		$query = $this->query($this->_count_string.$this->_protect_identifiers('numrows').' FROM '.$this->_protect_identifiers($table, TRUE, NULL, FALSE));
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
		$sql = "SELECT name FROM sqlite_master WHERE type='table'";

		if ($prefix_limit !== FALSE && $this->dbprefix != '')
		{
			return $sql." AND 'name' LIKE '".$this->escape_like_str($this->dbprefix)."%' ".sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	the table name
	 * @return	bool
	 */
	protected function _list_columns($table = '')
	{
		// Not supported
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _field_data($table)
	{
		return 'SELECT * FROM '.$table.' LIMIT 1';
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @return	string
	 */
	protected function _error_message()
	{
		return sqlite_error_string(sqlite_last_error($this->conn_id));
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @return	int
	 */
	protected function _error_number()
	{
		return sqlite_last_error($this->conn_id);
	}

	// --------------------------------------------------------------------

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
		if ($this->_escape_char == '')
		{
			return $item;
		}

		foreach ($this->_reserved_identifiers as $id)
		{
			if (strpos($item, '.'.$id) !== FALSE)
			{
				$item = str_replace('.', $this->_escape_char.'.', $item);

				// remove duplicates if the user already included the escape
				return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $this->_escape_char.$item);
			}
		}

		if (strpos($item, '.') !== FALSE)
		{
			$item = str_replace('.', $this->_escape_char.'.'.$this->_escape_char, $item);
		}

		// remove duplicates if the user already included the escape
		return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $this->_escape_char.$item.$this->_escape_char);
	}

	// --------------------------------------------------------------------

	/**
	 * From Tables
	 *
	 * This function implicitly groups FROM tables so there is no confusion
	 * about operator precedence in harmony with SQL standards
	 *
	 * @param	type
	 * @return	type
	 */
	protected function _from_tables($tables)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}

		return '('.implode(', ', $tables).')';
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
		return 'INSERT INTO '.$table.' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
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
	protected function _update($table, $values, $where, $orderby = array(), $limit = FALSE)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		return 'UPDATE '.$table.' SET '.implode(', ', $valstr)
			.(($where != '' && count($where) > 0) ? ' WHERE '.implode(' ', $where) : '')
			.(count($orderby) > 0 ? ' ORDER BY '.implode(', ', $orderby) : '')
			.( ! $limit ? '' : ' LIMIT '.$limit);
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
		return $this->_delete($table);
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
	protected function _delete($table, $where = array(), $like = array(), $limit = FALSE)
	{
		$conditions = '';

		if (count($where) > 0 OR count($like) > 0)
		{
			$conditions = "\nWHERE ".implode("\n", $this->ar_where);

			if (count($where) > 0 && count($like) > 0)
			{
				$conditions .= ' AND ';
			}
			$conditions .= implode("\n", $like);
		}

		return 'DELETE FROM '.$table.$conditions.( ! $limit ? '' : ' LIMIT '.$limit);
	}

	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	the sql query string
	 * @param	int	the number of rows to limit the query to
	 * @param	int	the offset value
	 * @return	string
	 */
	protected function _limit($sql, $limit, $offset)
	{
		return $sql.'LIMIT '.($offset == 0 ? '' : $offset.', ').$limit;
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
		@sqlite_close($conn_id);
	}


}

/* End of file sqlite_driver.php */
/* Location: ./system/database/drivers/sqlite/sqlite_driver.php */
