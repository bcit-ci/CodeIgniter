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
 * @since		Version 2.1.0
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
 */
class CI_DB_pdo_result extends CI_DB_result {

	public $result_array;
	public $result_object;
	public $num_rows;

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
		elseif (is_array($this->result_array))
		{
			return $this->num_rows = count($this->result_array);
		}
		elseif (is_array($this->result_object))
		{
			return $this->num_rows = count($this->result_object);
		}
		elseif (($this->num_rows = $this->result_id->rowCount()) > 0)
		{
			/* Not all subdrivers support rowCount() for
			 * returning the number of rows selected, but
			 * if the return value is greater than 0 - we
			 * can be sure that it's correct.
			 */
			return $this->num_rows;
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
		if ( ! method_exists($this->result_id, 'getColumnMeta'))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsuported_feature') : FALSE;
		}

		$field_names = array();
		for ($i = 0, $c = $this->num_fields(); $i < $c; $i++)
		{
			$meta = $this->result_id->getColumnMeta($i);
			$field_names[] = $meta['name'];
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
		if ( ! method_exists($this->result_id, 'getColumnMeta'))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsuported_feature') : FALSE;
		}

		$data = array();
		for ($i = 0, $c = $this->num_fields(); $i < $c; $i++)
		{
			$data[] = $this->result_id->getColumnMeta($i);
		}
		return $data;
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
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset.  We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @return	bool
	 */
	protected function _data_seek($n = 0)
	{
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
		return $this->result_id->fetch(PDO::FETCH_ASSOC);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @param	string	class name
	 * @return	object
	 */
	protected function _fetch_object($class_name = 'stdClass')
	{
		return method_exists($this->result_id, 'fetchObject')
			? $this->result_id->fetchObject($class_name)
			: $this->result_id->fetch(PDO::FETCH_CLASS);
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. Array version.
	 *
	 * @return	array
	 */
	public function result_array()
	{
		if (is_array($this->result_array))
		{
			return $this->result_array;
		}

		$this->result_array = array();

		if (is_array($this->result_object))
		{
			for ($i = 0, $c = count($this->result_object); $i < $c; $i++)
			{
				$this->result_array[$i] = (array) $this->result_object[$i];
			}
			return $this->result_array;
		}

		while ($row = $this->_fetch_assoc())
		{
			$this->result_array[] = $row;
		}

		return $this->result_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. "Object" version.
	 *
	 * @return	array
	 */
	public function result_object()
	{
		if (is_array($this->result_object))
		{
			return $this->result_object;
		}

		$this->result_object = array();

		if (is_array($this->result_array))
		{
			for ($i = 0, $c = count($this->result_array); $i < $c; $i++)
			{
				$this->result_object[$i] = (object) $this->result_array[$i];
			}
			return $this->result_object;
		}

		if ( ! method_exists($this->result_id, 'fetchObject'))
		{
			$this->result_id->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
		}

		while ($row = $this->_fetch_object())
		{
			$this->result_object[] = $row;
		}

		return $this->result_object;
	}

	// --------------------------------------------------------------------

	/**
	 * Query result. Custom object version.
	 *
	 * @param	string	class name
	 * @return	array
	 */
	public function custom_result_object($class_name)
	{
		if (isset($this->custom_result_object[$class_name]))
		{
			return $this->custom_result_object[$class_name];
		}
		elseif ( ! class_exists($class_name) OR $this->result_id === FALSE OR $this->num_rows === 0)
		{
			return array();
		}
		elseif (is_array($this->result_array))
		{
			$data =& $this->result_array;
		}
		elseif (is_array($this->result_object))
		{
			$data =& $this->result_object;
		}

		$this->custom_result_object[$class_name] = array();

		if (isset($data))
		{
			for ($i = 0, $c = count($data); $i < $c; $i++)
			{
				$this->custom_result_object[$class_name][$i] = new $class_name();
				foreach ($data[$i] as $key => $value)
				{
					$this->custom_result_object[$class_name][$i]->$key = $value;
				}
			}
		}
		else
		{
			if ( ! method_exists($this->result_id, 'fetchObject'))
			{
				$this->result_id->setFetchMode(PDO::FETCH_CLASS, $class_name);
			}

			while ($row = $this->_fetch_object($class_name))
			{
				$this->custom_result_object[$class_name][] = $row;
			}
		}

		return $this->custom_result_object[$class_name];
	}

}

/* End of file pdo_result.php */
/* Location: ./system/database/drivers/pdo/pdo_result.php */
