<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.4.1
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * oci8 Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage  Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/database/
 */

/**
 * oci8 Database Adapter Class
 *
 * This is a modification of the DB_driver class to
 * permit access to oracle databases
 *
 * @author	  Kelly McArdle
 */
class CI_DB_oci8_driver extends CI_DB {

	/**
	 * Database driver
	 *
	 * @var	string
	 */
	public $dbdriver = 'oci8';

	/**
	 * Statement ID
	 *
	 * @var	resource
	 */
	public $stmt_id;

	/**
	 * Cursor ID
	 *
	 * @var	resource
	 */
	public $curs_id;

	/**
	 * Commit mode flag
	 *
	 * @var	int
	 */
	public $commit_mode = OCI_COMMIT_ON_SUCCESS;

	/**
	 * Limit used flag
	 *
	 * If we use LIMIT, we'll add a field that will
	 * throw off num_fields later.
	 *
	 * @var	bool
	 */
	public $limit_used = FALSE;

	// --------------------------------------------------------------------

	/**
	 * Reset $stmt_id flag
	 *
	 * Used by stored_procedure() to prevent _execute() from
	 * re-setting the statement ID.
	 */
	protected $_reset_stmt_id = TRUE;

	/**
	 * List of reserved identifiers
	 *
	 * Identifiers that must NOT be escaped.
	 *
	 * @var	string[]
	 */
	protected $_reserved_identifiers = array('*', 'rownum');

	/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
	protected $_random_keyword = array('ASC', 'ASC'); // not currently supported

	/**
	 * COUNT string
	 *
	 * @used-by	CI_DB_driver::count_all()
	 * @used-by	CI_DB_query_builder::count_all_results()
	 *
	 * @var	string
	 */
	protected $_count_string = 'SELECT COUNT(1) AS ';

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		$valid_dsns = array(
			'tns'	=> '/^\(DESCRIPTION=(\(.+\)){2,}\)$/', // TNS
			// Easy Connect string (Oracle 10g+)
			'ec'	=> '/^(\/\/)?[a-z0-9.:_-]+(:[1-9][0-9]{0,4})?(\/[a-z0-9$_]+)?(:[^\/])?(\/[a-z0-9$_]+)?$/i',
			'in'	=> '/^[a-z0-9$_]+$/i' // Instance name (defined in tnsnames.ora)
		);

		/* Space characters don't have any effect when actually
		 * connecting, but can be a hassle while validating the DSN.
		 */
		$this->dsn = str_replace(array("\n", "\r", "\t", ' '), '', $this->dsn);

		if ($this->dsn !== '')
		{
			foreach ($valid_dsns as $regexp)
			{
				if (preg_match($regexp, $this->dsn))
				{
					return;
				}
			}
		}

		// Legacy support for TNS in the hostname configuration field
		$this->hostname = str_replace(array("\n", "\r", "\t", ' '), '', $this->hostname);
		if (preg_match($valid_dsns['tns'], $this->hostname))
		{
			$this->dsn = $this->hostname;
			return;
		}
		elseif ($this->hostname !== '' && strpos($this->hostname, '/') === FALSE && strpos($this->hostname, ':') === FALSE
			&& (( ! empty($this->port) && ctype_digit($this->port)) OR $this->database !== ''))
		{
			/* If the hostname field isn't empty, doesn't contain
			 * ':' and/or '/' and if port and/or database aren't
			 * empty, then the hostname field is most likely indeed
			 * just a hostname. Therefore we'll try and build an
			 * Easy Connect string from these 3 settings, assuming
			 * that the database field is a service name.
			 */
			$this->dsn = $this->hostname
				.(( ! empty($this->port) && ctype_digit($this->port)) ? ':'.$this->port : '')
				.($this->database !== '' ? '/'.ltrim($this->database, '/') : '');

			if (preg_match($valid_dsns['ec'], $this->dsn))
			{
				return;
			}
		}

		/* At this point, we can only try and validate the hostname and
		 * database fields separately as DSNs.
		 */
		if (preg_match($valid_dsns['ec'], $this->hostname) OR preg_match($valid_dsns['in'], $this->hostname))
		{
			$this->dsn = $this->hostname;
			return;
		}

		$this->database = str_replace(array("\n", "\r", "\t", ' '), '', $this->database);
		foreach ($valid_dsns as $regexp)
		{
			if (preg_match($regexp, $this->database))
			{
				return;
			}
		}

