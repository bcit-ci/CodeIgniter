<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.4.1
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * oci8 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_oci8_result extends CI_DB_result {

	/**
	 * Statement ID
	 *
	 * @var	resource
	 */
	public $stmt_id;

	/**
	 * Cursor ID
	 *
	 * @var	resource
	 */
	public $curs_id;

	/**
	 * Limit used flag
	 *
	 * @var	bool
	 */
	public $limit_used;

	/**
	 * Commit mode flag
	 *
	 * @var	int
	 */
	public $commit_mode;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	object	&$driver_object
	 * @return	void
	 */
	public function __construct(&$driver_object)
	{
		parent::__construct($driver_object);

		$this->stmt_id = $driver_object->stmt_id;
		$this->curs_id = $driver_object->curs_id;
		$this->limit_used = $driver_object->limit_used;
		$this->commit_mode =& $driver_object->commit_mode;
		$driver_object->stmt_id = FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		$count = oci_num_fields($this->stmt_id);

		// if we used a limit we subtract it
		return ($this->limit_used) ? $count - 1 : $count;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @return	array
	 */
	public function list_fields()
	{
		$field_names = array();
		for ($c = 1, $fieldCount = $this->num_fields(); $c <= $fieldCount; $c++)
		{
			$field_names[] = oci_field_name($this->stmt_id, $c);
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
		$retval = array();
		for ($c = 1, $fieldCount = $this->num_fields(); $c <= $fieldCount; $c++)
		{
			$F		= new stdClass();
			$F->name	= oci_field_name($this->stmt_id, $c);
			$F->type	= oci_field_type($this->stmt_id, $c);
			$F->max_length	= oci_field_size($this->stmt_id, $c);

			$retval[] = $F;
		}

		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	void
	 */
	public function free_result()
	{
		if (is_resource($this->result_id))
		{
			oci_free_statement($this->result_id);
			$this->result_id = FALSE;
		}

		if (is_resource($this->stmt_id))
		{
			oci_free_statement($this->stmt_id);
		}

		if (is_resource($this->curs_id))
		{
			oci_cancel($this->curs_id);
			$this->curs_id = NULL;
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
		$id = ($this->curs_id) ? $this->curs_id : $this->stmt_id;
		return oci_fetch_assoc($id);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @param	string	$class_name
	 * @return	object
	 */
	protected function _fetch_object($class_name = 'stdClass')
	{
		$row = ($this->curs_id)
			? oci_fetch_object($this->curs_id)
			: oci_fetch_object($this->stmt_id);

		if ($class_name === 'stdClass' OR ! $row)
		{
			return $row;
		}

		$class_name = new $class_name();
		foreach ($row as $key => $value)
		{
			$class_name->$key = $value;
		}

		return $class_name;
	}

}
