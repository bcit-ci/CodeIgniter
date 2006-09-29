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
 * MySQL Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_mysql_utility extends CI_DB_utility {
	
	/**
	 * Create database
	 *
	 * @access	private
	 * @param	string	the database name
	 * @return	bool
	 */
	function _create_database($name)
	{
		return "CREATE DATABASE ".$name;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @access	private
	 * @param	string	the database name
	 * @return	bool
	 */
	function _drop_database($name)
	{
		return "DROP DATABASE ".$name;
	}

	// --------------------------------------------------------------------

	/**
	 * List databases
	 *
	 * @access	private
	 * @return	bool
	 */
	function _list_databases()
	{
		return "SHOW DATABASES";
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access	private
	 * @return	string
	 */
	function _list_tables()
	{
		return "SHOW TABLES FROM `".$this->db->database."`";		
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @access	private
	 * @return	bool
	 */
	function _drop_table($table)
	{
		return "DROP TABLE IF EXISTS ".$this->db->_escape_table($name);
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize table query
	 *
	 * Generates a platform-specific query so that a table can be optimized
	 *
	 * @access	private
	 * @param	string	the table name
	 * @return	object
	 */
	function _optimize_table($table)
	{
		return "OPTIMIZE TABLE ".$this->db->_escape_table($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Repair table query
	 *
	 * Generates a platform-specific query so that a table can be repaired
	 *
	 * @access	private
	 * @param	string	the table name
	 * @return	object
	 */
	function _repair_table($table)
	{
		return "REPAIR TABLE ".$this->db->_escape_table($table);
	}

	// --------------------------------------------------------------------

	/**
	 * MySQL Export
	 *
	 * @access	public
	 * @param	object	The query result object
	 * @param	array	Any preferences
	 * @return	string
	 */
	function export($params = array())
	{
		// Set up our default preferences
		$prefs = array(
							'tables'		=> array(),
							'ignore'		=> array(),
							'format'		=> 'gzip',
							'download'		=> TRUE,
							'filename'		=> date('Y-m-d-H:i', time()),
							'filepath'		=> '',
							'add_drop'		=> TRUE,
							'add_insert'	=> TRUE,
							'newline'		=> "\n"
						);

		// Did the user submit any preference overrides? If so set them....
		if (count($params) > 0)
		{
			foreach ($prefs as $key => $val)
			{
				if (isset($params[$key]))
				{
					$prefs[$key] = $params[$key];
				}
			}
		}

		// Extract the prefs for simplicity
		extract($prefs);
	
		// Are we backing up a complete database or individual tables?	
		if (count($tables) == 0)
		{
			$tables = $this->list_tables();
		}
	
	
	
		// Start buffering the output
		ob_start();
	
		// Build the output
        foreach ($tables as $table)
        { 
        	// Is the table in the "ignore" list?
			if (in_array($table, $ignore))
			{
        		continue;
        	}

        	// Get the table schema
			$query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.'.$table);
			
			// No result means the table name was invalid
        	if ($query === FALSE)
        	{
        		continue;
        	}
        	
        	// Write out the table schema
      
            echo $newline.$newline.'#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;
                
 			if ($add_drop == TRUE)
 			{
            	echo 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
			}
			
			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val)
			{
			    if ($i++ % 2)
			    { 			    	
			    	echo $val.';'.$newline.$newline;
			    }
			}
			
			// Build the insert statements
			
			if ($add_insert == FALSE)
			{
				continue;
			}
			
			$query = $this->db->query("SELECT * FROM $table");
			
			if ($query->num_rows() == 0)
			{
				continue;
			}
		
			// Grab the field names and determine if the field is an
			// integer type.  We use this info to decide whether to 
			// surround the data with quotes or not
			
			$i = 0;
			$fields = '';
			$is_int = array();
			while ($field = mysql_fetch_field($query->result_id))
			{
				$is_int[$i] = (in_array(
										mysql_field_type($query->result_id, $i), 
										array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'timestamp'), 
										TRUE)
										) ? TRUE : FALSE;
										
				// Create a string of field names
				$fields .= $field->name.', ';     
				$i++;
			}
					
			$fields = preg_replace( "/, $/" , "" , $fields);
			
			
			// Build the inserts
			foreach ($query->result_array() as $row)
			{
				$values = '';
			
				$i = 0;
				foreach ($row as $v)
				{
					$v = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $v);   
					$v = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $v);   
					$v = str_replace('\\', '\\\\',	$v);
					$v = str_replace('\'', '\\\'',	$v);
					$v = str_replace('\\\n', '\n',	$v);
					$v = str_replace('\\\r', '\r',	$v);
					$v = str_replace('\\\t', '\t',	$v);
				
					// Escape the data if it's not an integer type
					$values .= ($is_int[$i] == FALSE) ? $this->db->escape($v) : $v;
					$values .= ', ';
					
					$i++;
				}
				
				$values = preg_replace( "/, $/" , "" , $values);
				
				if ($download == FALSE)
				{
					$values = htmlspecialchars($values);
				}
				
				// Build the INSERT string
				echo 'INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.');'.$newline;
	
			}
			
			
			
			$buffer = ob_get_contents();
			@ob_end_clean(); 
			
			echo $buffer;
			
		}

	
	}


}

?>