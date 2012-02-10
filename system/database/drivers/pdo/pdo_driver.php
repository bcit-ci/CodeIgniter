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
 * @since		Version 2.1.0
 * @filesource
 */

/**
 * PDO Database Adapter Class
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
class CI_DB_pdo_driver extends CI_DB {

	public $dbdriver = 'pdo';
	public $dsn;

	// the character used to excape - not necessary for PDO
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
	protected $_random_keyword;

	// PDO-specific properties
	protected $_pdo_driver;
	protected $_pdo_options = array();

	public function __construct($params)
	{
		parent::__construct($params);

		if (preg_match('/([^;]+):/', $this->dsn, $match) && count($match) === 2)
		{
			// If there is a valid DSN string found - we're done.
			// This is for general PDO users, who tend to have a full DSN string.
			$this->_pdo_driver = end($match);
		}
		else
		{
			// Try to build a complete DSN string from params
			$this->_connect_string($params);
		}

		// clause and character used for LIKE escape sequences
		// this one depends on the driver being used
		if ($this->_pdo_driver === 'mysql')
		{
			$this->_like_escape_str = $this->_like_escape_str = '';
		}
		elseif ($this->_pdo_driver === 'odbc')
		{
			$this->_like_escape_str = ' {escape \'%s\'} ';
		}

		$this->_random_keyword = ' RND('.time().')';
		$this->trans_enabled = FALSE;
	}

	/**
	 * Connection String
	 *
	 * @param	array
	 * @return	void
	 */
	// FIX HERE
	protected function _connect_string($params)
	{
		if (strpos($this->hostname, ':'))
		{
			// hostname generally would have this prototype
			// $db['hostname'] = '_pdo_driver:host(/Server(/DSN))=hostname(/DSN);';
			// We need to get the prefix (_pdo_driver used by PDO).
			$this->dsn = $this->hostname;
			$this->_pdo_driver = substr($this->hostname, 0, strpos($this->hostname, ':'));
		}
		else
		{
			// Invalid DSN, display an error
			if ( ! array_key_exists('_pdo_driver', $params))
			{
				show_error('Invalid DB Connection String for PDO');
			}

			// Assuming that the following DSN string format is used:
			// $dsn = 'pdo://username:password@hostname:port/database?_pdo_driver=pgsql';
			$this->dsn = $this->_pdo_driver.':';

			// Add hostname to the DSN for databases that need it
			if ( ! empty($this->hostname) && in_array($this->_pdo_driver, array('informix', 'mysql', 'pgsql', 'sybase', 'mssql', 'dblib', 'cubrid')))
			{
			    $this->dsn .= 'host='.$this->hostname.';';
			}

			// Add a port to the DSN for databases that can use it
			if ( ! empty($this->port) && in_array($this->_pdo_driver, array('informix', 'mysql', 'pgsql', 'ibm', 'cubrid')))
			{
			    $this->dsn .= 'port='.$this->port.';';
			}
		}

		// Add the database name to the DSN, if needed
	    if (stripos($this->dsn, 'dbname') === FALSE 
	       && in_array($this->_pdo_driver, array('4D', 'pgsql', 'mysql', 'firebird', 'sybase', 'mssql', 'dblib', 'cubrid')))
	    {
	        $this->dsn .= 'dbname='.$this->database.';';
	    }
	    elseif (stripos($this->dsn, 'database') === FALSE && in_array($this->_pdo_driver, array('ibm', 'sqlsrv')))
	    {
	    	if (stripos($this->dsn, 'dsn') === FALSE)
	    	{
		        $this->dsn .= 'database='.$this->database.';';
	    	}
	    }
	    elseif ($this->_pdo_driver === 'sqlite' && $this->dsn === 'sqlite:')
	    {
	        if ($this->database !== ':memory')
	        {
	            if ( ! file_exists($this->database))
	            {
	                show_error('Invalid DB Connection string for PDO SQLite');
	            }

	            $this->dsn .= (strpos($this->database, DIRECTORY_SEPARATOR) !== 0) ? DIRECTORY_SEPARATOR : '';
	        }

	        $this->dsn .= $this->database;
	    }

	    // Add charset to the DSN, if needed
	    if ( ! empty($this->char_set) && in_array($this->_pdo_driver, array('4D', 'mysql', 'sybase', 'mssql', 'dblib', 'oci')))
	    {
	        $this->dsn .= 'charset='.$this->char_set.';';
	    }
	}

	/**
	 * Non-persistent database connection
	 *
	 * @return	object
	 */
	public function db_connect()
	{
		return $this->_pdo_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @return	object
	 */
	public function db_pconnect()
	{
		return $this->_pdo_connect(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * PDO connection
	 *
	 * @param	bool
	 * @return	object
	 */
	protected function _pdo_connect($persistent = FALSE)
	{
		$this->_pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;

		$persistent == FALSE OR $this->_pdo_options[PDO::ATTR_PERSISTENT] = TRUE;

		// Refer: http://php.net/manual/en/ref.pdo-mysql.connection.php
		// FIX HERE
		if ($this->_pdo_driver === 'mysql' && is_php('5.3.6'))
		{
			$this->_pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES $this->char_set COLLATE '$this->dbcollat'";
		}

		// Connecting...
		try 
		{
			$db = new PDO($this->dsn, $this->username, $this->password, $this->_pdo_options);
		} 
		catch (PDOException $e) 
		{
			if ($this->db_debug && empty($this->failover))
			{
				$this->display_error($e->getMessage(), '', TRUE);
			}

			return FALSE;
		}

		return $db;
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
		return ($this->db_debug) ? $this->db->display_error('db_unsuported_feature') : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @return	bool
	 */
	public function db_select()
	{
		// Not needed for PDO
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
		// This is done upon connect
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
		return $this->conn_id->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	an SQL query
	 * @return	object
	 */
	protected function _execute($sql)
	{
		$sql = $this->_prep_query($sql);
		$result_id = $this->conn_id->query($sql);

		// FIX HERE: move to affected_rows()
		if (is_object($result_id))
		{
			$this->affect_rows = $result_id->rowCount();
		}
		else
		{
			$this->affect_rows = 0;
		}

		return $result_id;
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
		// Change the backtick(s) for Postgre and/or SQLite
		if ($this->_pdo_driver === 'pgsql')
		{
			return str_replace('`', '"', $sql);
		}
		elseif ($this->_pdo_driver === 'sqlite')
		{
			return str_replace('`', '', $sql);
		}

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

		return $this->conn_id->beginTransaction();
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

		return $this->conn->commit();
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

		return $this->conn_id->rollBack();
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

		// Escape the string
		$str = $this->conn_id->quote($str);

		// If there are duplicated quotes - trim them away
		if (strpos($str, "'") === 0)
		{
			$str = substr($str, 1, -1);
		}

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(array('%', '_', $this->_like_escape_chr),
						array($this->_like_escape_chr.'%',
						      $this->_like_escape_chr.'_',
						      $this->_like_escape_chr.$this->_like_escape_chr
						),
						$str
				);
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
		return $this->affect_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	// FIX HERE
	public function insert_id($name = NULL)
	{
		if ($this->_pdo_driver === 'pgsql')
		{
			// Convenience method for Postgre's last insert id
			$v = $this->_version();

			if ($name === NULL && $this->_version >= '8.1')
			{
				$sql = 'SELECT LASTVAL() as ins_id';
			}

			$query = $this->query($sql);
			$row   = $query->row();

			return $row->ins_id;
		}

		return $this->conn_id->lastInsertId($name);
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @param	string
	 * @return	string
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
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		if ($this->_pdo_driver === 'pgsql')
		{
			// Analog function to show all tables in Postgre
			$sql = "SELECT * FROM information_schema.tables WHERE table_schema = 'public'";
		}
		else
		{
			$sql = 'SHOW TABLES FROM `'.$this->database.'`';
		}

		return ($prefix_limit !== FALSE && $this->dbprefix != '') ? FALSE : $sql;
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
		return 'SHOW COLUMNS FROM '.$this->_from_tables($table);
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
	// FIX HERE
	protected function _field_data($table)
	{
		return 'SELECT TOP 1 FROM '.$this->_from_tables($table);
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @return	string
	 */
	protected function _error_message()
	{
		$error_array = $this->conn_id->errorInfo();
		return $error_array[2];
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @return	string
	 */
	protected function _error_number()
	{
		return $this->conn_id->errorCode();
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
			$item  = str_replace('.', $this->_escape_char.'.'.$this->_escape_char, $item);
		}

		// remove duplicates if the user already included the escape
		return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $this->_escape_char.$str.$this->_escape_char);
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

		return (count($tables) === 1) ? '`'.$tables[0].'`' : '('.implode(', ', $tables).')';
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
		return 'INSERT INTO '.$this->_from_tables($table).' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * Insert_batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param   string  the table name
	 * @param   array   the insert keys
	 * @param   array   the insert values
	 * @return  string
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		return 'INSERT INTO '.$this->_from_tables($table).' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
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

		return 'UPDATE '.$this->_from_tables($table).' SET '.implode(', ', $valstr)
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
		$ids   = array();
		foreach ($values as $key => $val)
		{
			$ids[] = $val[$index];

			foreach (array_keys($val) as $field)
			{
				if ($field != $index)
				{
					$final[$field][] =  'WHEN '.$index.' = '.$val[$index].' THEN '.$val[$field];
				}
			}
		}

		$cases = '';
		foreach ($final as $k => $v)
		{
			$cases .= $k." = CASE \n".implode("\n", $v)."\n"
				.'ELSE '.$k.' END, ';
		}

		return 'UPDATE'.$this->_from_tables($table).' SET '
			.substr($cases, 0, -2)
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
			$conditions = "\nWHERE ".implode("\n", $this->ar_where)
					.((count($where) > 0 && count($like) > 0) ? ' AND ' : '')
					.implode("\n", $like);
		}

		return 'DELETE FROM '.$this->_from_tables($table).$conditions.( ! $limit ? '' : ' LIMIT '.$limit);
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
		if ($this->_pdo_driver === 'cubrid' OR $this->_pdo_driver === 'sqlite')
		{
			return $sql.' LIMIT'.($offset == 0 ? '' : $offset.', ').$limit;
		}

		return $sql.' LIMIT '.$limit.($offset == 0 ? '' : ' OFFSET '.$offset);
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @param	object
	 * @return	void
	 */
	protected function _close($conn_id)
	{
		$this->conn_id = NULL;
	}

}

/* End of file pdo_driver.php */
/* Location: ./system/database/drivers/pdo/pdo_driver.php */
