<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 2.1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PDO Utility Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/database/
 */
class CI_DB_pdo_utility extends CI_DB_utility {

	/**
	 * Export
	 *
	 * @param	array	$params	Preferences
	 * @return	mixed
	 */
	protected function _backup($params = array())
	{
		{
			if (count($params) === 0)
			{
				return FALSE;
			}

			// Extract the prefs for simplicity
			extract($params);

			// Build the output
			$output = '';

			// Do we need to include a statement to disable foreign key checks?
			if ($foreign_key_checks === FALSE)
			{
				$output .= 'SET foreign_key_checks = 0;'.$newline;
			}

			foreach ( (array) $tables as $table)
			{
				// Is the table in the "ignore" list?
				if (in_array($table, (array) $ignore, TRUE))
				{
					continue;
				}

				// Get the table schema
				$query = $this->db->query('SHOW CREATE TABLE '.$this->db->escape_identifiers($this->db->database.'.'.$table));

				// No result means the table name was invalid
				if ($query === FALSE)
				{
					continue;
				}

				// Write out the table schema
				$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

				if ($add_drop === TRUE)
				{
					$output .= 'DROP TABLE IF EXISTS '.$this->db->protect_identifiers($table).';'.$newline.$newline;
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
				if ($add_insert === FALSE)
				{
					continue;
				}

				// Grab all the data from the current table
				$query = $this->db->query('SELECT * FROM '.$this->db->protect_identifiers($table));

				if ($query->num_rows() === 0)
				{
					continue;
				}
				// Fetch the field names and determine if the field is an
				// integer type. We use this info to decide whether to
				// surround the data with quotes or not

				$field_str = '';
				$is_int = array();
				$column_count = $query->result_id->columnCount();
				for ($i=0;$i < $column_count; $i++){
					$field = $query->result_id->getColumnMeta($i);

					// Most versions of MySQL store timestamp as a string
					$is_int[$i] = in_array($field['native_type'], array('TINY', 'SMALL', 'MEDIUM', 'LONG'), TRUE);

					// Create a string of field names
					$field_str .= $this->db->escape_identifiers($field['name']).', ';
				}

				// Trim off the end comma
				$field_str = preg_replace('/, $/' , '', $field_str);

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
							$val_str .= ($is_int[$i] === FALSE) ? $this->db->escape($v) : $v;
						}

						// Append a comma
						$val_str .= ', ';
						$i++;
					}

					// Remove the comma at the end of the string
					$val_str = preg_replace('/, $/' , '', $val_str);

					// Build the INSERT string
					$output .= 'INSERT INTO '.$this->db->protect_identifiers($table).' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
				}

				$output .= $newline.$newline;
			}

			// Do we need to include a statement to re-enable foreign key checks?
			if ($foreign_key_checks === FALSE)
			{
				$output .= 'SET foreign_key_checks = 1;'.$newline;
			}

			return $output;
		}
	}

}
