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
 * @since		Version 1.0
 * @filesource
 */

/**
 * MS SQL Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_mssql_driver extends CI_DB {

	public $dbdriver = 'mssql';

	// The character used for escaping
	protected $_escape_char = '"';

	// clause and character used for LIKE escape sequences
	protected $_like_escape_str = " ESCAPE '%s' ";
	protected $_like_escape_chr = '!';

	protected $_random_keyword = ' NEWID()';

	// MSSQL-specific properties
	protected $_quoted_identifier = TRUE;

	/*
	 * Constructor
	 *
	 * Appends the port number to the hostname, if needed.
	 *
	 * @param	array
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if ( ! empty($this->port))
		{
			$this->hostname .= (DIRECTORY_SEPARATOR === '\\' ? ',' : ':').$this->port;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Non-persistent database connection
	 *
	 * @param	bool
	 * @return	resource
	 */
	public function db_connect($persistent = FALSE)
	{
		$this->conn_id = ($persistent)
				? @mssql_pconnect($this->hostname, $this->username, $this->password)
				: @mssql_connect($this->hostname, $this->username, $this->password);

		if ( ! $this->conn_id)
		{
			return FALSE;
		}

		// Determine how identifiers are escaped
		$query = $this->query('SELECT CASE WHEN (@@OPTIONS | 256) = @@OPTIONS THEN 1 ELSE 0 END AS qi');
		$query = $query->row_array();
		$this->_quoted_identifier = empty($query) ? FALSE : (bool) $query['qi'];
		$this->_escape_char = ($this->_quoted_identifier) ? '"' : array('[', ']');

		return $this->conn_id;
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

		// Note: The brackets are required in the event that the DB name
		// contains reserved characters
		if (@mssql_select_db($this->escape_identifiers($database), $this->conn_id))
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
	 * @return	mixed	resource if rows are returned, bool otherwise
	 */
	protected function _execute($sql)
	{
		return @mssql_query($sql, $this->conn_id);
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

		return $this->simple_query('BEGIN TRAN');
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

		return $this->simple_query('COMMIT TRAN');
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

		return $this->simple_query('ROLLBACK TRAN');
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

		// Escape single quotes
		$str = str_replace("'", "''", remove_invisible_characters($str));

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(
				array($this->_like_escape_chr, '%', '_'),
				array($this->_like_escape_chr.$this->_like_escape_chr, $this->_like_escape_chr.'%', $this->_like_escape_chr.'_'),
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
		return @mssql_rows_affected($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * Returns the last id created in the Identity column.
	 *
	 * @return	string
	 */
	public function insert_id()
	{
		$query = version_compare($this->version(), '8', '>=')
			? 'SELECT SCOPE_IDENTITY() AS last_id'
			: 'SELECT @@IDENTITY AS last_id';

		$query = $this->query($query);
		$query = $query->row();
		return $query->last_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @return	string
	 */
	protected function _version()
	{
		return 'SELECT @@VERSION AS ver';
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
		$sql = 'SELECT '.$this->escape_identifiers('name')
			.' FROM '.$this->escape_identifiers('sysobjects')
			.' WHERE '.$this->escape_identifiers('type')." = 'U'";

		if ($prefix_limit !== FALSE AND $this->dbprefix !== '')
		{
			$sql .= ' AND '.$this->escape_identifiers('name')." LIKE '".$this->escape_like_str($this->dbprefix)."%' "
				.sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql.' ORDER BY '.$this->escape_identifiers('name');
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
		return "SELECT * FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '".$table."'";
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
		return 'SELECT TOP 1 * FROM '.$this->protect_identifiers($table);
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
		$query = $this->query('SELECT @@ERROR AS code');
		$query = $query->row();
		return array('code' => $query->code, 'message' => mssql_get_last_message());
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
		return is_array($tables) ? implode(', ', $tables) : $tables;
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
	 * @param	array	the orderby clause (ignored)
	 * @param	array	the limit clause (ignored)
	 * @param	array	the like clause
	 * @return	string
	 */
	protected function _update($table, $values, $where, $orderby = array(), $limit = FALSE, $like = array())
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		$where = empty($where) ? '' : ' WHERE '.implode(' ', $where);

		if ( ! empty($like))
		{
			$where .= ($where === '' ? ' WHERE ' : ' AND ').implode(' ', $like);
		}

		return 'UPDATE '.$table.' SET '.implode(', ', $valstr).' WHERE '.$where;
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the truncate() command,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return 'TRUNCATE TABLE '.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @param	array	the like clause
	 * @param	string	the limit clause
	 * @return	string
	 */
	protected function _delete($table, $where = array(), $like = array(), $limit = FALSE)
	{
		$conditions = array();

		empty($where) OR $conditions[] = implode(' ', $where);
		empty($like) OR $conditions[] = implode(' ', $like);

		$conditions = (count($conditions) > 0) ? ' WHERE '.implode(' AND ', $conditions) : '';

		return ($limit)
			? 'WITH ci_delete AS (SELECT TOP '.$limit.' * FROM '.$table.$conditions.') DELETE FROM ci_delete'
			: 'DELETE FROM '.$table.$conditions;
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
		// As of SQL Server 2012 (11.0.*) OFFSET is supported
		if (version_compare($this->version(), '11', '>='))
		{
			return $sql.' OFFSET '.(int) $offset.' ROWS FETCH NEXT '.(int) $limit.' ROWS ONLY';
		}

		$limit = $offset + $limit;

		// As of SQL Server 2005 (9.0.*) ROW_NUMBER() is supported,
		// however an ORDER BY clause is required for it to work
		if (version_compare($this->version(), '9', '>=') && $offset && ! empty($this->qb_orderby))
		{
			$orderby = 'ORDER BY '.implode(', ', $this->qb_orderby);

			// We have to strip the ORDER BY clause
			$sql = trim(substr($sql, 0, strrpos($sql, 'ORDER BY '.$orderby)));

			return 'SELECT '.(count($this->qb_select) === 0 ? '*' : implode(', ', $this->qb_select))." FROM (\n"
				.preg_replace('/^(SELECT( DISTINCT)?)/i', '\\1 ROW_NUMBER() OVER('.$orderby.') AS '.$this->escape_identifiers('CI_rownum').', ', $sql)
				."\n) ".$this->escape_identifiers('CI_subquery')
				."\nWHERE ".$this->escape_identifiers('CI_rownum').' BETWEEN '.((int) $offset + 1).' AND '.$limit;
		}

		return preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 TOP '.$limit.' ', $sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	protected function _close()
	{
		@mssql_close($this->conn_id);
	}

}

/* End of file mssql_driver.php */
/* Location: ./system/database/drivers/mssql/mssql_driver.php */