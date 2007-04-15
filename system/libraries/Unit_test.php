<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.3.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Unit Testing Class
 *
 * Simple testing class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	UnitTesting
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/uri.html
 */
class CI_Unit_test {

	var $active			= TRUE;
	var $results 		= array();
	var $strict			= FALSE;
	var $_template 		= NULL;
	var $_template_rows	= NULL;

	function CI_Unit_test()
	{
		log_message('debug', "Unit Testing Class Initialized");
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Run the tests
	 *
	 * Runs the supplied tests
	 *
	 * @access	public
	 * @param	mixed
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */	
	function run($test, $expected = TRUE, $test_name = 'undefined')
	{
		if ($this->active == FALSE)
			return FALSE;
			
		if (in_array($expected, array('is_string', 'is_bool', 'is_true', 'is_false', 'is_int', 'is_numeric', 'is_float', 'is_double', 'is_array', 'is_null'), TRUE))
		{
			$expected = str_replace('is_float', 'is_double', $expected);
			$result = ($expected($test)) ? TRUE : FALSE;	
			$extype = str_replace(array('true', 'false'), 'bool', str_replace('is_', '', $expected));
		}
		else
		{
			if ($this->strict == TRUE)
				$result = ($test === $expected) ? TRUE : FALSE;	
			else
				$result = ($test == $expected) ? TRUE : FALSE;	
			
			$extype = gettype($expected);
		}
				
		$back = $this->_backtrace();
	
		$report[] = array (
							'test_name'			=> $test_name,
							'test_datatype'		=> gettype($test),
							'res_datatype'		=> $extype,
							'result'			=> ($result === TRUE) ? 'passed' : 'failed',
							'file'				=> $back['file'],
							'line'				=> $back['line']
						);

		$this->results[] = $report;		
				
		return($this->report($this->result($report)));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Generate a report
	 *
	 * Displays a table with the test data
	 *
	 * @access	public
	 * @return	string
	 */
	function report($result = array())
	{	
		if (count($result) == 0)
		{
			$result = $this->result();
		}
		
		$this->_parse_template();
	
		$r = '';
		foreach ($result as $res)
		{
			$table = '';
		
			foreach ($res as $key => $val)
			{
				$temp = $this->_template_rows;			
				$temp = str_replace('{item}', $key, $temp);
				$temp = str_replace('{result}', $val, $temp);
				$table .= $temp;
			}
			
			$r .= str_replace('{rows}', $table, $this->_template);
		}
	
		return $r;	
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * Use strict comparison
	 *
	 * Causes the evaluation to use === rather then ==
	 *
	 * @access	public
	 * @param	bool
	 * @return	null
	 */
	function use_strict($state = TRUE)
	{
		$this->strict = ($state == FALSE) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Make Unit testing active
	 *
	 * Enables/disables unit testing
	 *
	 * @access	public
	 * @param	bool
	 * @return	null
	 */
	function active($state = TRUE)
	{
		$this->active = ($state == FALSE) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Result Array
	 *
	 * Returns the raw result data
	 *
	 * @access	public
	 * @return	array
	 */
	function result($results = array())
	{	
		$CI =& get_instance();
		$CI->load->language('unit_test');
		
		if (count($results) == 0)
		{
			$results = $this->results;
		}
		
		$retval = array();
		foreach ($results as $result)
		{
			$temp = array();
			foreach ($result as $key => $val)
			{
				if (is_array($val))
				{
					foreach ($val as $k => $v)
					{
						if (FALSE !== ($line = $CI->lang->line(strtolower('ut_'.$v))))
						{
							$v = $line;
						}				
						$temp[$CI->lang->line('ut_'.$k)] = $v;					
					}
				}
				else
				{
					if (FALSE !== ($line = $CI->lang->line(strtolower('ut_'.$val))))
					{
						$val = $line;
					}				
					$temp[$CI->lang->line('ut_'.$key)] = $val;
				}
			}
			
			$retval[] = $temp;
		}
	
		return $retval;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the template
	 *
	 * This lets us set the template to be used to display results
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_template($template)
	{
		$this->_template = $template;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generate a backtrace
	 *
	 * This lets us show file names and line numbers
	 *
	 * @access	private
	 * @return	array
	 */
	function _backtrace()
	{
		if (function_exists('debug_backtrace'))
		{
			$back = debug_backtrace();
			
			$file = ( ! isset($back['1']['file'])) ? '' : $back['1']['file'];
			$line = ( ! isset($back['1']['line'])) ? '' : $back['1']['line'];
						
			return array('file' => $file, 'line' => $line);
		}
		return array('file' => 'Unknown', 'line' => 'Unknown');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Default Template
	 *
	 * @access	private
	 * @return	string
	 */
	function _default_template()
	{	
		$this->_template = '	
		<div style="margin:15px;background-color:#ccc;">
		<table border="0" cellpadding="4" cellspacing="1" style="width:100%;">		
		{rows}
		</table></div>';
		
		$this->_template_rows = '
		<tr>
		<td style="background-color:#fff;width:140px;font-size:12px;font-weight:bold;">{item}</td>
		<td style="background-color:#fff;font-size:12px;">{result}</td>
		</tr>
		';	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Parse Template
	 *
	 * Harvests the data within the template {pseudo-variables}
	 *
	 * @access	private
	 * @return	void
	 */
 	function _parse_template()
 	{
 		if ( ! is_null($this->_template_rows))
 		{
 			return;
 		}
 		
 		if (is_null($this->_template))
 		{
 			$this->_default_template();
 			return;
 		}
 		
		if ( ! preg_match("/\{rows\}(.*?)\{\/rows\}/si", $this->_template, $match))
		{
 			$this->_default_template();
 			return;
		}

		$this->_template_rows = $match['1'];
		$this->_template = str_replace($match['0'], '{rows}', $this->_template); 	
 	}
 	
}
// END Unit_test Class

/**
 * Helper functions to test boolean true/false
 *
 *
 * @access	private
 * @return	bool
 */
function is_true($test)
{
	return (is_bool($test) AND $test === TRUE) ? TRUE : FALSE;
}
function is_false($test)
{
	return (is_bool($test) AND $test === FALSE) ? TRUE : FALSE;
}

?>