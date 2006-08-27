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
 * Code Igniter Encryption Class
 *
 * Provides two-way keyed encoding using XOR Hashing and Mcrypt
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Encrypt {
	var $_hash_type	= 'sha1';
	var $_mcrypt_exists = FALSE;
	var $_mcrypt_cipher;
	var $_mcrypt_mode;
	
	/**
	 * Constructor
	 *
	 * Simply determines whether the mcrypt library exists.
	 *
	 */
	function CI_Encrypt()
	{
		$this->_mcrypt_exists = ( ! function_exists('mcrypt_encrypt')) ? FALSE : TRUE;
		log_message('debug', "Encrypt Class Initialized");
	}
  	// END CI_Encrypt()
  	
	// --------------------------------------------------------------------

	/**
	 * Fetch the encryption key
	 *
	 * Returns it as MD5 in order to have an exact-length 128 bit key.
	 * Mcrypt is sensitive to keys that are not the correct length
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function get_key($key = '')
	{
		if ($key == '')
		{	
			$obj =& get_instance();
			$key = $obj->config->item('encryption_key');

			if ($key === FALSE)
			{
				show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
			}
		}
		
		return md5($key);
	}
  	// END get_key()
  	
	// --------------------------------------------------------------------

	/**
	 * Encode
	 *
	 * Encodes the message string using bitwise XOR encoding.  
	 * The key is combined with a random hash, and then it 
	 * too gets converted using XOR. The whole thing is then run 
	 * through mcrypt (if supported) using the randomized key.  
	 * The end result is a double-encrypted message string 
	 * that is randomized with each call to this function, 
	 * even if the supplied message and key are the same.
	 *
	 * @access	public
	 * @param	string	the string to encode
	 * @param	string	the key
	 * @return	string
	 */
	function encode($string, $key = '')
	{
		$key = $this->get_key($key);
		$enc = $this->_xor_encode($string, $key);
		
		if ($this->_mcrypt_exists === TRUE)
		{
			$enc = $this->mcrypt_encode($enc, $key);
		}
		return base64_encode($enc);		
	}
  	// END encode()
  	
	// --------------------------------------------------------------------

	/**
	 * Decode
	 *
	 * Reverses the above process
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function decode($string, $key = '')
	{
		$key = $this->get_key($key);
		$dec = base64_decode($string);
		
		 if ($dec === FALSE)
		 {
		 	return FALSE;
		 }
		
		if ($this->_mcrypt_exists === TRUE)
		{
			$dec = $this->mcrypt_decode($dec, $key);
		}
		
		return $this->_xor_decode($dec, $key);
	}
  	// END decode()
  	
	// --------------------------------------------------------------------

	/**
	 * XOR Encode
	 *
	 * Takes a plain-text string and key as input and generates an
	 * encoded bit-string using XOR
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _xor_encode($string, $key)
	{
		$rand = '';
		while (strlen($rand) < 32) 
		{    
			$rand .= mt_rand(0, mt_getrandmax());
		}
	
		$rand = $this->hash($rand);
		
		$enc = '';
		for ($i = 0; $i < strlen($string); $i++)
		{			
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
		}
				
		return $this->_xor_merge($enc, $key);
	}
  	// END _xor_encode()
  	
	// --------------------------------------------------------------------

	/**
	 * XOR Decode
	 *
	 * Takes an encoded string and key as input and generates the 
	 * plain-text original message
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);
		
		$dec = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));
		}
	
		return $dec;
	}
  	// END _xor_decode()
  	
	// --------------------------------------------------------------------

	/**
	 * XOR key + string Combiner
	 *
	 * Takes a string and key as input and computes the difference using XOR
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
		}
		
		return $str;
	}
  	// END _xor_merge()
  	
	// --------------------------------------------------------------------

	/**
	 * Encrypt using Mcrypt
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function mcrypt_encode($data, $key) 
	{	
		$this->_get_mcrypt();
		$init_size = mcrypt_get_iv_size($this->_mcrypt_cipher, $this->_mcrypt_mode);
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return mcrypt_encrypt($this->_mcrypt_cipher, $key, $data, $this->_mcrypt_mode, $init_vect);
	}
  	// END mcrypt_encode()
  	
	// --------------------------------------------------------------------

	/**
	 * Decrypt using Mcrypt
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function mcrypt_decode($data, $key) 
	{
		$this->_get_mcrypt();
		$init_size = mcrypt_get_iv_size($this->_mcrypt_cipher, $this->_mcrypt_mode);
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return rtrim(mcrypt_decrypt($this->_mcrypt_cipher, $key, $data, $this->_mcrypt_mode, $init_vect), "\0");
	}
  	// END mcrypt_decode()
  	
	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Cypher
	 *
	 * @access	public
	 * @param	constant
	 * @return	string
	 */
	function set_cypher($cypher)
	{
		$this->_mcrypt_cipher = $cypher;
	}
  	// END set_cypher()
  	
	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Mode
	 *
	 * @access	public
	 * @param	constant
	 * @return	string
	 */
	function set_mode($mode)
	{
		$this->_mcrypt_mode = $mode;
	}
  	// END set_mode()
  	
	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt value
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */	
	function _get_mcrypt()
	{
		if ($this->_mcrypt_cipher == '') 
		{
			$this->_mcrypt_cipher = MCRYPT_RIJNDAEL_256;
		}
		if ($this->_mcrypt_mode == '') 
		{
			$this->_mcrypt_mode = MCRYPT_MODE_ECB;
		}
	}
  	// END _get_mcrypt()
  	
	// --------------------------------------------------------------------

	/**
	 * Set the Hash type
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function set_hash($type = 'sha1')
	{
		$this->_hash_type = ($type != 'sha1' AND $type != 'md5') ? 'sha1' : $type;
	}
  	// END set_hash()
  	
	// --------------------------------------------------------------------

	/**
	 * Hash encode a string
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function hash($str)
	{
		return ($this->_hash_type == 'sha1') ? $this->sha1($str) : md5($str);
	}
  	// END hash()
  	
	// --------------------------------------------------------------------

	/**
	 * Generate an SHA1 Hash
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function sha1($str)
	{
		if ( ! function_exists('sha1'))
		{
			if ( ! function_exists('mhash'))
			{	
				require_once(BASEPATH.'libraries/Sha1'.EXT);
				$SH = new CI_SHA;
				return $SH->generate($str);            
			}
			else
			{
				return bin2hex(mhash(MHASH_SHA1, $str));
			}
		}
		else
		{
			return sha1($str);
		}	
	}  
	// END sha1()
	
}

// END CI_Encrypt class
?>