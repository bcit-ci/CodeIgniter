<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Code Igniter Profiler Class
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
 * @link		http://www.codeigniter.com/user_guide/libraries/benchmark.html
 */
class CI_Profiler {

	var $obj;
 	
 	function CI_Profiler()
 	{
 		$this->obj =& get_instance();
 		$this->obj->load->language('profiler');
 	}
 	
	// --------------------------------------------------------------------

	/**
	 * Auto Profiler
	 *
	 * This function cycles through the entire array of mark points and
	 * matches any two points that are named identially (ending in "_start"
	 * and "_end" respectively).  It then compiles the execution times for
	 * all points and returns it as an array
	 *
	 * @access	private
	 * @return	array
	 */
 	function _compile_benchmarks()
 	{
  		$profile = array();
 		foreach ($this->obj->benchmark->marker as $key => $val)
 		{
 			// We match the "end" marker so that the list ends
 			// up in the order that it was defined
 			if (preg_match("/(.+?)_end/i", $key, $match))
 			{ 			
 				if (isset($this->obj->benchmark->marker[$match[1].'_end']))
 				{
 					$profile[$match[1]] = $this->obj->benchmark->elapsed_time($match[1].'_start', $key);
 				}
 			}
 		}

		// Build a table containing the profile data.
		// Note: At some point we should turn this into a template that can
		// be modified.  We also might want to make this data available to be logged
	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #990000;padding:6px 0 10px 10px;margin:0 0 20px 0;background-color:#efefef">';
		$output .= "\n";
		$output .= '<legend style="color:#990000;">&nbsp;&nbsp;'.$this->obj->lang->line('profiler_benchmarks').'&nbsp;&nbsp;</legend>';
		$output .= "\n";			
		$output .= "\n\n<table cellpadding='4' cellspacing='1' border='0'>\n";
		
		foreach ($profile as $key => $val)
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output .= "<tr><td style='color:#0000;font-weight:bold;'>".$key."&nbsp;&nbsp;</td><td style='color:#990000;font-weight:normal;'>".$val."</td></tr>\n";
		}
		
		$output .= "</table>\n";
		$output .= "</fieldset>\n\n";
 		
 		return $output;
 	}
 	
	// --------------------------------------------------------------------


	function _compile_queries()
	{
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#efefef">';
		$output .= "\n";
		$output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;'.$this->obj->lang->line('profiler_queries').'&nbsp;&nbsp;</legend>';
		$output .= "\n";		
		
		if ( ! class_exists('CI_DB_driver'))
		{
			$output .= "<div style='color:#0000FF;font-weight:normal;padding:4px 0 0 0;'>".$this->obj->lang->line('profiler_no_db')."</div>";
		}
		else
		{
			if (count($this->obj->db->queries) == 0)
			{
				$output .= "<div style='color:#0000FF;font-weight:normal;padding:4px 0 4px 0;'>".$this->obj->lang->line('profiler_no_queries')."</div>";
			}
			else
			{
				foreach ($this->obj->db->queries as $val)
				{
					$output .= '<div style="padding:0;margin:12px 0 12px 0;background-color:#efefef;color:#000">';
					$output .= $val;
					$output .= "</div>\n";
				}	
			}
		}
		
		$output .= "</fieldset>\n\n";		
		
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	function _compile_post()
	{
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#efefef">';
		$output .= "\n";
		$output .= '<legend style="color:#009900;">&nbsp;&nbsp;'.$this->obj->lang->line('profiler_post_data').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
				
		if (count($_POST) == 0)
		{
			$output .= "<div style='color:#009900;font-weight:normal;padding:4px 0 4px 0'>".$this->obj->lang->line('profiler_no_post')."</div>";
		}
		else
		{
			$output .= "\n\n<table cellpadding='4' cellspacing='1' border='0'>\n";
		
			foreach ($_POST as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = "'".$key."'";
				}
			
				$output .= "<tr><td style='color:#0000;'>&#36;_POST[".$key."]&nbsp;&nbsp;</td><td style='color:#009900;font-weight:normal;'>".htmlspecialchars(stripslashes($val))."</td></tr>\n";
			}
			
			$output .= "</table>\n";
		}
		$output .= "</fieldset>\n\n";

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
		$output = '<br style="margin:0;padding:0;clear:both;" />';
	
		$output .= $this->_compile_benchmarks();
		$output .= $this->_compile_post();
		$output .= $this->_compile_queries();
		
		return $output;
	}

}

// END CI_Profiler class
?>