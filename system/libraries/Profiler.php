<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Profiler Class
 *
 * This class enables you to display benchmark, query, and other data
 * in order to help with debugging and optimization.
 *
 * Note: At some point it would be good to move all the HTML in this class
 * into a set of template files in order to allow customization.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/general/profiling.html
 */
class CI_Profiler {

	/**
	 * List of profiler sections available to show
	 *
	 * @var array
	 */
	protected $_available_sections = array(
		'benchmarks',
		'get',
		'memory_usage',
		'post',
		'uri_string',
		'controller_info',
		'queries',
		'http_headers',
		'session_data',
		'config'
	);

	/**
	 * Number of queries to show before making the additional queries togglable
	 *
	 * @var int
	 */
	protected $_query_toggle_count = 25;

	/**
	 * The View file to use for displaying the profiler sections
	 *
	 * @var string
	 */
	protected $_profiler_view = 'profiler_template';

	/**
	 * Reference to the CodeIgniter singleton
	 *
	 * @var object
	 */
	protected $CI;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Initialize Profiler
	 *
	 * @param	array	$config	Parameters
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->language('profiler');
		
		if (isset($config['query_toggle_count']))
		{
			$this->_query_toggle_count = (int) $config['query_toggle_count'];
			unset($config['query_toggle_count']);
		}

		// default all sections to display
		foreach ($this->_available_sections as $section)
		{
			if ( ! isset($config[$section]))
			{
				$this->_compile_{$section} = TRUE;
			}
		}
		
		$this->set_sections($config);
		log_message('info', 'Profiler Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Set Sections
	 *
	 * Sets the private _compile_* properties to enable/disable Profiler sections
	 *
	 * @param	mixed	$config
	 * @return	void
	 */
	public function set_sections($config)
	{
		if (isset($config['query_toggle_count']))
		{
			$this->_query_toggle_count = (int) $config['query_toggle_count'];
			unset($config['query_toggle_count']);
		}

		foreach ($config as $method => $enable)
		{
			if (in_array($method, $this->_available_sections))
			{
				$this->_compile_{$method} = ($enable !== FALSE);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Auto Profiler
	 *
	 * This function cycles through the entire array of mark points and
	 * matches any two points that are named identically (ending in "_start"
	 * and "_end" respectively).  It then compiles the execution times for
	 * all points and returns it as an array
	 *
	 * @return	array
	 */
	protected function _compile_benchmarks()
	{
		$profile = array();
		foreach ($this->CI->benchmark->marker as $key => $val)
		{
			// We match the "end" marker so that the list ends
			// up in the order that it was defined
			if (preg_match('/(.+?)_end$/i', $key, $match)
				&& isset($this->CI->benchmark->marker[$match[1].'_end'], $this->CI->benchmark->marker[$match[1].'_start']))
			{
				$profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1].'_start', $key);
			}
		}
		
		// Build a table containing the profile data.
		// Note: At some point we should turn this into a template that can
		// be modified. We also might want to make this data available to be logged

		$output = array();
		
		foreach ($profile as $key => $val)
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output[$key] = $val;
		}
		
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile Queries
	 *
	 * @return	string
	 */
	protected function _compile_queries()
	{
		$dbs = array();
		
		// Let's determine which databases are currently connected to
		foreach (get_object_vars($this->CI) as $name => $cobject)
		{
			if (is_object($cobject))
			{
				if ($cobject instanceof CI_DB)
				{
					$dbs[get_class($this->CI).':$'.$name] = $cobject;
				}
				elseif ($cobject instanceof CI_Model)
				{
					foreach (get_object_vars($cobject) as $mname => $mobject)
					{
						if ($mobject instanceof CI_DB)
						{
							$dbs[get_class($cobject).':$'.$mname] = $mobject;
						}
					}
				}
			}
		}
		
		$output = array();
		
		foreach ($dbs as $name => $db)
		{
			$output[$db->database] = array(
				'name' => $name,
				'total' => array_sum($db->query_times)
			);
			
			foreach ($db->queries as $key => $query)
			{
				$output[$db->database][] = array(
				  'query' => $query,
				  'time' => $db->query_times[$key]
				);
			}
		}
		
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile $_GET Data
	 *
	 * @return	string
	 */
	protected function _compile_get()
	{
		$output = array();
		
		foreach ($_GET as $key => $val)
		{
			is_int($key) OR $key = "'".$key."'";
			
			if (is_array($val) OR is_object($val))
			{
				$output[$key] = '<pre>'.htmlspecialchars(stripslashes(print_r($val, TRUE))).'</pre>';
			}
			else
			{
				$output[$key] = htmlspecialchars(stripslashes($val));
			}
		}
		
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile $_POST Data
	 *
	 * @return	string
	 */
	protected function _compile_post()
	{
		$output = array();
		
		foreach ($_POST as $key => $val)
		{
			is_int($key) OR $key = "'".$key."'";
			
			if (is_array($val) OR is_object($val))
			{
				$output[$key] = '<pre>'.htmlspecialchars(stripslashes(print_r($val, TRUE))).'</pre>';
			}
			else
			{
				$output[$key] = htmlspecialchars(stripslashes($val));
			}
		}
		
		foreach ($_FILES as $key => $val)
		{
			is_int($key) OR $key = "'".$key."'";
			
			if (is_array($val) OR is_object($val))
			{
				$output[$key] = '<pre>'.htmlspecialchars(stripslashes(print_r($val, TRUE))).'</pre>';
			}
		}
		
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Show query string
	 *
	 * @return	string
	 */
	protected function _compile_uri_string()
	{
		return $this->CI->uri->uri_string;
	}

	// --------------------------------------------------------------------

	/**
	 * Show the controller and function that were called
	 *
	 * @return	string
	 */
	protected function _compile_controller_info()
	{
		return $this->CI->router->class.'/'.$this->CI->router->method;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile memory usage
	 *
	 * Display total used memory
	 *
	 * @return	string
	 */
	protected function _compile_memory_usage()
	{
		return memory_get_usage();
	}

	// --------------------------------------------------------------------

	/**
	 * Compile header information
	 *
	 * Lists HTTP headers
	 *
	 * @return	string
	 */
	protected function _compile_http_headers()
	{
		$output = array();
		
		foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR', 'HTTP_DNT') as $header)
		{
			$output[$header] = isset($_SERVER[$header]) ? $_SERVER[$header] : '';
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile config information
	 *
	 * Lists developer config variables
	 *
	 * @return	string
	 */
	protected function _compile_config()
	{
		$output = array();
		
		foreach ($this->CI->config->config as $config => $val)
		{
			if (is_array($val) OR is_object($val))
			{
				$val = print_r($val, TRUE);
			}
			$output[$config] = htmlspecialchars($val);
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile session userdata
	 *
	 * @return 	string
	 */
	protected function _compile_session_data()
	{
		$output = array();
		
		if (isset($this->CI->session))
		{
			foreach ($this->CI->session->userdata() as $key => $val)
			{
				if (is_array($val) OR is_object($val))
				{
					$val = print_r($val, TRUE);
				}
				$output[$key] = htmlspecialchars($val);
			}
		}
		
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Run the Profiler
	 *
	 * @return	string
	 */
	public function run()
	{
		$sections = array();

		foreach ($this->_available_sections as $section)
		{
			if ($this->_compile_{$section} !== FALSE)
			{
				$func = '_compile_'.$section;
				$sections[$section] = $this->{$func}();
			}
		}

		return $this->CI->load->view($this->_profiler_view, array(
			'sections' => $sections,
			'toggle' => $this->_query_toggle_count
		), TRUE);
	}

}
