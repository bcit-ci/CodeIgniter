<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright		Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright		Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CUBRID Forge Class
 *
 * @category	Database
 * @author		Esen Sagynov
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_cubrid_forge extends CI_DB_forge {

	/**
	 * Create database
	 *
	 * @access	private
	 * @param	string	the database name
	 * @return	bool
	 */
	function _create_database($name)
	{
		// CUBRID does not allow to create a database in SQL. The GUI tools
		// have to be used for this purpose.
		return FALSE;
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
		// CUBRID does not allow to drop a database in SQL. The GUI tools
		// have to be used for this purpose.
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Process Fields
	 *
	 * @access	private
	 * @param	mixed	the fields
	 * @return	string
	 */
	function _process_fields($fields)
	{
		$current_field_count = 0;
		$sql = '';

		foreach ($fields as $field=>$attributes)
		{
			// Numeric field names aren't allowed in databases, so if the key is
			// numeric, we know it was assigned by PHP and the developer manually
			// entered the field information, so we'll simply add it to the list
			if (is_numeric($field))
			{
				$sql .= "\n\t$attributes";
			}
			else
			{
				$attributes = array_change_key_case($attributes, CASE_UPPER);

				$sql .= "\n\t\"" . $this->db->_protect_identifiers($field) . "\"";

				if (array_key_exists('NAME', $attributes))
				{
					$sql .= ' '.$this->db->_protect_identifiers($attributes['NAME']).' ';
				}

				if (array_key_exists('TYPE', $attributes))
				{
					$sql .= ' '.$attributes['TYPE'];

					if (array_key_exists('CONSTRAINT', $attributes))
					{
						switch ($attributes['TYPE'])
						{
							case 'decimal':
							case 'float':
							case 'numeric':
								$sql .= '('.implode(',', $attributes['CONSTRAINT']).')';
								break;
							case 'enum': 	// As of version 8.4.0 CUBRID does not support
											// enum data type.
											break;
							case 'set':
								$sql .= '("'.implode('","', $attributes['CONSTRAINT']).'")';
								break;
							default:
								$sql .= '('.$attributes['CONSTRAINT'].')';
						}
					}
				}

				if (array_key_exists('UNSIGNED', $attributes) && $attributes['UNSIGNED'] === TRUE)
				{
					//$sql .= ' UNSIGNED';
					// As of version 8.4.0 CUBRID does not support UNSIGNED INTEGER data type.
					// Will be supported in the next release as a part of MySQL Compatibility.
				}

				if (array_key_exists('DEFAULT', $attributes))
				{
					$sql .= ' DEFAULT \''.$attributes['DEFAULT'].'\'';
				}

				if (array_key_exists('NULL', $attributes) && $attributes['NULL'] === TRUE)
				{
					$sql .= ' NULL';
				}
				else
				{
					$sql .= ' NOT NULL';
				}

				if (array_key_exists('AUTO_INCREMENT', $attributes) && $attributes['AUTO_INCREMENT'] === TRUE)
				{
					$sql .= ' AUTO_INCREMENT';
				}

				if (array_key_exists('UNIQUE', $attributes) && $attributes['UNIQUE'] === TRUE)
				{
					$sql .= ' UNIQUE';
				}
			}

			// don't add a comma on the end of the last field
			if (++$current_field_count < count($fields))
			{
				$sql .= ',';
			}
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Table
	 *
	 * @access	private
	 * @param	string	the table name
	 * @param	mixed	the fields
	 * @param	mixed	primary key(s)
	 * @param	mixed	key(s)
	 * @param	boolean	should 'IF NOT EXISTS' be added to the SQL
	 * @return	bool
	 */
	function _create_table($table, $fields, $primary_keys, $keys, $if_not_exists)
	{
		$sql = 'CREATE TABLE ';

		if ($if_not_exists === TRUE)
		{
			//$sql .= 'IF NOT EXISTS ';
			// As of version 8.4.0 CUBRID does not support this SQL syntax.
		}

		$sql .= $this->db->_escape_identifiers($table)." (";

		$sql .= $this->_process_fields($fields);

		// If there is a PK defined
		if (count($primary_keys) > 0)
		{
			$key_name = "pk_" . $table . "_" .
				$this->db->_protect_identifiers(implode('_', $primary_keys));
			
			$primary_keys = $this->db->_protect_identifiers($primary_keys);
			$sql .= ",\n\tCONSTRAINT " . $key_name . " PRIMARY KEY(" . implode(', ', $primary_keys) . ")";
		}

		if (is_array($keys) && count($keys) > 0)
		{
			foreach ($keys as $key)
			{
				if (is_array($key))
				{
					$key_name = $this->db->_protect_identifiers(implode('_', $key));
					$key = $this->db->_protect_identifiers($key);
				}
				else
				{
					$key_name = $this->db->_protect_identifiers($key);
					$key = array($key_name);
				}
				
				$sql .= ",\n\tKEY \"{$key_name}\" (" . implode(', ', $key) . ")";
			}
		}

		$sql .= "\n);";

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @access	private
	 * @return	string
	 */
	function _drop_table($table)
	{
		return "DROP TABLE IF EXISTS ".$this->db->_escape_identifiers($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Alter table query
	 *
	 * Generates a platform-specific query so that a table can be altered
	 * Called by add_column(), drop_column(), and column_alter(),
	 *
	 * @access	private
	 * @param	string	the ALTER type (ADD, DROP, CHANGE)
	 * @param	string	the column name
	 * @param	array	fields
	 * @param	string	the field after which we should add the new field
	 * @return	object
	 */
	function _alter_table($alter_type, $table, $fields, $after_field = '')
	{
		$sql = 'ALTER TABLE '.$this->db->_protect_identifiers($table)." $alter_type ";

		// DROP has everything it needs now.
		if ($alter_type == 'DROP')
		{
			return $sql.$this->db->_protect_identifiers($fields);
		}

		$sql .= $this->_process_fields($fields);

		if ($after_field != '')
		{
			$sql .= ' AFTER ' . $this->db->_protect_identifiers($after_field);
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Rename a table
	 *
	 * Generates a platform-specific query so that a table can be renamed
	 *
	 * @access	private
	 * @param	string	the old table name
	 * @param	string	the new table name
	 * @return	string
	 */
	function _rename_table($table_name, $new_table_name)
	{
		$sql = 'RENAME TABLE '.$this->db->_protect_identifiers($table_name)." AS ".$this->db->_protect_identifiers($new_table_name);
		return $sql;
	}

}

/* End of file cubrid_forge.php */
/* Location: ./system/database/drivers/cubrid/cubrid_forge.php */