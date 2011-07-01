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
 * MySQL Utility Class
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_mysql_utility extends CI_DB_utility {

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
		return "OPTIMIZE TABLE ".$this->db->_escape_identifiers($table);
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
		return "REPAIR TABLE ".$this->db->_escape_identifiers($table);
	}

	// --------------------------------------------------------------------
	/**
	 * MySQL Export
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
			while ($field = mysql_fetch_field($query->result_id))
			{
				// Most versions of MySQL store timestamp as a string
				$is_int[$i] = (in_array(
										strtolower(mysql_field_type($query->result_id, $i)),
										array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'),
										TRUE)
										) ? TRUE : FALSE;

				// Create a string of field names
				$field_str .= '`'.$field->name.'`, ';
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
					// Is the value NULL?
					if ($v === NULL)
					{
						$val_str .= 'NULL';
					}
					else
					{
						// Escape the data if it's not an integer
						if ($is_int[$i] == FALSE)
						{
							$val_str .= $this->db->escape($v);
						}
						else
						{
							$val_str .= $v;
						}
					}

					// Append a comma
					$val_str .= ', ';
					$i++;
				}

				// Remove the comma at the end of the string
				$val_str = preg_replace( "/, $/" , "" , $val_str);

				// Build the INSERT string
				$output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}

			$output .= $newline.$newline;
		}

		return $output;
	}
}

/* End of file mysql_utility.php */
/* Location: ./system/database/drivers/mysql/mysql_utility.php */