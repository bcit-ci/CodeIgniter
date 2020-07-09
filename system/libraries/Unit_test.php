<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.3.1
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Unit Testing Class
 *
 * Simple testing class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	UnitTesting
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/unit_testing.html
 */
class CI_Unit_test {

	/**
	 * Active flag
	 *
	 * @var	bool
	 */
	public $active = TRUE;

	/**
	 * Test results
	 *
	 * @var	array
	 */
	public $results = array();

	/**
	 * Strict comparison flag
	 *
	 * Whether to use === or == when comparing
	 *
	 * @var	bool
	 */
	public $strict = FALSE;

	/**
	 * Template
	 *
	 * @var	string
	 */
	protected $_template = NULL;

	/**
	 * Template rows
	 *
	 * @var	string
	 */
	protected $_template_rows = NULL;

	/**
	 * List of visible test items
	 *
	 * @var	array
	 */
	protected $_test_items_visible	= array(
		'test_name',
		'test_datatype',
		'res_datatype',
		'result',
		'file',
		'line',
		'notes'
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		log_message('info', 'Unit Testing Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Run the tests
	 *
	 * Runs the supplied tests
	 *
	 * @param	array	$items
	 * @return	void
	 */
	public function set_test_items($items)
	{
		if ( ! empty($items) && is_array($items))
		{
			$this->_test_items_visible = $items;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Run the tests
	 *
	 * Runs the supplied tests
	 *
	 * @param	mixed	$test
	 * @param	mixed	$expected
	 * @param	string	$test_name
	 * @param	string	$notes
	 * @return	string
	 */
	public function run($test, $expected = TRUE, $test_name = 'undefined', $notes = '')
	{
		if ($this->active === FALSE)
		{
			return FALSE;
		}

		if (in_array($expected, array('is_object', 'is_string', 'is_bool', 'is_true', 'is_false', 'is_int', 'is_numeric', 'is_float', 'is_double', 'is_array', 'is_null', 'is_resource'), TRUE))
		{
			$result = $expected($test);
			$extype = str_replace(array('true', 'false'), 'bool', str_replace('is_', '', $expected));
		}
		else
		{
			$result = ($this->strict === TRUE) ? ($test === $expected) : ($test == $expected);
			$extype = gettype($expected);
		}

		$back = $this->_backtrace();

		$report = array (
			'test_name'     => $test_name,
			'test_datatype' => gettype($test),
			'res_datatype'  => $extype,
			'result'        => ($result === TRUE) ? 'passed' : 'failed',
			'file'          => $back['file'],
			'line'          => $back['line'],
			'notes'         => $notes
		);

		$this->results[] = $report;

		return $this->report($this->result(array($report)));
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a report
	 *
	 * Displays a table with the test data
	 *
	 * @param	array	 $result
	 * @return	string
	 */
	public function report($result = array())
	{
		if (count($result) === 0)
		{
			$result = $this->result();
		}

		$CI =& get_instance();
		$CI->load->language('unit_test');

		$this->_parse_template();

		$r = '';
		foreach ($result as $res)
		{
			$table = '';

			foreach ($res as $key => $val)
			{
				if ($key === $CI->lang->line('ut_result'))
				{
					if ($val === $CI->lang->line('ut_passed'))
					{
						$val = '<span style="color: #0C0;">'.$val.'</span>';
					}
					elseif ($val === $CI->lang->line('ut_failed'))
					{
						$val = '<span style="color: #C00;">'.$val.'</span>';
					}
				}

				$table .= str_replace(array('{item}', '{result}'), array($key, $val), $this->_template_rows);
			}

			$r .= str_replace('{rows}', $table, $this->_template);
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Use strict comparison
	 *
	 * Causes the evaluation to use === rather than ==
	 *
	 * @param	bool	$state
	 * @return	void
	 */
	public function use_strict($state = TRUE)
	{
		$this->strict = (bool) $state;
	}

	// --------------------------------------------------------------------

	/**
	 * Make Unit testing active
	 *
	 * Enables/disables unit testing
	 *
	 * @param	bool
	 * @return	void
	 */
	public function active($state = TRUE)
	{
		$this->active = (bool) $state;
	}

	// --------------------------------------------------------------------

	/**
	 * Result Array
	 *
	 * Returns the raw result data
	 *
	 * @param	array	$results
	 * @return	array
	 */
	public function result($results = array())
	{
		$CI =& get_instance();
		$CI->load->language('unit_test');

		if (count($results) === 0)
		{
			$results = $this->results;
		}

		$retval = array();
		foreach ($results as $result)
		{
			$temp = array();
			foreach ($result as $key => $val)
			{
				if ( ! in_array($key, $this->_test_items_visible))
				{
					continue;
				}
				elseif (in_array($key, array('test_name', 'test_datatype', 'res_datatype', 'result'), TRUE))
				{
					if (FALSE !== ($line = $CI->lang->line(strtolower('ut_'.$val), FALSE)))
					{
						$val = $line;
					}
				}

				$temp[$CI->lang->line('ut_'.$key, FALSE)] = $val;
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
	 * @param	string
	 * @return	void
	 */
	public function set_template($template)
	{
		$this->_template = $template;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a backtrace
	 *
	 * This lets us show file names and line numbers
	 *
	 * @return	array
	 */
	protected function _backtrace()
	{
		$back = debug_backtrace();
		return array(
			'file' => (isset($back[1]['file']) ? $back[1]['file'] : ''),
			'line' => (isset($back[1]['line']) ? $back[1]['line'] : '')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Default Template
	 *
	 * @return	string
	 */
	protected function _default_template()
	{
		$this->_template = "\n".'<table style="width:100%; font-size:small; margin:10px 0; border-collapse:collapse; border:1px solid #CCC;">{rows}'."\n</table>";

		$this->_template_rows = "\n\t<tr>\n\t\t".'<th style="text-align: left; border-bottom:1px solid #CCC;">{item}</th>'
					."\n\t\t".'<td style="border-bottom:1px solid #CCC;">{result}</td>'."\n\t</tr>";
	}

	// --------------------------------------------------------------------

	/**
	 * Parse Template
	 *
	 * Harvests the data within the template {pseudo-variables}
	 *
	 * @return	void
	 */
	protected function _parse_template()
	{
		if ($this->_template_rows !== NULL)
		{
			return;
		}

		if ($this->_template === NULL OR ! preg_match('/\{rows\}(.*?)\{\/rows\}/si', $this->_template, $match))
		{
			$this->_default_template();
			return;
		}

		$this->_template_rows = $match[1];
		$this->_template = str_replace($match[0], '{rows}', $this->_template);
	}

}

/**
 * Helper function to test boolean TRUE
 *
 * @param	mixed	$test
 * @return	bool
 */
function is_true($test)
{
	return ($test === TRUE);
}

/**
 * Helper function to test boolean FALSE
 *
 * @param	mixed	$test
 * @return	bool
 */
function is_false($test)
{
	return ($test === FALSE);
}
