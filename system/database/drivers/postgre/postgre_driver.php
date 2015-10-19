<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Postgre Database Adapter Class
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
class CI_DB_postgre_driver extends CI_DB {

	/**
	 * Database driver
	 *
	 * @var	string
	 */
	public $dbdriver = 'postgre';

	/**
	 * Database schema
	 *
	 * @var	string
	 */
	public $schema = 'public';

	// --------------------------------------------------------------------

	/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
	protected $_random_keyword = array('RANDOM()', 'RANDOM()');

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Creates a DSN string to be used for db_connect() and db_pconnect()
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if ( ! empty($this->dsn))
		{
			return;
		}

		$this->dsn === '' OR $this->dsn = '';

		if (strpos($this->hostname, '/') !== FALSE)
		{
			// If UNIX sockets are used, we shouldn't set a port
			$this->port = '';
		}

		$this->hostname === '' OR $this->dsn = 'host='.$this->hostname.' ';

		if ( ! empty($this->port) && ctype_digit($this->port))
		{
			$this->dsn .= 'port='.$this->port.' ';
		}

		if ($this->username !== '')
		{
			$this->dsn .= 'user='.$this->username.' ';

			/* An empty password is valid!
			 *
			 * $db['password'] = NULL must be done in order to ignore it.
			 */
			$this->password === NULL OR $this->dsn .= "password='".$this->password."' ";
		}

		$this->database === '' OR $this->dsn .= 'dbname='.$this->database.' ';

		/* We don't have these options as elements in our standard configuration
		 * array, but they might be set by parse_url() if the configuration was
		 * provided via string. Example:
		 *
		 * postgre://username:password@localhost:5432/database?connect_timeout=5&sslmode=1
		 */
		foreach (array('connect_timeout', 'options', 'sslmode', 'service') as $key)
		{
			if (isset($this->$key) && is_string($this->key) && $this->key !== '')
			{
				$this->dsn .= $key."='".$this->key."' ";
			}
		}

