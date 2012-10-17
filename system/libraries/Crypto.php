<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team and Michael Brooks
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

include("AES.php");

/**
 * CodeIgniter Crypto Class
 *
 * A class that contains useful cryptographic functions
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team and Michael Brooks
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Crypto {

	/**
	 * Reference to the user's encryption key
	 *
	 * @var string
	 */
	public $encryption_key		= '';
 
	/**
	 *   AES-128 has a block size of 16
	 *
	 * @var string
	 */
        protected $block_size = 16;

	/**
	 *   sha1 is small yet provides a comfortable level of security. 
	 *   A sha1 collision has not been generated at the time of implamentation.
	 *
	 * @var string
	 */
        protected $_hash_type = "sha1";
 
	/**
	 * Initialize Encryption class
	 *
	 * @return	void
	 */
	public function __construct()
	{

		log_message('debug', 'Crypto Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * This is the string to key function for the block cipher this class is using.
	 *
	 * @param	string
	 * @return	string
	 */
	public function get_key($key = '')
	{
		if ($key === '')
		{
			if ($this->encryption_key !== '')
			{
				$key = $this->encryption_key;
			}
			else
			{
				$key = config_item('encryption_key');

				if ($key === FALSE)
				{
					$new_key =$this->new_key();
					show_error('In order to use encryption you must specify an encryption key in your config file. You can use this randomly generated key:'.$new_key);
				}
			}
		}
		
		//Make sure the key is the right size for our block cihper.
		$ret = "";
		$ret = $this->keygen_s2k("sha1", $key, "", $this->block_size);
		return $ret;
	}

	/**
	 * A string to key function.
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	string
	 */
	function keygen_s2k($hash, $password, $salt, $bytes)
	{
		$result = false;
		$chunk_len = strlen(hash($hash, null, true));
		foreach (range(0, ceil($bytes / $chunk_len) - 1) as $i)
		{
			$result .= hash($hash, str_repeat("\0", $i) . $salt . $password, true);
		}

		$result = substr($result, 0, intval($bytes));
		return $result;
	}

	/**
	 * Set the encryption key
	 *
	 * @param	string
	 * @return	object
	 */
	public function set_key($key = '')
	{
		$this->encryption_key = $key;
		return $this;
	}

	/**
	 * Generate a new encryption key
	 *
	 * @return	object
	 */
	public function new_key()
	{
		$key = $this->random($this->block_size);
		return base64_encode($key);
	}

	/**
	* Encrypt using AES 128 using the most efficent method possilbe. 
	*
	* @param data $string,
	* @param string $key
	* @return base64 encrypted string
	*/
	public function encrypt($string, $key="")
	{
		if($key=="")
		{
			$key = $this->get_key();
		}
		$iv = $this->random($this->block_size);
		//might get a null byte...
		if(strlen($iv) < $this->block_size){
			$iv=$this->pkcs7_pad($iv);
		}
		$ciphertext = $this->aes_128_cbc_encrypt($string, $key, $iv);
		
		$auth_code = $this->hmac($iv.$ciphertext, $key);

		return base64_encode($iv.$ciphertext.$auth_code);
	}

	/**
	* Decrypt using AES 128  useing the most efficent method possilbe. 
	*
	* @param data $string
	* @param string $key
	* @param string $iv		
	* @return dencrypted string
	*/
	public function decrypt($ciphertext, $key="", $iv="")
	{	
		$ciphertext = base64_decode($ciphertext);

		if($key=="")
		{
			$key = $this->get_key();
		}
		$iv = substr($ciphertext, 0 , $this->block_size);
		$ciphertext = substr($ciphertext, $this->block_size);
		$auth_code = substr($ciphertext, strlen($ciphertext) - 40);

		$ciphertext = substr($ciphertext, 0, strlen($ciphertext) - 40);
	
		$check_auth_code = $this->hmac($iv.$ciphertext, $key);
		
		// validate cipher text,  prevent the creation of a cryptographic oracle.
		if($auth_code !== $check_auth_code){
			return FALSE;
		}
		$message = $this->aes_128_cbc_decrypt($ciphertext, $key, $iv);
		return $message;
	}

	/**
	* Obtain a random stirng using the best PRNG aviable.
	*
	* @param	int
	* @return	string
	*/		    
	public function random($length)
	{
		$ret="";
		if(file_exists("/dev/urandom"))
		{
			$rand=fopen("/dev/urandom","r");
			$ret = fgets($rand, $length + 1);
			fclose($rand);
		}
		else
		{
		      for($x=0;$x<$length;$x++)
		      {
			   $ret.=chr(mt_rand(0,255));
		      }
		}
		return $ret;
	}

	/**
	*  aes-128 encryption using the most efficent implamentation. 
	*
	* @param	string
	* @param	string
	* @param	string
	* @return	string
	*/
	protected function aes_128_cbc_encrypt($string, $key, $iv)
	{
		$ret = "";
		if (function_exists('openssl_encrypt')) 
		{
		       $ret = openssl_encrypt($this->pkcs7_pad($string), 'aes-128-cbc', $key, true, $iv);
		}		
		else if (function_exists('mcrypt_module_open'))
		{ 
			$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			//Make sure we hae this cipher
			if (mcrypt_generic_init($cipher, $key, $iv) != -1) {
			    $encrypted = mcrypt_generic($cipher, $this->pkcs7_pad($string));
			    mcrypt_generic_deinit($cipher);
			    mcrypt_module_close($cipher);
			    $ret = $encrypted;
			}
		}

		//Use a native PHP AES CBC implamentation. 
		if($ret == "")
		{
			$aes = new Crypt_AES(CRYPT_AES_MODE_CBC);
			$aes->setKey($key);
			$aes->setIV($iv);
			$aes->paddable = False;
			$ret = $this->pkcs7_pad($ret);
			$ret = $aes->encrypt($string);
		}
		return $ret;
	}

	/**
	*  aes-128 decryption using the most efficent implamentation. 
	*
	* @param	string
	* @param	string
	* @param	string
	* @return	string
	*/
	protected function aes_128_cbc_decrypt($ciphertext, $key, $iv) 
	{
		$ret="";
		if (function_exists('openssl_decrypt')) 
		{
			$ret = openssl_decrypt($ciphertext, 'aes-128-cbc', $key, true, $iv);
			$ret = $this->remove_pkcs7_pad($ret);
			
		}
		else if (function_exists('mcrypt_module_open'))
		{ 
			$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			if (mcrypt_generic_init($cipher, $key, $iv) != -1) {
				$decrypted = mdecrypt_generic($cipher, $ciphertext);
				$ret  = $this->remove_pkcs7_pad($decrypted);
			}
		}

		//Use a native PHP AES CBC implamentation. 
		if($ret == "")
		{
			$aes = new Crypt_AES(CRYPT_AES_MODE_CBC);
			$aes->setKey($key);
			$aes->setIV($iv);
			$aes->disablePadding();
			$ret = $aes->decrypt($ciphertext);
			$ret = $this->remove_pkcs7_pad($ret);
		}
		return $ret;
	}

	/**
	*  PKCS#7 padding implamentation
	*
	* @param	string
	* @param	int	
	* @return	string
	*/
	protected function pkcs7_pad($string, $size = 0) 
	{
		if($size == 0 ){
			$size = $this->block_size;
		}
		$pad = $size - (strlen($string) % $size);
		return $string.str_repeat(chr($pad), $pad);
	}

	/**
	*  Remove PKCS#7 padding
	*
	* @param	string
	* @return	string
	*/
	protected function remove_pkcs7_pad($string)
	{
		$len = strlen($string);
		$pad = ord($string[$len - 1]);
		if ($pad > 0 && $pad <= $this->block_size) {
		    $valid_pad = true;
		    for ($i = 1; $i <= $pad; $i++) {
			if (ord($string[$len - $i]) != $pad) {
			    $valid_pad = false;
			    break;
			}
		    }
		    if ($valid_pad) {
			$string = substr($string, 0, $len - $pad);
		    }
		}
		return $string;
	}

	/**
	 * Set the Hash type
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_hash($type = 'sha1')
	{
		$this->_hash_type = in_array($type, hash_algos()) ? $type : 'sha1';
	}

	/**
	 * Hashed Messaage Authentication Code
	 *
	 * @param	string
	 * @return	string
	 */
	public function hmac($str, $key="")
	{
		if($key=="")
		{
			$key=$this->get_key();
		}
		return hash_hmac($this->_hash_type, $str, $key);
	}

}

/* End of file Crypto.php */
/* Location: ./system/libraries/Crypto.php */