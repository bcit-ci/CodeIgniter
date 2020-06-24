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
 * @since	Version 1.3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Postgres Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/database/
 */
class CI_DB_postgre_result extends CI_DB_result {

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		return is_int($this->num_rows)
			? $this->num_rows
			: $this->num_rows = pg_num_rows($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return pg_num_fields($this->result_id);
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
		for ($i = 0, $c = $this->num_fields(); $i < $c; $i++)
		{
			$field_names[] = pg_field_name($this->result_id, $i);
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
		for ($i = 0, $c = $this->num_fields(); $i < $c; $i++)
		{
			$retval[$i]			= new stdClass();
			$retval[$i]->name		= pg_field_name($this->result_id, $i);
			$retval[$i]->type		= pg_field_type($this->result_id, $i);
			$retval[$i]->max_length		= pg_field_size($this->result_id, $i);
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
			pg_free_result($this->result_id);
			$this->result_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset. We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero.
	 *
	 * @param	int	$n
	 * @return	bool
	 */
	public function data_seek($n = 0)
	{
		return pg_result_seek($this->result_id, $n);
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
		return pg_fetch_assoc($this->result_id);
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
		return pg_fetch_object($this->result_id, NULL, $class_name);
	}

}
