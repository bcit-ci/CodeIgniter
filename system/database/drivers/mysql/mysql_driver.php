<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MySQL Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_mysql_driver extends CI_DB {

	/**
	 * Database driver
	 *
	 * @var	string
	 */
	public $dbdriver = 'mysql';

	/**
	 * Compression flag
	 *
	 * @var	bool
	 */
	public $compress = FALSE;

	/**
	 * DELETE hack flag
	 *
	 * Whether to use the MySQL "delete hack" which allows the number
	 * of affected rows to be shown. Uses a preg_replace when enabled,
	 * adding a bit more processing to all queries.
	 *
	 * @var	bool
	 */
	public $delete_hack = TRUE;

	/**
	 * Strict ON flag
	 *
	 * Whether we're running in strict SQL mode.
	 *
	 * @var	bool
	 */
	public $stricton;

	// --------------------------------------------------------------------

	/**
	 * Identifier escape character
	 *
	 * @var	string
	 */
	protected $_escape_char = '`';

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

		if ( ! empty($this->port))
		{
			$this->hostname .= ':'.$this->port;
		}
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
		$client_flags = ($this->compress === FALSE) ? 0 : MYSQL_CLIENT_COMPRESS;

		if ($this->encrypt === TRUE)
		{
			$client_flags = $client_flags | MYSQL_CLIENT_SSL;
		}

		// Error suppression is necessary mostly due to PHP 5.5+ issuing E_DEPRECATED messages
		$this->conn_id = ($persistent === TRUE)
			? mysql_pconnect($this->hostname, $this->username, $this->password, $client_flags)
			: mysql_connect($this->hostname, $this->username, $this->password, TRUE, $client_flags);

		// ----------------------------------------------------------------

		// Select the DB... assuming a database name is specified in the config file
		if ($this->database !== '' && ! $this->db_select())
		{
			log_message('error', 'Unable to select database: '.$this->database);

			return ($this->db_debug === TRUE)
				? $this->display_error('db_unable_to_select', $this->database)
				: FALSE;
		}

		if (is_resource($this->conn_id))
		{
			if ( ! mysql_set_charset($this->char_set, $this->conn_id))
			{
				log_message('error', "Database: Unable to set the configured connection charset ('{$this->char_set}').");
				$this->close();
				return ($this->db->debug) ? $this->display_error('db_unable_to_set_charset', $this->char_set) : FALSE;
			}

			if (isset($this->stricton))
			{
				if ($this->stricton)
				{
					$this->simple_query('SET SESSION sql_mode = CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")');
				}
				else
				{
					$this->simple_query(
						'SET SESSION sql_mode =
						REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
						@@sql_mode,
						"STRICT_ALL_TABLES,", ""),
						",STRICT_ALL_TABLES", ""),
						"STRICT_ALL_TABLES", ""),
						"STRICT_TRANS_TABLES,", ""),
						",STRICT_TRANS_TABLES", ""),
						"STRICT_TRANS_TABLES", "")'
					);
				}
			}

			return $this->conn_id;
		}

		return FALSE;
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
		if (mysql_ping($this->conn_id) === FALSE)
		{
			$this->conn_id = FALSE;
		}
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

		if (mysql_select_db($database, $this->conn_id))
		{
			$this->database = $database;
			$this->data_cache = array();
			return TRUE;
		}

		return FALSE;
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

		if ( ! $this->conn_id OR ($version = mysql_get_server_info($this->conn_id)) === FALSE)
		{
			return FALSE;
		}

		return $this->data_cache['version'] = $version;
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	$sql	an SQL query
	 * @return	mixed
	 */
	protected function _execute($sql)
	{
		return mysql_query($this->_prep_query($sql), $this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @param	string	$sql	an SQL query
	 * @return	string
	 */
	protected function _prep_query($sql)
	{
		// mysql_affected_rows() returns 0 for "DELETE FROM TABLE" queries. This hack
		// modifies the query so that it a proper number of affected rows is returned.
		if ($this->delete_hack === TRUE && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql))
		{
			return trim($sql).' WHERE 1=1';
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_begin()
	{
		$this->simple_query('SET AUTOCOMMIT=0');
		return $this->simple_query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_commit()
	{
		if ($this->simple_query('COMMIT'))
		{
			$this->simple_query('SET AUTOCOMMIT=1');
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_rollback()
	{
		if ($this->simple_query('ROLLBACK'))
		{
			$this->simple_query('SET AUTOCOMMIT=1');
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Platform-dependent string escape
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _escape_str($str)
	{
		return mysql_real_escape_string($str, $this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return mysql_affected_rows($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insert_id()
	{
		return mysql_insert_id($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool	$prefix_limit
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = 'SHOW TABLES FROM '.$this->escape_identifiers($this->database);

		if ($prefix_limit !== FALSE && $this->dbprefix !== '')
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
	 * @param	string	$table
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return 'SHOW COLUMNS FROM '.$this->protect_identifiers($table, TRUE, NULL, FALSE);
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
		if (($query = $this->query('SHOW COLUMNS FROM '.$this->protect_identifiers($table, TRUE, NULL, FALSE))) === FALSE)
		{
			return FALSE;
		}
		$query = $query->result_object();

		$retval = array();
		for ($i = 0, $c = count($query); $i < $c; $i++)
		{
			$retval[$i]			= new stdClass();
			$retval[$i]->name		= $query[$i]->Field;

			sscanf($query[$i]->Type, '%[a-z](%d)',
				$retval[$i]->type,
				$retval[$i]->max_length
			);

			$retval[$i]->default		= $query[$i]->Default;
			$retval[$i]->primary_key	= (int) ($query[$i]->Key === 'PRI');
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
		return array('code' => mysql_errno($this->conn_id), 'message' => mysql_error($this->conn_id));
	}

	// --------------------------------------------------------------------

	/**
	 * FROM tables
	 *
	 * Groups tables in FROM clauses if needed, so there is no confusion
	 * about operator precedence.
	 *
	 * @return	string
	 */
	protected function _from_tables()
	{
		if ( ! empty($this->qb_join) && count($this->qb_from) > 1)
		{
			return '('.implode(', ', $this->qb_from).')';
		}

		return implode(', ', $this->qb_from);
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @return	void
	 */
	protected function _close()
	{
		// Error suppression to avoid annoying E_WARNINGs in cases
		// where the connection has already been closed for some reason.
		@mysql_close($this->conn_id);
	}

}
