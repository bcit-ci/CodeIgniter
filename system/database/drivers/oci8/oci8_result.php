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
 * @copyright   Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * oci8 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_oci8_result extends CI_DB_result {

	public $stmt_id;
	public $curs_id;
	public $limit_used;
	public $commit_mode;

	/* Overwriting the parent here, so we have a way to know if it's
	 * already called or not:
	 */
	public $num_rows;

	public function __construct(&$driver_object)
	{
		parent::__construct($driver_object);
		$this->stmt_id = $driver_object->stmt_id;
		$this->curs_id = $driver_object->curs_id;
		$this->limit_used = $driver_object->limit_used;
		$this->commit_mode =& $driver_object->commit_mode;
		$driver_object->stmt_id = FALSE;
	}

	/**
	 * Number of rows in the result set.
	 *
	 * Oracle doesn't have a graceful way to return the number of rows
	 * so we have to use what amounts to a hack.
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		if ( ! is_int($this->num_rows))
		{
			if (count($this->result_array) > 0)
			{
				return $this->num_rows = count($this->result_array);
			}
			elseif (count($this->result_object) > 0)
			{
				return $this->num_rows = count($this->result_object);
			}

			return $this->num_rows = count($this->result_array());
		}

		return $this->num_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		$count = @oci_num_fields($this->stmt_id);

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
	 * @return	object
	 */
	protected function _fetch_object()
	{
		$id = ($this->curs_id) ? $this->curs_id : $this->stmt_id;
		return oci_fetch_object($id);
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. Array version.
	 *
	 * @return	array
	 */
	public function result_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}
		elseif (count($this->result_object) > 0)
		{
			for ($i = 0, $c = count($this->result_object); $i < $c; $i++)
			{
				$this->result_array[$i] = (array) $this->result_object[$i];
			}

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
		while ($row = $this->_fetch_object())
		{
			$this->row_data[$row_index] = (array) $row;
			$this->result_object[$row_index++] = $row;
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
		if (isset($this->custom_result_object[$class_name]))
		{
			return $this->custom_result_object[$class_name];
		}

		if ( ! class_exists($class_name) OR $this->result_id === FALSE OR $this->num_rows() === 0)
		{
			return array();
		}

		/* Even if we didn't have result_array or result_object
		 * set prior to custom_result_object() being called,
		 * num_rows() has already done so.
		 * Pass by reference, as we don't know how
		 * large it might be and we don't want 1000 row
		 * sets being copied.
		 */
		if (count($this->result_array) > 0)
		{
			$data = &$this->result_array;
		}
		elseif (count($this->result_object) > 0)
		{
			$data = &$this->result_object;
		}

		$this->custom_result_object[$class_name] = array();
		for ($i = 0, $c = count($data); $i < $c; $i++)
		{
			$this->custom_result_object[$class_name][$i] = new $class_name();
			foreach ($data[$i] as $key => $value)
			{
				$this->custom_result_object[$class_name][$i]->$key = $value;
			}
		}

		return $this->custom_result_object[$class_name];
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
			 *	- num_rows being set, result_array and/or result_object
			 *	  having count() > 0 means that we've already fetched all
			 *	  data and $n is greater than our highest row index available
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
				OR count($this->result_array) > 0 OR count($this->result_object) > 0
				OR $n < $this->current_row)
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
			// Set this, if not already done.
			if ( ! is_int($this->num_rows))
			{
				$this->num_rows = count($this->result_object);
			}

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
			if ($this->current_row > $count OR ($this->current_row === 0 && $count === 0))
			{
				$n = $count;
			}
			else
			{
				$n = $this->current_row + 1;
			}
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
	 * result set starts at zero.
	 *
	 * Oracle's PHP extension doesn't have an easy way of doing this
	 * and the only workaround is to (re)execute the statement or cursor
	 * in order to go to the first (zero) index of the result set.
	 * Then, we would need to "dummy" fetch ($n - 1) rows to get to the
	 * right one.
	 *
	 * This is as ridiculous as it sounds and it's the reason why every
	 * other method that is fetching data tries to use an already "cached"
	 * result set. Keeping this just in case it becomes needed at
	 * some point in the future, but it will only work for resetting the
	 * pointer to zero.
	 *
	 * @return	bool
	 */
	protected function _data_seek()
	{
		/* The PHP manual says that if OCI_NO_AUTO_COMMIT mode
		 * is used, and oci_rollback() and/or oci_commit() are
		 * not subsequently called - this will cause an unnecessary
		 * rollback to be triggered at the end of the script execution.
		 *
		 * Therefore we'll try to avoid using that mode flag
		 * if we're not currently in the middle of a transaction.
		 */
		if ($this->commit_mode !== OCI_COMMIT_ON_SUCCESS)
		{
			$result = @oci_execute($this->stmt_id, $this->commit_mode);
		}
		else
		{
			$result = @oci_execute($this->stmt_id);
		}

		if ($result && $this->curs_id)
		{
			if ($this->commit_mode !== OCI_COMMIT_ON_SUCCESS)
			{
				return @oci_execute($this->curs_id, $this->commit_mode);
			}
			else
			{
				return @oci_execute($this->curs_id);
			}
		}

		return $result;
	}

}

/* End of file oci8_result.php */
/* Location: ./system/database/drivers/oci8/oci8_result.php */