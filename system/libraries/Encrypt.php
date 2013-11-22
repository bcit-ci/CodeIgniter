<?php
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
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Encryption Class
 *
 * Provides two-way keyed encoding using XOR Hashing and Mcrypt
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Encrypt {

	/**
	 * Reference to the user's encryption key
	 *
	 * @var string
	 */
	public $encryption_key		= '';
	
	/**
	 * Used to authenticate the ciphertext
	 *
	 * @var string
	 */
	public $authentication_key	= '';

	/**
	 * Type of hash operation
	 *
	 * @var string
	 */
	protected $_hash_type		= 'sha1';

	/**
	 * Flag for the existance of mcrypt
	 *
	 * @var bool
	 */
	protected $_mcrypt_exists	= FALSE;

	/**
	 * Flag for the existance of openssl (always in PHP 5.3.0 and newer)
	 *
	 * @var bool
	 */
	protected $_openssl_exists	= FALSE;

	/**
	 * Current cipher to be used with mcrypt
	 *
	 * @var string
	 */
	protected $_mcrypt_cipher;

	/**
	 * Method for encrypting/decrypting data
	 *
	 * @var int
	 */
	protected $_mcrypt_mode;


	/**
	 * Used by OpenSSL; a combination of cipher and block mode
	 *
	 * @var int
	 */
	protected $_openssl_method;
	/**
	 * Initialize Encryption class
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->_mcrypt_exists = function_exists('mcrypt_encrypt');
		$this->_openssl_exists = function_exists('openssl_encrypt');
		log_message('debug', 'Encrypt Class Initialized');
	}
	/**
	 * Wipe encryption_key if Encrypt is ever serialized.
	 * You should never serialize this object, but just in case...
	 *
	 * @return	void
	 */
	public function __sleep() {
		$this->encryption_key = null;
		$this->authentication_key = null;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the encryption key
	 *
	 * Returns it as raw SHA-256 (legacy: MD5) so the key length is 256 bits
	 * Mcrypt is sensitive to keys that are not the correct length
	 *
	 * @param	string
	 * @return	string
	 */
	public function get_key($key = '', $legacy = false)
	{
		if ($key === '')
		{
			if ($this->encryption_key !== '')
			{
				return $this->encryption_key;
			}

			$key = config_item('encryption_key');

			if ($key === FALSE)
			{
				show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
			}
		}
		if($legacy)
		{
			return md5($key); // EVIL
		}
		return hash('sha256', $key, true);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Fetch the authenticatioion key
	 *
	 * Returns it as MD5 in order to have an exact-length 128 bit key.
	 * Mcrypt is sensitive to keys that are not the correct length
	 *
	 * @param	string
	 * @return	string
	 */
	public function get_hmac_key($key = '')
	{
		if ($key === '')
		{
			if ($this->authentication_key !== '')
			{
				return $this->authentication_key;
			}

			$key = config_item('encryption_key');

			if ($key === FALSE)
			{
				show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
			}
		}
		// Return the last 32 bytes of a raw SHA-512 hash.
		// Different from the encryption hey, but derived from the same input
		return substr(hash('sha512', $key, true), 32);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the encryption key
	 *
	 * @param	string
	 * @return	CI_Encrypt
	 */
	public function set_key($key = '')
	{
		$this->encryption_key = $key;
		return $this;
	}

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
	 * @param	string	the string to encode
	 * @param	string	the key
	 * @return	string
	 */
	public function encode($string, $key = '')
	{
		if($this->_mcrypt_exists || $this->_openssl_exists )
		{
			$method = ($this->_mcrypt_exists === TRUE) ? 'mcrypt_encode' : 'openssl_encode';
			$cipher = $this->$method($string, $this->get_key($key));
			$hmac = $this->hmac($cipher, $this->get_hmac_key($key));
			return base64_encode($cipher).':'.$hmac;
		}	
		else
		{
			show_error("Please install mcrypt or upgrade PHP to 5.3.0 or newer", E_USER_NOTICE);
			$method = '_xor_encode';
			return base64_encode($this->$method($string, $this->get_key($key, true)));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Decode
	 *
	 * Reverses the above process
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function decode($string, $key = '')
	{
		if($this->_mcrypt_exists || $this->_openssl_exists )
		{
			// 
			if (!preg_match('/^([a-zA-Z0-9\/\+]+={0,3}):?([0-9a-f]{32,})?$/', $string, $m) )
			{
				return FALSE;
			}
			$method = ($this->_mcrypt_exists === TRUE) ? 'mcrypt_decode' : 'openssl_decode';
			if (count($m) > 2)
			{
				$ciphertext = base64_decode($m[1]);
				$calculated = $this->hmac($data, $this->get_hmac_key($key));
				if (!$this->_slow_equals($calculated, $hmac))
				{
					show_error("HMAC validation failed!");
					return FALSE;
				}
				return $this->$method($ciphertext, $this->get_key($key));
			}
			// Before the days of HMAC... legacy mode
			return $this->$method(base64_decode($m[1]), $this->get_key($key), true);
		}
		else
		{
			show_error("Please install mcrypt or upgrade PHP to 5.3.0 or newer");
			$method = '_xor_encode';
			return $this->$method(base64_decode($string), $this->get_key($key, true), true);
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Encode from Legacy
	 *
	 * Takes an encoded string from the original Encryption class algorithms and
	 * returns a newly encoded string using the improved method added in 2.0.0
	 * This allows for backwards compatibility and a method to transition to the
	 * new encryption algorithms.
	 *
	 * For more details, see http://codeigniter.com/user_guide/installation/upgrade_200.html#encryption
	 *
	 * @param	string
	 * @param	int		(mcrypt mode constant)
	 * @param	string
	 * @return	string
	 */
	public function encode_from_legacy($string, $legacy_mode = MCRYPT_MODE_ECB, $key = '')
	{
		if ($this->_mcrypt_exists === FALSE)
		{
			log_message('error', 'Encoding from legacy is available only when Mcrypt is in use.');
			return FALSE;
		}
		elseif (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
		{
			return FALSE;
		}

		// decode it first
		// set mode temporarily to what it was when string was encoded with the legacy
		// algorithm - typically MCRYPT_MODE_ECB
		$current_mode = $this->_get_mode();
		$this->set_mode($legacy_mode);

		$key = $this->get_key($key);
		$dec = base64_decode($string);
		if (($dec = $this->mcrypt_decode($dec, $key)) === FALSE)
		{
			$this->set_mode($current_mode);
			return FALSE;
		}

		$dec = $this->_xor_decode($dec, $key);

		// set the mcrypt mode back to what it should be, typically MCRYPT_MODE_CBC
		$this->set_mode($current_mode);

		// and re-encode
		return base64_encode($this->mcrypt_encode($dec, $key));
	}

	// --------------------------------------------------------------------

	/**
	 * XOR Encode
	 *
	 * Takes a plain-text string and key as input and generates an
	 * encoded bit-string using XOR
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_encode($string, $key)
	{
		show_error("Encryption is falling back to XOR because neither mcrypt nor openssl are available to PHP.");
		$rand = '';
		do
		{
			$rand .= mt_rand();
		}
		while (strlen($rand) < 32);

		$rand = $this->hash($rand);

		$enc = '';
		for ($i = 0, $ls = strlen($string), $lr = strlen($rand); $i < $ls; $i++)
		{
			$enc .= $rand[($i % $lr)].($rand[($i % $lr)] ^ $string[$i]);
		}

		return $this->_xor_merge($enc, $key);
	}

	// --------------------------------------------------------------------

	/**
	 * XOR Decode
	 *
	 * Takes an encoded string and key as input and generates the
	 * plain-text original message
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);

		$dec = '';
		for ($i = 0, $l = strlen($string); $i < $l; $i++)
		{
			$dec .= ($string[$i++] ^ $string[$i]);
		}

		return $dec;
	}

	// --------------------------------------------------------------------

	/**
	 * XOR key + string Combiner
	 *
	 * Takes a string and key as input and computes the difference using XOR
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0, $ls = strlen($string), $lh = strlen($hash); $i < $ls; $i++)
		{
			$str .= $string[$i] ^ $hash[($i % $lh)];
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Encrypt using Mcrypt
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_encode($data, $key, $legacy = false)
	{
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		if (defined('MCRYPT_DEV_URANDOM'))
		{
			// Why pass up the opportunity to provide better assurance?
			$init_vect = mcrypt_create_iv($init_size, MCRYPT_DEV_URANDOM);
		}
		else
		{
			$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		}
		if($legacy)
		{
			return $this->_add_cipher_noise($init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), $key);
		}
		// Get ciphertext; we will HMAC it in ::encode()
		return $init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect);
	}


	/**
	 * Encrypt using OpenSSL (PHP >= 5.3.0)
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function openssl_encode($data, $key)
	{
		// Experimental. Still a better love story than xor encode
		$method = $this->_get_method();
		$init_size = openssl_cipher_iv_length($method);
		$init_vect = openssl_random_pseudo_bytes($init_size);
		return   $init_vect . openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $init_vect);
		
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt using Mcrypt
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_decode($data, $key, $legacy = false)
	{
		
		if ($legacy)
		{
			$data = $this->_remove_cipher_noise($data, $key);
		}
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());

		if ($init_size > strlen($data))
		{
			return FALSE;
		}

		$init_vect = substr($data, 0, $init_size);
		$data = substr($data, $init_size);
		return rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
	}

	/**
	 * Decrypt using OpenSSL (PHP >= 5.3.0)
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function openssl_decode($data, $key)
	{
		$method = $this->_get_method();
		$init_size = openssl_cipher_iv_length($method);
		if ($init_size > strlen($data))
		{
			return FALSE;
		}
		$init_vect = substr($data, 0, $init_size);
		$message = substr($data, $init_size);
		return rtrim(openssl_decrypt($message, $method, $key, OPENSSL_RAW_DATA, $init_vect ), "\0");
	}
	// --------------------------------------------------------------------

	/**
	 * Adds permuted noise to the IV + encrypted data to protect
	 * against Man-in-the-middle attacks on CBC mode ciphers
	 * http://www.ciphersbyritter.com/GLOSSARY.HTM#IV
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _add_cipher_noise($data, $key)
	{
		$key = $this->hash($key);
		$str = '';

		for ($i = 0, $j = 0, $ld = strlen($data), $lk = strlen($key); $i < $ld; ++$i, ++$j)
		{
			if ($j >= $lk)
			{
				$j = 0;
			}

			$str .= chr((ord($data[$i]) + ord($key[$j])) % 256);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Removes permuted noise from the IV + encrypted data, reversing
	 * _add_cipher_noise()
	 *
	 * Function description
	 *
	 * @param	string	$data
	 * @param	string	$key
	 * @return	string
	 */
	protected function _remove_cipher_noise($data, $key)
	{
		$key = $this->hash($key);
		$str = '';

		for ($i = 0, $j = 0, $ld = strlen($data), $lk = strlen($key); $i < $ld; ++$i, ++$j)
		{
			if ($j >= $lk)
			{
				$j = 0;
			}

			$temp = ord($data[$i]) - ord($key[$j]);

			if ($temp < 0)
			{
				$temp += 256;
			}

			$str .= chr($temp);
		}

		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Constant time comparison function to prevent timing attacks
	 *
	 * Function description
	 *
	 * @param	string	$a
	 * @param	string	$b
	 * @return	boolean
	 * 
	 * From https://defuse.ca/secure-php-encryption.htm
	 */
	
	protected static function _slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Cipher
	 *
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_cipher($cipher)
	{
		$this->_mcrypt_cipher = $cipher;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Mode
	 *
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_mode($mode)
	{
		$this->_mcrypt_mode = $mode;
		return $this;
	}


	/**
	 * Set the OpenSSL Cipher Method
	 *
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_method($mode)
	{
		$this->_openssl_method = $mode;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get OpenSSL Encryption Method (PHP 5.3.0)
	 *
	 * @return	int
	 */
	
	protected function _get_method()
	{
		// Derive an OpenSSL cipher constant from the mcrypt values stored
		if($this->_openssl_method !== NULL) {
			return $this->_openssl_method;
		}
		
		return OPENSSL_CIPHER_AES_256_CBC;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt cipher Value
	 *
	 * @return	int
	 */
	protected function _get_cipher()
	{
		if ($this->_mcrypt_cipher === NULL)
		{
			return $this->_mcrypt_cipher = MCRYPT_RIJNDAEL_128;
			// MCRYPT_RIJNDAEL_128 is AES; if you give it a 32-byte string
			// you will get AES-256; Rijndael 256 is not AES
		}

		return $this->_mcrypt_cipher;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt Mode Value
	 *
	 * @return	int
	 */
	protected function _get_mode()
	{
		if ($this->_mcrypt_mode === NULL)
		{
			return $this->_mcrypt_mode = MCRYPT_MODE_CBC;
		}

		return $this->_mcrypt_mode;
	}

	// --------------------------------------------------------------------

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

	// --------------------------------------------------------------------

	/**
	 * Hash encode a string
	 *
	 * @param	string
	 * @return	string
	 */
	public function hash($str)
	{
		return hash($this->_hash_type, $str);
	}

	/**
	 * Calculate a keyed hash message authentication code of a string
	 *
	 * @param	string
	 * @return	string
	 */
	public function hmac($str, $key = NULL, $algo = NULL, $raw = false)
	{
		// We may need stricter rules for this.
		
		if($key === NULL)
		{
			if($this->authentication_key !== NULL)
			{
				$key = $this->authentication_key;
			}
			else
			{
				
				$key = $this->encryption_key;
			}
		}
		if($key === NULL)
		{
			// It's still undefined? Kill it with fire
			show_error("Danger: Undefined authentication key!");
			return;
		}
		// Can pass the algorithm or just use the same as the hash type
		if($algo === NULL)
		{
			$algo = $this->_hash_type;
		}
		else
		{
			$algo = in_array($algo, hash_algos()) ? $algo : 'sha1';
		}
		return hash_hmac($algo, $str, $key, $raw);
	}

}

/* End of file Encrypt.php */
/* Location: ./system/libraries/Encrypt.php */
