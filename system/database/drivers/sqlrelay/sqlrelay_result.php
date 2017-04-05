<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * SQL Relay Result Class
 *
 * This class extends the parent result class: CI_DB_result
 * 
 * @category	Database
 * @author		jjang9b
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlrelay_result extends CI_DB_result {

	/**
	 * Number of rows in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	public function num_rows()
	{
		return sqlrcur_rowCount($this->result_id);
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
		return sqlrcur_colCount($this->result_id);
	}
   
	// --------------------------------------------------------------------

    /**
     * Fetch Field Names
     *
     * Generates an array of column names
     *
     * @access  public
     * @return  array
     */
    public function list_fields()
    {
		// not use this method;
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
	public function field_data()
	{
        $retval = array();
        for($i=0;$i<sqlrcur_colCount($this->result_id);$i++)
        {
            $F              = new stdClass();
            $F->name        = strtolower(sqlrcur_getColumnName($this->result_id, $i));
            $F->type        = sqlrcur_getColumnType($this->result_id, $i);
            $F->max_length  = sqlrcur_getColumnLength($this->result_id, $i);
            $F->primary_key = sqlrcur_getColumnIsPrimaryKey($this->result_id, $i);
            $F->default     = '';

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
			sqlrcur_free($this->result_id);
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
	 * @access	protected
	 * @return	array
	 */
	protected function _data_seek($n = 0)
	{
		RETURN FALSE;
		// not use thie method;
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function _fetch_assoc()
	{
        if(is_int($this->current_row))
        {
            $result = $this->_fetch_array();
            if($result != false)
            {
               foreach($result as $key=>$value)
               {
					$result[$key] = $value;
               }
            }
            return $result;
        }
	}

    // --------------------------------------------------------------------

    /**
     * Result - array
     *
     * Returns the result set as an array
     *
     * @access  private
     * @return  array
     */	
	private function _fetch_array()
	{
		$result = sqlrcur_getRowAssoc($this->result_id, $this->current_row);
		$this->current_row++;
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @access	protected
	 * @return	object
	 */
	protected function _fetch_object()
	{
        if(is_int($this->current_row))
        {
    	    $result = $this->_fetch_array();
            if($result != false)
            {
         	   $obj = new stdClass();
               foreach($result as $key=>$value)
               {
             	  	$obj->{$key} = $value;
               }
               $result = $obj;
            }
            return $result;
        }
	}
	
}
/* End of file sqlrelay_result.php */
/* Location: ./system/database/drivers/sqlrelay/sqlrelay_result.php */
