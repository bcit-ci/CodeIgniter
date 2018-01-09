<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Encryption Class
 *
 * Provides two-way keyed encoding using Mcrypt
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Encrypt {

	/**
	 * Reference to the user's encryption key
	 *
	 * @var string
	 */
	public $encryption_key		= '';

	/**
	 * Type of hash operation
	 *
	 * @var string
	 */
	protected $_hash_type		= 'sha1';

	/**
	 * Flag for the existence of mcrypt
	 *
	 * @var bool
	 */
	protected $_mcrypt_exists	= FALSE;

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
	 * Initialize Encryption class
	 *
	 * @return	void
	 */
	public function __construct()
	{
		if (($this->_mcrypt_exists = function_exists('mcrypt_encrypt')) === FALSE)
		{
			show_error('The Encrypt library requires the Mcrypt extension.');
		}

		log_message('info', 'Encrypt Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the encryption key
	 *
	 * Returns it as MD5 in order to have an exact-length 128 bit key.
	 * Mcrypt is sensitive to keys that are not the correct length
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
				return $this->encryption_key;
			}

			$key = config_item('encryption_key');

			if ( ! self::strlen($key))
			{
				show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
			}
		}

		return md5($key);
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
	 * through mcrypt using the randomized key. The end result
	 * is a double-encrypted message string that is randomized
	 * with each call to this function, even if the supplied
	 * message and key are the same.
	 *
	 * @param	string	the string to encode
	 * @param	string	the key
	 * @return	string
	 */
	public function encode($string, $key = '')
	{
		return base64_encode($this->mcrypt_encode($string, $this->get_key($key)));
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
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string) OR base64_encode(base64_decode($string)) !== $string)
		{
			return FALSE;
		}

		return $this->mcrypt_decode(base64_decode($string), $this->get_key($key));
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
	 * For more details, see https://codeigniter.com/user_guide/installation/upgrade_200.html#encryption
	 *
	 * @param	string
	 * @param	int		(mcrypt mode constant)
	 * @param	string
	 * @return	string
	 */
	public function encode_from_legacy($string, $legacy_mode = MCRYPT_MODE_ECB, $key = '')
	{
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
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
		for ($i = 0, $l = self::strlen($string); $i < $l; $i++)
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

		for ($i = 0, $ls = self::strlen($string), $lh = self::strlen($hash); $i < $ls; $i++)
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
	public function mcrypt_encode($data, $key)
	{
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_DEV_URANDOM);
		return $this->_add_cipher_noise($init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), $key);
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt using Mcrypt
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_decode($data, $key)
	{
		$data = $this->_remove_cipher_noise($data, $key);
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());

		if ($init_size > self::strlen($data))
		{
			return FALSE;
		}

		$init_vect = self::substr($data, 0, $init_size);
		$data      = self::substr($data, $init_size);

		return rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
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

		for ($i = 0, $j = 0, $ld = self::strlen($data), $lk = self::strlen($key); $i < $ld; ++$i, ++$j)
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

		for ($i = 0, $j = 0, $ld = self::strlen($data), $lk = self::strlen($key); $i < $ld; ++$i, ++$j)
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
			return $this->_mcrypt_cipher = MCRYPT_RIJNDAEL_256;
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

	// --------------------------------------------------------------------

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */
	protected static function strlen($str)
	{
		return defined('MB_OVERLOAD_STRING')
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */
	protected static function substr($str, $start, $length = NULL)
	{
		if (defined('MB_OVERLOAD_STRING'))
		{
			// mb_substr($str, $start, null, '8bit') returns an empty
			// string on PHP 5.3
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}
