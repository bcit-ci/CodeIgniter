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
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/profiling.html
 */
class CI_Profiler {

	var $CI;
 	
 	function CI_Profiler()
 	{
 		$this->CI =& get_instance();
 		$this->CI->load->language('profiler');
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
	 * @access	private
	 * @return	array
	 */
 	function _compile_benchmarks()
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
		$output .= '<fieldset style="border:1px solid #990000;padding:6px 10px 10px 10px;margin:0 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#990000;">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_benchmarks').'&nbsp;&nbsp;</legend>';
		$output .= "\n";			
		$output .= "\n\n<table cellpadding='4' cellspacing='1' border='0' width='100%'>\n";
		
		foreach ($profile as $key => $val)
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output .= "<tr><td width='50%' style='color:#000;font-weight:bold;background-color:#ddd;'>".$key."&nbsp;&nbsp;</td><td width='50%' style='color:#990000;font-weight:normal;background-color:#ddd;'>".$val."</td></tr>\n";
		}
		
		$output .= "</table>\n";
		$output .= "</fieldset>";
 		
 		return $output;
 	}
 	
	// --------------------------------------------------------------------

	/**
	 * Compile Queries
	 *
	 * @access	private
	 * @return	string
	 */	
	function _compile_queries()
	{
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_queries').'&nbsp;&nbsp;</legend>';
		$output .= "\n";		
		
		if ( ! class_exists('CI_DB_driver'))
		{
			$output .= "<div style='color:#0000FF;font-weight:normal;padding:4px 0 0 0;'>".$this->CI->lang->line('profiler_no_db')."</div>";
		}
		else
		{
			if (count($this->CI->db->queries) == 0)
			{
				$output .= "<div style='color:#0000FF;font-weight:normal;padding:4px 0 4px 0;'>".$this->CI->lang->line('profiler_no_queries')."</div>";
			}
			else
			{
				foreach ($this->CI->db->queries as $val)
				{
					$output .= '<div style="padding:3px;margin:12px 0 12px 0;background-color:#ddd;color:#000">';
					$output .= htmlspecialchars($val, ENT_QUOTES);
					$output .= "</div>\n";
				}	
			}
		}
		
		$output .= "</fieldset>";		
		
		return $output;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Compile $_POST Data
	 *
	 * @access	private
	 * @return	string
	 */	
	function _compile_post()
	{	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#009900;">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_post_data').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
				
		if (count($_POST) == 0)
		{
			$output .= "<div style='color:#009900;font-weight:normal;padding:4px 0 4px 0'>".$this->CI->lang->line('profiler_no_post')."</div>";
		}
		else
		{
			$output .= "\n\n<table cellpadding='4' cellspacing='1' border='0' width='100%'>\n";
		
			foreach ($_POST as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = "'".$key."'";
				}
			
//				$output .= "<tr><td width='50%' style='color:#000;background-color:#ddd;'>&#36;_POST[".$key."]&nbsp;&nbsp;</td><td width='50%' style='color:#009900;font-weight:normal;background-color:#ddd;'>".htmlspecialchars(stripslashes($val))."</td></tr>\n";
				$output .= "<tr><td width='50%' style='color:#000;background-color:#ddd;'>&#36;_POST[".$key."]&nbsp;&nbsp; </td><td width='50%' style='color:#009900;font-weight:normal;background-color:#ddd;'>";
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
		$output .= "</fieldset>";

		return $output;	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Run the Profiler
	 *
	 * @access	private
	 * @return	string
	 */	
	function run($output = '')
	{		
		$output = '<br clear="all" />';
		$output .= "<div style='background-color:#fff;padding:10px;'>";
	
		$output .= $this->_compile_benchmarks();
		$output .= $this->_compile_post();
		$output .= $this->_compile_queries();
		
		$output .= '</div>';
		
		return $output;
	}

}

// END CI_Profiler class
?>