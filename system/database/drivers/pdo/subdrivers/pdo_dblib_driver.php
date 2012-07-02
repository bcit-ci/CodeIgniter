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
 * @since		Version 3.0.0
 * @filesource
 */

/**
 * PDO DBLIB Database Adapter Class
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
class CI_DB_pdo_dblib_driver extends CI_DB_pdo_driver {

	public $subdriver = 'dblib';

	protected $_random_keyword = ' NEWID()';

	protected $_quoted_identifier;

	/**
	 * Constructor
	 *
	 * Builds the DSN if not already set.
	 *
	 * @param	array
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if (empty($this->dsn))
		{
			$this->dsn = $params['subdriver'].':host='.(empty($this->hostname) ? '127.0.0.1' : $this->hostname);

			if ( ! empty($this->port))
			{
				$this->dsn .= (DIRECTORY_SEPARATOR === '\\' ? ',' : ':').$this->port;
			}

			empty($this->database) OR $this->dsn .= ';dbname='.$this->database;
			empty($this->char_set) OR $this->dsn .= ';charset='.$this->char_set;
			empty($this->appname) OR $this->dsn .= ';appname='.$this->appname;
		}
		else
		{
			if ( ! empty($this->char_set) && strpos($this->dsn, 'charset=', 6) === FALSE)
			{
				$this->dsn .= ';charset='.$this->char_set;
			}

			$this->subdriver = 'dblib';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Non-persistent database connection
	 *
	 * @param	bool
	 * @return	object
	 */
	public function db_connect($persistent = FALSE)
	{
		$this->conn_id = parent::db_connect($persistent);

		if ( ! is_object($this->conn_id))
		{
			return $this->conn_id;
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
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		return 'SELECT '.$this->escape_identifiers('name')
			.' FROM '.$this->escape_identifiers('sysobjects')
			.' WHERE '.$this->escape_identifiers('type')." = 'U'";

		if ($prefix_limit === TRUE && $this->dbprefix !== '')
		{
			$sql .= ' AND '.$this->escape_identifiers('name')." LIKE '".$this->escape_like_str($this->dbprefix)."%' "
				.sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql.' ORDER BY '.$this->escape_identifiers('name');
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
		return 'SELECT '.$this->escape_identifiers('column_name')
				.' FROM '.$this->escape_identifiers('information_schema.columns')
				.' WHERE '.$this->escape_identifiers('table_name').' = '.$this->escape($table);
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
		foreach ($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		$where = empty($where) ? '' : ' WHERE '.implode(' ', $where);

		if ( ! empty($like))
		{
			$where .= ($where === '' ? ' WHERE ' : ' AND ').implode(' ', $like);
		}

		return 'UPDATE '.$table.' SET '.implode(', ', $valstr).$where;
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

}

/* End of file pdo_dblib_driver.php */
/* Location: ./system/database/drivers/pdo/subdrivers/pdo_dblib_driver.php */
