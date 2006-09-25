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
 * Database Result Class
 * 
 * This is the platform-independent result class.
 * This class will not be called directly. Rather, the adapter
 * class for the specific database will extend and instantiate it.
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
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
	 * Number of rows in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_rows()
	{
		// Implemented in the platform-specific result adapter
	}
	
	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_fields()
	{
		// Implemented in the platform-specific result adapter
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @access	public
	 * @return	array
	 */
	function field_names()
	{
		// Implemented in the platform-specific result adapter
	}

	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @access	public
	 * @return	array
	 */
	function field_data()
	{
		// Implemented in the platform-specific result adapter
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	null
	 */		
	function free_result()
	{
		// Implemented in the platform-specific result adapter
	}


}

?>