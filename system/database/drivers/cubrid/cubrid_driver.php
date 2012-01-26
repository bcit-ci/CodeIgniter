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
 * @since		Version 2.0.2
 * @filesource
 */

/**
 * CUBRID Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		Esen Sagynov
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_cubrid_driver extends CI_DB {

	public $dbdriver = 'cubrid';

	// The character used for escaping - no need in CUBRID
	protected $_escape_char = '';

	// clause and character used for LIKE escape sequences - not used in CUBRID
	protected $_like_escape_str = '';
	protected $_like_escape_chr = '';

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	protected $_count_string = 'SELECT COUNT(*) AS ';
	protected $_random_keyword = ' RAND()'; // database specific random keyword

	public function __construct()
	{
		// If no port is defined by the user, use the default value
		if ($this->port == '')
		{
			// Default CUBRID broker port
			$this->port = 33000;
		}
	}

	/**
	 * Non-persistent database connection
	 *
	 * @return	resource
	 */
	public function db_connect()
	{
		$conn = cubrid_connect($this->hostname, $this->port, $this->database, $this->username, $this->password);

		if ($conn)
		{
			// Check if a user wants to run queries in dry, i.e. run the
			// queries but not commit them.
			if (isset($this->auto_commit) && ! $this->auto_commit)
			{
				cubrid_set_autocommit($conn, CUBRID_AUTOCOMMIT_FALSE);
			}
			else
			{
				cubrid_set_autocommit($conn, CUBRID_AUTOCOMMIT_TRUE);
				$this->auto_commit = TRUE;
			}
		}

		return $conn;
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 * In CUBRID persistent DB connection is supported natively in CUBRID
	 * engine which can be configured in the CUBRID Broker configuration
	 * file by setting the CCI_PCONNECT parameter to ON. In that case, all
	 * connections established between the client application and the
	 * server will become persistent. This is calling the same
	 * @cubrid_connect function will establish persisten connection
	 * considering that the CCI_PCONNECT is ON.
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */
	public function db_pconnect()
	{
		return $this->db_connect();
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
		if (cubrid_ping($this->conn_id) === FALSE)
		{
			$this->conn_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @return	resource
	 */
	public function db_select()
	{
		// In CUBRID there is no need to select a database as the database
		// is chosen at the connection time.
		// So, to determine if the database is "selected", all we have to
		// do is ping the server and return that value.
		return cubrid_ping($this->conn_id);
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
		// In CUBRID, there is no need to set charset or collation.
		// This is why returning true will allow the application continue
		// its normal process.
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @return	string
	 */
	protected function _version()
	{
		return cubrid_get_server_info($this->conn_id);
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
		return @cubrid_query($sql, $this->conn_id);
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
		// No need to prepare
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

		if (cubrid_get_autocommit($this->conn_id))
		{
			cubrid_set_autocommit($this->conn_id, CUBRID_AUTOCOMMIT_FALSE);
		}

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

		cubrid_commit($this->conn_id);

		if ($this->auto_commit && ! cubrid_get_autocommit($this->conn_id))
		{
			cubrid_set_autocommit($this->conn_id, CUBRID_AUTOCOMMIT_TRUE);
		}

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

		cubrid_rollback($this->conn_id);

		if ($this->auto_commit && ! cubrid_get_autocommit($this->conn_id))
		{
			cubrid_set_autocommit($this->conn_id, CUBRID_AUTOCOMMIT_TRUE);
		}

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

		if (function_exists('cubrid_real_escape_string') && is_resource($this->conn_id))
		{
			$str = cubrid_real_escape_string($str, $this->conn_id);
		}
		else
		{
			$str = addslashes($str);
		}

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(array('%', '_'), array('\\%', '\\_'), $str);
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
		return @cubrid_affected_rows($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insert_id()
	{
		return @cubrid_insert_id($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified table
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
	 * @param	boolean
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = 'SHOW TABLES';

		if ($prefix_limit !== FALSE && $this->dbprefix != '')
		{
			return $sql." LIKE '".$this->escape_like_str($this->dbprefix)."%'";
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
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return 'SHOW COLUMNS FROM '.$this->_protect_identifiers($table, TRUE, NULL, FALSE);
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
		return cubrid_error($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @return	int
	 */
	protected function _error_number()
	{
		return cubrid_errno($this->conn_id);
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
	 * @param	string	the table name
	 * @return	string
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
		return 'INSERT INTO '.$table.' ("'.implode('", "', $keys).'") VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------


	/**
	 * Replace statement
	 *
	 * Generates a platform-specific replace string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _replace($table, $keys, $values)
	{
		return 'REPLACE INTO '.$table.' ("'.implode('", "', $keys).'") VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * Insert_batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		return 'INSERT INTO '.$table.' ("'.implode('", "', $keys).'") VALUES '.implode(', ', $values);
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
			$valstr[] = sprintf('"%s" = %s', $key, $val);
		}

		return 'UPDATE '.$table.' SET '.implode(', ', $valstr)
			.(($where != '' && count($where) > 0) ? ' WHERE '.implode(' ', $where) : '')
			.(count($orderby) > 0 ? ' ORDER BY '.implode(', ', $orderby) : '')
			.( ! $limit ? '' : ' LIMIT '.$limit);
	}

	// --------------------------------------------------------------------


	/**
	 * Update_Batch statement
	 *
	 * Generates a platform-specific batch update string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @return	string
	 */
	protected function _update_batch($table, $values, $index, $where = NULL)
	{
		$ids = array();
		$where = ($where != '' && count($where) > 0) ? implode(' ', $where).' AND ' : '';

		foreach ($values as $key => $val)
		{
			$ids[] = $val[$index];

			foreach (array_keys($val) as $field)
			{
				if ($field != $index)
				{
					$final[$field][] = 'WHEN '.$index.' = '.$val[$index].' THEN '.$val[$field];
				}
			}
		}

		$sql = 'UPDATE '.$table.' SET ';
		$cases = '';

		foreach ($final as $k => $v)
		{
			$cases .= $k." = CASE \n"
				.implode("\n", $v)
				.'ELSE '.$k.' END, ';
		}

		return $sql.substr($cases, 0, -2)
			.' WHERE '.(($where != '' && count($where) > 0) ? implode(' ', $where).' AND ' : '')
			.$index.' IN ('.implode(',', $ids).')';
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
		return $sql.'LIMIT '.($offset == 0 ? '' : ', ').$limit;
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
		@cubrid_close($conn_id);
	}

}

/* End of file cubrid_driver.php */
/* Location: ./system/database/drivers/cubrid/cubrid_driver.php */
