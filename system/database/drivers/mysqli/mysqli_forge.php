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
 * @since		Version 1.0
 * @filesource
 */

/**
 * MySQLi Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_mysqli_forge extends CI_DB_forge {

	protected $_create_database	= 'CREATE DATABASE %s CHARACTER SET %s COLLATE %s';

	/**
	 * Process Fields
	 *
	 * @param	mixed	the fields
	 * @return	string
	 */
	protected function _process_fields($fields)
	{
		$current_field_count = 0;
		$sql = '';

		foreach ($fields as $field => $attributes)
		{
			// Numeric field names aren't allowed in databases, so if the key is
			// numeric, we know it was assigned by PHP and the developer manually
			// entered the field information, so we'll simply add it to the list
			if (is_numeric($field))
			{
				$sql .= "\n\t".$attributes;
			}
			else
			{
				$attributes = array_change_key_case($attributes, CASE_UPPER);

				$sql .= "\n\t".$this->db->escape_identifiers($field);

				empty($attributes['NAME']) OR $sql .= ' '.$this->db->escape_identifiers($attributes['NAME']).' ';

				if ( ! empty($attributes['TYPE']))
				{
					$sql .= ' '.$attributes['TYPE'];

					if ( ! empty($attributes['CONSTRAINT']))
					{
						switch (strtolower($attributes['TYPE']))
						{
							case 'decimal':
							case 'float':
							case 'numeric':
								$sql .= '('.implode(',', $attributes['CONSTRAINT']).')';
								break;
							case 'enum':
							case 'set':
								$sql .= '("'.implode('","', $attributes['CONSTRAINT']).'")';
								break;
							default:
								$sql .= '('.$attributes['CONSTRAINT'].')';
						}
					}
				}

				if ( ! empty($attributes['UNSIGNED']) && $attributes['UNSIGNED'] === TRUE)
				{
					$sql .= ' UNSIGNED';
				}

				if (isset($attributes['DEFAULT']))
				{
					$sql .= " DEFAULT '".$attributes['DEFAULT']."'";
				}

				$sql .= ( ! empty($attributes['NULL']) && $attributes['NULL'] === TRUE)
					? ' NULL' : ' NOT NULL';

				if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE)
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

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Table
	 *
	 * @param	string	the table name
	 * @param	mixed	the fields
	 * @param	mixed	primary key(s)
	 * @param	mixed	key(s)
	 * @param	bool	should 'IF NOT EXISTS' be added to the SQL
	 * @return	bool
	 */
	protected function _create_table($table, $fields, $primary_keys, $keys, $if_not_exists)
	{
		$sql = 'CREATE TABLE ';

		if ($if_not_exists === TRUE)
		{
			$sql .= 'IF NOT EXISTS ';
		}

		$sql .= $this->db->escape_identifiers($table).' ('.$this->_process_fields($fields);

		if (count($primary_keys) > 0)
		{
			$key_name = $this->db->escape_identifiers(implode('_', $primary_keys));
			$sql .= ",\n\tPRIMARY KEY ".$key_name.' ('.implode(', ', $this->db->escape_identifiers($primary_keys)).')';
		}

		if (is_array($keys) && count($keys) > 0)
		{
			foreach ($keys as $key)
			{
				if (is_array($key))
				{
					$key_name = $this->db->escape_identifiers(implode('_', $key));
					$key = $this->db->escape_identifiers($key);
				}
				else
				{
					$key_name = $this->db->escape_identifiers($key);
					$key = array($key_name);
				}

				$sql .= ",\n\tKEY ".$key_name.' ('.implode(', ', $key).')';
			}
		}

		return $sql."\n) DEFAULT CHARACTER SET ".$this->db->char_set.' COLLATE '.$this->db->dbcollat.';';
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
	 * @param	array	fields
	 * @param	string	the field after which we should add the new field
	 * @return	string
	 */
	protected function _alter_table($alter_type, $table, $fields, $after_field = '')
	{
		$sql = 'ALTER TABLE '.$this->db->escape_identifiers($table).' '.$alter_type.' ';

		// DROP has everything it needs now.
		if ($alter_type === 'DROP')
		{
			return $sql.$this->db->escape_identifiers($fields);
		}

		return $sql.$this->_process_fields($fields)
			.($after_field !== '' ? ' AFTER '.$this->db->escape_identifiers($after_field) : '');
	}

}

/* End of file mysqli_forge.php */
/* Location: ./system/database/drivers/mysqli/mysqli_forge.php */