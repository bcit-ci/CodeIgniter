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

	// CUBRID-specific properties
	public $auto_commit = TRUE;

	public function __construct($params)
	{
		parent::__construct($params);

		if (preg_match('/^CUBRID:[^:]+(:[0-9][1-9]{0,4})?:[^:]+:[^:]*:[^:]*:(\?.+)?$/', $this->dsn, $matches))
		{
			if (stripos($matches[2], 'autocommit=off') !== FALSE)
			{
				$this->auto_commit = FALSE;
			}
		}
		else
		{
			// If no port is defined by the user, use the default value
			$this->port == '' OR $this->port = 33000;
		}
	}

	/**
	 * Non-persistent database connection
	 *
	 * @return	resource
	 */
	public function db_connect()
	{
		return $this->_cubrid_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * In CUBRID persistent DB connection is supported natively in CUBRID
	 * engine which can be configured in the CUBRID Broker configuration
	 * file by setting the CCI_PCONNECT parameter to ON. In that case, all
	 * connections established between the client application and the
	 * server will become persistent.
	 *
	 * @return	resource
	 */
	public function db_pconnect()
	{
		return $this->_cubrid_connect(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * CUBRID connection
	 *
	 * A CUBRID-specific method to create a connection to the database.
	 * Except for determining if a persistent connection should be used,
	 * the rest of the logic is the same for db_connect() and db_pconnect().
	 *
	 * @param	bool
	 * @return	resource
	 */
	protected function _cubrid_connect($persistent = FALSE)
	{
		if (preg_match('/^CUBRID:[^:]+(:[0-9][1-9]{0,4})?:[^:]+:([^:]*):([^:]*):(\?.+)?$/', $this->dsn, $matches))
		{
			$_temp = ($persistent !== TRUE) ? 'cubrid_connect_with_url' : 'cubrid_pconnect_with_url';
			$conn_id = ($matches[2] === '' && $matches[3] === '' && $this->username !== '' && $this->password !== '')
					? $_temp($this->dsn, $this->username, $this->password)
					: $_temp($this->dsn);
		}
		else
		{
			$_temp = ($persistent !== TRUE) ? 'cubrid_connect' : 'cubrid_pconnect';
			$conn_id = ($this->username !== '')
					? $_temp($this->hostname, $this->port, $this->database, $this->username, $this->password)
					: $_temp($this->hostname, $this->port, $this->database);
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
		if (cubrid_ping($this->conn_id) === FALSE)
		{
			$this->conn_id = FALSE;
		}
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
			: $this->data_cache['version'] = cubrid_get_server_info($this->conn_id);
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
		return @cubrid_query($sql, $this->conn_id);
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
		$this->_trans_failure = ($test_mode === TRUE) ? TRUE : FALSE;

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
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
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
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
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

		if (function_exists('cubrid_real_escape_string') AND is_resource($this->conn_id))
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
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
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
		return @cubrid_affected_rows();
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

		$query = $this->query($this->_count_string.$this->protect_identifiers('numrows').' FROM '.$this->protect_identifiers($table, TRUE, NULL, FALSE));
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
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = "SHOW TABLES";

		if ($prefix_limit !== FALSE AND $this->dbprefix != '')
		{
			$sql .= " LIKE '".$this->escape_like_str($this->dbprefix)."%'";
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
		return 'SHOW COLUMNS FROM '.$this->protect_identifiers($table, TRUE, NULL, FALSE);
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
		return "SELECT * FROM ".$table." LIMIT 1";
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
		return array('code' => cubrid_errno($this->conn_id), 'message' => cubrid_error($this->conn_id));
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

		return '('.implode(', ', $tables).')';
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
		$where = ($where != '' AND count($where) >=1) ? implode(" ", $where).' AND ' : '';

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

		$sql = "UPDATE ".$table." SET ";
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