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
 * FTP Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/encryption.html
 */ 
class CI_FTP {

	var $hostname	= '';
	var $username	= '';
	var $password	= '';
	var $port		= 21;
	var $passive	= TRUE;
	var $secure		= FALSE;
	var $debug		= FALSE;
	var $conn_id;


	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */	
	function CI_FTP($config = array())
	{		
		if (count($config) > 0)
		{
			$this->initialize($config);
		}	

		log_message('debug', "FTP Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */	
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		
		// Prep the hostname
		$this->hostname = preg_replace('|.+?://|', '', $this->hostname);
	}

	// --------------------------------------------------------------------

	/**
	 * FTP Connect
	 *
	 * @access	public
	 * @return	bool
	 */	
	function connect()
	{	
		$method = ($this->secure == FALSE) ? 'ftp_connect' : 'ftp_ssl_connect';
	
		if (FALSE === ($this->conn_id = @$method($this->hostname, $this->port)))
		{
			if ($this->debug == TRUE)
			{
				$this->_error('ftp_unable_to_connect');
			}		
			return FALSE;
		}
		
		if ( ! $this->_login())
		{
			if ($this->debug == TRUE)
			{
				$this->_error('ftp_unable_to_login');
			}		
			return FALSE;
		}
		
		// Set passive mode if needed
		if ($this->passive == TRUE)
		{
			ftp_pasv($this->conn_id, TRUE);
		}
		
		return TRUE;
	}
		
	// --------------------------------------------------------------------

	/**
	 * FTP Login
	 *
	 * @access	private
	 * @return	bool
	 */	
	function _login()
	{
		return @ftp_login($this->conn_id, $this->username, $this->password);
	}

	// --------------------------------------------------------------------

	/**
	 * Change direcotries
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	lets us momentarily turn off debugging.
	 * @return	array
	 */	
	function changedir($path = '', $supress_debug = FALSE)
	{
		if ($path == '')
		{
			return FALSE;
		}
		
		$result = @ftp_chdir($this->conn_id, $path);
		
		if ($result === FALSE)
		{
			if ($this->debug == TRUE AND $supress_debug != TRUE)
			{
				$this->_error('ftp_unable_to_changedir');
			}		
			return FALSE;		
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Create a directory
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function mkdir($path = '')
	{
		if ($path == '')
		{
			return FALSE;
		}
	
		$result = @ftp_mkdir($this->conn_id, $path);
		
		if ($result === FALSE)
		{
			if ($this->debug == TRUE)
			{
				$this->_error('ftp_unable_to_makdir');
			}		
			return FALSE;		
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Upload a file to the server
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	array
	 */	
	function upload($locpath, $rempath, $mode = 'auto', $permissions = NULL)
	{
		if ( ! file_exists($locpath))
		{
			$this->_error('ftp_no_source_file');
			
			return FALSE;
		}
	
		// Set the mode if not specified
		if ($mode == 'auto')
		{
			// Get the file extension so we can se the upload type
			$ext = $this->_getext($locpath);
			$mode = $this->_settype($ext);
		}
		
		$mode = ($mode == 'ascii') ? FTP_ASCII : FTP_BINARY;
		
		$result = @ftp_put($this->conn_id, $rempath, $locpath, $mode);
		
		if ($result === FALSE)
		{
			if ($this->debug == TRUE)
			{
				$this->_error('ftp_unable_to_upload');
			}		
			return FALSE;		
		}
		
		// Set file permissions if needed
		if ( ! is_null($permissions))
		{
			$this->chmod($rempath, (int)$permissions);
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set file permissions
	 *
	 * @access	public
	 * @param	string 	the file path
	 * @param	string	the permissions
	 * @return	array
	 */		
	function chmod($path, $perm)
	{		
		$result = @ftp_chmod($this->conn_id, $perm, $path);
		
		if ($result === FALSE)
		{
			if ($this->debug == TRUE)
			{
				$this->_error('ftp_unable_to_chmod');
			}		
			return FALSE;		
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * FTP List files in the specified directory
	 *
	 * @access	public
	 * @return	array
	 */	
	function filelist($path = '.')
	{		
		return ftp_nlist($this->conn_id, $path);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Read a directory and recreate it remotely
	 *
	 * This function recursively reads a folder and everything it contains (including
	 * sub-folders) and creates a mirror via FTP based on it.  Whatever directory structure
	 * is in the original file path will be recreated in the zip file.
	 *
	 * @access	public
	 * @param	string	path to source
	 * @param	string	path to destination
	 * @return	bool
	 */	
	function mirror($locpath, $rempath)
	{	
		// Open the local file path
		if ($fp = @opendir($locpath))
		{
			// Attempt to open the remote file path.
			if ( ! $this->changedir($rempath, TRUE))
			{
				// If it doesn't exist we'll attempt to create the direcotory
				if ( ! $this->mkdir($rempath) OR ! $this->changedir($rempath))
				{
					return FALSE;
				}
			}
		
			// Recursively read the local directory
			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($locpath.$file) && substr($file, 0, 1) != '.')
				{					
					$this->mirror($locpath.$file."/", $rempath.$file."/");
				}
				elseif (substr($file, 0, 1) != ".")
				{
					// Get the file extension so we can se the upload type
					$ext = $this->_getext($file);
					$mode = $this->_settype($ext);
					
					$this->upload($locpath.$file, $rempath.$file, $mode);
				}
			}
			return TRUE;
		}
		
		return FALSE;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Extract the file extension
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */	
	function _getext($filename)
	{
		if (FALSE === strpos($filename, '.'))
		{
			return 'txt';
		}
	
		$x = explode('.', $filename);
		return end($x);
	}	


	// --------------------------------------------------------------------
	
	/**
	 * Set the upload type
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */	
	function _settype($ext)
	{
		$text_types = array(
							'txt',
							'text',
							'php',
							'phps',
							'php4',
							'js',
							'css',
							'htm',
							'html',
							'phtml',
							'shtml',
							'log',
							'xml'
							);
	
	
		return (in_array($ext, $text_types)) ? 'ascii' : 'binary';
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Close the connection
	 *
	 * @access	public
	 * @param	string	path to source
	 * @param	string	path to destination
	 * @return	bool
	 */	
	function close()
	{
		@ftp_close($this->conn_id);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Display error message
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */	
	function _error($line)
	{
		$CI =& get_instance();
		$CI->lang->load('ftp');
		show_error($CI->lang->line($line));		
	}


}
// END FTP Class
?>