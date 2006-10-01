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
 * Database Utility Class
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
class CI_DB_utility {

	var $db;
	var $cache = array();

	/**
	 * Constructor
	 *
	 * Grabs the CI super object instance so we can access it.
	 *
	 */	
	function CI_DB_utility()
	{
		// Assign the main database object to $this->db
		$obj =& get_instance();
		$this->db =& $obj->db;
		
		log_message('debug', "Database Utility Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Create database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */
	function create_database($db_name)
	{
		$sql = $this->_create_database($db_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @access	public
	 * @param	string	the database name
	 * @return	bool
	 */
	function drop_database($db_name)
	{
		$sql = $this->_drop_database($db_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * List databases
	 *
	 * @access	public
	 * @return	bool
	 */
	function list_databases()
	{	
		// Is there a cached result?
		if (isset($this->cache['db_names']))
		{
			return $this->cache['db_names'];
		}
	
		$query = $this->db->query($this->_list_database());
		$dbs = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$dbs[] = current($row);
			}
		}
			
		return $this->cache['db_names'] =& $dbs;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize Table
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	bool
	 */
	function optimize_table($table_name)
	{
		$sql = $this->_optimize_table($table_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		$query = $this->db->query($sql);
		return current($query->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize Database
	 *
	 * @access	public
	 * @return	array
	 */
	function optimize_database()
	{
		$result = array();
		foreach ($this->list_tables() as $table_name)
		{
			$sql = $this->_optimize_table($table_name);
			
			if (is_bool($sql))
			{
				return $sql;
			}
			
			$query = $this->db->query($sql);
			
			// Build the result array...
			$res = current($query->result_array());
			$key = str_replace($this->db->database.'.', '', current($res));
			$keys = array_keys($res);
			unset($res[$keys[0]]);
			
			$result[$key] = $res;
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize Table
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	bool
	 */

	function repair_table($table_name)
	{
		$sql = $this->_repair_table($table_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		$query = $this->db->query($sql);
		return current($query->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	bool
	 */
	function drop_table($table_name)
	{
		$sql = $this->_drop_table($table_name);
		
		if (is_bool($sql))
		{
			return $sql;
		}
	
		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate CSV from a query result object
	 *
	 * @access	public
	 * @param	object	The query result object
	 * @param	string	The delimiter - tab by default
	 * @param	string	The newline character - \n by default
	 * @return	string
	 */
	function csv_from_result($query, $delim = "\t", $newline = "\n")
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
			
		// Load the xml helper
		$obj =& get_instance();
		$obj->load->helper('xml');

		// Generate the result
		$xml = "<{$root}/>".$newline;
		foreach ($query->result_array() as $row)
		{
			$xml .= $tab."<{$element}/>".$newline;
			
			foreach ($row as $key => $val)
			{
				$xml .= $tab.$tab."<{$key}>".xml_convert($val)."</{$key}>".$newline;
			}
			$xml .= $tab."</{$element}>".$newline;
		}
		$xml .= "</$root>".$newline;  
		
		return $xml;
	}

	// --------------------------------------------------------------------

	/**
	 * Database Backup
	 *
	 * @access	public
	 * @return	void
	 */
	function backup($params = array())
	{
		// If the parameters have not been submitted as an
		// array then we know that it is simply the table
		// name, which is a valid short cut.
		if (is_string($params))
		{
			$params = array('tables' => $params);
		}
		
		// ------------------------------------------------------
	
		// Set up our default preferences
		$prefs = array(
							'tables'		=> array(),
							'ignore'		=> array(),
							'format'		=> 'gzip', // gzip, zip, txt
							'action'		=> 'download', // download, archive, echo, return
							'filename'		=> '',
							'filepath'		=> '',
							'add_drop'		=> TRUE,
							'add_insert'	=> TRUE,
							'newline'		=> "\n"
						);

		// Did the user submit any preferences? If so set them....
		if (count($params) > 0)
		{
			foreach ($prefs as $key => $val)
			{
				if (isset($params[$key]))
				{
					$prefs[$key] = $params[$key];
				}
			}
		}

		// ------------------------------------------------------

		// Are we backing up a complete database or individual tables?	
		// If no table names were submitted we'll fetch the entire table list
		if (count($prefs['tables']) == 0)
		{
			$prefs['tables'] = $this->list_tables();
		}
		
		// ------------------------------------------------------

		// Validate the format
		if ( ! in_array($prefs['format'], array('gzip', 'zip', 'txt'), TRUE))
		{
			$prefs['format'] = 'txt';
		}

		// ------------------------------------------------------

		// Is the encoder supported?  If not, we'll either issue an
		// error or use plain text depending on the debug settings
		if (($prefs['format'] == 'gzip' AND ! @function_exists('gzencode')) 
		 OR ($prefs['format'] == 'zip'  AND ! @function_exists('gzcompress'))) 
		{
            if ($this->db->db_debug)
            {
				return $this->db->display_error('db_unsuported_compression');
            }
		
			$prefs['format'] = 'txt';
		}

		// ------------------------------------------------------

		// Set the filename if not provided
		if ($prefs['filename'] == '')
		{
			$prefs['filename'] = (count($prefs['tables']) == 1) ? $prefs['tables'] : $this->db->database;
			$prefs['filename'] .= '_'.date('Y-m-d_H-i', time());
		}

		// ------------------------------------------------------

		// If we are archiving the export, does this filepath exist
		// and resolve to a writable directory
		if ($prefs['action'] == 'archive')
		{
			if ($prefs['filepath'] == '' OR ! is_writable($prefs['filepath']))
			{
				if ($this->db->db_debug)
				{
					return $this->db->display_error('db_filepath_error');
				}
			
				$prefs['action'] = 'download';
			}
		}

		// ------------------------------------------------------
		
		// Are we returning the backup data?  If so, we're done...
		if ($prefs['action'] == 'return')
		{
			return $this->_backup($prefs);
		}

		// ------------------------------------------------------
		
		// Are we echoing the backup?  If so, format the data and spit it at the screen...
		if ($prefs['action'] == 'echo')
		{
			echo '<pre>';
			echo htmlspecialchars($this->_backup($prefs));
			echo '</pre>';
			
			return TRUE;
		}
	
		// ------------------------------------------------------

		// Are we archiving the data to the server?
		if ($prefs['action'] == 'archive')
		{
			// Make sure the filepath has a trailing slash
			if (ereg("/$", $prefs['filepath']) === FALSE)
			{
				$prefs['filepath'] .= '/';
			}

			// Assemble the path and tack on the file extension
			$ext = array('gzip' => 'gz', 'zip' => 'zip', 'txt' => 'sql');
			$path = $prefs['filepath'].$prefs['filename'].$ext[$prefs['format']];
			
			// Load the file helper
			$obj =& get_instance();
			$obj->load->helper('file');
			
			// Write the file based on type
			switch ($prefs['format'])
			{
				case 'gzip' : 	
								write_file($path, gzencode($this->_backup($prefs)));
								return TRUE;
					break;
				case 'txt'	: 	
								write_file($path, $this->_backup($prefs));
								return TRUE;
					break;
				default		:
								$obj->load->library('zip');
								$obj->zip->add_data($prefs['filename'].'.sql', $this->_backup($prefs));
								$obj->zip->archive($path);
								return TRUE;
					break;			
			}
	
		}

		// ------------------------------------------------------
				
		
		// Grab the super object
		$obj =& get_instance();
		
		// Remap the file extensions
		$ext = array('gzip' => 'gz', 'zip' => 'zip', 'txt' => 'sql');	
				
		// Is a Zip file requested?	
		if ($prefs['format'] == 'zip')
		{
			$obj->load->library('zip');
			$obj->zip->add_data($prefs['filename'].'.sql', $this->_backup($prefs));
			$obj->zip->download($prefs['filename'].'.'.$ext[$prefs['format']]);
			return TRUE;
		}
		
		
		// Set the mime type
		switch ($prefs['format'])
		{
			case 'gzip' : $mime = 'application/x-gzip';
				break;
			default     : $mime = (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") || strstr($_SERVER['HTTP_USER_AGENT'], "OPERA")) ? 'application/octetstream' : 'application/octet-stream';
				break;
		}	
	
		$filename = $prefs['filename'].'.sql.'.$ext[$prefs['format']];
	
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: '.$mime);
			header('Content-Disposition: inline; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
		} 
		else 
		{
			header('Content-Type: '.$mime);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
		}

		// Write the file based on type
		switch ($prefs['format'])
		{
			case 'gzip' : 	echo gzencode($this->_backup($prefs));
				break;
			case 'txt'	: 	echo $this->_backup($prefs);
				break;
		}

		return TRUE;
	}






}

?>