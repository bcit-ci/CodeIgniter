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
 * Zip Compression Class
 *
 * This class is based on a library aquired at Zend:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/encryption.html
 */
class Zip  {  
	
	var $zipdata	= array();
	var $directory	= array();
	var $offset		= 0;
	var $zipfile	= '';

	/**
	 * Add Directory
	 *
	 * Lets you add a virtual directory into which you can place files.
	 *
	 * @access	public
	 * @param	string	the directory name
	 * @return	void
	 */
	function add_dir($dir) 
	{
		$this->zipfile = '';
	
		$dir = str_replace("\\", "/", $dir);  
		
		$this->zipdata[] = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"
							.pack('V', 0)
							.pack('V', 0)
							.pack('V', 0)
							.pack('v', strlen($dir))
							.pack('v', 0)
							.$dir
							.pack('V', 0)
							.pack('V', 0)
							.pack('V', 0); 
		
		$newoffset = strlen(implode('', $this->zipdata));
		
		$record = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"
					.pack('V',0)
					.pack('V',0)
					.pack('V',0)
					.pack('v', strlen($dir))
					.pack('v', 0)
					.pack('v', 0)
					.pack('v', 0)
					.pack('v', 0)
					.pack('V', 16)
					.pack('V', $this->offset)
					.$dir;
		
		$this->offset = $newoffset;
		$this->directory[] = $record;  
	}	 

	// --------------------------------------------------------------------

	/**
	 * Add File
	 *
	 * Lets you add files to the archive. If the path is included
	 * in the filename it will be placed within a directory.  Make
	 * sure you use add_dir() first to create the folder.
	 *
	 * @access	public
	 * @param	string	the file name
	 * @param	string	the data to be encoded
	 * @return	void
	 */	
	function add_file($filename, $data)
	{
		$this->zipfile = '';
	
		$filename = str_replace("\\", "/", $filename);  
			
		$oldlen	= strlen($data);  
		$crc32	= crc32($data);  
		
		$gzdata = gzcompress($data);
		$gzdata = substr(substr($gzdata, 0, strlen($gzdata) - 4), 2); 	
		$newlen = strlen($gzdata);  
	
		$this->zipdata[] = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
							.pack('V', $crc32)
							.pack('V', $newlen)
							.pack('V', $oldlen)
							.pack('v', strlen($filename))
							.pack('v', 0)
							.$filename
							.$gzdata
							.pack('V', $crc32)
							.pack('V', $newlen)
							.pack('V', $oldlen); 
			
		$newoffset = strlen(implode("", $this->zipdata));
		
		$record = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
					.pack('V', $crc32)
					.pack('V', $newlen)
					.pack('V', $oldlen)
					.pack('v', strlen($filename))
					.pack('v', 0)
					.pack('v', 0)
					.pack('v', 0)
					.pack('v', 0)
					.pack('V', 32)
					.pack('V', $this->offset); 
		
		$this->offset = $newoffset;
		$this->directory[] = $record.$filename;  
	}

	// --------------------------------------------------------------------

	/**
	 * Read the content of a file
	 *
	 * @access	public
	 * @param	string	the file path
	 * @return	string
	 */	
	function read_file($filepath)
	{
		if ( ! file_exists($filepath))
		{
			return FALSE;
		}
	
		return file_get_contents($filepath);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the Zip file
	 *
	 * @access	public
	 * @return	binary string
	 */	
	function get_zip()
	{ 
		if ($this->zipfile != '')
		{
			return $this->zipfile;
		}
	
		$data	= implode('', $this->zipdata);  
		$dir	= implode('', $this->directory);  
				
		$this->zipfile = $data.$dir."\x50\x4b\x05\x06\x00\x00\x00\x00"
						.pack('v', sizeof($this->directory))
						.pack('v', sizeof($this->directory))
						.pack('V', strlen($dir))
						.pack('V', strlen($data))
						."\x00\x00";
						
		return $this->zipfile;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Write File
	 *
	 * Lets you write a file
	 *
	 * @access	public
	 * @param	string	the file name
	 * @param	string	the data to be encoded
	 * @return	bool
	 */	
	function write_file($filename, $data)
	{
		if ( ! ($fp = fopen($filename, "wb")))
		{
			return FALSE;
		}
		
		flock($fp, LOCK_EX);	
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;	
	}

	// --------------------------------------------------------------------

	/**
	 * Download
	 *
	 *
	 * @access	public
	 * @param	string	the file name
	 * @param	string	the data to be encoded
	 * @return	bool
	 */		
	function download($filename, $data)
	{
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: application/x-zip');
			header('Content-Disposition: inline; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		} 
		else 
		{
			header('Content-Type: application/x-zip');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}
	
		echo $data;
	}
	
}
?>