<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Database Driver Class
 * 
 * This is the platform-independent base DB implementation class.
 * This class will not be called directly. Rather, the adapter
 * class for the specific database will extend and instantiate it.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/database/
 */
class CI_DB_driver {

	var $username;
	var $password;
	var $hostname;
	var $database;
	var $dbdriver		= 'mysql';
	var $dbprefix		= '';
	var $port			= '';
	var $pconnect		= FALSE;
	var $conn_id		= FALSE;
	var $result_id		= FALSE;
	var $db_debug		= FALSE;
	var $benchmark		= 0;
	var $query_count	= 0;
	var $bind_marker	= '?';
	var $queries		= array();
	
	/**
	 * Constructor.  Accepts one parameter containing the database
	 * connection settings. 
	 *
	 * Database settings can be passed as discreet 
	 * parameters or as a data source name in the first 
	 * parameter. DSNs must have this prototype:
	 * $dsn = 'driver://username:password@hostname/database';
	 *
	 * @param mixed. Can be an array or a DSN string
	 */	
	function CI_DB_driver($params)
	{
		$this->initialize($params);
		log_message('debug', 'Database Driver Class Initialized');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Initialize Database Settings
	 *
	 * @access	private Called by the constructor
	 * @param	mixed
	 * @return	void
	 */	
	function initialize($params = '')
	{	
		if (is_array($params))
		{
			foreach (array('hostname' => '', 'username' => '', 'password' => '', 'database' => '', 'dbdriver' => 'mysql', 'dbprefix' => '', 'port' => '', 'pconnect' => FALSE, 'db_debug' => FALSE) as $key => $val)
			{
				$this->$key = ( ! isset($params[$key])) ? $val : $params[$key];
			}
		}
		elseif (strpos($params, '://'))
		{
			if (FALSE === ($dsn = @parse_url($params)))
			{
				log_message('error', 'Invalid DB Connection String');
			
				if ($this->db_debug)
				{
					return $this->display_error('db_invalid_connection_str');
				}
				return FALSE;			
			}
			
			$this->hostname = ( ! isset($dsn['host'])) ? '' : rawurldecode($dsn['host']);
			$this->username = ( ! isset($dsn['user'])) ? '' : rawurldecode($dsn['user']);
			$this->password = ( ! isset($dsn['pass'])) ? '' : rawurldecode($dsn['pass']);
			$this->database = ( ! isset($dsn['path'])) ? '' : rawurldecode(substr($dsn['path'], 1));
		}
	
		if ($this->pconnect == FALSE)
		{
			$this->conn_id = $this->db_connect();
		}
		else
		{
			$this->conn_id = $this->db_pconnect();
		}	
       
        if ( ! $this->conn_id)
        { 
			log_message('error', 'Unable to connect to the database');
			
            if ($this->db_debug)
            {
				$this->display_error('db_unable_to_connect');
            }
        }
		else
		{
			if ( ! $this->db_select())
			{
				log_message('error', 'Unable to select database: '.$this->database);
			
				if ($this->db_debug)
				{
					$this->display_error('db_unable_to_select', $this->database);
				}
			}	
		}	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Database Version Number.  Returns a string containing the 
	 * version of the database being used
	 *
	 * @access	public
	 * @return	string	
	 */	
	function version()
	{
		if (FALSE === ($sql = $this->_version()))
		{
            if ($this->db_debug)
            {
				return $this->display_error('db_unsupported_function');
            }
            return FALSE;        
		}
	
		$query = $this->query($sql);
		$row = $query->row();
		return $row->ver;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * Accepts an SQL string as input and returns a result object upon 
	 * successful execution of a "read" type query.  Returns boolean TRUE 
	 * upon successful execution of a "write" type query. Returns boolean 
	 * FALSE upon failure, and if the $db_debug variable is set to TRUE 
	 * will raise an error.
	 * 
	 * @access	public
	 * @param	string	An SQL query string
	 * @param	array	An array of binding data
	 * @return	mixed		 
	 */	
    function query($sql, $binds = FALSE)
    {    
		if ( ! $this->conn_id)
		{
			$this->initialize();
		}

		if ($sql == '')
		{
            if ($this->db_debug)
            {
				log_message('error', 'Invalid query: '.$sql);
				return $this->display_error('db_invalid_query');
            }
            return FALSE;        
		}
		
		// Compile binds if needed
		if ($binds !== FALSE)
		{
			$sql = $this->compile_binds($sql, $binds);
		}

		// Start the Query Timer
        $time_start = list($sm, $ss) = explode(' ', microtime());
        
        // Save the  query for debugging
        $this->queries[] = $sql;
        
		// Run the Query
        if (FALSE === ($this->result_id = $this->execute($sql, $this->conn_id)))
        { 
            if ($this->db_debug)
            {
				log_message('error', 'Query error: '.$this->error_message());
				return $this->display_error(
										array(
												'Error Number: '.$this->error_number(), 
												$this->error_message(),
												$sql
											)
										);
            }
          
          return FALSE;
        }
        
		// Stop and aggregate the query time results
		$time_end = list($em, $es) = explode(' ', microtime());
		$this->benchmark += ($em + $es) - ($sm + $ss);

		// Increment the query counter
        $this->query_count++;
        
		// Was the query a "write" type?
		// If so we'll return simply return true
		if ($this->is_write_type($sql) === TRUE)
		{
			return TRUE;
		}

		// Instantiate and return the DB result object
		$result = 'CI_DB_'.$this->dbdriver.'_result';
		
        $RES = new $result();
        $RES->conn_id	= $this->conn_id;
        $RES->db_debug	= $this->db_debug;
        $RES->result_id	= $this->result_id;

		return $RES;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Enables a native PHP function to be run, using a platform agnostic wrapper.
	 * 
	 * @access	public
	 * @param	string	the function name
	 * @param	mixed	any parameters needed by the function
	 * @return	mixed		 
	 */	
	function call_function($function)
	{
		$driver = ($this->dbdriver == 'postgre') ? 'pg_' : $this->dbdriver.'_';
	
		if (FALSE === strpos($driver, $function))
		{
			$function = $driver.$function;
		}
		
		if ( ! function_exists($function))
		{ 
			if ($this->db_debug)
			{
				return $this->display_error('db_unsupported_function');
			}
			return FALSE;			
		}
		else
		{
			$args = (func_num_args() > 1) ? array_shift(func_get_args()) : null;
			
			return call_user_func_array($function, $args); 
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Determines if a query is a "write" type. 
	 * 
	 * @access	public
	 * @param	string	An SQL query string
	 * @return	boolean		 
	 */	
	function is_write_type($sql)
	{
		if ( ! preg_match('/^\s*"?(INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql)) 
		{
			return FALSE;
		}
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Calculate the aggregate query elapsed time 
	 * 
	 * @access	public
	 * @param	intiger	The number of decimal places
	 * @return	integer		 
	 */	
	function elapsed_time($decimals = 6)
	{
		return number_format($this->benchmark, $decimals);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the total number of queries
	 * 
	 * @access	public
	 * @return	integer		 
	 */	
	function total_queries()
	{
		return $this->query_count;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the last query that was executed
	 * 
	 * @access	public
	 * @return	void		 
	 */	
	function last_query()
	{
		return end($this->queries);
	}
	
	// --------------------------------------------------------------------

	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 * Sets boolean and null types
	 * 
	 * @access	public
	 * @param	string
	 * @return	integer		 
	 */	
	function escape($str)
	{	
		if ( ! is_numeric($str)) // bug fix to ensure that numbers are not treated as strings.
		{
			switch (gettype($str))
			{
				case 'string'	:	$str = "'".$this->escape_str($str)."'";
					break;
				case 'boolean'	:	$str = ($str === FALSE) ? 0 : 1;
					break;
				default			:	$str = ($str === NULL) ? 'NULL' : $str;
					break;
			}		
		}
	
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of table names
	 * 
	 * @access	public
	 * @return	array		 
	 */	
	function tables()
	{      
		if (FALSE === ($sql = $this->_show_tables()))
		{
            if ($this->db_debug)
            {
				return $this->display_error('db_unsupported_function');
            }
            return FALSE;        
		}

		$retval = array();
		$query = $this->query($sql);
		
		if ($query->num_rows() > 0)
		{
			foreach($query->result_array() as $row)
			{
				$retval[] = array_shift($row);
			}
		}

		return $retval;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Determine if a particular table exists
	 * @access	public
	 * @return	boolean
	 */
	function table_exists($table_name)
	{		
		return ( ! in_array($this->dbprefix.$table_name, $this->tables())) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Fetch MySQL Field Names
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	array		 
	 */
    function field_names($table = '')
    {
    	if ($table == '')
    	{
			if ($this->db_debug)
			{
				return $this->display_error('db_field_param_missing');
			}
			return FALSE;			
    	}
    	
		if (FALSE === ($sql = $this->_show_columns($this->dbprefix.$table)))
		{
            if ($this->db_debug)
            {
				return $this->display_error('db_unsupported_function');
            }
            return FALSE;        
		}
    	
    	$query = $this->query($sql);
    	
    	$retval = array();
		foreach($query->result_array() as $row)
		{
			if ($this->dbdriver == 'mssql' AND isset($row['COLUMN_NAME']))
			{
				$retval[] = $row['COLUMN_NAME'];
			}
			else
			{
				$retval[] = current($row);
			}    	
		}
    	
    	return $retval;
    }
	
	// --------------------------------------------------------------------

	/**
	 * Returns an object with field data
	 * 
	 * @access	public
	 * @param	string	the table name
	 * @return	object		 
	 */	
	function field_data($table = '')
	{
    	if ($table == '')
    	{
			if ($this->db_debug)
			{
				return $this->display_error('db_field_param_missing');
			}
			return FALSE;			
    	}
    	
    	return $this->_field_data($this->dbprefix.$table);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Primary
	 *
	 * Retrieves the primary key.  It assumes that the row in the first
	 * position is the primary key
	 * 
	 * @access	public
	 * @param	string	the table name
	 * @return	string		 
	 */	
	function primary($table = '')
	{	
		$fields = $this->field_names($table);
		
		if ( ! is_array($fields))
		{
			return FALSE;
		}

		return current($fields);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Compile Bindings
	 * 
	 * @access	public
	 * @param	string	the sql statement
	 * @param	array	an array of bind data
	 * @return	string		 
	 */	
	function compile_binds($sql, $binds)
	{	
		if (FALSE === strpos($sql, $this->bind_marker))
		{
			return $sql;
		}
		
		if ( ! is_array($binds))
		{
			$binds = array($binds);
		}
		
		foreach ($binds as $val)
		{
			$val = $this->escape($val);
					
			// Just in case the replacement string contains the bind
			// character we'll temporarily replace it with a marker
			$val = str_replace($this->bind_marker, '{%bind_marker%}', $val);
			$sql = preg_replace("#".preg_quote($this->bind_marker, '#')."#", str_replace('$', '\$', $val), $sql, 1);
		}

		return str_replace('{%bind_marker%}', $this->bind_marker, $sql);		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Generate an insert string
	 * 
	 * @access	public
	 * @param	string	the table upon which the query will be performed
	 * @param	array	an associative array data of key/values
	 * @return	string		 
	 */	
	function insert_string($table, $data)
	{
		$fields = array();      
		$values = array();
		
		foreach($data as $key => $val) 
		{
			$fields[] = $key;
			$values[] = $this->escape($val);
		}

		return $this->_insert($this->dbprefix.$table, $fields, $values);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Generate an update string
	 * 
	 * @access	public
	 * @param	string	the table upon which the query will be performed
	 * @param	array	an associative array data of key/values
	 * @param	mixed	the "where" statement
	 * @return	string		 
	 */	
	function update_string($table, $data, $where)
	{
		if ($where == '')
			return false;
					
		$fields = array();
		foreach($data as $key => $val) 
		{
			$fields[$key] = $this->escape($val);
		}

		if ( ! is_array($where))
		{
			$dest = array($where);
		}
		else
		{
			$dest = array();
			foreach ($where as $key => $val)
			{
				$prefix = (count($dest) == 0) ? '' : ' AND ';
	
				if ($val != '')
				{
					if ( ! $this->_has_operator($key))
					{
						$key .= ' =';
					}
				
					$val = ' '.$this->escape($val);
				}
							
				$dest[] = $prefix.$key.$val;
			}
		}		

		return $this->_update($this->dbprefix.$table, $fields, $dest);
	}    
		
	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 * 
	 * @access	public
	 * @return	void		 
	 */	
    function close()
    {
        if (is_resource($this->conn_id))
        {
            $this->destroy($this->conn_id);
		}   
		$this->conn_id = FALSE;
    }
	
	// --------------------------------------------------------------------

	/**
	 * Display an error message
	 * 
	 * @access	public
	 * @param	string	the error message
	 * @param	string	any "swap" values
	 * @param	boolean	whether to localize the message
	 * @return	string	sends the application/errror_db.php template		 
	 */	
    function display_error($error = '', $swap = '', $native = FALSE) 
    {
		$LANG = new CI_Language();
		$LANG->load('db');

		$heading = 'MySQL Error';
		
		if ($native == TRUE)
		{
			$message = $error;
		}
		else
		{
			$message = ( ! is_array($error)) ? array(str_replace('%s', $swap, $LANG->line($error))) : $error;
		}

		if ( ! class_exists('CI_Exceptions'))
		{
			include_once(BASEPATH.'libraries/Exceptions.php');
		}
		
		$error = new CI_Exceptions();
		echo $error->show_error('An Error Was Encountered', $message, 'error_db');
		exit;

    }  
	
	// --------------------------------------------------------------------

	/**
	 * Field Data - old version - DEPRECATED
	 * 
	 * @deprecated	use $this->db->field_data() instead
	 */	
	function fields($table = '')
	{
		return $this->field_data($table);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Smart Escape String - old version - DEPRECATED
	 * 
	 * @deprecated	use $this->db->escape() instead
	 */	
	function smart_escape_str($str)
	{
		return $this->escape($str);
	}
}


/**
 * Database Result Class
 * 
 * This is the platform-independent result class.
 * This class will not be called directly. Rather, the adapter
 * class for the specific database will extend and instantiate it.
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/database/
 */
class CI_DB_result {

	var $conn_id		= FALSE;
	var $result_id		= FALSE;
	var $db_debug		= FALSE;
	var $result_array	= array();
	var $result_object	= array();
	var $current_row 	= 0;

	/**
	 * Query result.  Acts as a wrapper function for the following functions.
	 * 
	 * @access	public
	 * @param	string	can be "object" or "array"
	 * @return	mixed	either a result object or array	 
	 */	
	function result($type = 'object')
	{
		return ($type == 'object') ? $this->result_object() : $this->result_array();
	}
		
	// --------------------------------------------------------------------

	/**
	 * Query result.  "object" version.
	 * 
	 * @access	public
	 * @return	object 
	 */	
	function result_object()
	{
		if (count($this->result_object) > 0)
		{
			return $this->result_object;
		}

		while ($row = $this->_fetch_object())
		{
			$this->result_object[] = $row;
		}
		
		if (count($this->result_object) == 0)
		{
			return FALSE;
		}
		
		return $this->result_object;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result.  "array" version.
	 * 
	 * @access	public
	 * @return	array 
	 */	
	function result_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}
			
		while ($row = $this->_fetch_assoc())
		{
			$this->result_array[] = $row;
		}
		
		if (count($this->result_array) == 0)
		{
			return FALSE;
		}
		
		return $this->result_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result.  Acts as a wrapper function for the following functions.
	 * 
	 * @access	public
	 * @param	string	can be "object" or "array"
	 * @return	mixed	either a result object or array	 
	 */	
	function row($n = 0, $type = 'object')
	{
		return ($type == 'object') ? $this->row_object($n) : $this->row_array($n);
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a single result row - object version
	 * 
	 * @access	public
	 * @return	object 
	 */	
	function row_object($n = 0)
	{
		if (FALSE ===  ($result = $this->result_object()))
		{
			return FALSE;
		}
			
		if ($n != $this->current_row AND isset($result[$n]))
		{
			$this->current_row = $n;
		}
		
		return $result[$this->current_row];
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a single result row - array version
	 * 
	 * @access	public
	 * @return	array 
	 */	
	function row_array($n = 0)
	{
		if (FALSE ===  ($result = $this->result_array()))
		{
			return FALSE;
		}
			
		if ($n != $this->current_row AND isset($result[$n]))
		{
			$this->current_row = $n;
		}
		
		return $result[$this->current_row];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the "next" row
	 * 
	 * @access	public
	 * @return	object 
	 */	
	function next_row($type = 'object')
	{
		if (FALSE ===  ($result = $this->result($type)))
		{
			return FALSE;
		}

		if (isset($result[$this->current_row + 1]))
		{
			++$this->current_row;
		}
				
		return $result[$this->current_row];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the "previous" row
	 * 
	 * @access	public
	 * @return	object 
	 */	
	function previous_row($type = 'object')
	{
		if (FALSE ===  ($result = $this->result($type)))
		{
			return FALSE;
		}

		if (isset($result[$this->current_row - 1]))
		{
			--$this->current_row;
		}
		return $result[$this->current_row];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the "first" row
	 * 
	 * @access	public
	 * @return	object 
	 */	
	function first_row($type = 'object')
	{
		if (FALSE ===  ($result = $this->result($type)))
		{
			return FALSE;
		}
		return $result[0];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the "last" row
	 * 
	 * @access	public
	 * @return	object 
	 */	
	function last_row($type = 'object')
	{
		if (FALSE ===  ($result = $this->result($type)))
		{
			return FALSE;
		}
		return $result[count($result) -1];
	}	

}



/**
 * Database Field Class
 * 
 * This class will contain the field meta-data.  It 
 * is called by one of the field result functions
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/database/
 */
class CI_DB_field {
	var $name;
	var $type;
	var $default;
	var $max_length;
	var $primary_key;
}

?>