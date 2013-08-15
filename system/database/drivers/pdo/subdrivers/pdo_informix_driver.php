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
 * @since		Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PDO Informix Database Adapter Class
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
class CI_DB_pdo_informix_driver extends CI_DB_pdo_driver {

	/**
	 * Sub-driver
	 *
	 * @var	string
	 */
	public $subdriver = 'informix';

	// --------------------------------------------------------------------

	/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
	protected $_random_keyword = array('ASC', 'ASC'); // Currently not supported

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Builds the DSN if not already set.
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if (empty($this->dsn))
		{
			$this->dsn = 'informix:';

			// Pre-defined DSN
			if (empty($this->hostname) && empty($this->host) && empty($this->port) && empty($this->service))
			{
				if (isset($this->DSN))
				{
					$this->dsn .= 'DSN='.$this->DSN;
				}
				elseif ( ! empty($this->database))
				{
					$this->dsn .= 'DSN='.$this->database;
				}

				return;
			}

			if (isset($this->host))
			{
				$this->dsn .= 'host='.$this->host;
			}
			else
			{
				$this->dsn .= 'host='.(empty($this->hostname) ? '127.0.0.1' : $this->hostname);
			}

			if (isset($this->service))
			{
				$this->dsn .= '; service='.$this->service;
			}
			elseif ( ! empty($this->port))
			{
				$this->dsn .= '; service='.$this->port;
			}

			empty($this->database) OR $this->dsn .= '; database='.$this->database;
			empty($this->server) OR $this->dsn .= '; server='.$this->server;

			$this->dsn .= '; protocol='.(isset($this->protocol) ? $this->protocol : 'onsoctcp')
				.'; EnableScrollableCursors=1';
		}
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
		$sql = 'SELECT "tabname" FROM "systables"
			WHERE "tabid" > 99 AND "tabtype" = \'T\' AND LOWER("owner") = '.$this->escape(strtolower($this->username));

		if ($prefix_limit === TRUE && $this->dbprefix !== '')
		{
			$sql .= ' AND "tabname" LIKE \''.$this->escape_like_str($this->dbprefix)."%' "
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

		return 'SELECT "colname" FROM "systables", "syscolumns"
			WHERE "systables"."tabid" = "syscolumns"."tabid"
				AND "systables"."tabtype" = \'T\'
				AND LOWER("systables"."owner") = '.$this->escape(strtolower($owner)).'
				AND LOWER("systables"."tabname") = '.$this->escape(strtolower($table));
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
		$sql = 'SELECT "syscolumns"."colname" AS "name",
				CASE "syscolumns"."coltype"
					WHEN 0 THEN \'CHAR\'
					WHEN 1 THEN \'SMALLINT\'
					WHEN 2 THEN \'INTEGER\'
					WHEN 3 THEN \'FLOAT\'
					WHEN 4 THEN \'SMALLFLOAT\'
					WHEN 5 THEN \'DECIMAL\'
					WHEN 6 THEN \'SERIAL\'
					WHEN 7 THEN \'DATE\'
					WHEN 8 THEN \'MONEY\'
					WHEN 9 THEN \'NULL\'
					WHEN 10 THEN \'DATETIME\'
					WHEN 11 THEN \'BYTE\'
					WHEN 12 THEN \'TEXT\'
					WHEN 13 THEN \'VARCHAR\'
					WHEN 14 THEN \'INTERVAL\'
					WHEN 15 THEN \'NCHAR\'
					WHEN 16 THEN \'NVARCHAR\'
					WHEN 17 THEN \'INT8\'
					WHEN 18 THEN \'SERIAL8\'
					WHEN 19 THEN \'SET\'
					WHEN 20 THEN \'MULTISET\'
					WHEN 21 THEN \'LIST\'
					WHEN 22 THEN \'Unnamed ROW\'
					WHEN 40 THEN \'LVARCHAR\'
					WHEN 41 THEN \'BLOB/CLOB/BOOLEAN\'
					WHEN 4118 THEN \'Named ROW\'
					ELSE "syscolumns"."coltype"
				END AS "type",
				"syscolumns"."collength" as "max_length",
				CASE "sysdefaults"."type"
					WHEN \'L\' THEN "sysdefaults"."default"
					ELSE NULL
				END AS "default"
			FROM "syscolumns", "systables", "sysdefaults"
			WHERE "syscolumns"."tabid" = "systables"."tabid"
				AND "systables"."tabid" = "sysdefaults"."tabid"
				AND "syscolumns"."colno" = "sysdefaults"."colno"
				AND "systables"."tabtype" = \'T\'
				AND LOWER("systables"."owner") = '.$this->escape(strtolower($this->username)).'
				AND LOWER("systables"."tabname") = '.$this->escape(strtolower($table)).'
			ORDER BY "syscolumns"."colno"';

		return (($query = $this->query($sql)) !== FALSE)
			? $query->result_object()
			: FALSE;
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
		return 'TRUNCATE TABLE ONLY '.$table;
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
	 * @param	string	$sql	$SQL Query
	 * @return	string
	 */
	protected function _limit($sql)
	{
		$select = 'SELECT '.($this->qb_offset ? 'SKIP '.$this->qb_offset : '').'FIRST '.$this->qb_limit.' ';
		return preg_replace('/^(SELECT\s)/i', $select, $sql, 1);
	}

}

/* End of file pdo_informix_driver.php */
/* Location: ./system/database/drivers/pdo/subdrivers/pdo_informix_driver.php */