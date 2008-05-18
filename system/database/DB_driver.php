<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
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
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_driver {

	var $username;
	var $password;
	var $hostname;
	var $database;
	var $dbdriver		= 'mysql';
	var $dbprefix		= '';
	var $char_set		= 'utf8';
	var $dbcollat		= 'utf8_general_ci';
	var $autoinit		= TRUE; // Whether to automatically initialize the DB
	var $swap_pre		= '';
	var $port			= '';
	var $pconnect		= FALSE;
	var $conn_id		= FALSE;
	var $result_id		= FALSE;
	var $db_debug		= FALSE;
	var $benchmark		= 0;
	var $query_count	= 0;
	var $bind_marker	= '?';
	var $save_queries	= TRUE;
	var $queries		= array();
	var $query_times	= array();
	var $data_cache		= array();
	var $trans_enabled	= TRUE;
	var $trans_strict	= TRUE;
	var $_trans_depth	= 0;
	var $_trans_status	= TRUE; // Used with transactions to determine if a rollback should occur
	var $cache_on		= FALSE;
	var $cachedir		= '';
	var $cache_autodel	= FALSE;
	var $CACHE; // The cache class object


	// These are use with Oracle
	var $stmt_id;
	var $curs_id;
	var $limit_used;


	
	/**
	 * Constructor.  Accepts one parameter containing the database
	 * connection settings.
	 *
	 * @param array
	 */	
	function CI_DB_driver($params)
	{
		if (is_array($params))
		{
			foreach ($params as $key => $val)
			{
				$this->$key = $val;
			}
		}

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
	function initialize($create_db = FALSE)
	{
		// If an existing DB connection resource is supplied
		// there is no need to connect and select the database
		if (is_resource($this->conn_id) OR is_object($this->conn_id))
		{
			return TRUE;
		}
		
		// Connect to the database
		$this->conn_id = ($this->pconnect == FALSE) ? $this->db_connect() : $this->db_pconnect();

		// No connection?  Throw an error
		if ( ! $this->conn_id)
		{
			log_message('error', 'Unable to connect to the database');
			
			if ($this->db_debug)
			{
				$this->display_error('db_unable_to_connect');
			}
			return FALSE;
		}

		// Select the database
		if ($this->database != '')
		{
			if ( ! $this->db_select())
			{
				// Should we attempt to create the database?
				if ($create_db == TRUE)
				{ 
					// Load the DB utility class
					$CI =& get_instance();
					$CI->load->dbutil();
					
					// Create the DB
					if ( ! $CI->dbutil->create_database($this->database))
					{
						log_message('error', 'Unable to create database: '.$this->database);
					
						if ($this->db_debug)
						{
							$this->display_error('db_unable_to_create', $this->database);
						}
						return FALSE;				
					}
					else
					{
						// In the event the DB was created we need to select it
						if ($this->db_select())
						{
							if ( ! $this->db_set_charset($this->char_set, $this->dbcollat))
							{
								log_message('error', 'Unable to set database connection charset: '.$this->char_set);

								if ($this->db_debug)
								{
									$this->display_error('db_unable_to_set_charset', $this->char_set);
								}

								return FALSE;
							}
							
							return TRUE;
						}
					}
				}
			
				log_message('error', 'Unable to select database: '.$this->database);
			
				if ($this->db_debug)
				{
					$this->display_error('db_unable_to_select', $this->database);
				}
				return FALSE;
			}
			
			if ( ! $this->db_set_charset($this->char_set, $this->dbcollat))
			{
				log_message('error', 'Unable to set database connection charset: '.$this->char_set);
			
				if ($this->db_debug)
				{
					$this->display_error('db_unable_to_set_charset', $this->char_set);
				}
				
				return FALSE;
			}
		}

		return TRUE;
	}
		
	// --------------------------------------------------------------------

	/**
	 * The name of the platform in use (mysql, mssql, etc...)
	 *
	 * @access	public
	 * @return	string		
	 */	
	function platform()
	{
		return $this->dbdriver;
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
		
		if ($this->dbdriver == 'oci8')
		{
			return $sql;
		}
	
		$query = $this->query($sql);
		return $query->row('ver');
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
	function query($sql, $binds = FALSE, $return_object = TRUE)
	{
		if ($sql == '')
		{
			if ($this->db_debug)
			{
				log_message('error', 'Invalid query: '.$sql);
				return $this->display_error('db_invalid_query');
			}
			return FALSE;		
		}

		// Verify table prefix and replace if necessary
		if ( ($this->dbprefix != '' AND $this->swap_pre != '') AND ($this->dbprefix != $this->swap_pre) )
		{			
			$sql = preg_replace("/(\W)".$this->swap_pre."(\S+?)/", "\\1".$this->dbprefix."\\2", $sql);
		}
		
		// Is query caching enabled?  If the query is a "read type"
		// we will load the caching class and return the previously
		// cached query if it exists
		if ($this->cache_on == TRUE AND stristr($sql, 'SELECT'))
		{
			if ($this->_cache_init())
			{
				$this->load_rdriver();
				if (FALSE !== ($cache = $this->CACHE->read($sql)))
				{
					return $cache;
				}
			}
		}
		
		// Compile binds if needed
		if ($binds !== FALSE)
		{
			$sql = $this->compile_binds($sql, $binds);
		}

		// Save the  query for debugging
		if ($this->save_queries == TRUE)
		{
			$this->queries[] = $sql;
		}
		
		// Start the Query Timer
		$time_start = list($sm, $ss) = explode(' ', microtime());
	
		// Run the Query
		if (FALSE === ($this->result_id = $this->simple_query($sql)))
		{
			if ($this->save_queries == TRUE)
			{
				$this->query_times[] = 0;
			}
		
			// This will trigger a rollback if transactions are being used
			$this->_trans_status = FALSE;

			if ($this->db_debug)
			{
				// grab the error number and message now, as we might run some
				// additional queries before displaying the error
				$error_no = $this->_error_number();
				$error_msg = $this->_error_message();
				
				// We call this function in order to roll-back queries
				// if transactions are enabled.  If we don't call this here
				// the error message will trigger an exit, causing the 
				// transactions to remain in limbo.
				$this->trans_complete();

				// Log and display errors
				log_message('error', 'Query error: '.$this->_error_message());
				return $this->display_error(
										array(
												'Error Number: '.$error_no,
												$error_msg,
												$sql
											)
										);
			}
		
			return FALSE;
		}
		
		// Stop and aggregate the query time results
		$time_end = list($em, $es) = explode(' ', microtime());
		$this->benchmark += ($em + $es) - ($sm + $ss);

		if ($this->save_queries == TRUE)
		{
			$this->query_times[] = ($em + $es) - ($sm + $ss);
		}
		
		// Increment the query counter
		$this->query_count++;
		
		// Was the query a "write" type?
		// If so we'll simply return true
		if ($this->is_write_type($sql) === TRUE)
		{
			// If caching is enabled we'll auto-cleanup any
			// existing files related to this particular URI
			if ($this->cache_on == TRUE AND $this->cache_autodel == TRUE AND $this->_cache_init())
			{
				$this->CACHE->delete();
			}
		
			return TRUE;
		}
		
		// Return TRUE if we don't need to create a result object
		// Currently only the Oracle driver uses this when stored
		// procedures are used
		if ($return_object !== TRUE)
		{
			return TRUE;
		}
	
		// Load and instantiate the result driver	
		
		$driver 		= $this->load_rdriver();
		$RES 			= new $driver();
		$RES->conn_id	= $this->conn_id;
		$RES->result_id	= $this->result_id;
		$RES->num_rows	= $RES->num_rows();

		if ($this->dbdriver == 'oci8')
		{
			$RES->stmt_id		= $this->stmt_id;
			$RES->curs_id		= NULL;
			$RES->limit_used	= $this->limit_used;
		}
		
		// Is query caching enabled?  If so, we'll serialize the
		// result object and save it to a cache file.
		if ($this->cache_on == TRUE AND $this->_cache_init())
		{
			// We'll create a new instance of the result object
			// only without the platform specific driver since
			// we can't use it with cached data (the query result
			// resource ID won't be any good once we've cached the
			// result object, so we'll have to compile the data
			// and save it)
			$CR = new CI_DB_result();
			$CR->num_rows 		= $RES->num_rows();
			$CR->result_object	= $RES->result_object();
			$CR->result_array	= $RES->result_array();
			
			// Reset these since cached objects can not utilize resource IDs.
			$CR->conn_id		= NULL;
			$CR->result_id		= NULL;

			$this->CACHE->write($sql, $CR);
		}
		
		return $RES;
	}

	// --------------------------------------------------------------------

	/**
	 * Load the result drivers
	 *
	 * @access	public
	 * @return	string 	the name of the result class		
	 */		
	function load_rdriver()
	{
		$driver = 'CI_DB_'.$this->dbdriver.'_result';

		if ( ! class_exists($driver))
		{
			include_once(BASEPATH.'database/DB_result'.EXT);
			include_once(BASEPATH.'database/drivers/'.$this->dbdriver.'/'.$this->dbdriver.'_result'.EXT);
		}
		
		return $driver;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Simple Query
	 * This is a simplified version of the query() function.  Internally
	 * we only use it when running transaction commands since they do
	 * not require all the features of the main query() function.
	 *
	 * @access	public
	 * @param	string	the sql query
	 * @return	mixed		
	 */	
	function simple_query($sql)
	{
		if ( ! $this->conn_id)
		{
			$this->initialize();
		}

		return $this->_execute($sql);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Disable Transactions
	 * This permits transactions to be disabled at run-time.
	 *
	 * @access	public
	 * @return	void		
	 */	
	function trans_off()
	{
		$this->trans_enabled = FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Enable/disable Transaction Strict Mode
	 * When strict mode is enabled, if you are running multiple groups of
	 * transactions, if one group fails all groups will be rolled back.
	 * If strict mode is disabled, each group is treated autonomously, meaning
	 * a failure of one group will not affect any others
	 *
	 * @access	public
	 * @return	void		
	 */	
	function trans_strict($mode = TRUE)
	{
		$this->trans_strict = is_bool($mode) ? $mode : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Start Transaction
	 *
	 * @access	public
	 * @return	void		
	 */	
	function trans_start($test_mode = FALSE)
	{	
		if ( ! $this->trans_enabled)
		{
			return FALSE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			$this->_trans_depth += 1;
			return;
		}
		
		$this->trans_begin($test_mode);
	}

	// --------------------------------------------------------------------

	/**
	 * Complete Transaction
	 *
	 * @access	public
	 * @return	bool		
	 */	
	function trans_complete()
	{
		if ( ! $this->trans_enabled)
		{
			return FALSE;
		}
	
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 1)
		{
			$this->_trans_depth -= 1;
			return TRUE;
		}
	
		// The query() function will set this flag to FALSE in the event that a query failed
		if ($this->_trans_status === FALSE)
		{
			$this->trans_rollback();
			
			// If we are NOT running in strict mode, we will reset
			// the _trans_status flag so that subsequent groups of transactions
			// will be permitted.
			if ($this->trans_strict === FALSE)
			{
				$this->_trans_status = TRUE;
			}

			log_message('debug', 'DB Transaction Failure');			
			return FALSE;			
		}
		
		$this->trans_commit();
		return TRUE;	
	}

	// --------------------------------------------------------------------

	/**
	 * Lets you retrieve the transaction flag to determine if it has failed
	 *
	 * @access	public
	 * @return	bool		
	 */	
	function trans_status()
	{
		return $this->_trans_status;
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
		if (strpos($sql, $this->bind_marker) === FALSE)
		{
			return $sql;
		}
		
		if ( ! is_array($binds))
		{
			$binds = array($binds);
		}
		
		// Get the sql segments around the bind markers
		$segments = explode($this->bind_marker, $sql);

		// The count of bind should be 1 less then the count of segments
		// If there are more bind arguments trim it down
		if (count($binds) >= count($segments)) {
			$binds = array_slice($binds, 0, count($segments)-1);
		}

		// Construct the binded query
		$result = $segments[0];
		$i = 0;
		foreach ($binds as $bind)
		{
			$result .= $this->escape($bind);
			$result .= $segments[++$i];
		}

		return $result;
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
		if ( ! preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql))
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
	 * @param	integer	The number of decimal places
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
	 * Protect Identifiers
	 *
	 * This function adds backticks if appropriate based on db type
	 *
	 * @access	private
	 * @param	mixed	the item to escape
	 * @param	boolean	only affect the first word
	 * @return	mixed	the item with backticks
	 */
	function protect_identifiers($item, $first_word_only = FALSE)
	{
		return $this->_protect_identifiers($item, $first_word_only);
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
		switch (gettype($str))
		{
			case 'string'	:	$str = "'".$this->escape_str($str)."'";
				break;
			case 'boolean'	:	$str = ($str === FALSE) ? 0 : 1;
				break;
			default			:	$str = ($str === NULL) ? 'NULL' : $str;
				break;
		}		

		return $str;
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
		$fields = $this->list_fields($table);
		
		if ( ! is_array($fields))
		{
			return FALSE;
		}

		return current($fields);
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array of table names
	 *
	 * @access	public
	 * @return	array		
	 */	
	function list_tables($constrain_by_prefix = FALSE)
	{
		// Is there a cached result?
		if (isset($this->data_cache['table_names']))
		{
			return $this->data_cache['table_names'];
		}
	
		if (FALSE === ($sql = $this->_list_tables($constrain_by_prefix)))
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
				if (isset($row['TABLE_NAME']))
				{
					$retval[] = $row['TABLE_NAME'];
				}
				else
				{
					$retval[] = array_shift($row);
				}
			}
		}

		$this->data_cache['table_names'] = $retval;
		return $this->data_cache['table_names'];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Determine if a particular table exists
	 * @access	public
	 * @return	boolean
	 */
	function table_exists($table_name)
	{
		return ( ! in_array($this->prep_tablename($table_name), $this->list_tables())) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Fetch MySQL Field Names
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	array		
	 */
	function list_fields($table = '')
	{
		// Is there a cached result?
		if (isset($this->data_cache['field_names'][$table]))
		{
			return $this->data_cache['field_names'][$table];
		}
	
		if ($table == '')
		{
			if ($this->db_debug)
			{
				return $this->display_error('db_field_param_missing');
			}
			return FALSE;			
		}
		
		if (FALSE === ($sql = $this->_list_columns($this->prep_tablename($table))))
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
			if (isset($row['COLUMN_NAME']))
			{
				$retval[] = $row['COLUMN_NAME'];
			}
			else
			{
				$retval[] = current($row);
			}		
		}
		
		$this->data_cache['field_names'][$table] = $retval;
		return $this->data_cache['field_names'][$table];
	}

	// --------------------------------------------------------------------

	/**
	 * Determine if a particular field exists
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	function field_exists($field_name, $table_name)
	{	
		return ( ! in_array($field_name, $this->list_fields($table_name))) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * DEPRECATED - use list_fields()
	 */
	function field_names($table = '')
	{
		return $this->list_fields($table);
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
		
		$query = $this->query($this->_field_data($this->prep_tablename($table)));
		return $query->field_data();
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
				
		
		return $this->_insert($this->prep_tablename($table), $fields, $values);
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
		{
			return false;
		}
					
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
	
				if ($val !== '')
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

		return $this->_update($this->prep_tablename($table), $fields, $dest);
	}	

	// --------------------------------------------------------------------

	/**
	 * Tests whether the string has an SQL operator
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _has_operator($str)
	{
		$str = trim($str);
		if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
		{
			return FALSE;
		}

		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Prep the table name - simply adds the table prefix if needed
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string		
	 */	
	function prep_tablename($table = '')
	{
		// Do we need to add the table prefix?
		if ($this->dbprefix != '')
		{
			if (substr($table, 0, strlen($this->dbprefix)) != $this->dbprefix)
			{
				$table = $this->dbprefix.$table;
			}
		}

		return $table;
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
			$args = (func_num_args() > 1) ? array_splice(func_get_args(), 1) : null;

			return call_user_func_array($function, $args);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache Directory Path
	 *
	 * @access	public
	 * @param	string	the path to the cache directory
	 * @return	void
	 */		
	function cache_set_path($path = '')
	{
		$this->cachedir = $path;
	}

	// --------------------------------------------------------------------

	/**
	 * Enable Query Caching
	 *
	 * @access	public
	 * @return	void
	 */		
	function cache_on()
	{
		$this->cache_on = TRUE;
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Disable Query Caching
	 *
	 * @access	public
	 * @return	void
	 */	
	function cache_off()
	{
		$this->cache_on = FALSE;
		return FALSE;
	}
	

	// --------------------------------------------------------------------

	/**
	 * Delete the cache files associated with a particular URI
	 *
	 * @access	public
	 * @return	void
	 */		
	function cache_delete($segment_one = '', $segment_two = '')
	{
		if ( ! $this->_cache_init())
		{
			return FALSE;
		}
		return $this->CACHE->delete($segment_one, $segment_two);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete All cache files
	 *
	 * @access	public
	 * @return	void
	 */		
	function cache_delete_all()
	{
		if ( ! $this->_cache_init())
		{
			return FALSE;
		}

		return $this->CACHE->delete_all();
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the Cache Class
	 *
	 * @access	private
	 * @return	void
	 */	
	function _cache_init()
	{
		if (is_object($this->CACHE) AND class_exists('CI_DB_Cache'))
		{
			return TRUE;
		}
	
		if ( ! @include(BASEPATH.'database/DB_cache'.EXT))
		{
			return $this->cache_off();
		}
		
		$this->CACHE = new CI_DB_Cache($this); // pass db object to support multiple db connections and returned db objects
		return TRUE;
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
		if (is_resource($this->conn_id) OR is_object($this->conn_id))
		{
			$this->_close($this->conn_id);
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
	 * @return	string	sends the application/error_db.php template		
	 */	
	function display_error($error = '', $swap = '', $native = FALSE)
	{
		global $LANG;
		$LANG->load('db');

		$heading = $LANG->line('db_error_heading');

		if ($native == TRUE)
		{
			$message = $error;
		}
		else
		{
			$message = ( ! is_array($error)) ? array(str_replace('%s', $swap, $LANG->line($error))) : $error;
		}
		
		$error =& load_class('Exceptions');
		echo $error->show_error($heading, $message, 'error_db');
		exit;
	}
	
}


/* End of file DB_driver.php */
/* Location: ./system/database/DB_driver.php */