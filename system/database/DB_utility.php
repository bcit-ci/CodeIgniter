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


}

?>