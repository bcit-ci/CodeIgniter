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
 * Code Igniter Benchmark Class
 *
 * This class enables you to mark points and calculate the time difference
 * between them.  Memory consumption can also be displayed.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/benchmark.html
 */
class CI_Benchmark {

	var $marker = array();

    
	// --------------------------------------------------------------------

	/**
	 * Set a benchmark marker
	 *
	 * Multiple calls to this function can be made so that several
	 * execution points can be timed
	 *
	 * @access	public
	 * @param	string	$name	name of the marker
	 * @return	void
	 */
    function mark($name)
    {
        $this->marker[$name] = microtime();
    }
  	// END mark()
  	
	// --------------------------------------------------------------------

	/**
	 * Calculates the time difference between two marked points.
	 *
	 * If the first parameter is empty this function instead returns the 
	 * {elapsed_time} pseudo-variable. This permits the the full system 
	 * execution time to be shown in a template. The output class will
	 * swap the real value for this variable.
	 *
	 * @access	public
	 * @param	string	a paricular marked point
	 * @param	string	a paricular marked point
	 * @param	integer	the number of decimal places
	 * @return	mixed
	 */
    function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
    {
    	if ($point1 == '')
    	{
			return '{elapsed_time}';
    	}
    	    
    	if ( ! isset($this->marker[$point2]))
        	$this->marker[$point2] = microtime();
        	    
        list($sm, $ss) = explode(' ', $this->marker[$point1]);
        list($em, $es) = explode(' ', $this->marker[$point2]);
                        
        return number_format(($em + $es) - ($sm + $ss), $decimals);
    }
 	// END elapsed_time()
 	
	// --------------------------------------------------------------------

	/**
	 * Auto Profiler
	 *
	 * This function cycles through the entire array of mark points and
	 * matches any two points that are named identially (ending in "_start"
	 * and "_end" respectively).  It then compiles the execution times for
	 * all points and returns it as an array
	 *
	 * @access	public
	 * @return	array
	 */
 	function auto_profiler()
 	{  		
 		$marker_keys = array_reverse(array_keys($this->marker));
 
  		$times = array();
 		foreach ($marker_keys as $val)
 		{
 			if (preg_match("/(.+?)_start/i", $val, $match))
 			{ 			
 				if (isset($this->marker[$match[1].'_end']))
 				{
 					$times[$match[1]] = $this->elapsed_time($val, $match[1].'_end');
 				}
 			}
 		}
 	
 		return $times;
 	}
 	
	// --------------------------------------------------------------------

	/**
	 * Memory Usage
	 *
	 * This function returns the {memory_usage} pseudo-variable.
	 * This permits it to be put it anywhere in a template 
	 * without the memory being calculated until the end. 
	 * The output class will swap the real value for this variable.
	 *
	 * @access	public
	 * @return	string
	 */
	function memory_usage()
	{
		return '{memory_usage}';
	}
	// END memory_usage()

}

// END CI_Benchmark class
?>