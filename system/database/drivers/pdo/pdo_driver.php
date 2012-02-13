<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

// ------------------------------------------------------------------------

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

	var $dbdriver = 'pdo';

	// the character used to excape - not necessary for PDO
	var $_escape_char = '';

	// clause and character used for LIKE escape sequences
	var $_like_escape_str;
	var $_like_escape_chr;

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	var $_count_string = "SELECT COUNT(*) AS ";
	var $_random_keyword;

	// need to track the pdo DSN, driver and options
	var $dsn;
	var $pdodriver;
	var $options = array();

	function __construct($params)
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
			$this->_connect_string($params);
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
		
		$this->trans_enabled   = FALSE;
		$this->_random_keyword = ' RND('.time().')'; // database specific random keyword
	}

	/**
	 * Connection String
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _connect_string($params)
	{
		if (strpos($this->hostname, ':'))
		{
			// hostname generally would have this prototype
			// $db['hostname'] = 'pdodriver:host(/Server(/DSN))=hostname(/DSN);';
			// We need to get the prefix (pdodriver used by PDO).
			$dsnarray = explode(':', $this->hostname);
			$this->pdodriver = $dsnarray[0];

			// End dsn with a semicolon for extra backward compability
			// if database property was not empty.
			if ( ! empty($this->database))
			{
				$this->dsn .= rtrim($this->hostname, ';').';';
			}
		}
		else
		{
			// Invalid DSN, display an error
			if ( ! array_key_exists('pdodriver', $params))
			{
				show_error('Invalid DB Connection String for PDO');
			}

			// Assuming that the following DSN string format is used:
			// $dsn = 'pdo://username:password@hostname:port/database?pdodriver=pgsql';
			$this->dsn = $this->pdodriver.':';

			// Add hostname to the DSN for databases that need it
			if ( ! empty($this->hostname) 
				&& strpos($this->hostname, ':') === FALSE
				&& in_array($this->pdodriver, array('informix', 'mysql', 'pgsql', 'sybase', 'mssql', 'dblib', 'cubrid')))
			{
			    $this->dsn .= 'host='.$this->hostname.';';
			}

			// Add a port to the DSN for databases that can use it
			if ( ! empty($this->port) && in_array($this->pdodriver, array('informix', 'mysql', 'pgsql', 'ibm', 'cubrid')))
			{
			    $this->dsn .= 'port='.$this->port.';';
			}
		}

		// Add the database name to the DSN, if needed
	    if (stripos($this->dsn, 'dbname') === FALSE 
	       && in_array($this->pdodriver, array('4D', 'pgsql', 'mysql', 'firebird', 'sybase', 'mssql', 'dblib', 'cubrid')))
	    {
	        $this->dsn .= 'dbname='.$this->database.';';
	    }
	    elseif (stripos($this->dsn, 'database') === FALSE && in_array($this->pdodriver, array('ibm', 'sqlsrv')))
	    {
	    	if (stripos($this->dsn, 'dsn') === FALSE)
	    	{
		        $this->dsn .= 'database='.$this->database.';';
	    	}
	    }
	    elseif ($this->pdodriver === 'sqlite' && $this->dsn === 'sqlite:')
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
	    if ( ! empty($this->char_set) && in_array($this->pdodriver, array('4D', 'mysql', 'sybase', 'mssql', 'dblib', 'oci')))
	    {
	        $this->dsn .= 'charset='.$this->char_set.';';
	    }
	}

	/**
	 * Non-persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */
	function db_connect()
	{
		$this->options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;

		return $this->pdo_connect();
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
		$this->options[PDO::ATTR_ERRMODE]    = PDO::ERRMODE_SILENT;
		$this->options[PDO::ATTR_PERSISTENT] = TRUE;
	
		return $this->pdo_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * PDO connection
	 *
	 * @access	private called by the PDO driver class
	 * @return	resource
	 */
	function pdo_connect()
	{
		// Refer : http://php.net/manual/en/ref.pdo-mysql.connection.php
		if ($this->pdodriver == 'mysql' && is_php('5.3.6'))
		{
			$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES $this->char_set COLLATE '$this->dbcollat'";
		}

		// Connecting...
		try 
		{
			$db = new PDO($this->dsn, $this->username, $this->password, $this->options);
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
	 * @access	public
	 * @return	void
	 */
	function reconnect()
	{
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}

		return FALSE;
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
		// Not needed for PDO
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
		return $this->conn_id->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @access	private called by the base class
	 * @param	string	an SQL query
	 * @return	object
	 */
	function _execute($sql)
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
	 * @access	private called by execute()
	 * @param	string	an SQL query
	 * @return	string
	 */
	function _prep_query($sql)
	{
		if ($this->pdodriver === 'pgsql')
		{
			// Change the backtick(s) for Postgre
			$sql = str_replace('`', '"', $sql);
		}
		elseif ($this->pdodriver === 'sqlite')
		{
			// Change the backtick(s) for SQLite
			$sql = str_replace('`', '', $sql);
		}

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
		$this->_trans_failure = (bool) ($test_mode === TRUE);

		return $this->conn_id->beginTransaction();
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

		$ret = $this->conn->commit();
		
		return $ret;
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

		$ret = $this->conn_id->rollBack();

		return $ret;
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
			$str = str_replace(	array('%', '_', $this->_like_escape_chr),
								array($this->_like_escape_chr.'%', 
								      $this->_like_escape_chr.'_', 
								      $this->_like_escape_chr.$this->_like_escape_chr),
								$str);
		}

		return $str;
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
		return $this->affect_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 * 
	 * @access	public
	 * @return	integer
	 */
	function insert_id($name=NULL)
	{
		if ($this->pdodriver == 'pgsql')
		{
			//Convenience method for postgres insertid
			$v = $this->_version();

			$table	= func_num_args() > 0 ? func_get_arg(0) : NULL;

			if ($table == NULL && $v >= '8.1')
			{
				$sql='SELECT LASTVAL() as ins_id';
			}

			$query = $this->query($sql);
			$row   = $query->row();

			return $row->ins_id;
		}
		else
		{
			return $this->conn_id->lastInsertId($name);
		}
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
		{
			return 0;
		}

		$sql   = $this->_count_string.$this->_protect_identifiers('numrows').' FROM ';
		$sql  .= $this->_protect_identifiers($table, TRUE, NULL, FALSE);
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
	 * @access	private
	 * @param	boolean
	 * @return	string
	 */
	function _list_tables($prefix_limit = FALSE)
	{
		if ($this->pdodriver == 'pgsql')
		{
			// Analog function to show all tables in postgre
			$sql = "SELECT * FROM information_schema.tables WHERE table_schema = 'public'";
		}
		else
		{
			$sql = "SHOW TABLES FROM `".$this->database."`";
		}

		if ($prefix_limit !== FALSE AND $this->dbprefix != '')
		{
			return FALSE; 
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
		return 'SHOW COLUMNS FROM '.$this->_from_tables($table);
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
		return 'SELECT TOP 1 FROM '.$this->_from_tables($table);
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
		$error_array = $this->conn_id->errorInfo();

		return $error_array[2];
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
		return $this->conn_id->errorCode();
	}

	// --------------------------------------------------------------------

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

		return (count($tables) == 1) ? '`'.$tables[0].'`' : '('.implode(', ', $tables).')';
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
		return 'INSERT INTO '.$this->_from_tables($table).' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert_batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @access  public
	 * @param   string  the table name
	 * @param   array   the insert keys
	 * @param   array   the insert values
	 * @return  string
	 */
	function _insert_batch($table, $keys, $values)
	{
		return 'INSERT INTO '.$this->_from_tables($table).' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
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
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @return	string
	 */
	function _update_batch($table, $values, $index, $where = NULL)
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
	 * @access	public
	 * @param	string	the sql query string
	 * @param	integer	the number of rows to limit the query to
	 * @param	integer	the offset value
	 * @return	string
	 */
	function _limit($sql, $limit, $offset)
	{
		if ($this->pdodriver == 'cubrid' OR $this->pdodriver == 'sqlite')
		{
			$offset = ($offset == 0) ? '' : $offset.', ';

			return $sql.'LIMIT '.$offset.$limit;
		}
		else
		{
			$sql .= 'LIMIT '.$limit;
			$sql .= ($offset > 0) ? ' OFFSET '.$offset : '';
			
			return $sql;
		}
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
		$this->conn_id = null;
	}

}

/* End of file pdo_driver.php */
/* Location: ./system/database/drivers/pdo/pdo_driver.php */