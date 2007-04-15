<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MySQLi Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_mysqli_utility extends CI_DB_utility {
	
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
	 * Drop Table
	 *
	 * @access	private
	 * @return	bool
	 */
	function _drop_table($table)
	{
		return "DROP TABLE IF EXISTS ".$this->db->_escape_table($table);
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
	 * MySQLi Export
	 *
	 * @access	private
	 * @param	array	Preferences
	 * @return	mixed
	 */
	function _backup($params = array())
	{
		if (count($params) == 0)
		{
			return FALSE;
		}

		// Extract the prefs for simplicity
		extract($params);
	
		// Build the output
		$output = '';
		foreach ((array)$tables as $table)
		{
			// Is the table in the "ignore" list?
			if (in_array($table, (array)$ignore, TRUE))
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
			$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

 			if ($add_drop == TRUE)
 			{
				$output .= 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
			}
			
			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val)
			{
				if ($i++ % 2)
				{ 					
					$output .= $val.';'.$newline.$newline;
				}
			}
			
			// If inserts are not needed we're done...
			if ($add_insert == FALSE)
			{
				continue;
			}

			// Grab all the data from the current table
			$query = $this->db->query("SELECT * FROM $table");
			
			if ($query->num_rows() == 0)
			{
				continue;
			}
		
			// Fetch the field names and determine if the field is an
			// integer type.  We use this info to decide whether to
			// surround the data with quotes or not
			
			$i = 0;
			$field_str = '';
			$is_int = array();
			while ($field = mysqli_fetch_field($query->result_id))
			{
				$is_int[$i] = (in_array(
										strtolower(mysql_field_type($query->result_id, $i)),
										array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'timestamp'),
										TRUE)
										) ? TRUE : FALSE;
										
				// Create a string of field names
				$field_str .= $field->name.', ';
				$i++;
			}
			
			// Trim off the end comma
			$field_str = preg_replace( "/, $/" , "" , $field_str);
			
			
			// Build the insert string
			foreach ($query->result_array() as $row)
			{
				$val_str = '';
			
				$i = 0;
				foreach ($row as $v)
				{
					// Do a little formatting...
					$v = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $v);
					$v = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $v);
					$v = str_replace('\\', '\\\\',	$v);
					$v = str_replace('\'', '\\\'',	$v);
					$v = str_replace('\\\n', '\n',	$v);
					$v = str_replace('\\\r', '\r',	$v);
					$v = str_replace('\\\t', '\t',	$v);
				
					// Escape the data if it's not an integer type
					$val_str .= ($is_int[$i] == FALSE) ? $this->db->escape($v) : $v;
					$val_str .= ', ';
					
					$i++;
				}
				
				$val_str = preg_replace( "/, $/" , "" , $val_str);
								
				// Build the INSERT string
				$output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}
			
			$output .= $newline.$newline;
		}

		return $output;
	}



}

?>