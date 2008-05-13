<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------



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
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite_driver extends CI_DB {

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	var $_count_string = "SELECT COUNT(*) AS ";
	var $_random_keyword = ' Random()'; // database specific random keyword

	/**
	 * Non-persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_connect()
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
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_pconnect()
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
	 * Select the database
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_select()
	{
		return TRUE;
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
		// TODO - add support if needed
		return TRUE;
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
		return sqlite_libversion();
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
		return @sqlite_query($this->conn_id, $sql);
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

		$this->simple_query('BEGIN TRANSACTION');
		return TRUE;
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

		$this->simple_query('COMMIT');
		return TRUE;
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

		$this->simple_query('ROLLBACK');
		return TRUE;
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
		return sqlite_escape_string($str);
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
		return sqlite_changes($this->conn_id);
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
		return @sqlite_last_insert_rowid($this->conn_id);
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
	
		$query = $this->query($this->_count_string . $this->_protect_identifiers('numrows'). " FROM " . $this->_protect_identifiers($this->dbprefix.$table));
		
		if ($query->num_rows() == 0)
			return '0';

		$row = $query->row();
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
		$sql = "SELECT name from sqlite_master WHERE type='table'";

		if ($prefix_limit !== FALSE AND $this->dbprefix != '')
		{
			$sql .= " AND 'name' LIKE '".$this->dbprefix."%'";
		}
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function _list_columns($table = '')
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
	 * @access	public
	 * @param	string	the table name
	 * @return	object
	 */
	function _field_data($table)
	{
		return "SELECT * FROM ".$this->_escape_table($table)." LIMIT 1";
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
		return sqlite_error_string(sqlite_last_error($this->conn_id));
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
		return sqlite_last_error($this->conn_id);
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

		// other database drivers use this to add backticks, hence this
		// function is simply going to return the tablename for sqlite		
		return $table;
	}
		
	// --------------------------------------------------------------------

	/**
	 * Protect Identifiers
	 *
	 * This function adds backticks if appropriate based on db type
	 *
	 * @access	private
	 * @param	mixed	the item to escape
	 * @param	boolean	only affect the first word
	 * @return	mixed	the item with backticks
	 */
	function _protect_identifiers($item, $first_word_only = FALSE)
	{
		if (is_array($item))
		{
			$escaped_array = array();

			foreach($item as $k=>$v)
			{
				$escaped_array[$this->_protect_identifiers($k)] = $this->_protect_identifiers($v, $first_word_only);
			}

			return $escaped_array;
		}	

		// This function may get "item1 item2" as a string, and so
		// we may need "item1 item2" and not "item1 item2"
		if (ctype_alnum($item) === FALSE)
		{
			if (strpos($item, '.') !== FALSE)
			{
				$aliased_tables = implode(".",$this->ar_aliased_tables).'.';
				$table_name =  substr($item, 0, strpos($item, '.')+1);
				$item = (strpos($aliased_tables, $table_name) !== FALSE) ? $item = $item : $this->dbprefix.$item;
			}

			// This function may get "field >= 1", and need it to return "field >= 1"
			$lbound = ($first_word_only === TRUE) ? '' : '|\s|\(';

			$item = preg_replace('/(^'.$lbound.')([\w\d\-\_]+?)(\s|\)|$)/iS', '$1$2$3', $item);
		}
		else
		{
			return "{$item}";
		}

		$exceptions = array('AS', '/', '-', '%', '+', '*', 'OR', 'IS');
		
		foreach ($exceptions as $exception)
		{
		
			if (stristr($item, " {$exception} ") !== FALSE)
			{
				$item = preg_replace('/ ('.preg_quote($exception).') /i', ' $1 ', $item);
			}
		}
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
		
		return '('.implode(', ', $tables).')';
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
	function _update($table, $values, $where, $orderby = array(), $limit = FALSE)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
		
		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;
		
		$orderby = (count($orderby) >= 1)?' ORDER BY '.implode(", ", $orderby):'';
	
		$sql = "UPDATE ".$this->_escape_table($table)." SET ".implode(', ', $valstr);
		$sql .= ($where != '' AND count($where) >=1) ? " WHERE ".implode(" ", $where) : '';
		$sql .= $orderby.$limit;
		
		return $sql;
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
		return $this->_delete($table);
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
	function _delete($table, $where = array(), $like = array(), $limit = FALSE)
	{
		$conditions = '';

		if (count($where) > 0 OR count($like) > 0)
		{
			$conditions = "\nWHERE ";
			$conditions .= implode("\n", $this->ar_where);

			if (count($where) > 0 && count($like) > 0)
			{
				$conditions .= " AND ";
			}
			$conditions .= implode("\n", $like);
		}

		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;
	
		return "DELETE FROM ".$table.$conditions.$limit;
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
		if ($offset == 0)
		{
			$offset = '';
		}
		else
		{
			$offset .= ", ";
		}
		
		return $sql."LIMIT ".$offset.$limit;
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
		@sqlite_close($conn_id);
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
		$sql = 'ALTER TABLE '.$this->db->_protect_identifiers($table_name)." RENAME TO ".$this->db->_protect_identifiers($new_table_name);
		return $sql;
	}


}


/* End of file sqlite_driver.php */
/* Location: ./system/database/drivers/sqlite/sqlite_driver.php */