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

 
// INITIALIZE THE CLASS ---------------------------------------------------

$obj =& get_instance();
$obj->init_class('CI_DB_export', 'dbexport');

// ------------------------------------------------------------------------

/**
 * DB Exporting Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_export {


	/**
	 * Constructor.  Simply calls the log function 
	 */	
	function CI_DB_export()
	{
		log_message('debug', "Database Export Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Generate CVS from a query result object
	 *
	 * @access	public
	 * @param	object	The query result object
	 * @param	string	The delimiter - tab by default
	 * @param	string	The newline character - \n by default
	 * @return	string
	 */
	function cvs_from_result($query, $delim = "\t", $newline = "\n")
	{
		if ( ! is_object($query) OR ! method_exists($query, 'field_names'))
		{
			show_error('You must submit a valid result object');
		}	
	
		$out = '';
		
		// First generate the headings from the table column names
		foreach ($query->field_names() as $name)
		{
			$out .= $name.$delim;
		}
		
		$out = rtrim($out);
		$out .= $newline;
		
		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $item)
			{
				$out .= $item.$delim;			
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
	}
	
	// --------------------------------------------------------------------


	/**
	 * Generate XML data from a query result object
	 *
	 * @access	public
	 * @param	object	The query result object
	 * @param	array	Any preferences
	 * @return	string
	 */
	function xml_from_result($query, $params = array())
	{
		if ( ! is_object($query) OR ! method_exists($query, 'field_names'))
		{
			show_error('You must submit a valid result object');
		}
		
		// Set our default values
		foreach (array('root' => 'root', 'element' => 'element', 'newline' => "\n", 'tab' => "\t") as $key => $val)
		{
			if ( ! isset($params[$key]))
			{
				$params[$key] = $val;
			}
		}
		
		// Create variables for convenience
		extract($params);
			
		// Generate the result
		
		$xml = "<{$root}/>".$newline;
		foreach ($query->result_array() as $row)
		{
			$xml .= $tab."<{$element}/>".$newline;
			
			foreach ($row as $key => $val)
			{
				$xml .= $tab.$tab."<{$key}>".$this->_xml_convert($val)."</{$key}>".$newline;
			}
			$xml .= $tab."</{$element}>".$newline;
		}
		$xml .= "</$root>".$newline;  
		
		return $xml;
	}

	
	// ------------------------------------------------------------------------
	
	/**
	 * Convert Reserved XML characters to Entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function _xml_convert($str)
	{
		$temp = '__TEMP_AMPERSANDS';
		
		$str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
		$str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);
		
		$str = str_replace(array("&","<",">","\"", "'", "-"),
						   array("&amp;", "&lt;", "&gt;", "&quot;", "&#39;", "&#45;"),
						   $str);
			
		$str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
		$str = preg_replace("/$temp(\w+);/","&\\1;", $str);
			
		return $str;
	}    

}

?>