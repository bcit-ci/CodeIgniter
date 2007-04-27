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
 * Zip Compression Class
 *
 * This class is based on a library I found at Zend:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Rick Ellis
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/zip.html
 */
class CI_Zip  {

	var $zipfile	= '';	
	var $zipdata	= array();
	var $directory	= array();
	var $offset		= 0;

	function CI_Zip()
	{
		log_message('debug', "Zip Compression Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Add Directory
	 *
	 * Lets you add a virtual directory into which you can place files.
	 *
	 * @access	public
	 * @param	mixed	the directory name. Can be string or array
	 * @return	void
	 */
	function add_dir($directory)
	{
		foreach ((array)$directory as $dir)
		{
			if ( ! preg_match("|.+/$|", $dir))
			{
				$dir .= '/';
			}
		
			$this->_add_dir($dir);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add Directory
	 *
	 * @access	private
	 * @param	string	the directory name
	 * @return	void
	 */
	function _add_dir($dir)
	{
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
	 * Add Data to Zip
	 *
	 * Lets you add files to the archive. If the path is included
	 * in the filename it will be placed within a directory.  Make
	 * sure you use add_dir() first to create the folder.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */	
	function add_data($filepath, $data = NULL)
	{
		if (is_array($filepath))
		{
			foreach ($filepath as $path => $data)
			{
				$this->_add_data($path, $data);
			}
		}
		else
		{
			$this->_add_data($filepath, $data);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add Data to Zip
	 *
	 * @access	private
	 * @param	string	the file name/path
	 * @param	string	the data to be encoded
	 * @return	void
	 */	
	function _add_data($filepath, $data)
	{	
		$filepath = str_replace("\\", "/", $filepath);
			
		$oldlen	= strlen($data);
		$crc32	= crc32($data);
		
		$gzdata = gzcompress($data);
		$gzdata = substr($gzdata, 2, -4);
		$newlen = strlen($gzdata);
	
		$this->zipdata[] = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
							.pack('V', $crc32)
							.pack('V', $newlen)
							.pack('V', $oldlen)
							.pack('v', strlen($filepath))
							.pack('v', 0)
							.$filepath
							.$gzdata;
			
		$newoffset = strlen(implode("", $this->zipdata));
		
		$record = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
					.pack('V', $crc32)
					.pack('V', $newlen)
					.pack('V', $oldlen)
					.pack('v', strlen($filepath))
					.pack('v', 0)
					.pack('v', 0)
					.pack('v', 0)
					.pack('v', 0)
					.pack('V', 32)
					.pack('V', $this->offset);
		
		$this->offset = $newoffset;
		$this->directory[] = $record.$filepath;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Read the contents of a file and add it to the zip
	 *
	 * @access	public
	 * @return	bool
	 */	
	function read_file($path, $preserve_filepath = FALSE)
	{
		if ( ! file_exists($path))
		{
			return FALSE;
		}
	
		if (FALSE !== ($data = file_get_contents($path)))
		{
			$name = str_replace("\\", "/", $path);
			
			if ($preserve_filepath === FALSE)
			{
				$name = preg_replace("|.*/(.+)|", "\\1", $name);
			}
			
			$this->add_data($name, $data);
			return TRUE;
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Read a directory and add it to the zip.
	 *
	 * This function recursively reads a folder and everything it contains (including
	 * sub-folders) and creates a zip based on it.  Whatever directory structure
	 * is in the original file path will be recreated in the zip file.
	 *
	 * @access	public
	 * @param	string	path to source
	 * @return	bool
	 */	
	function read_dir($path)
	{	
		if ($fp = @opendir($path))
		{
			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($path.$file) && substr($file, 0, 1) != '.')
				{					
					$this->read_dir($path.$file."/");
				}
				elseif (substr($file, 0, 1) != ".")
				{
					if (FALSE !== ($data = file_get_contents($path.$file)))
					{						
						$this->add_data(str_replace("\\", "/", $path).$file, $data);
					}
				}
			}
			return TRUE;
		}
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
		// We cache the zip data so multiple calls
		// do not require recompiling
		if ($this->zipfile != '')
		{
			return $this->zipfile;
		}
	
		// Is there any data to return?
		if (count($this->zipdata) == 0)
		{
			return FALSE;
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
	 * Write File to the specified directory
	 *
	 * Lets you write a file
	 *
	 * @access	public
	 * @param	string	the file name
	 * @param	string	the data to be encoded
	 * @return	bool
	 */	
	function archive($filepath)
	{
		if ( ! ($fp = @fopen($filepath, "wb")))
		{
			return FALSE;
		}
		
		flock($fp, LOCK_EX);	
		fwrite($fp, $this->get_zip());
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;	
	}

	// --------------------------------------------------------------------

	/**
	 * Download
	 *
	 * @access	public
	 * @param	string	the file name
	 * @param	string	the data to be encoded
	 * @return	bool
	 */		
	function download($filename = 'backup.zip')
	{
		if ( ! preg_match("|.+?\.zip$|", $filename))
		{
			$filename .= '.zip';
		}
	
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: application/x-zip');
			header('Content-Disposition: inline; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($this->get_zip()));
		}
		else
		{
			header('Content-Type: application/x-zip');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($this->get_zip()));
		}
	
		echo $this->get_zip();
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Data
	 *
	 * Lets you clear current zip data.  Useful if you need to create
	 * multiple zips with different data.
	 *
	 * @access	public
	 * @return	void
	 */		
	function clear_data()
	{
		$this->zipfile		= '';
		$this->zipdata 		= array();
		$this->directory	= array();
		$this->offset		= array();
	}
	
}
?>