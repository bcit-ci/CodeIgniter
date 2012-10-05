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
 * PDO Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 * @since	2.1
 */
class CI_DB_pdo_result extends CI_DB_result {

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		if (is_int($this->num_rows))
		{
			return $this->num_rows;
		}
		elseif (count($this->result_array) > 0)
		{
			return $this->num_rows = count($this->result_array);
		}
		elseif (count($this->result_object) > 0)
		{
			return $this->num_rows = count($this->result_object);
		}
		elseif (($num_rows = $this->result_id->rowCount()) > 0)
		{
			return $this->num_rows = $num_rows;
		}

		return $this->num_rows = count($this->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return $this->result_id->columnCount();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @return	bool
	 */
	public function list_fields()
	{
		$field_names = array();
		for ($i = 0, $c = $this->num_fields(); $i < $c; $i++)
		{
			$field_names[$i] = @$this->result_id->getColumnMeta();
			$field_names[$i] = $field_names[$i]['name'];
		}

		return $field_names;
	}

	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @return	array
	 */
	public function field_data()
	{
		$data = array();

		try
		{
			if (strpos($this->result_id->queryString, 'PRAGMA') !== FALSE)
			{
				foreach ($this->result_array() as $field)
				{
					preg_match('/([a-zA-Z]+)(\(\d+\))?/', $field['type'], $matches);

					$F		= new stdClass();
					$F->name	= $field['name'];
					$F->type	= ( ! empty($matches[1])) ? $matches[1] : NULL;
					$F->default	= NULL;
					$F->max_length	= ( ! empty($matches[2])) ? preg_replace('/[^\d]/', '', $matches[2]) : NULL;
					$F->primary_key = (int) $field['pk'];
					$F->pdo_type	= NULL;

					$data[] = $F;
				}
			}
			else
			{
				for($i = 0, $max = $this->num_fields(); $i < $max; $i++)
				{
					$field = $this->result_id->getColumnMeta($i);

					$F		= new stdClass();
					$F->name	= $field['name'];
					$F->type	= $field['native_type'];
					$F->default	= NULL;
					$F->pdo_type	= $field['pdo_type'];

					if ($field['precision'] < 0)
					{
						$F->max_length	= NULL;
						$F->primary_key = 0;
					}
					else
					{
						$F->max_length	= ($field['len'] > 255) ? 0 : $field['len'];
						$F->primary_key = (int) ( ! empty($field['flags']) && in_array('primary_key', $field['flags']));
					}

					$data[] = $F;
				}
			}

			return $data;
		}
		catch (Exception $e)
		{
			if ($this->db->db_debug)
			{
				return $this->db->display_error('db_unsuported_feature');
			}

			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	void
	 */
	public function free_result()
	{
		if (is_object($this->result_id))
		{
			$this->result_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @return	array
	 */
	protected function _fetch_assoc()
	{
		return $this->result_id->fetch(PDO::FETCH_ASSOC);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @param	string
	 * @return	object
	 */
	protected function _fetch_object($class_name = 'stdClass')
	{
		return $this->result_id->fetchObject($class_name);
	}

}

/* End of file pdo_result.php */
/* Location: ./system/database/drivers/pdo/pdo_result.php */