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
 * Database Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_utility {

	var $db;
	var $cache = array();

	/**
	 * Constructor
	 *
	 * Grabs the CI super object instance so we can access it.
	 *
	 */	
	function CI_DB_utility()
	{
		// Assign the main database object to $this->db
		$obj =& get_instance();
		$this->db =& $obj->db;
		
		log_message('debug', "Database Utility Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Create database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */
	function create_database($db_name)
	{
		$sql = $this->_create_database($db_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */
	function drop_database($db_name)
	{
		$sql = $this->_drop_database($db_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * List databases
	 *
	 * @access	public
	 * @return	bool
	 */
	function list_databases()
	{	
		// Is there a cached result?
		if (isset($this->cache['db_names']))
		{
			return $this->cache['db_names'];
		}
	
		$query = $this->db->query($this->_list_database());
		$dbs = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$dbs[] = current($row);
			}
		}
			
		return $this->cache['db_names'] =& $dbs;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array of table names
	 * 
	 * @access	public
	 * @return	array		 
	 */	
	function list_tables()
	{
		// Is there a cached result?
		if (isset($this->cache['table_names']))
		{
			return $this->cache['table_names'];
		}
	
		if (FALSE === ($sql = $this->_list_tables()))
		{
            if ($this->db->db_debug)
            {
				return $this->db->display_error('db_unsupported_function');
            }
            return FALSE;        
		}

		$retval = array();
		$query = $this->db->query($sql);
		
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

		return $this->cache['table_names'] =& $retval;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Determine if a particular table exists
	 * @access	public
	 * @return	boolean
	 */
	function table_exists($table_name)
	{
		return ( ! in_array($this->db->dbprefix.$table_name, $this->list_tables())) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Optimize Table
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	bool
	 */
	function optimize_table($table_name)
	{
		$sql = $this->_optimize_table($table_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		$query = $this->db->query($sql);
		return current($query->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize Database
	 *
	 * @access	public
	 * @return	array
	 */
	function optimize_database()
	{
		$result = array();
		foreach ($this->list_tables() as $table_name)
		{
			$sql = $this->_optimize_table($table_name);
			
			if (is_bool($sql))
			{
				return $sql;
			}
			
			$query = $this->db->query($sql);
			
			// Build the result array...
			$res = current($query->result_array());
			$key = str_replace($this->db->database.'.', '', current($res));
			$keys = array_keys($res);
			unset($res[$keys[0]]);
			
			$result[$key] = $res;
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize Table
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	bool
	 */

	function repair_table($table_name)
	{
		$sql = $this->_repair_table($table_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		$query = $this->db->query($sql);
		return current($query->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	bool
	 */
	function drop_table($table_name)
	{
		$sql = $this->_drop_table($table_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate CVS from a query result object
	 *
	 * @access	public
	 * @param	object	The query result object
	 * @param	string	The delimiter - tab by default
	 * @param	string	The newline character - \n by default
	 * @return	string
	 */
	function cvs_from_result($query, $delim = "\t", $newline = "\n")
	{
		if ( ! is_object($query) OR ! method_exists($query, 'field_names'))
		{
			show_error('You must submit a valid result object');
		}	
	
		$out = '';
		
		// First generate the headings from the table column names
		foreach ($query->field_names() as $name)
		{
			$out .= $name.$delim;
		}
		
		$out = rtrim($out);
		$out .= $newline;
		
		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $item)
			{
				$out .= $item.$delim;			
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Generate XML data from a query result object
	 *
	 * @access	public
	 * @param	object	The query result object
	 * @param	array	Any preferences
	 * @return	string
	 */
	function xml_from_result($query, $params = array())
	{
		if ( ! is_object($query) OR ! method_exists($query, 'field_names'))
		{
			show_error('You must submit a valid result object');
		}
		
		// Set our default values
		foreach (array('root' => 'root', 'element' => 'element', 'newline' => "\n", 'tab' => "\t") as $key => $val)
		{
			if ( ! isset($params[$key]))
			{
				$params[$key] = $val;
			}
		}
		
		// Create variables for convenience
		extract($params);
			
		// Load the xml helper
		$obj =& get_instance();
		$obj->load->helper('xml');

		// Generate the result
		$xml = "<{$root}/>".$newline;
		foreach ($query->result_array() as $row)
		{
			$xml .= $tab."<{$element}/>".$newline;
			
			foreach ($row as $key => $val)
			{
				$xml .= $tab.$tab."<{$key}>".xml_convert($val)."</{$key}>".$newline;
			}
			$xml .= $tab."</{$element}>".$newline;
		}
		$xml .= "</$root>".$newline;  
		
		return $xml;
	}

	// --------------------------------------------------------------------

	/**
	 * Database Backup
	 *
	 * @access	public
	 * @return	void
	 */
	function export()
	{
		// The individual driver overloads this method
	}


}

?>