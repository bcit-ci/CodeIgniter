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
 * Output Class
 *
 * Responsible for sending final output to browser
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Output
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/output.html
 */
class CI_Output {

	var $final_output;
	var $cache_expiration = 0;

	function CI_Output()
	{
		log_message('debug', "Output Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Output 
	 *
	 * Returns the current output string
	 *
	 * @access	public
	 * @return	string
	 */	
	function get_output()
	{
		return $this->final_output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Output 
	 *
	 * Sets the output string
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_output($output)
	{
		$this->final_output = $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Cache 
	 *
	 * @access	public
	 * @param	integer
	 * @return	void
	 */	
	function cache($time)
	{
		$this->cache_expiration = ( ! is_numeric($time)) ? 0 : $time;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Output
	 *
	 * All "view" data is automatically put into this variable 
	 * by the controller class:
	 *
	 * $this->final_output
	 *
	 * This function simply echos the variable out.  It also does the following:
	 * 
	 * Stops the benchmark timer so the page rendering speed can be shown.
	 *
	 * Determines if the "memory_get_usage' function is available so that 
	 * the memory usage can be shown.
	 *
	 * @access	public
	 * @return	void
	 */		
	function _display($output = '')
	{	
		$BM =& _load_class('CI_Benchmark');
		
		if ($output == '')
		{
			$output =& $this->final_output;
		}
		
		if ($this->cache_expiration > 0)
		{
			$this->_write_cache($output);
		}

		$elapsed = $BM->elapsed_time('code_igniter_start', 'code_igniter_end');		
		$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';

		$output = str_replace('{memory_usage}', $memory, $output);		
		$output = str_replace('{elapsed_time}', $elapsed, $output);
		
		echo $output;
		
		log_message('debug', "Final output sent to browser");
		log_message('debug', "Total execution time: ".$elapsed);		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write a Cache File 
	 *
	 * @access	public
	 * @return	void
	 */	
	function _write_cache($output)
	{
		$obj =& get_instance();	
		$path = $obj->config->item('cache_path');
	
		$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
		
		if ( ! is_dir($cache_path) OR ! is_writable($cache_path))
		{
			return;
		}
		
		$uri =	$obj->config->slash_item('base_url').
				$obj->config->item('index_page').
				$obj->uri->uri_string();
		
		$cache_path .= md5($uri);

        if ( ! $fp = @fopen($cache_path, 'wb'))
        {
			log_message('error', "Unable to write ache file: ".$cache_path);
            return;
		}
		
		$expire = time() + ($this->cache_expiration * 60);
		
        flock($fp, LOCK_EX);
        fwrite($fp, $expire.'TS--->'.$output);
        flock($fp, LOCK_UN);
        fclose($fp);
		@chmod($cache_path, 0777); 

		log_message('debug', "Cache file written: ".$cache_path);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Update/serve a cached file 
	 *
	 * @access	public
	 * @return	void
	 */	
	function _display_cache()
	{
		$CFG =& _load_class('CI_Config');
		$RTR =& _load_class('CI_Router');
	
		$cache_path = ($CFG->item('cache_path') == '') ? BASEPATH.'cache/' : $CFG->item('cache_path');
			
		if ( ! is_dir($cache_path) OR ! is_writable($cache_path))
		{
			return FALSE;
		}
		
		// Build the file path.  The file name is an MD5 hash of the full URI
		$uri = $CFG->item('base_url', 1).$CFG->item('index_page').$RTR->uri_string;
		
		$filepath = $cache_path.md5($uri);
		
		if ( ! @file_exists($filepath))
		{
			return FALSE;
		}
	
		if ( ! $fp = @fopen($filepath, 'rb'))
		{
			return FALSE;
		}
			
		flock($fp, LOCK_SH);
		
		$cache = '';
		if (filesize($filepath) > 0) 
		{
			$cache = fread($fp, filesize($filepath)); 
		}
	
		flock($fp, LOCK_UN);
		fclose($fp); 
					
		// Strip out the embedded timestamp		
		if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
		{
			return FALSE;
		}
		
		// Has the file expired? If so we'll delete it.
		if (time() >= trim(str_replace('TS--->', '', $match['1'])))
		{ 		
			@unlink($filepath);
			log_message('debug', "Cache file has expired. File deleted");
			return FALSE;
		}

		// Display the cache
		$this->_display(str_replace($match['0'], '', $cache));
		log_message('debug', "Cache file is current. Sending it to browser.");		
		return TRUE;
	}

}
// END Output Class
?>