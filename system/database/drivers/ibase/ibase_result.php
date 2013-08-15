<?php
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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interbase/Firebird Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 * @since	3.0
 */
class CI_DB_ibase_result extends CI_DB_result {

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return @ibase_num_fields($this->result_id);
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
		for ($i = 0, $num_fields = $this->num_fields(); $i < $num_fields; $i++)
		{
			$info = ibase_field_info($this->result_id, $i);
			$field_names[] = $info['name'];
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
			$info = ibase_field_info($this->result_id, $i);

			$retval[$i]			= new stdClass();
			$retval[$i]->name		= $info['name'];
			$retval[$i]->type		= $info['type'];
			$retval[$i]->max_length		= $info['length'];
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
		@ibase_free_result($this->result_id);
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
		return @ibase_fetch_assoc($this->result_id, IBASE_FETCH_BLOBS);
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
		$row = @ibase_fetch_object($this->result_id, IBASE_FETCH_BLOBS);

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

/* End of file ibase_result.php */
/* Location: ./system/database/drivers/ibase/ibase_result.php */