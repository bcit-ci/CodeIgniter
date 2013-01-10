<?php
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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 2.0.3
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SQLSRV Database Adapter Class
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
class CI_DB_sqlsrv_driver extends CI_DB {

	/**
	 * Database driver
	 *
	 * @var	string
	 */
	public $dbdriver = 'sqlsrv';

	// --------------------------------------------------------------------

	/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
	protected $_random_keyword = array('NEWID()', 'RAND(%d)');

	/**
	 * Quoted identifier flag
	 *
	 * Whether to use SQL-92 standard quoted identifier
	 * (double quotes) or brackets for identifier escaping.
	 *
	 * @var	bool
	 */
	protected $_quoted_identifier = TRUE;

	// --------------------------------------------------------------------

	/**
	 * Database connection
	 *
	 * @param	bool	$pooling
	 * @return	resource
	 */
	public function db_connect($pooling = FALSE)
	{
		$charset = in_array(strtolower($this->char_set), array('utf-8', 'utf8'), TRUE)
			? 'UTF-8' : SQLSRV_ENC_CHAR;

		$connection = array(
			'UID'			=> empty($this->username) ? '' : $this->username,
			'PWD'			=> empty($this->password) ? '' : $this->password,
			'Database'		=> $this->database,
			'ConnectionPooling'	=> ($pooling === TRUE) ? 1 : 0,
			'CharacterSet'		=> $charset,
			'Encrypt'		=> ($this->encrypt === TRUE) ? 1 : 0,
			'ReturnDatesAsStrings'	=> 1
		);

		// If the username and password are both empty, assume this is a
		// 'Windows Authentication Mode' connection.
		if (empty($connection['UID']) && empty($connection['PWD']))
		{
			unset($connection['UID'], $connection['PWD']);
		}

		$this->conn_id = sqlsrv_connect($this->hostname, $connection);

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
	 * @param	string	$database
	 * @return	bool
	 */
	public function db_select($database = '')
	{
		if ($database === '')
		{
			$database = $this->database;
		}

		if ($this->_execute('USE '.$this->escape_identifiers($database)))
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
	 * @param	string	$sql	an SQL query
	 * @return	resource
	 */
	protected function _execute($sql)
	{
		return ($this->is_write_type($sql) && stripos($sql, 'INSERT') === FALSE)
			? sqlsrv_query($this->conn_id, $sql)
			: sqlsrv_query($this->conn_id, $sql, NULL, array('Scrollable' => SQLSRV_CURSOR_STATIC));
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @param	bool	$test_mode
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
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return sqlsrv_rows_affected($this->result_id);
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
		$query = $this->query('SELECT @@IDENTITY AS insert_id');
		$query = $query->row();
		return $query->insert_id;
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
		elseif ( ! $this->conn_id)
		{
			$this->initialize();
		}

		if ( ! $this->conn_id OR ($info = sqlsrv_server_info($this->conn_id)) === FALSE)
		{
			return FALSE;
		}

		return $this->data_cache['version'] = $info['SQLServerVersion'];
	}

	// --------------------------------------------------------------------

	/**
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string	$prefix_limit
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = 'SELECT '.$this->escape_identifiers('name')
			.' FROM '.$this->escape_identifiers('sysobjects')
			.' WHERE '.$this->escape_identifiers('type')." = 'U'";

		if ($prefix_limit === TRUE && $this->dbprefix !== '')
		{
			$sql .= ' AND '.$this->escape_identifiers('name')." LIKE '".$this->escape_like_str($this->dbprefix)."%' "
				.sprintf($this->_escape_like_str, $this->_escape_like_chr);
		}

		return $sql.' ORDER BY '.$this->escape_identifiers('name');
	}

	// --------------------------------------------------------------------

	/**
	 * List column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return 'SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.Columns
			WHERE UPPER(TABLE_NAME) = '.$this->escape(strtoupper($table));
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an object with field data
	 *
	 * @param	string	$table
	 * @return	array
	 */
	public function field_data($table = '')
	{
		if ($table === '')
		{
			return ($this->db_debug) ? $this->display_error('db_field_param_missing') : FALSE;
		}

		$sql = 'SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, COLUMN_DEFAULT
			FROM INFORMATION_SCHEMA.Columns
			WHERE UPPER(TABLE_NAME) = '.$this->escape(strtoupper($table));

		if (($query = $this->query($sql)) === FALSE)
		{
			return FALSE;
		}
		$query = $query->result_object();

		$retval = array();
		for ($i = 0, $c = count($query); $i < $c; $i++)
		{
			$retval[$i]			= new stdClass();
			$retval[$i]->name		= $query[$i]->COLUMN_NAME;
			$retval[$i]->type		= $query[$i]->DATA_TYPE;
			$retval[$i]->max_length		= ($query[$i]->CHARACTER_MAXIMUM_LENGTH > 0) ? $query[$i]->CHARACTER_MAXIMUM_LENGTH : $query[$i]->NUMERIC_PRECISION;
			$retval[$i]->default		= $query[$i]->COLUMN_DEFAULT;
		}

		return $retval;
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
		$sqlsrv_errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);

		if ( ! is_array($sqlsrv_errors))
		{
			return $error;
		}

		$sqlsrv_error = array_shift($sqlsrv_errors);
		if (isset($sqlsrv_error['SQLSTATE']))
		{
			$error['code'] = isset($sqlsrv_error['code']) ? $sqlsrv_error['SQLSTATE'].'/'.$sqlsrv_error['code'] : $sqlsrv_error['SQLSTATE'];
		}
		elseif (isset($sqlsrv_error['code']))
		{
			$error['code'] = $sqlsrv_error['code'];
		}

		if (isset($sqlsrv_error['message']))
		{
			$error['message'] = $sqlsrv_error['message'];
		}

		return $error;
	}

	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	$table
	 * @param	array	$values
	 * @return	string
	 */
	protected function _update($table, $values)
	{
		$this->qb_limit = FALSE;
		$this->qb_orderby = array();
		return parent::_update($table, $values);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the TRUNCATE statement,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	$table
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
	 * @param	string	$table
	 * @return	string
	 */
	protected function _delete($table)
	{
		if ($this->qb_limit)
		{
			return 'WITH ci_delete AS (SELECT TOP '.$this->qb_limit.' * FROM '.$table.$this->_compile_wh('qb_where').') DELETE FROM ci_delete';
		}

		return parent::_delete($table);
	}

	// --------------------------------------------------------------------

	/**
	 * LIMIT
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	$sql	SQL Query
	 * @return	string
	 */
	protected function _limit($sql)
	{
		// As of SQL Server 2012 (11.0.*) OFFSET is supported
		if (version_compare($this->version(), '11', '>='))
		{
			return $sql.' OFFSET '.(int) $this->qb_offset.' ROWS FETCH NEXT '.$this->qb_limit.' ROWS ONLY';
		}

		$limit = $this->qb_offset + $this->qb_limit;

		// An ORDER BY clause is required for ROW_NUMBER() to work
		if ($this->qb_offset && ! empty($this->qb_orderby))
		{
			$orderby = $this->_compile_order_by();

			// We have to strip the ORDER BY clause
			$sql = trim(substr($sql, 0, strrpos($sql, $orderby)));

			// Get the fields to select from our subquery, so that we can avoid CI_rownum appearing in the actual results
			if (count($this->qb_select) === 0)
			{
				$select = '*'; // Inevitable
			}
			else
			{
				// Use only field names and their aliases, everything else is out of our scope.
				$select = array();
				$field_regexp = ($this->_quoted_identifier)
					? '("[^\"]+")' : '(\[[^\]]+\])';
				for ($i = 0, $c = count($this->qb_select); $i < $c; $i++)
				{
					$select[] = preg_match('/(?:\s|\.)'.$field_regexp.'$/i', $this->qb_select[$i], $m)
						? $m[1] : $this->qb_select[$i];
				}
				$select = implode(', ', $select);
			}

			return 'SELECT '.$select." FROM (\n\n"
				.preg_replace('/^(SELECT( DISTINCT)?)/i', '\\1 ROW_NUMBER() OVER('.trim($orderby).') AS '.$this->escape_identifiers('CI_rownum').', ', $sql)
				."\n\n) ".$this->escape_identifiers('CI_subquery')
				."\nWHERE ".$this->escape_identifiers('CI_rownum').' BETWEEN '.($this->qb_offset + 1).' AND '.$limit;
		}

		return preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 TOP '.$limit.' ', $sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data.
	 *
	 * @param	string	$table	Table name
	 * @param	array	$keys	INSERT keys
	 * @param	array	$values	INSERT values
	 * @return	string|bool
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		// Multiple-value inserts are only supported as of SQL Server 2008
		if (version_compare($this->version(), '10', '>='))
		{
			return parent::_insert_batch($table, $keys, $values);
		}

		return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	protected function _close()
	{
		@sqlsrv_close($this->conn_id);
	}

}

/* End of file sqlsrv_driver.php */
/* Location: ./system/database/drivers/sqlsrv/sqlsrv_driver.php */