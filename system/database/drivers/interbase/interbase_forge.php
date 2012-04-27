<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */

/**
 * Interbase/Firebird Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_interbase_forge extends CI_DB_forge {

	protected $_drop_table	= 'DROP TABLE %s';

	/**
	 * Create database
	 *
	 * @param	string	the database name
	 * @return	string
	 */
	public function create_database($db_name)
	{
		// Firebird databases are flat files, so a path is required

		// Hostname is needed for remote access
		empty($this->db->hostname) OR $db_name = $this->hostname.':'.$db_name;

		return parent::create_database('"'.$db_name.'"');
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @param	string	the database name
	 *		- not used in this driver, the current db is dropped
	 * @return	bool
	 */
	public function drop_database($db_name = '')
	{
		if ( ! ibase_drop_db($this->conn_id))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unable_to_drop') : FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Table
	 *
	 * @param	string	the table name
	 * @param	array	the fields
	 * @param	mixed	primary key(s)
	 * @param	mixed	key(s)
	 * @param	bool	should 'IF NOT EXISTS' be added to the SQL
	 * @return	string
	 */
	protected function _create_table($table, $fields, $primary_keys, $keys, $if_not_exists)
	{
		$sql = 'CREATE TABLE ';

		$sql .= $this->db->protect_identifiers($table)."(";
		$current_field_count = 0;

		foreach ($fields as $field => $attributes)
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

				$sql .= "\n\t".$this->db->protect_identifiers($field);

				$sql .=  ' '.$attributes['TYPE'];

				if (array_key_exists('CONSTRAINT', $attributes))
				{
					$sql .= '('.$attributes['CONSTRAINT'].')';
				}

				if (array_key_exists('UNSIGNED', $attributes) && $attributes['UNSIGNED'] === TRUE)
				{
					$sql .= ' UNSIGNED';
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
			}

			// don't add a comma on the end of the last field
			if (++$current_field_count < count($fields))
			{
				$sql .= ',';
			}
		}

		if (count($primary_keys) > 0)
		{
			$primary_keys = $this->db->protect_identifiers($primary_keys);
			$sql .= ",\n\tPRIMARY KEY (" . implode(', ', $primary_keys) . ")";
		}

		if (is_array($keys) && count($keys) > 0)
		{
			foreach ($keys as $key)
			{
				if (is_array($key))
				{
					$key = $this->db->protect_identifiers($key);
				}
				else
				{
					$key = array($this->db->protect_identifiers($key));
				}

				$sql .= ",\n\tUNIQUE (" . implode(', ', $key) . ")";
			}
		}

		$sql .= "\n)";

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Alter table query
	 *
	 * Generates a platform-specific query so that a table can be altered
	 * Called by add_column(), drop_column(), and column_alter(),
	 *
	 * @param	string	the ALTER type (ADD, DROP, CHANGE)
	 * @param	string	the column name
	 * @param	string	the table name
	 * @param	string	the column definition
	 * @param	string	the default value
	 * @param	bool	should 'NOT NULL' be added
	 * @param	string	the field after which we should add the new field
	 * @return	string
	 */
	protected function _alter_table($alter_type, $table, $column_name, $column_definition = '', $default_value = '', $null = '', $after_field = '')
	{
		$sql = 'ALTER TABLE '.$this->db->protect_identifiers($table)." $alter_type ".$this->db->protect_identifiers($column_name);

		$sql .= " {$column_definition}";

		if ($default_value != '')
		{
			$sql .= " DEFAULT \"{$default_value}\"";
		}

		if ($null === NULL)
		{
			$sql .= ' NULL';
		}
		else
		{
			$sql .= ' NOT NULL';
		}

		if ($after_field != '')
		{
			$sql .= ' AFTER ' . $this->db->protect_identifiers($after_field);
		}

		return $sql;

	}

}

/* End of file interbase_forge.php */
/* Location: ./system/database/drivers/interbase/interbase_forge.php */