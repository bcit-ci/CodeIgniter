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
 * SQLite3 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/database/
 * @since	3.0
 */
class CI_DB_sqlite3_result extends CI_DB_result {

	// num_fields() might be called multiple times, so we'll use this one to cache it's result
	protected $_num_fields;

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return ( ! is_int($this->_num_fields))
			? $this->_num_fields = $this->result_id->numColumns()
			: $this->_num_fields;
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
			$field_names[] = $this->result_id->columnName($i);
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
		for ($i = 0, $c = $this->num_fields(); $i < $this->num_fields(); $i++)
		{
			$retval[$i]			= new stdClass();
			$retval[$i]->name		= $this->result_id->columnName($i);
			$retval[$i]->type		= 'varchar';
			$retval[$i]->max_length		= 0;
			$retval[$i]->primary_key	= 0;
			$retval[$i]->default		= '';
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
		if (is_object($this->result_id))
		{
			$this->result_id->finalize();
			$this->result_id = NULL;
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
		return $this->result_id->fetchArray(SQLITE3_ASSOC);
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
		// No native support for fetching rows as objects
		if (($row = $this->result_id->fetchArray(SQLITE3_ASSOC)) === FALSE)
		{
			return FALSE;
		}
		elseif ($class_name === 'stdClass')
		{
			return (object) $row;
		}

		$class_name = new $class_name();
		foreach (array_keys($row) as $key)
		{
			$class_name->$key = $row[$key];
		}

		return $class_name;
	}

	// --------------------------------------------------------------------

	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset. We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @return	array
	 */
	protected function _data_seek($n = 0)
	{
		// Only resetting to the start of the result set is supported
		return $this->result_id->reset();
	}

}

/* End of file sqlite3_result.php */
/* Location: ./system/database/drivers/sqlite3/sqlite3_result.php */