<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * oci8 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_oci8_result extends CI_DB_result {

	public $stmt_id;
	public $curs_id;
	public $limit_used;

	/**
	 * Number of rows in the result set.
	 *
	 * Oracle doesn't have a graceful way to retun the number of rows
	 * so we have to use what amounts to a hack.
	 *
	 * @return  integer
	 */
	public function num_rows()
	{
		if ($this->num_rows === 0 && count($this->result_array()) > 0)
		{
			$this->num_rows = count($this->result_array());
			@oci_execute($this->stmt_id, OCI_DEFAULT);

			if ($this->curs_id)
			{
				@oci_execute($this->curs_id, OCI_DEFAULT);
			}
		}

		return $this->num_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @access  public
	 * @return  integer
	 */
	public function num_fields()
	{
		$count = @oci_num_fields($this->stmt_id);

		// if we used a limit we subtract it
		if ($this->limit_used)
		{
			$count = $count - 1;
		}

		return $count;
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
	 * @access  public
	 * @return  array
	 */
	public function field_data()
	{
		$retval = array();
		for ($c = 1, $fieldCount = $this->num_fields(); $c <= $fieldCount; $c++)
		{
			$F			= new stdClass();
			$F->name		= oci_field_name($this->stmt_id, $c);
			$F->type		= oci_field_type($this->stmt_id, $c);
			$F->max_length		= oci_field_size($this->stmt_id, $c);

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
		if (is_resource($this->result_id))
		{
			oci_free_statement($this->result_id);
			$this->result_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @access  protected
	 * @return  array
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
	 * @access  protected
	 * @return  object
	 */
	protected function _fetch_object()
	{
		$id = ($this->curs_id) ? $this->curs_id : $this->stmt_id;
		return @oci_fetch_object($id);
	}

	// --------------------------------------------------------------------

	/**
	 * Query result.  "array" version.
	 *
	 * @access  public
	 * @return  array
	 */
	public function result_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		$row = NULL;
		while ($row = $this->_fetch_assoc())
		{
			$this->result_array[] = $row;
		}

		return $this->result_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset.  We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function _data_seek($n = 0)
	{
		return FALSE; // Not needed
	}

}


/* End of file oci8_result.php */
/* Location: ./system/database/drivers/oci8/oci8_result.php */
