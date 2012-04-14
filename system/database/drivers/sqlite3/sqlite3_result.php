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
 * SQLite Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite3_result extends CI_DB_result {

	// Overwriting the parent here, so we have a way to know if it's already set
	public $num_rows;

	// num_fields() might be called multiple times, so we'll use this one to cache it's result
	protected $_num_fields;

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		/* The SQLite3 driver doesn't have a graceful way to do this,
		 * so we'll have to do it on our own.
		 */
		return is_int($this->num_rows)
			? $this->num_rows
			: $this->num_rows = count($this->result_array());
	}

	// --------------------------------------------------------------------

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
	 * @return	object
	 */
	protected function _fetch_object()
	{
		// No native support for fetching as an object
		$row = $this->_fetch_assoc();
		return ($row !== FALSE) ? (object) $row : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. "array" version.
	 *
	 * return	array
	 */
	public function result_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}
		elseif (is_array($this->row_data))
		{
			if (count($this->row_data) === 0)
			{
				return $this->result_array;
			}
			else
			{
				$row_index = count($this->row_data);
			}
		}
		else
		{
			$row_index = 0;
			$this->row_data = array();
		}

		$row = NULL;
		while ($row = $this->_fetch_assoc())
		{
			$this->row_data[$row_index++] = $row;
		}

		return $this->result_array = $this->row_data;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. "object" version.
	 *
	 * @return	array
	 */
	public function result_object()
	{
		if (count($this->result_object) > 0)
		{
			return $this->result_object;
		}
		elseif (count($this->result_array) > 0)
		{
			for ($i = 0, $c = count($this->result_array); $i < $c; $i++)
			{
				$this->result_object[] = (object) $this->result_array[$i];
			}

			return $this->result_object;
		}
		elseif (is_array($this->row_data))
		{
			if (count($this->row_data) === 0)
			{
				return $this->result_object;
			}
			else
			{
				$row_index = count($this->row_data);
				for ($i = 0; $i < $row_index; $i++)
				{
					$this->result_object[$i] = (object) $this->row_data[$i];
				}
			}
		}
		else
		{
			$row_index = 0;
			$this->row_data = array();
		}

		$row = NULL;
		while ($row = $this->_fetch_assoc())
		{
			$this->row_data[$row_index] = $row;
			$this->result_object[$row_index++] = (object) $row;
		}

		$this->result_array = $this->row_data;

		/* As described for the num_rows() method - there's no easy
		 * way to get the number of rows selected. Our work-around
		 * solution (as in here as well) first checks if result_array
		 * exists and returns its count. It doesn't however check for
		 * custom_object_result, so - do it here.
		 */
		if ( ! is_int($this->num_rows))
		{
			$this->num_rows = count($this->result_object);
		}

		return $this->result_object;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. Custom object version.
	 *
	 * @param	string	class name used to instantiate rows to
	 * @return	array
	 */
	public function custom_result_object($class_name)
	{
		if (array_key_exists($class_name, $this->custom_result_object))
		{
			return $this->custom_result_object[$class_name];
		}

		if ( ! class_exists($class_name) OR ! is_object($this->result_id) OR $this->num_rows() === 0)
		{
			return array();
		}

		/* Even if result_array hasn't been set prior to custom_result_object being called,
		 * num_rows() has done it.
		*/
		$data = &$this->result_array;

		$result_object = array();
		for ($i = 0, $c = count($data); $i < $c; $i++)
		{
			$result_object[$i] = new $class_name();
			foreach ($data[$i] as $key => $value)
			{
				$result_object[$i]->$key = $value;
			}
		}

		/* As described for the num_rows() method - there's no easy
		 * way to get the number of rows selected. Our work-around
		 * solution (as in here as well) first checks if result_array
		 * exists and returns its count. It doesn't however check for
		 * custom_object_result, so - do it here.
		 */
		if ( ! is_int($this->num_rows))
		{
			$this->num_rows = count($result_object);
		}

		// Cache and return the array
		return $this->custom_result_object[$class_name] = $result_object;
        }

	// --------------------------------------------------------------------

	/* Single row result.
	 *
	 * Acts as a wrapper for row_object(), row_array()
	 * and custom_row_object(). Also used by first_row(), next_row()
	 * and previous_row().
	 *
	 * @param	int	row index
	 * @param	string	('object', 'array' or a custom class name)
	 * @return	mixed	whatever was passed to the second parameter
	 */
	public function row($n = 0, $type = 'object')
	{
		if ($type === 'object')
		{
			return $this->row_object($n);
		}
		elseif ($type === 'array')
		{
			return $this->row_array($n);
		}

		return $this->custom_row_object($n, $type);
	}

	// --------------------------------------------------------------------

	/* Single row result. Array version.
	 *
	 * @param	int	row index
	 * @return	array
	 */
	public function row_array($n = 0)
	{
		// Make sure $n is not a string
		if ( ! is_int($n))
		{
			$n = (int) $n;
		}

		/* If row_data is initialized, it means that we've already tried
		 * (at least) to fetch some data, so ... check if we already have
		 * this row.
		*/
		if (is_array($this->row_data))
		{
			/* If we already have row_data[$n] - return it.
			 *
			 * If we enter the elseif, there's a number of reasons to
			 * return an empty array:
			 *
			 *	- count($this->row_data) === 0 means there are no results
			 *	- num_rows being set or result_array having count() > 0 means
			 *	  that we've already fetched all data and $n is greater than
			 *	  our highest row index available
			 *	- $n < $this->current_row means that if such row existed,
			 *	  we would've already returned it, therefore $n is an
			 *	  invalid index
			 */
			if (isset($this->row_data[$n])) // We already have this row
			{
				$this->current_row = $n;
				return $this->row_data[$n];
			}
			elseif (count($this->row_data) === 0 OR is_int($this->num_rows)
				OR count($this->result_array) > 0 OR $n < $this->current_row)
			{
				// No such row exists
				return array();
			}

			// Get the next row index that would actually need to be fetched
			$current_row = ($this->current_row < count($this->row_data)) ? count($this->row_data) : $this->current_row + 1;
		}
		else
		{
			$current_row = $this->current_row = 0;
			$this->row_data = array();
		}

		/* Fetch more data, if available
		 *
		 * NOTE: Operator precedence is important here, if you change
		 *	 'AND' with '&&' - it WILL BREAK the results, as
		 *	 $row will be assigned the scalar value of both
		 *	 expressions!
		 */
		while ($row = $this->_fetch_assoc() AND $current_row <= $n)
		{
			$this->row_data[$current_row++] = $row;
		}

		// This would mean that there's no (more) data to fetch
		if ( ! is_array($this->row_data) OR ! isset($this->row_data[$n]))
		{
			// Cache what we already have
			if (is_array($this->row_data))
			{
				$this->num_rows = count($this->row_data);
				/* Usually, row_data could have less elements than result_array,
				 * but at this point - they should be exactly the same.
				 */
				$this->result_array = $this->row_data;
			}
			else
			{
				$this->num_rows = 0;
			}

			return array();
		}

		$this->current_row = $n;
		return $this->row_data[$n];
	}

	// --------------------------------------------------------------------

	/* Single row result. Object version.
	 *
	 * @param	int	row index
	 * @return	mixed	object if row found; empty array if not
	 */
	public function row_object($n = 0)
	{
		// Make sure $n is not a string
		if ( ! is_int($n))
		{
			$n = (int) $n;
		}

		/* Logic here is exactly the same as in row_array,
		 * except we have to cast row_data[$n] to an object.
		 *
		 * If we already have result_object though - we can
		 * directly return from it.
		 */
		if (isset($this->result_object[$n]))
		{
			$this->current_row = $n;
			return $this->result_object[$n];
		}

		$row = $this->row_array($n);
		// Cast only if the row exists
		if (count($row) > 0)
		{
			$this->current_row = $n;
			return (object) $row;
		}

		return array();
	}

	// --------------------------------------------------------------------

	/* Single row result. Custom object version.
	 *
	 * @param	int	row index
	 * @param	string	custom class name
	 * @return	mixed	custom object if row found; empty array otherwise
	 */
	public function custom_row_object($n = 0, $class_name)
	{
		// Make sure $n is not a string
		if ( ! is_int($n))
		{
			$n = (int) $n;
		}

		if (array_key_exists($class_name, $this->custom_result_object))
		{
			/* We already have a the whole result set with this class_name,
			 * return the specified row if it exists, and an empty array if
			 * it doesn't.
			 */
			if (isset($this->custom_result_object[$class_name][$n]))
			{
				$this->current_row = $n;
				return $this->custom_result_object[$class_name][$n];
			}
			else
			{
				return array();
			}
		}
		elseif ( ! class_exists($class_name)) // No such class exists
		{
			return array();
		}

		$row = $this->row_array($n);
		// An array would mean that the row doesn't exist
		if (is_array($row))
		{
			return $row;
		}

		// Convert to the desired class and return
		$row_object = new $class_name();
		foreach ($row as $key => $value)
		{
			$row_object->$key = $value;
		}

		$this->current_row = $n;
		return $row_object;
	}

	// --------------------------------------------------------------------

	/* First row result.
	 *
	 * @param	string	('object', 'array' or a custom class name)
	 * @return	mixed	whatever was passed to the second parameter
	 */
	public function first_row($type = 'object')
	{
		return $this->row(0, $type);
	}

	// --------------------------------------------------------------------

	/* Last row result.
	 *
	 * @param	string	('object', 'array' or a custom class name)
	 * @return	mixed	whatever was passed to the second parameter
	 */
	public function last_row($type = 'object')
	{
		$result = &$this->result($type);
		if ( ! isset($this->num_rows))
		{
			$this->num_rows = count($result);
		}
		$this->current_row = $this->num_rows - 1;
		return $result[$this->current_row];
	}

	// --------------------------------------------------------------------

	/* Next row result.
	 *
	 * @param	string	('object', 'array' or a custom class name)
	 * @return	mixed	whatever was passed to the second parameter
	 */
	public function next_row($type = 'object')
	{
		if (is_array($this->row_data))
		{
			$count = count($this->row_data);
			$n = ($this->current_row > $count OR ($this->current_row === 0 && $count === 0)) ? $count : $this->current_row + 1;
		}
		else
		{
			$n = 0;
		}

		return $this->row($n, $type);
	}

	// --------------------------------------------------------------------

	/* Previous row result.
	 *
	 * @param	string	('object', 'array' or a custom class name)
	 * @return	mixed	whatever was passed to the second parameter
	 */
	public function previous_row($type = 'object')
	{
		$n = ($this->current_row !== 0) ? $this->current_row - 1 : 0;
		return $this->row($n, $type);
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