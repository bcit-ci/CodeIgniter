<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * SQL Relay Database Adapter Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		jjang9b
 * @link		http://codeigniter.com/user_guide/database/
 */

class CI_DB_sqlrelay_driver extends CI_DB {

	var $dbdriver = 'sqlrelay';
	
	// These variables are used for stored_procedure function
	var $output_bool = FALSE;
	var $output_name = array();
	var $output_result = array();

	function __construct($params)
	{
		parent::__construct($params);
        require_once(BASEPATH.'database/drivers/'.$this->dbcase.'/'.$this->dbcase.'_driver.php');
		$class_name = "CI_DB_".$this->dbcase."_driver";
		
		if(! class_exists('CI_DB_each_driver'))
		{
			eval('class CI_DB_each_driver extends '.$class_name.' 
				  {
        	      	function __construct($params) { parent::__construct($params); }
            	    function call_1($method,$a){ return $this->$method($a); }
            	    function call_2($method,$a,$b){ return $this->$method($a,$b); }
            	    function call_3($method,$a,$b,$c){ return $this->$method($a,$b,$c); }
            	    function call_4($method,$a,$b,$c,$d){ return $this->$method($a,$b,$c,$d); }
            	    function call_5($method,$a,$b,$c,$d,$e){ return $this->$method($a,$b,$c,$d,$e); }
             	  }');
		}
        $this->CI_sqlrelay_driver = new CI_DB_each_driver($params);
	}

	/*
	 * Non-persistent database connection
	 *
	 * @access  private called by the base class
	 * @return  resource
	 */

	public function db_connect()
	{
		return sqlrcon_alloc($this->hostname, $this->port, "", $this->username, $this->password, 0, 1);
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @access  private called by the base class
	 * @return  resource
	 */

	public function db_pconnect()
	{
		return sqlrcon_alloc($this->hostname, $this->port, "", $this->username, $this->password, 0, 1);
	}

	// --------------------------------------------------------------------

	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 *
	 * @access	public
	 * @return	void
	 */

	public function reconnect()
	{
		// not supported in sqlrelay
		return $this->display_error('db_unsupported_function');
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @access  private called by the base class
	 * @return  resource
	 */

	public function db_select()
	{
 		// not supported in sqlrelay, but return true;
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

	public function db_set_charset($charset, $collation)
	{
 		// not supported in sqlrelay, but return true;
		return TRUE;

	}

	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @access  public
	 * @return  string
	 */

	protected function _version()
	{
		return sqlrcon_dbVersion($this->conn_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @access  private called by the base class
	 * @param   string  an SQL query
	 * @return  resource
	 */

	protected function _execute($sql)
	{
	    $this->get_cursor();
		sqlrcur_lowerCaseColumnNames($this->curs_id);

		if(!sqlrcur_sendQuery($this->curs_id,$sql)){
            $errstr = sqlrcur_errorMessage($this->curs_id);
            echo $errstr;
        }
		
	
        return $this->curs_id;
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

	private function _prep_query($sql)
	{
		return sqlrcur_prepareQuery($this->curs_id, $sql);
	}

	// --------------------------------------------------------------------

	/**
	 * getCursor.  Returns a cursor from the datbase
	 *
	 * @access  public
	 * @return  cursor id
	 */

    public function get_cursor()
    {
    	return $this->curs_id = sqlrcur_alloc($this->conn_id);
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
	 * name		no		the name of the parameter should be in :<param_name> format
	 * value	no		the value of the parameter.  If this is an OUT or IN OUT parameter,
	 *					this should be a reference to a variable
	 * type		yes		the type of the parameter
	 * length	yes		the max size of the parameter
	 */
	
	public function stored_procedure($package, $procedure, $params)
	{
		if($this->dbcase =='oci8')
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

			foreach ($params as $param)
			{
				$sql .= $param['name'] . ",";
			}
			$sql = trim($sql, ",") . "); end;";
	
			$this->get_cursor();	
			$this->_prep_query($sql);
			$this->_bind_params($params);

			$result = sqlrcur_executeQuery($this->curs_id);

			if($this->output_bool = TRUE)
			{
				foreach($this->output_name as $k)
				{	
					$this->output_result[$k] = sqlrcur_getOutputBindString($this->curs_id, $k);
				}
				return $this->output_result;
			}
			return $result;
		}
		else
		{
			return $this->display_error('db_unsupported_function');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Bind parameters
	 *
	 * @access  private
	 * @return  none
	 */
	
	private function _bind_params($params)
	{
		if ( ! is_array($params))
		{
			return;
		}

		foreach ($params as $param)
		{
			foreach (array('name', 'value') as $val)
			{
				if ( ! isset($param[$val]))
				{
					$param[$val] = '';
				}
			}
			$param['name'] = substr($param['name'], 1, strlen($param['name']));

			if(strtolower($param['value']) == '@out')
			{
				sqlrcur_defineOutputBindString($this->curs_id, $param['name'], 1000);
				$this->output_bool = TRUE;
				$this->output_name[] = $param['name']; 
			}
			else
			{
				sqlrcur_inputBind($this->curs_id, $param['name'], $param['value']);
			}
		}
	}	

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @access	public
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

        $this->_trans_failure = ($test_mode === TRUE) ? TRUE : FALSE;
		sqlrcon_autoCommitOff($this->conn_id);	
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @access	public
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

		$ref = sqlrcon_commit($this->conn_id);
		if($this->dbcase == 'oci8' OR $this->dbcase == 'odbc' OR $this->dbcase == 'mysql' OR $this->dbcase == 'mysqli' OR $this->dbcase == 'cubrid')
		{
	    	sqlrcon_autoCommitOn($this->conn_id);
		}
		return $ref;
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @access	public
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

        $ret = sqlrcon_rollback($this->conn_id);
		if($this->dbcase == 'oci8' OR $this->dbcase == 'odbc' OR $this->dbcase == 'mysql' OR $this->dbcase == 'mysqli' OR $this->dbcase == 'cubrid')
		{
			sqlrcon_autoCommitOn($this->conn_id);
		}
        return $ret;		
	}

	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @access  public
	 * @param   string
	 * @param	bool	whether or not the string will be used in a LIKE condition
	 * @return  string
	 */

	public function escape_str($str, $like = FALSE)
	{
		return $this->CI_sqlrelay_driver->call_2('escape_str', $str, $like);
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @access  public
	 * @return  integer
	 */

	public function affected_rows()
	{
		return sqlrcur_affectedRows($this->curs_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @access  public
	 * @return  integer
	 */

	public function insert_id()
	{
		// not supported in sqlrelay
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

	public function count_all($table = "")
	{
		$_count_string = $this->CI_sqlrelay_driver->_count_string;

        if ($table === "")
        {
            return 0;
        }

        $query = $this->query($_count_string . $this->_protect_identifiers('numrows') . " FROM " . $this->_protect_identifiers($table, TRUE, NULL, FALSE));

        if ($query->num_rows() == 0)
        {
            return 0;
        }

        $row = $query->row();
        return (int) $row->numrows;
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access  protected
	 * @param	boolean
	 * @return  string
	 */

	protected function _list_tables($prefix_limit = FALSE)
	{
		return $this->CI_sqlrelay_driver->call_1('_list_tables', $prefix_limit);
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access  protected
	 * @param   string  the table name
	 * @return  string
	 */

	protected function _list_columns($table = '')
	{
		return $this->CI_sqlrelay_driver->call_1('_list_columns', $table);
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @access  protected
	 * @param   string  the table name
	 * @return  object
	 */

	protected function _field_data($table)
	{
		return $this->CI_sqlrelay_driver->call_1('_field_data', $table);
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @access  protected
	 * @return  string
	 */

	protected function _error_message()
	{
		return sqlrcur_errorMessage($this->curs_id);
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @access  protected
	 * @return  integer
	 */

	protected function _error_number()
	{
		// not supported in sqlrelay, but return true;
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Escape the SQL Identifiers
	 *
	 * This function escapes column and table names
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */

	protected function _escape_identifiers($item)
	{
		return $this->CI_sqlrelay_driver->call_1('_escape_identifiers', $item);
	}

	// --------------------------------------------------------------------

	/**
	 * From Tables
	 *
	 * This function implicitly groups FROM tables so there is no confusion
	 * about operator precedence in harmony with SQL standards
	 *
	 * @access	protected
	 * @param	type
	 * @return	type
	 */

	protected function _from_tables($tables)
	{
		return $this->CI_sqlrelay_driver->call_1('_from_tables', $tables);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @access  protected
	 * @param   string  the table name
	 * @param   array   the insert keys
	 * @param   array   the insert values
	 * @return  string
	 */

	protected function _insert($table, $keys, $values)
	{
		return $this->CI_sqlrelay_driver->call_3('_insert', $table, $keys, $values);
	}

	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @access	protected
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @param	array	the orderby clause
	 * @param	array	the limit clause
	 * @return	string
	 */

	protected function _update($table, $values, $where, $orderby = array(), $limit = FALSE)
	{
		return $this->CI_sqlrelay_driver->call_5('_update', $table, $values, $where, $orderby, $limit);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 * If the database does not support the truncate() command
	 * This function maps to "DELETE FROM table"
	 *
	 * @access	protected
	 * @param	string	the table name
	 * @return	string
	 */

	protected function _truncate($table)
	{
		return $this->CI_sqlrelay_driver->call_1('_truncate', $table);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @access	protected
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @param	string	the limit clause
	 * @return	string
	 */

	protected function _delete($table, $where = array(), $like = array(), $limit = FALSE)
	{
		$params = array('ar_where' => $this->ar_where); 
		$this->CI_sqlrelay_driver = new CI_DB_each_driver($params);	
	    return $this->CI_sqlrelay_driver->call_4('_delete', $table, $where, $like, $limit);
	}

	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @access  protected
	 * @param   string  the sql query string
	 * @param   integer the number of rows to limit the query to
	 * @param   integer the offset value
	 * @return  string
	 */

	protected function _limit($sql, $limit, $offset)
	{
		return $this->CI_sqlrelay_driver->call_3('_limit', $sql, $limit, $offset);
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @access  protected
	 * @param   resource
	 * @return  void
	 */

	protected function _close($conn_id)
	{
		sqlrcon_endSession($conn_id);
		sqlrcon_free($conn_id);
	}

}
/* End of file sqlrelay_driver.php */
/* Location: ./system/database/drivers/sqlrelay/sqlrelay_driver.php */
