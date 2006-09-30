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
 * I'm not sure this library is all that reliable, but it's the only
 * zip compressor I'm aware of -- Rick Ellis
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/encryption.html
 */
class Zip {

    var $zdata  = array();
    var $cdir   = array();
    var $offset = 0;

	/**
	 * Add a Directory
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
    function add_dir($name)
    {
        $name =str_replace ("\\", "/", $name);
        
        $fd = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"    
              .pack("V", 0)
              .pack("V", 0)
              .pack("V", 0)
              .pack("v", strlen($name))
              .pack("v", 0)
              .$name;
        
        $this->cdata[] = $fd;
                
        $cd = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"
              .pack("V", 0)
              .pack("V", 0)
              .pack("V", 0)
              .pack("v", strlen ($name))
              .pack("v", 0)
              .pack("v", 0)
              .pack("v", 0)
              .pack("v", 0)
              .pack("V", 16)
              .pack("V", $this->offset)
              .$name;
        
        $this->offset = strlen(implode('', $this->cdata));
        
        $this->cdir[] = $cd;
    }

	// --------------------------------------------------------------------

	/**
	 * Add a File
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
    function add_file($data, $name)
    {
        $name = str_replace("\\", "/", $name);
        
        $u_len = strlen($data);
        $crc   = crc32($data);
        $data  = gzcompress($data);
        $data  = substr(substr($data, 0,strlen ($data) - 4), 2);
        $c_len = strlen($data);
        
        $fd = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
              .pack("V", $crc)
              .pack("V", $c_len)
              .pack("V", $u_len)
              .pack("v", strlen($name))
              .pack("v", 0)
              .$name
              .$data
              .pack("V", $crc)
              .pack("V", $c_len)
              .pack("V", $u_len);
        
        $this->zdata[] = $fd;
                
        $cd = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
              .pack("V", $crc)
              .pack("V", $c_len)
              .pack("V", $u_len)
              .pack("v", strlen ($name))
              .pack("v", 0)
              .pack("v", 0)
              .pack("v", 0)
              .pack("v", 0)
              .pack("V", 32 )
              .pack("V", $this->offset)
              .$name;
  
        $this->offset = strlen(implode('', $this->zdata));
        
        $this->cdir[] = $cd;
    }

	// --------------------------------------------------------------------

	/**
	 * Output the zip file
	 *
	 * @access	public
	 * @return	string
	 */	
    function output_zipfile()
    {
        $data = implode("", $this->zdata);
        $cdir = implode("", $this->cdir);

        return   $data
                .$cdir
                ."\x50\x4b\x05\x06\x00\x00\x00\x00"
                .pack("v", sizeof($this->cdir))
                .pack("v", sizeof($this->cdir))
                .pack("V", strlen($cdir))
                .pack("V", strlen($data))
                ."\x00\x00";
    }

}
// END CLASS
?>