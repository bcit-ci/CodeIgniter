<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

// ------------------------------------------------------------------------

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

	/**
	 * @var bool  Hold the flag whether a result handler already fetched before
	 */
	protected $is_fetched = FALSE;

	/**
	 * @var mixed Hold the fetched assoc array of a result handler
	 */
	protected $result_assoc;

	/**
	 * Number of rows in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_rows()
	{
		if (empty($this->result_id) OR ! is_object($this->result_id))
		{
			// invalid result handler
			return 0;
		}
		elseif (($num_rows = $this->result_id->rowCount()) && $num_rows > 0)
		{
			// If rowCount return something, we're done.
			return $num_rows;
		}

		// Fetch the result, instead perform another extra query
		return ($this->is_fetched && is_array($this->result_assoc)) ? count($this->result_assoc) : count($this->result_assoc());
	}

	/**
	 * Fetch the result handler
	 *
	 * @access	public
	 * @return	mixed
	 */
	function result_assoc()
	{
		// If the result already fetched before, use that one
		if (count($this->result_array) > 0 OR $this->is_fetched)
		{
			return $this->result_array();
		}

		// Define the output
		$output = array('assoc', 'object');

		// Fetch the result
		foreach ($output as $type)
		{
			// Define the method and handler
			$res_method  = '_fetch_'.$type;
			$res_handler = 'result_'.$type;
			
			$this->$res_handler = array();
			$this->_data_seek(0);

			while ($row = $this->$res_method())
			{
				$this->{$res_handler}[] = $row;
			}
		}

		// Save this as buffer and marked the fetch flag
		$this->result_array = $this->result_assoc;
		$this->is_fetched = TRUE;

		return $this->result_assoc;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_fields()
	{
		return $this->result_id->columnCount();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @access	public
	 * @return	array
	 */
	function list_fields()
	{
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @access	public
	 * @return	array
	 */
	function field_data()
	{
		$data = array();
	
		try
		{
			for($i = 0; $i < $this->num_fields(); $i++)
			{
				$data[] = $this->result_id->getColumnMeta($i);
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
	 * @return	null
	 */
	function free_result()
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
	 * @access	private
	 * @return	array
	 */
	function _data_seek($n = 0)
	{
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @access	private
	 * @return	array
	 */
	function _fetch_assoc()
	{
		return $this->result_id->fetch(PDO::FETCH_ASSOC);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @access	private
	 * @return	object
	 */
	function _fetch_object()
	{	
		return $this->result_id->fetchObject();
	}

}

/* End of file pdo_result.php */
/* Location: ./system/database/drivers/pdo/pdo_result.php */