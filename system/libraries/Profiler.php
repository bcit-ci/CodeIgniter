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
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/profiling.html
 */
class CI_Profiler {

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

	protected $_query_toggle_count = 25;

	protected $CI;

	// --------------------------------------------------------------------

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
	}

	// --------------------------------------------------------------------

	/**
	 * Set Sections
	 *
	 * Sets the private _compile_* properties to enable/disable Profiler sections
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function set_sections($config)
	{
		foreach ($config as $method => $enable)
		{
			if (in_array($method, $this->_available_sections))
			{
				$this->_compile_{$method} = ($enable !== FALSE) ? TRUE : FALSE;
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
			if (preg_match("/(.+?)_end/i", $key, $match))
			{
				if (isset($this->CI->benchmark->marker[$match[1].'_end']) AND isset($this->CI->benchmark->marker[$match[1].'_start']))
				{
					$profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1].'_start', $key);
				}
			}
		}

		// Build a table containing the profile data.
		// Note: At some point we should turn this into a template that can
		// be modified.  We also might want to make this data available to be logged

		$output  = "\n\n";
		$output .= '<div style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #900;color:#900" id="ci_profiler_benchmarks">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_benchmarks').'&nbsp;&nbsp;</span>';
		$output .= "\n";
		$output .= "\n\n<table style='width:100%;text-align:left;font-size:14px'>\n";

		foreach ($profile as $key => $val)
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output .= "<tr><td style='padding:5px;width:50%;background-color:#ddd;color:#000;font-weight:bold'>".$key."&nbsp;&nbsp;</td><td style='padding:5px;width:50%;background-color:#ddd;color:#900;font-weight:normal'>".$val."</td></tr>\n";
		}

		$output .= "</table>\n";
		$output .= "</div>";

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
		foreach (get_object_vars($this->CI) as $CI_object)
		{
			if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB') )
			{
				$dbs[] = $CI_object;
			}
		}

		if (count($dbs) == 0)
		{
			$output  = "\n\n";
			$output .= '<div style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #0000FF;color:#0000FF" id="ci_profiler_queries">';
			$output .= "\n";
			$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_queries').'&nbsp;&nbsp;</span>';
			$output .= "\n";
			$output .= "\n\n<table style='width:100%;text-align:left;font-size:16px;border:none'>\n";
			$output .="<tr><td style='padding:5px;'>".$this->CI->lang->line('profiler_no_db')."</td></tr>\n";
			$output .= "</table>\n";
			$output .= "</div>";

			return $output;
		}

		// Load the text helper so we can highlight the SQL
		$this->CI->load->helper('text');

		// Key words we want bolded
		$highlight = array('SESSION', 'SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');

		$output  = "\n\n";

		$count = 0;

		foreach ($dbs as $db)
		{
			$count++;

			$hide_queries = (count($db->queries) > $this->_query_toggle_count) ? ' display:none' : '';

			$show_hide_js = '(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_queries_db_'.$count.'\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_hide').'\'?\''.$this->CI->lang->line('profiler_section_show').'\':\''.$this->CI->lang->line('profiler_section_hide').'\';">'.$this->CI->lang->line('profiler_section_hide').'</span>)';

			if ($hide_queries != '')
			{
				$show_hide_js = '(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_queries_db_'.$count.'\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_show').'\'?\''.$this->CI->lang->line('profiler_section_hide').'\':\''.$this->CI->lang->line('profiler_section_show').'\';">'.$this->CI->lang->line('profiler_section_show').'</span>)';
			}

			$output .= '<div style="padding:4px 0 4px 0;border:1px solid #0000FF;background-color:transparent;position:static">';
			$output .= "\n";
			$output .= '<span style="position:relative;bottom:15px;background-color:#fff;">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_database').':&nbsp; '.$db->database.'&nbsp;&nbsp;&nbsp;'.$this->CI->lang->line('profiler_queries').': '.count($db->queries).'&nbsp;&nbsp;'.$show_hide_js.'</span>';
			$output .= "\n";
			$output .= "\n\n<table style='width:100%;text-align:left;font-size:16px;border:none;{$hide_queries}' id='ci_profiler_queries_db_{$count}'>\n";

			if (count($db->queries) == 0)
			{
				$output .= "<tr><td style='padding:5px;width:100%;color:#0000FF;font-size:12px'>".$this->CI->lang->line('profiler_no_queries')."</td></tr>\n";
			}
			else
			{
				foreach ($db->queries as $key => $val)
				{
					$time = number_format($db->query_times[$key], 4);

					$val = highlight_code($val, ENT_QUOTES);

					foreach ($highlight as $bold)
					{
						$val = str_replace($bold, '<strong>'.$bold.'</strong>', $val);
					}

					$output .= "<tr><td style='padding:5px;background-color:#eee;font-size:14px;width:1%;vertical-align:top;color:#900;'>".$time."&nbsp;&nbsp;</td><td style='padding:5px;width:100%;color:#000;background-color:#eee;font-size:12px'>".$val."</td></tr>\n";
				}
			}

			$output .= "</table>\n";
			$output .= "</div>";

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
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_get" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #cd6e00;color:#cd6e00;">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_get_data').'&nbsp;&nbsp;</span>';
		$output .= "\n";

		if (count($_GET) == 0)
		{
			$output .= "<div style='padding:5px'>".$this->CI->lang->line('profiler_no_get')."</div>";
		}
		else
		{
			$output .= "\n\n<table style='width:100%;text-align:left;font-size:16px'>\n";

			foreach ($_GET as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = "'".$key."'";
				}

				$output .= "<tr><td style='padding:5px;width:50%;background-color:#ddd;color:#000'>&#36;_GET[".$key."]&nbsp;&nbsp; </td><td style='padding:5px;width:50%;background-color:#ddd;color:#cd6e00;font-weight:normal;'>";
				if (is_array($val))
				{
					$output .= "<pre>" . htmlspecialchars(stripslashes(print_r($val, true))) . "</pre>";
				}
				else
				{
					$output .= htmlspecialchars(stripslashes($val));
				}
				$output .= "</td></tr>\n";
			}

			$output .= "</table>\n";
		}
		$output .= "</div>";

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
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_post" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #090;color:#090">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_post_data').'&nbsp;&nbsp;</span>';
		$output .= "\n";

		if (count($_POST) == 0)
		{
			$output .= "<div style='padding:5px'>".$this->CI->lang->line('profiler_no_post')."</div>";
		}
		else
		{
			$output .= "\n\n<table style='width:100%;text-align:left;font-size:16px'>\n";

			foreach ($_POST as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = "'".$key."'";
				}

				$output .= "<tr><td style='padding:5px;width:50%;background-color:#ddd;color:#000;'>&#36;_POST[".$key."]&nbsp;&nbsp; </td><td style='padding:5px;width:50%;background-color:#ddd;color:#009900;font-weight:normal;'>";
				if (is_array($val))
				{
					$output .= "<pre>" . htmlspecialchars(stripslashes(print_r($val, TRUE))) . "</pre>";
				}
				else
				{
					$output .= htmlspecialchars(stripslashes($val));
				}
				$output .= "</td></tr>\n";
			}

			$output .= "</table>\n";
		}
		$output .= "</div>";

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
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_uri_string" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #000;color:#000">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_uri_string').'&nbsp;&nbsp;</span>';
		$output .= "\n";

		if ($this->CI->uri->uri_string == '')
		{
			$output .= "<div style='padding:5px'>".$this->CI->lang->line('profiler_no_uri')."</div>";
		}
		else
		{
			$output .= "<div style='padding:5px'>".$this->CI->uri->uri_string."</div>";
		}

		$output .= "</div>";

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Show the controller and function that were called
	 *
	 * @return	string
	 */
	protected function _compile_controller_info()
	{
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_controller_info" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #995300;color:#995300">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_controller_info').'&nbsp;&nbsp;</span>';
		$output .= "\n";

		$output .= "<div style='padding:5px'>".$this->CI->router->fetch_class()."/".$this->CI->router->fetch_method()."</div>";

		$output .= "</div>";

		return $output;
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
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_memory_usage" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #5a0099;color:#5a0099">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_memory_usage').'&nbsp;&nbsp;</span>';
		$output .= "\n";

		if (function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '')
		{
			$output .= "<div style='padding:5px'>".number_format($usage).' bytes</div>';
		}
		else
		{
			$output .= "<div style='padding:5px'>".$this->CI->lang->line('profiler_no_memory')."</div>";
		}

		$output .= "</div>";

		return $output;
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
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_http_headers" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #000;color:#000">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_headers').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_httpheaders_table\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_show').'\'?\''.$this->CI->lang->line('profiler_section_hide').'\':\''.$this->CI->lang->line('profiler_section_show').'\';">'.$this->CI->lang->line('profiler_section_show').'</span>)</span>';
		$output .= "\n";

		$output .= "\n\n<table style='width:100%;text-align:left;font-size:16px;display:none' id='ci_profiler_httpheaders_table'>\n";

		foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header)
		{
			$val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
			$output .= "<tr><td style='padding:5px;width:50%;background-color:#ddd;vertical-align:top;color:#900'>".$header."&nbsp;&nbsp;</td><td style='padding:5px;width:50%;background-color:#ddd;color:#000;'>".$val."</td></tr>\n";
		}

		$output .= "</table>\n";
		$output .= "</div>";

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
		$output  = "\n\n";
		$output .= '<div id="ci_profiler_config" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #000;color:#000">';
		$output .= "\n";
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_config').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_config_table\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_show').'\'?\''.$this->CI->lang->line('profiler_section_hide').'\':\''.$this->CI->lang->line('profiler_section_show').'\';">'.$this->CI->lang->line('profiler_section_show').'</span>)</span>';
		$output .= "\n";

		$output .= "\n\n<table style='width:100%;text-align:left;font-size:16px;display:none' id='ci_profiler_config_table'>\n";

		foreach ($this->CI->config->config as $config=>$val)
		{
			if (is_array($val))
			{
				$val = print_r($val, TRUE);
			}

			$output .= "<tr><td style='padding:5px;width:50%;background-color:#ddd;vertical-align:top;color:#900'>".$config."&nbsp;&nbsp;</td><td style='padding:5px;width:50%;background-color:#ddd;color:#000'>".htmlspecialchars($val)."</td></tr>\n";
		}

		$output .= "</table>\n";
		$output .= "</div>";

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
		if ( ! isset($this->CI->session))
		{
			return;
		}

		$output = '<div id="ci_profiler_csession" style="padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#fff;border-radius:10px;border:1px solid #000;color:#000">';
		$output .= '<span style="position:relative;bottom:15px;background-color:#fff">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_session_data').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_session_data\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_show').'\'?\''.$this->CI->lang->line('profiler_section_hide').'\':\''.$this->CI->lang->line('profiler_section_show').'\';">'.$this->CI->lang->line('profiler_section_show').'</span>)</span>';
		$output .= "<table style='width:100%;text-align:left;font-size:16px;display:none' id='ci_profiler_session_data'>";

		foreach ($this->CI->session->all_userdata() as $key => $val)
		{
			if (is_array($val))
			{
				$val = print_r($val, TRUE);
			}

			$output .= "<tr><td style='padding:5px;width:50%;background-color:#ddd;vertical-align:top;color:#900'>".$key."&nbsp;&nbsp;</td><td style='padding:5px;width:50%;background-color:#ddd;color:#000'>".htmlspecialchars($val)."</td></tr>\n";
		}

		$output .= '</table>';
		$output .= "</div>";
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
		$output = "<div id='codeigniter_profiler' style='text-align:left;clear:both;background-color:#fff;background-image:linear-gradient(top, darkgrey, #fff 150px);padding:10px;margin-top:25px;font-family:auto;border-radius:10px'>";
		$fields_displayed = 0;

		foreach ($this->_available_sections as $section)
		{
			if ($this->_compile_{$section} !== FALSE)
			{
				$func = "_compile_{$section}";
				$output .= $this->{$func}();
				$fields_displayed++;
			}
		}

		if ($fields_displayed == 0)
		{
			$output .= '<p style="border:1px solid #5a0099;margin:20px 0;background-color:#eee">'.$this->CI->lang->line('profiler_no_profiles').'</p>';
		}

		$output .= '</div>';

		return $output;
	}
}

// END CI_Profiler class

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */