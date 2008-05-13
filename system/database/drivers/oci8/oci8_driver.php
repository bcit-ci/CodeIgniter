<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package	 	CodeIgniter
 * @author	  	ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2006, EllisLab, Inc.
 * @license	 	http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since	   	Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * oci8 Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package	 	CodeIgniter
 * @subpackage  Drivers
 * @category	Database
 * @author	  	ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */

/**
 * oci8 Database Adapter Class
 *
 * This is a modification of the DB_driver class to
 * permit access to oracle databases
 *
 * NOTE: this uses the PHP 4 oci methods
 *
 * @author	  Kelly McArdle
 *
 */

class CI_DB_oci8_driver extends CI_DB {

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	var $_count_string = "SELECT COUNT(1) AS ";
	var $_random_keyword = ' ASC'; // not currently supported

	// Set "auto commit" by default
	var $_commit = OCI_COMMIT_ON_SUCCESS;

	// need to track statement id and cursor id
	var $stmt_id;
	var $curs_id;

	// if we use a limit, we will add a field that will
	// throw off num_fields later
	var $limit_used;

	/**
	 * Non-persistent database connection
	 *
	 * @access  private called by the base class
	 * @return  resource
	 */
	function db_connect()
	{
		return @ocilogon($this->username, $this->password, $this->hostname);
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @access  private called by the base class
	 * @return  resource
	 */
	function db_pconnect()
	{
		return @ociplogon($this->username, $this->password, $this->hostname);
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @access  private called by the base class
	 * @return  resource
	 */
	function db_select()
	{
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
		// TODO - add support if needed
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Version number query string
	 *
	 * @access  public
	 * @return  string
	 */
	function _version()
	{
		return ociserverversion($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @access  private called by the base class
	 * @param   string  an SQL query
	 * @return  resource
	 */
	function _execute($sql)
	{
		// oracle must parse the query before it is run. All of the actions with
		// the query are based on the statement id returned by ociparse
		$this->_set_stmt_id($sql);
		ocisetprefetch($this->stmt_id, 1000);
		return @ociexecute($this->stmt_id, $this->_commit);
	}

	/**
	 * Generate a statement ID
	 *
	 * @access  private
	 * @param   string  an SQL query
	 * @return  none
	 */
	function _set_stmt_id($sql)
	{
		if ( ! is_resource($this->stmt_id))
		{
			$this->stmt_id = ociparse($this->conn_id, $this->_prep_query($sql));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @access  private called by execute()
	 * @param   string  an SQL query
	 * @return  string
	 */
	function _prep_query($sql)
	{
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * getCursor.  Returns a cursor from the datbase
	 *
	 * @access  public
	 * @return  cursor id
	 */
	function get_cursor()
	{
		$this->curs_id = ocinewcursor($this->conn_id);
		return $this->curs_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Stored Procedure.  Executes a stored procedure
	 *
	 * @access  public
	 * @param   package	 package stored procedure is in
	 * @param   procedure   stored procedure to execute
	 * @param   params	  array of parameters
	 * @return  array
	 *
	 * params array keys
	 *
	 * KEY	  OPTIONAL	NOTES
	 * name	 no		  the name of the parameter should be in :<param_name> format
	 * value	no		  the value of the parameter.  If this is an OUT or IN OUT parameter,
	 *					  this should be a reference to a variable
	 * type	 yes		 the type of the parameter
	 * length   yes		 the max size of the parameter
	 */
	function stored_procedure($package, $procedure, $params)
	{
		if ($package == '' OR $procedure == '' OR ! is_array($params))
		{
			if ($this->db_debug)
			{
				log_message('error', 'Invalid query: '.$package.'.'.$procedure);
				return $this->display_error('db_invalid_query');
			}
			return FALSE;
		}
		
		// build the query string
		$sql = "begin $package.$procedure(";

		$have_cursor = FALSE;
		foreach($params as $param)
		{
			$sql .= $param['name'] . ",";
			
			if (array_key_exists('type', $param) && ($param['type'] == OCI_B_CURSOR))
			{
				$have_cursor = TRUE;
			}
		}
		$sql = trim($sql, ",") . "); end;";
				
		$this->stmt_id = FALSE;
		$this->_set_stmt_id($sql);
		$this->_bind_params($params);
		$this->query($sql, FALSE, $have_cursor);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Bind parameters
	 *
	 * @access  private
	 * @return  none
	 */
	function _bind_params($params)
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

			ocibindbyname($this->stmt_id, $param['name'], $param['value'], $param['length'], $param['type']);
		}
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
		$this->_trans_failure = ($test_mode === TRUE) ? TRUE : FALSE;
		
		$this->_commit = OCI_DEFAULT;
		return TRUE;
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

		$ret = OCIcommit($this->conn_id);
		$this->_commit = OCI_COMMIT_ON_SUCCESS;
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

		$ret = OCIrollback($this->conn_id);
		$this->_commit = OCI_COMMIT_ON_SUCCESS;
		return $ret;
	}

	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @access  public
	 * @param   string
	 * @return  string
	 */
	function escape_str($str)
	{
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @access  public
	 * @return  integer
	 */
	function affected_rows()
	{
		return @ocirowcount($this->stmt_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @access  public
	 * @return  integer
	 */
	function insert_id()
	{
		// not supported in oracle
		return $this->display_error('db_unsupported_function');
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @access  public
	 * @param   string
	 * @return  string
	 */
	function count_all($table = '')
	{
		if ($table == '')
			return '0';

		$query = $this->query($this->_count_string . $this->_protect_identifiers('numrows'). " FROM " . $this->_protect_identifiers($this->dbprefix.$table));

		if ($query == FALSE)
			{
			return 0;
			}

		$row = $query->row();
		return $row->NUMROWS;
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access  private
	 * @param	boolean
	 * @return  string
	 */
	function _list_tables($prefix_limit = FALSE)
	{
		$sql = "SELECT TABLE_NAME FROM ALL_TABLES";

		if ($prefix_limit !== FALSE AND $this->dbprefix != '')
		{
			$sql .= " WHERE TABLE_NAME LIKE '".$this->dbprefix."%'";
		}
		
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access  public
	 * @param   string  the table name
	 * @return  string
	 */
	function _list_columns($table = '')
	{
		return "SELECT COLUMN_NAME FROM all_tab_columns WHERE table_name = '$table'";
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @access  public
	 * @param   string  the table name
	 * @return  object
	 */
	function _field_data($table)
	{
		return "SELECT * FROM ".$this->_escape_table($table)." where rownum = 1";
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @access  private
	 * @return  string
	 */
	function _error_message()
	{
		$error = ocierror($this->conn_id);
		return $error['message'];
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @access  private
	 * @return  integer
	 */
	function _error_number()
	{
		$error = ocierror($this->conn_id);
		return $error['code'];
	}

	// --------------------------------------------------------------------

	/**
	 * Escape Table Name
	 *
	 * This function adds backticks if the table name has a period
	 * in it. Some DBs will get cranky unless periods are escaped
	 *
	 * @access  private
	 * @param   string  the table name
	 * @return  string
	 */
	function _escape_table($table)
	{
		if (strpos($table, '.') !== FALSE)
		{
			$table = '"' . str_replace('.', '"."', $table) . '"';
		}

		return $table;
	}

	// --------------------------------------------------------------------

	/**
	 * Protect Identifiers
	 *
	 * This function adds backticks if appropriate based on db type
	 *
	 * @access	private
	 * @param	mixed	the item to escape
	 * @param	boolean	only affect the first word
	 * @return	mixed	the item with backticks
	 */
	function _protect_identifiers($item, $first_word_only = FALSE)
	{
		if (is_array($item))
		{
			$escaped_array = array();

			foreach($item as $k=>$v)
			{
				$escaped_array[$this->_protect_identifiers($k)] = $this->_protect_identifiers($v, $first_word_only);
			}

			return $escaped_array;
		}	

		// This function may get "item1 item2" as a string, and so
		// we may need ""item1" "item2"" and not ""item1 item2""
		if (ctype_alnum($item) === FALSE)
		{
			if (strpos($item, '.') !== FALSE)
			{
				$aliased_tables = implode(".",$this->ar_aliased_tables).'.';
				$table_name =  substr($item, 0, strpos($item, '.')+1);
				$item = (strpos($aliased_tables, $table_name) !== FALSE) ? $item = $item : $this->dbprefix.$item;
			}

			// This function may get "field >= 1", and need it to return ""field" >= 1"
			$lbound = ($first_word_only === TRUE) ? '' : '|\s|\(';

			$item = preg_replace('/(^'.$lbound.')([\w\d\-\_]+?)(\s|\)|$)/iS', '$1"$2"$3', $item);
		}
		else
		{
			return "\"{$item}\"";
		}

		$exceptions = array('AS', '/', '-', '%', '+', '*', 'OR', 'IS');
		
		foreach ($exceptions as $exception)
		{
		
			if (stristr($item, " \"{$exception}\" ") !== FALSE)
			{
				$item = preg_replace('/ "('.preg_quote($exception).')" /i', ' $1 ', $item);
			}
		}
		return $item;
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
		
		return implode(', ', $tables);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @access  public
	 * @param   string  the table name
	 * @param   array   the insert keys
	 * @param   array   the insert values
	 * @return  string
	 */
	function _insert($table, $keys, $values)
	{
	return "INSERT INTO ".$this->_escape_table($table)." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
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
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
		
		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;
		
		$orderby = (count($orderby) >= 1)?' ORDER BY '.implode(", ", $orderby):'';
	
		$sql = "UPDATE ".$this->_escape_table($table)." SET ".implode(', ', $valstr);
		$sql .= ($where != '' AND count($where) >=1) ? " WHERE ".implode(" ", $where) : '';
		$sql .= $orderby.$limit;
		
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
		return "TRUNCATE TABLE ".$this->_escape_table($table);
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
			$conditions = "\nWHERE ";
			$conditions .= implode("\n", $this->ar_where);

			if (count($where) > 0 && count($like) > 0)
			{
				$conditions .= " AND ";
			}
			$conditions .= implode("\n", $like);
		}

		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;
	
		return "DELETE FROM ".$table.$conditions.$limit;
	}

	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @access  public
	 * @param   string  the sql query string
	 * @param   integer the number of rows to limit the query to
	 * @param   integer the offset value
	 * @return  string
	 */
	function _limit($sql, $limit, $offset)
	{
		$limit = $offset + $limit;
		$newsql = "SELECT * FROM (select inner_query.*, rownum rnum FROM ($sql) inner_query WHERE rownum < $limit)";

		if ($offset != 0)
		{
			$newsql .= " WHERE rnum >= $offset";
		}

		// remember that we used limits
		$this->limit_used = TRUE;

		return $newsql;
	}	

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @access  public
	 * @param   resource
	 * @return  void
	 */
	function _close($conn_id)
	{
		@ocilogoff($conn_id);
	}


}



/* End of file oci8_driver.php */
/* Location: ./system/database/drivers/oci8/oci8_driver.php */