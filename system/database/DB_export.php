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


	function CI_DB_export()
	{
		log_message('debug', "Database Export Class Initialized");
	}

	/**
	 * Generate CVS
	 *
	 * @access	public
	 * @return	integer
	 */
	function generate_cvs($query, $delim = "\t", $newline = "\n")
	{
		if ( ! is_object($query) OR ! method_exists($query, 'field_names'))
		{
			show_error('You must submit a valid result object');
		}	
	
		$out = '';
		foreach ($query->field_names() as $name)
		{
			$out .= $name.$delim;
		}

		$out .= $newline;
		
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $item)
			{
				$out .= $item.$delim;			
			}
			
			$out .= $newline;
		}
		

		return $out;
	}
	
	// --------------------------------------------------------------------


}

?>