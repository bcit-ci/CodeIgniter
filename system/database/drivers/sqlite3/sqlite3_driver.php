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
 * SQLite3 Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/database/
 * @since		Version 3.0
 */
class CI_DB_sqlite3_driver extends CI_DB {

	public $dbdriver = 'sqlite3';

	// The character used for escaping
	protected $_escape_char = '"';

	// clause and character used for LIKE escape sequences
	protected $_like_escape_str = ' ESCAPE \'%s\' ';
	protected $_like_escape_chr = '!';

	protected $_random_keyword = ' RANDOM()';

	/**
	 * Non-persistent database connection
	 *
	 * @return	object	type SQLite3
	 */
	public function db_connect()
	{
		try
		{
			return ( ! $this->password)
				? new SQLite3($this->database)
				: new SQLite3($this->database, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $this->password);
		}
		catch (Exception $e)
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @return  object	type SQLite3
	 */
	public function db_pconnect()
	{
		log_message('debug', 'SQLite3 doesn\'t support persistent connections');
		return $this->db_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * Database version number
	 *
	 * @return	string
	 */
	public function version()
	{
		if (isset($this->data_cache['version']))
		{
			return $this->data_cache['version'];
		}

		$version = SQLite3::version();
		return $this->data_cache['version'] = $version['versionString'];
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	an SQL query
	 * @return	mixed	SQLite3Result object or bool
	 */
	protected function _execute($sql)
	{
		// TODO: Implement use of SQLite3::querySingle(), if needed

		return $this->is_write_type($sql)
			? $this->conn_id->exec($sql)
			: $this->conn_id->query($sql);
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

		return $this->conn_id->exec('BEGIN TRANSACTION');
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

		return $this->conn_id->exec('END TRANSACTION');
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

		return $this->conn_id->exec('ROLLBACK');
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

		$str = $this->conn_id->escapeString(remove_invisible_characters($str));

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
		return $this->conn_id->changes();
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insert_id()
	{
		return $this->conn_id->lastInsertRowID();
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
		return 'SELECT "NAME" FROM "SQLITE_MASTER" WHERE "TYPE" = \'table\''
			.(($prefix_limit !== FALSE && $this->dbprefix != '')
				? ' AND "NAME" LIKE \''.$this->escape_like_str($this->dbprefix).'%\' '.sprintf($this->_like_escape_str, $this->_like_escape_chr)
				: '');
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
		return 'SELECT * FROM '.$table.' LIMIT 0,1';
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @return	string
	 */
	protected function _error_message()
	{
		return $this->conn_id->lastErrorMsg();
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @return	int
	 */
	protected function _error_number()
	{
		return $this->conn_id->lastErrorCode();
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
		return 'INSERT OR '.parent::_replace($table, $keys, $values);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the truncate() command, then,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return 'DELETE FROM '.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	protected function _close()
	{
		$this->conn_id->close();
	}

}

/* End of file sqlite3_driver.php */
/* Location: ./system/database/drivers/sqlite3/sqlite3_driver.php */