		$this->dsn = rtrim($this->dsn);
	}

	// --------------------------------------------------------------------

	/**
	 * Database connection
	 *
	 * @param	bool	$persistent
	 * @return	resource
	 */
	public function db_connect($persistent = FALSE)
	{
		$this->conn_id = ($persistent === TRUE)
			? pg_pconnect($this->dsn)
			: pg_connect($this->dsn);

		if ($this->conn_id !== FALSE)
		{
			if ($persistent === TRUE
				&& pg_connection_status($this->conn_id) === PGSQL_CONNECTION_BAD
				&& pg_ping($this->conn_id) === FALSE
			)
			{
				return FALSE;
			}

			empty($this->schema) OR $this->simple_query('SET search_path TO '.$this->schema.',public');
		}

		return $this->conn_id;
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
		if (pg_ping($this->conn_id) === FALSE)
		{
			$this->conn_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set client character set
	 *
	 * @param	string	$charset
	 * @return	bool
	 */
	protected function _db_set_charset($charset)
	{
		return (pg_set_client_encoding($this->conn_id, $charset) === 0);
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

		if ( ! $this->conn_id OR ($pg_version = pg_version($this->conn_id)) === FALSE)
		{
			return FALSE;
		}

		/* If PHP was compiled with PostgreSQL lib versions earlier
		 * than 7.4, pg_version() won't return the server version
		 * and so we'll have to fall back to running a query in
		 * order to get it.
		 */
		return isset($pg_version['server'])
			? $this->data_cache['version'] = $pg_version['server']
			: parent::version();
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
		return pg_query($this->conn_id, $sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_begin()
	{
		return (bool) pg_query($this->conn_id, 'BEGIN');
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_commit()
	{
		return (bool) pg_query($this->conn_id, 'COMMIT');
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_rollback()
	{
		return (bool) pg_query($this->conn_id, 'ROLLBACK');
	}

	// --------------------------------------------------------------------

	/**
	 * Determines if a query is a "write" type.
	 *
	 * @param	string	An SQL query string
	 * @return	bool
	 */
	public function is_write_type($sql)
	{
		return (bool) preg_match('/^\s*"?(SET|INSERT(?![^\)]+\)\s+RETURNING)|UPDATE(?!.*\sRETURNING)|DELETE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', str_replace(array("\r\n", "\r", "\n"), ' ', $sql));
	}

	// --------------------------------------------------------------------

	/**
	 * Platform-dependant string escape
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _escape_str($str)
	{
		return pg_escape_string($this->conn_id, $str);
	}

	// --------------------------------------------------------------------

	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 *
	 * @param	string	$str
	 * @return	mixed
	 */
	public function escape($str)
	{
		if (is_php('5.4.4') && (is_string($str) OR (is_object($str) && method_exists($str, '__toString'))))
		{
			return pg_escape_literal($this->conn_id, $str);
		}
		elseif (is_bool($str))
		{
			return ($str) ? 'TRUE' : 'FALSE';
		}

		return parent::escape($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return pg_affected_rows($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	string
	 */
	public function insert_id()
	{
		$v = pg_version($this->conn_id);
		$v = isset($v['server']) ? $v['server'] : 0; // 'server' key is only available since PosgreSQL 7.4

		$table	= (func_num_args() > 0) ? func_get_arg(0) : NULL;
		$column	= (func_num_args() > 1) ? func_get_arg(1) : NULL;

		if ($table === NULL && $v >= '8.1')
		{
			$sql = 'SELECT LASTVAL() AS ins_id';
		}
		elseif ($table !== NULL)
		{
			if ($column !== NULL && $v >= '8.0')
			{
				$sql = 'SELECT pg_get_serial_sequence(\''.$table."', '".$column."') AS seq";
				$query = $this->query($sql);
				$query = $query->row();
				$seq = $query->seq;
			}
			else
			{
				// seq_name passed in table parameter
				$seq = $table;
			}

			$sql = 'SELECT CURRVAL(\''.$seq."') AS ins_id";
		}
		else
		{
			return pg_last_oid($this->result_id);
		}

		$query = $this->query($sql);
		$query = $query->row();
		return (int) $query->ins_id;
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
		$sql = 'SELECT "table_name" FROM "information_schema"."tables" WHERE "table_schema" = \''.$this->schema."'";

		if ($prefix_limit !== FALSE && $this->dbprefix !== '')
		{
			return $sql.' AND "table_name" LIKE \''
				.$this->escape_like_str($this->dbprefix)."%' "
				.sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql;
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
		return 'SELECT "column_name"
			FROM "information_schema"."columns"
			WHERE LOWER("table_name") = '.$this->escape(strtolower($table));
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
		$sql = 'SELECT "column_name", "data_type", "character_maximum_length", "numeric_precision", "column_default"
			FROM "information_schema"."columns"
			WHERE LOWER("table_name") = '.$this->escape(strtolower($table));

		if (($query = $this->query($sql)) === FALSE)
		{
			return FALSE;
		}
		$query = $query->result_object();

		$retval = array();
		for ($i = 0, $c = count($query); $i < $c; $i++)
		{
			$retval[$i]			= new stdClass();
			$retval[$i]->name		= $query[$i]->column_name;
			$retval[$i]->type		= $query[$i]->data_type;
			$retval[$i]->max_length		= ($query[$i]->character_maximum_length > 0) ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
			$retval[$i]->default		= $query[$i]->column_default;
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
		return array('code' => '', 'message' => pg_last_error($this->conn_id));
	}

	// --------------------------------------------------------------------

	/**
	 * ORDER BY
	 *
	 * @param	string	$orderby
	 * @param	string	$direction	ASC, DESC or RANDOM
	 * @param	bool	$escape
	 * @return	object
	 */
	public function order_by($orderby, $direction = '', $escape = NULL)
	{
		$direction = strtoupper(trim($direction));
		if ($direction === 'RANDOM')
		{
			if ( ! is_float($orderby) && ctype_digit((string) $orderby))
			{
				$orderby = ($orderby > 1)
					? (float) '0.'.$orderby
					: (float) $orderby;
			}

			if (is_float($orderby))
			{
				$this->simple_query('SET SEED '.$orderby);
			}

			$orderby = $this->_random_keyword[0];
			$direction = '';
			$escape = FALSE;
		}

		return parent::order_by($orderby, $direction, $escape);
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
	 * Update_Batch statement
	 *
	 * Generates a platform-specific batch update string from the supplied data
	 *
	 * @param	string	$table	Table name
	 * @param	array	$values	Update data
	 * @param	string	$index	WHERE key
	 * @return	string
	 */
	protected function _update_batch($table, $values, $index)
	{
		$ids = array();
		foreach ($values as $key => $val)
		{
			$ids[] = $val[$index];

			foreach (array_keys($val) as $field)
			{
				if ($field !== $index)
				{
					$final[$field][] = 'WHEN '.$val[$index].' THEN '.$val[$field];
				}
			}
		}

		$cases = '';
		foreach ($final as $k => $v)
		{
			$cases .= $k.' = (CASE '.$index."\n"
				.implode("\n", $v)."\n"
				.'ELSE '.$k.' END), ';
		}

		$this->where($index.' IN('.implode(',', $ids).')', NULL, FALSE);

		return 'UPDATE '.$table.' SET '.substr($cases, 0, -2).$this->_compile_wh('qb_where');
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
		$this->qb_limit = FALSE;
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
		return $sql.' LIMIT '.$this->qb_limit.($this->qb_offset ? ' OFFSET '.$this->qb_offset : '');
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	protected function _close()
	{
		pg_close($this->conn_id);
	}

}
