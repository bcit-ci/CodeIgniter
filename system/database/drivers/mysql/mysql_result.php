<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
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
 * MySQL Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_mysql_result extends CI_DB_result {

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		return @mysql_num_rows($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return @mysql_num_fields($this->result_id);
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
		while ($field = mysql_fetch_field($this->result_id))
		{
			$field_names[] = $field->name;
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
		while ($field = mysql_fetch_object($this->result_id))
		{
			preg_match('/([a-zA-Z]+)(\(\d+\))?/', $field->Type, $matches);

			$F		= new stdClass();
			$F->name	= $field->Field;
			$F->type	= ( ! empty($matches[1])) ? $matches[1] : NULL;
			$F->default	= $field->Default;
			$F->max_length	= ( ! empty($matches[2])) ? preg_replace('/[^\d]/', '', $matches[2]) : NULL;
			$F->primary_key = (int) ($field->Key === 'PRI');

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
			mysql_free_result($this->result_id);
			$this->result_id = FALSE;
		}
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
		return mysql_data_seek($this->result_id, $n);
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
		return mysql_fetch_assoc($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @return	object
	 */
	protected function _fetch_object()
	{
		return mysql_fetch_object($this->result_id);
	}

}

/* End of file mysql_result.php */
/* Location: ./system/database/drivers/mysql/mysql_result.php */
