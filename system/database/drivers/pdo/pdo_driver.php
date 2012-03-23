<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
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

	// the character used to excape
	public $_escape_char = '"';

	// clause and character used for LIKE escape sequences
	protected $_like_escape_str;
	protected $_like_escape_chr;

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() public functions.
	 */
	protected $_count_string = 'SELECT COUNT(*) AS ';
	protected $_random_keyword;

	// need to track the pdo driver and options
	public $pdodriver;
	public $options = array();
	protected $driver;

	public function __construct($params)
	{
		parent::__construct($params);

		if (preg_match('/([^;]+):/', $this->dsn, $match) && count($match) == 2)
		{
			// If there is a minimum valid dsn string pattern found, we're done
			// This is for general PDO users, who tend to have a full DSN string.
			$this->pdodriver = end($match);
		}
		else
		{
			// Try to build a complete DSN string from params
			if (strpos($this->hostname, ':'))
			{
				// hostname generally would have this prototype
				// $db['hostname'] = 'pdodriver:host(/Server(/DSN))=hostname(/DSN);';
				// We need to get the prefix (pdodriver used by PDO).
				$dsnarray = explode(':', $this->hostname);
				$this->pdodriver = $dsnarray[0];

				// Extract the hostname from the partial dsn
				$this->hostname = preg_replace('`(host|server)=`', '', $dsnarray[1]);
			}
			else
			{
				// Invalid DSN, display an error
				if ( ! array_key_exists('pdodriver', $params))
				{
					show_error('Invalid DB Connection String for PDO');
				}
			}
		}

		// clause and character used for LIKE escape sequences
		// this one depends on the driver being used
		if ($this->pdodriver == 'mysql')
		{
			$this->_like_escape_str = '';
			$this->_like_escape_chr = '';
		}
		elseif ($this->pdodriver == 'odbc')
		{
			$this->_like_escape_str = " {escape '%s'} ";
			$this->_like_escape_chr = '!';
		}
		else
		{
			$this->_like_escape_str = " ESCAPE '%s' ";
			$this->_like_escape_chr = '!';
		}

		$this->trans_enabled = FALSE;
		$this->_random_keyword = ' RND('.time().')'; // database specific random keyword
	}

	// --------------------------------------------------------------------------

	/**
	 * Non-persistent database connection
	 *
	 * @return	object
	 */
	public function db_connect()
	{
		$this->options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;

		return $this->pdo_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @return	object
	 */
	public function db_pconnect()
	{
		$this->options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;
		$this->options[PDO::ATTR_PERSISTENT] = TRUE;

		return $this->pdo_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * Load the sub driver and connect to the database
	 *
	 * @return	object
	 */
	protected function pdo_connect()
	{
		// Load the sub driver for database-specific stuff
		$driver = strtolower($this->pdodriver);

		// So many libraries for connecting to the same database!
		// Since SQL Server is a fork of Sybase, this should cover
		// a lot of bases with one sub-driver
		if (in_array($driver, array('dblib','mssql','sybase','sqlsrv')))
		{
			$driver = 'sqlsrv';
		}

		$file = dirname(__FILE__)."/sub_drivers/{$driver}.php";

		// Fallback to odbc if a non-existant or unimplemented
		// pdo driver is specified
		if (file_exists($file))
		{
			require($file);
		}
		else
		{
			$driver = 'odbc';
			require(dirname(__FILE__).'/sub_drivers/odbc.php');
		}

		// Instantiate the sub-driver, and
		// return the connection object
		$driver_class = "{$driver}_PDO_Driver";
		$this->driver = new $driver_class($this);

		return $this->conn_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @return	resource
	 */
	public function db_select()
	{
		// Not needed for PDO
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Database version number
	 *
	 * @return	string
	 */
	public function version()
	{
		return isset($this->data_cache['version'])
			? $this->data_cache['version']
			: $this->data_cache['version'] = $this->conn_id->getAttribute(PDO::ATTR_SERVER_VERSION);
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	an SQL query
	 * @return	mixed
	 */
	protected function _execute($sql)
	{
		$sql = $this->_prep_query($sql);

		$result_id = $this->conn_id->query($sql);

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
		return $this->driver->prep_query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	public function trans_begin($test_mode = FALSE)
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
		$this->_trans_failure = (bool) ($test_mode === TRUE);

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
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		$ret = $this->conn->commit();

		return $ret;
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	public function trans_rollback()
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

		$ret = $this->conn_id->rollBack();

		return $ret;
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

		//Escape the string
		$str = $this->conn_id->quote($str);

		//If there are duplicated quotes, trim them away
		if (strpos($str, "'") === 0)
		{
			$str = substr($str, 1, -1);
		}

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(array($this->_like_escape_chr, '%', '_'),
						array($this->_like_escape_chr.$this->_like_escape_chr, $this->_like_escape_chr.'%', $this->_like_escape_chr.'_'),
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
		return $this->affect_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insert_id($name = NULL)
	{
		if (method_exists($this->driver, 'insert_id'))
		{
			return $this->driver->insert_id($name);
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

		$sql = $this->_count_string.$this->protect_identifiers('numrows')
			.' FROM '.$this->protect_identifiers($table, TRUE, NULL, FALSE);
		
		$query = $this->query($sql);

		if ($query->num_rows() == 0)
		{
			return 0;
		}

		$row = $query->row();
		$this->_reset_select();

		return (int) $row->numrows;
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
		return $this->driver->list_tables();
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
	protected function _field_data($table)
	{
		return $this->driver->field_data($this->_from_tables($table));
	}

	// --------------------------------------------------------------------

	/**
	 * Error
	 *
	 * Returns an array containing code and message of the last
	 * database error that has occured.
	 *
	 * @return	array
	 */
	public function error()
	{
		$error = array('code' => '00000', 'message' => '');
		$pdo_error = $this->conn_id->errorInfo();

		if (empty($pdo_error[0]))
		{
			return $error;
		}

		$error['code'] = isset($pdo_error[1]) ? $pdo_error[0].'/'.$pdo_error[1] : $pdo_error[0];
		if (isset($pdo_error[2]))
		{
			 $error['message'] = $pdo_error[2];
		}

		return $error;
	}

	// --------------------------------------------------------------------

	/**
	 * Escape the SQL Identifiers
	 *
	 * This public function escapes column and table names
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
				$str = $this->_escape_char. str_replace('.', $this->_escape_char.'.', $item);

				// remove duplicates if the user already included the escape
				return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $str);
			}
		}

		if (strpos($item, '.') !== FALSE)
		{
			$str  = $this->_escape_char.str_replace('.', $this->_escape_char.'.'.$this->_escape_char, $item);
			$str .= $this->_escape_char;
		}
		else
		{
			$str = $this->_escape_char.$item.$this->_escape_char;
		}

		// remove duplicates if the user already included the escape
		return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $str);
	}

	// --------------------------------------------------------------------

	/**
	 * From Tables
	 *
	 * This public function implicitly groups FROM tables so there is no confusion
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

		return (count($tables) == 1) ? $tables[0] : '('.implode(', ', $tables).')';
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
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
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
			$valstr[] = $key." = ".$val;
		}

		$limit   = ( ! $limit) ? '' : ' LIMIT '.$limit;
		$orderby = (count($orderby) >= 1) ? ' ORDER BY '.implode(', ', $orderby) : '';

		$sql  = 'UPDATE '.$this->_from_tables($table).' SET '.implode(', ', $valstr);
		$sql .= ($where != '' && count($where) >= 1) ? ' WHERE '.implode(' ', $where) : '';
		$sql .= $orderby.$limit;

		return $sql;
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
		$where = ($where != '' && count($where) >=1) ? implode(" ", $where).' AND ' : '';

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

		$sql   = 'UPDATE '.$this->_from_tables($table).' SET ';
		$cases = '';

		foreach ($final as $k => $v)
		{
			$cases .= $k.' = CASE '."\n";

			foreach ($v as $row)
			{
				$cases .= $row."\n";
			}

			$cases .= 'ELSE '.$k.' END, ';
		}

		$sql .= substr($cases, 0, -2);
		$sql .= ' WHERE '.$where.$index.' IN ('.implode(',', $ids).')';

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 * If the database does not support the truncate() command
	 * This public function maps to "DELETE FROM table"
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
			$conditions  = "\nWHERE ";
			$conditions .= implode("\n", $this->ar_where);

			if (count($where) > 0 && count($like) > 0)
			{
				$conditions .= " AND ";
			}

			$conditions .= implode("\n", $like);
		}

		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;

		return 'DELETE FROM '.$this->_from_tables($table).$conditions.$limit;
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
	public function _limit($sql, $limit, $offset)
	{
		return $this->driver->limit($sql, $limit, $offset);
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
		$this->conn_id = null;
	}

}

/* End of file pdo_driver.php */
/* Location: ./system/database/drivers/pdo/pdo_driver.php */