		/* Well - OK, an empty string should work as well.
		 * PHP will try to use environment variables to
		 * determine which Oracle instance to connect to.
		 */
		$this->dsn = '';
	}

	// --------------------------------------------------------------------

	/**
	 * Non-persistent database connection
	 *
	 * @param	bool	$persistent
	 * @return	resource
	 */
	public function db_connect($persistent = FALSE)
	{
		$func = ($persistent === TRUE) ? 'oci_pconnect' : 'oci_connect';
		return empty($this->char_set)
			? $func($this->username, $this->password, $this->dsn)
			: $func($this->username, $this->password, $this->dsn, $this->char_set);
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

		if ( ! $this->conn_id OR ($version_string = oci_server_version($this->conn_id)) === FALSE)
		{
			return FALSE;
		}
		elseif (preg_match('#Release\s(\d+(?:\.\d+)+)#', $version_string, $match))
		{
			return $this->data_cache['version'] = $match[1];
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
		/* Oracle must parse the query before it is run. All of the actions with
		 * the query are based on the statement id returned by oci_parse().
		 */
		if ($this->_reset_stmt_id === TRUE)
		{
			$this->stmt_id = oci_parse($this->conn_id, $sql);
		}

		oci_set_prefetch($this->stmt_id, 1000);
		return oci_execute($this->stmt_id, $this->commit_mode);
	}

	// --------------------------------------------------------------------

	/**
	 * Get cursor. Returns a cursor from the database
	 *
	 * @return	resource
	 */
	public function get_cursor()
	{
		return $this->curs_id = oci_new_cursor($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Stored Procedure.  Executes a stored procedure
	 *
	 * @param	string	package name in which the stored procedure is in
	 * @param	string	stored procedure name to execute
	 * @param	array	parameters
	 * @return	mixed
	 *
	 * params array keys
	 *
	 * KEY      OPTIONAL  NOTES
	 * name     no        the name of the parameter should be in :<param_name> format
	 * value    no        the value of the parameter.  If this is an OUT or IN OUT parameter,
	 *                    this should be a reference to a variable
	 * type     yes       the type of the parameter
	 * length   yes       the max size of the parameter
	 */
	public function stored_procedure($package, $procedure, array $params)
	{
		if ($package === '' OR $procedure === '')
		{
			log_message('error', 'Invalid query: '.$package.'.'.$procedure);
			return ($this->db_debug) ? $this->display_error('db_invalid_query') : FALSE;
		}

		// Build the query string
		$sql = 'BEGIN '.$package.'.'.$procedure.'(';

		$have_cursor = FALSE;
		foreach ($params as $param)
		{
			$sql .= $param['name'].',';

			if (isset($param['type']) && $param['type'] === OCI_B_CURSOR)
			{
				$have_cursor = TRUE;
			}
		}
		$sql = trim($sql, ',').'); END;';

		$this->_reset_stmt_id = FALSE;
		$this->stmt_id = oci_parse($this->conn_id, $sql);
		$this->_bind_params($params);
		$result = $this->query($sql, FALSE, $have_cursor);
		$this->_reset_stmt_id = TRUE;
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Bind parameters
	 *
	 * @param	array	$params
	 * @return	void
	 */
	protected function _bind_params($params)
	{
		if ( ! is_array($params) OR ! is_resource($this->stmt_id))
		{
			return;
		}

		foreach ($params as $param)
		{
			foreach (array('name', 'value', 'type', 'length') as $val)
			{
				if ( ! isset($param[$val]))
				{
					$param[$val] = '';
				}
			}

			oci_bind_by_name($this->stmt_id, $param['name'], $param['value'], $param['length'], $param['type']);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_begin()
	{
		$this->commit_mode = OCI_NO_AUTO_COMMIT;
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_commit()
	{
		$this->commit_mode = OCI_COMMIT_ON_SUCCESS;

		return oci_commit($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_rollback()
	{
		$this->commit_mode = OCI_COMMIT_ON_SUCCESS;
		return oci_rollback($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return oci_num_rows($this->stmt_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insert_id()
	{
		// not supported in oracle
		return $this->display_error('db_unsupported_function');
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool	$prefix_limit
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = 'SELECT "TABLE_NAME" FROM "ALL_TABLES"';

		if ($prefix_limit !== FALSE && $this->dbprefix !== '')
		{
			return $sql.' WHERE "TABLE_NAME" LIKE \''.$this->escape_like_str($this->dbprefix)."%' "
				.sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		if (strpos($table, '.') !== FALSE)
		{
			sscanf($table, '%[^.].%s', $owner, $table);
		}
		else
		{
			$owner = $this->username;
		}

		return 'SELECT COLUMN_NAME FROM ALL_TAB_COLUMNS
			WHERE UPPER(OWNER) = '.$this->escape(strtoupper($owner)).'
				AND UPPER(TABLE_NAME) = '.$this->escape(strtoupper($table));
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an object with field data
	 *
	 * @param	string	$table
	 * @return	array
	 */
	public function field_data($table)
	{
		if (strpos($table, '.') !== FALSE)
		{
			sscanf($table, '%[^.].%s', $owner, $table);
		}
		else
		{
			$owner = $this->username;
		}

		$sql = 'SELECT COLUMN_NAME, DATA_TYPE, CHAR_LENGTH, DATA_PRECISION, DATA_LENGTH, DATA_DEFAULT, NULLABLE
			FROM ALL_TAB_COLUMNS
			WHERE UPPER(OWNER) = '.$this->escape(strtoupper($owner)).'
				AND UPPER(TABLE_NAME) = '.$this->escape(strtoupper($table));

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

			$length = ($query[$i]->CHAR_LENGTH > 0)
				? $query[$i]->CHAR_LENGTH : $query[$i]->DATA_PRECISION;
			if ($length === NULL)
			{
				$length = $query[$i]->DATA_LENGTH;
			}
			$retval[$i]->max_length		= $length;

			$default = $query[$i]->DATA_DEFAULT;
			if ($default === NULL && $query[$i]->NULLABLE === 'N')
			{
				$default = '';
			}
			$retval[$i]->default = $default;
		}

		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Error
	 *
	 * Returns an array containing code and message of the last
	 * database error that has occurred.
	 *
	 * @return	array
	 */
	public function error()
	{
		// oci_error() returns an array that already contains
		// 'code' and 'message' keys, but it can return false
		// if there was no error ....
		if (is_resource($this->curs_id))
		{
			$error = oci_error($this->curs_id);
		}
		elseif (is_resource($this->stmt_id))
		{
			$error = oci_error($this->stmt_id);
		}
		elseif (is_resource($this->conn_id))
		{
			$error = oci_error($this->conn_id);
		}
		else
		{
			$error = oci_error();
		}

		return is_array($error)
			? $error
			: array('code' => '', 'message' => '');
	}

	// --------------------------------------------------------------------

	/**
	 * Insert batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	Table name
	 * @param	array	$keys	INSERT keys
	 * @param 	array	$values	INSERT values
	 * @return	string
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		$keys = implode(', ', $keys);
		$sql = "INSERT ALL\n";

		for ($i = 0, $c = count($values); $i < $c; $i++)
		{
			$sql .= '	INTO '.$table.' ('.$keys.') VALUES '.$values[$i]."\n";
		}

		return $sql.'SELECT * FROM dual';
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
			$this->where('rownum <= ',$this->qb_limit, FALSE);
			$this->qb_limit = FALSE;
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
		if (version_compare($this->version(), '12.1', '>='))
		{
			// OFFSET-FETCH can be used only with the ORDER BY clause
			empty($this->qb_orderby) && $sql .= ' ORDER BY 1';

			return $sql.' OFFSET '.(int) $this->qb_offset.' ROWS FETCH NEXT '.$this->qb_limit.' ROWS ONLY';
		}

		$this->limit_used = TRUE;
		return 'SELECT * FROM (SELECT inner_query.*, rownum rnum FROM ('.$sql.') inner_query WHERE rownum < '.($this->qb_offset + $this->qb_limit + 1).')'
			.($this->qb_offset ? ' WHERE rnum >= '.($this->qb_offset + 1) : '');
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	protected function _close()
	{
		if (is_resource($this->curs_id))
		{
			oci_free_statement($this->curs_id);
		}

		if (is_resource($this->stmt_id))
		{
			oci_free_statement($this->stmt_id);
		}

		oci_close($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * We need to reset our $limit_used hack flag, so it doesn't propagate
	 * to subsequent queries.
	 *
	 * @return	void
	 */
	protected function _reset_select()
	{
		$this->limit_used = FALSE;
		parent::_reset_select();
	}
}
