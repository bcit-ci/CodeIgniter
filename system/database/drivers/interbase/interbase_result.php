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

// ------------------------------------------------------------------------

/**
 * Interbase/Firebird Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_interbase_result extends CI_DB_result {

	public $num_rows;

	/**
	 * Number of rows in the result set
	 *
	 * @return	integer
	 */
	public function num_rows()
	{
		if( ! is_null($this->num_rows))
		{
			return $this->num_rows;
		}
		
		//Get the results so that you can get an accurate rowcount
		$this->result();
		
		return $this->num_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	integer
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
		for ($i = 0, $num_fields = $this->num_fields(); $i < $num_fields; $i++)
		{
			$info = ibase_field_info($this->result_id, $i);
		
			$F				= new stdClass();
			$F->name		= $info['name'];
			$F->type		= $info['type'];
			$F->max_length	= $info['length'];
			$F->primary_key = 0;
			$F->default		= '';

			$retval[] = $F;
		}

		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	null
	 */
	public function free_result()
	{
		@ibase_free_result($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset.  We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @return	array
	 */
	protected function _data_seek($n = 0)
	{
		//Set the row count to 0
		$this->num_rows = 0;
	
		//Interbase driver doesn't implement a suitable function
		return FALSE;	
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
		if (($row = @ibase_fetch_assoc($this->result_id, IBASE_FETCH_BLOBS)) !== FALSE)
		{
			//Increment row count
			$this->num_rows++;
		}
	
		return $row;
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
		if (($row = @ibase_fetch_object($this->result_id, IBASE_FETCH_BLOBS)) !== FALSE)
		{
			//Increment row count
			$this->num_rows++;
		}
		
		return $row;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Query result.  "object" version.
	 *
	 * @return	object
	 */
	public function result_object()
	{
		if (count($this->result_object) > 0)
		{
			return $this->result_object;
		}
		
		// Convert result array to object so that 
		// We don't have to get the result again
		if (count($this->result_array) > 0)
		{
			$i = 0;
		
			foreach ($this->result_array as $array)
			{
				$this->result_object[$i] = new StdClass();
			
				foreach ($array as $key => $val)
				{
					$this->result_object[$i]->{$key} = $val;
				}
				
				++$i;
			}
			
			return $this->result_object;
		}

		// In the event that query caching is on the result_id variable
		// will return FALSE since there isn't a valid SQL resource so
		// we'll simply return an empty array.
		if ($this->result_id === FALSE)
		{
			return array();
		}

		$this->num_rows = 0;
		while ($row = $this->_fetch_object())
		{
			$this->result_object[] = $row;
		}

		return $this->result_object;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result.  "array" version.
	 *
	 * @return	array
	 */
	public function result_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}
		
		// Since the object and array are really similar, just case
		// the result object to an array  if need be
		if (count($this->result_object) > 0)
		{
			foreach ($this->result_object as $obj)
			{
				$this->result_array[] = (array) $obj;
			}
		
			return $this->result_array;
		}

		// In the event that query caching is on the result_id variable
		// will return FALSE since there isn't a valid SQL resource so
		// we'll simply return an empty array.
		if ($this->result_id === FALSE)
		{
			return array();
		}

		$this->num_rows = 0;
		while ($row = $this->_fetch_assoc())
		{
			$this->result_array[] = $row;
		}

		return $this->result_array;
	}

}

/* End of file interbase_result.php */
/* Location: ./system/database/drivers/interbase/interbase_result.php */