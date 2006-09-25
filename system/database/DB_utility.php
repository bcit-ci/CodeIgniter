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

	function CI_DB_utility()
	{
		// Assign the main database object to $this->db
		$obj =& get_instance();
		$this->db =& $obj->db;
	}


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
            if ($this->db->db_debug)
            {
				return $this->db->display_error('db_unsupported_function');
            }
            return FALSE;        
		}
		
        if ($this->db->dbdriver == 'oci8')
        {
			return $sql;
		}
	
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->ver;
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
		return ( ! in_array($this->db->dbprefix.$table_name, $this->tables())) ? FALSE : TRUE;
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
			if ($this->db->db_debug)
			{
				return $this->db->display_error('db_field_param_missing');
			}
			return FALSE;			
    	}
    	
		if (FALSE === ($sql = $this->_show_columns($this->db->dbprefix.$table)))
		{
            if ($this->db->db_debug)
            {
				return $this->db->display_error('db_unsupported_function');
            }
            return FALSE;        
		}
    	
    	$query = $this->db->query($sql);
    	
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
			if ($this->db->db_debug)
			{
				return $this->db->display_error('db_field_param_missing');
			}
			return FALSE;			
    	}
    	
    	return $this->_field_data($this->db->dbprefix.$table);
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




	
	function create_database()
	{
	}
	
	function drop_database()
	{
	}
	
	function show_databases()
	{
	}
	
	function create_table()
	{
	}
	
	function alter_table()
	{
	}
	
	function create_index()
	{
	}
	
	function drop_index()
	{
	}
	
	function optimize()
	{
	}



}

?>