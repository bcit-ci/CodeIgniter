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
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Encryption Class
 *
 * Provides two-way keyed encryption via PHP's MCrypt and/or OpenSSL extensions.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Encryption {

	/**
	 * Encryption cipher
	 *
	 * @var	string
	 */
	protected $_cipher = 'rijndael-128';

	/**
	 * Cipher mode
	 *
	 * @var	string
	 */
	protected $_mode = 'cbc';

	/**
	 * Cipher handle
	 *
	 * @var	mixed
	 */
	protected $_handle;

	/**
	 * Encryption key
	 *
	 * @var	string
	 */
	protected $_key;

	/**
	 * PHP extension to be used
	 *
	 * @var	string
	 */
	protected $_driver;

	/**
	 * List of usable drivers (PHP extensions)
	 *
	 * @var	array
	 */
	protected $_drivers = array();

	/**
	 * List of supported HMAC algorightms
	 *
	 * name => digest size pairs
	 *
	 * @var	array
	 */
	protected $_digests = array(
		'sha224' => 28,
		'sha256' => 32,
		'sha384' => 48,
		'sha512' => 64
	);

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(array $params = array())
	{
		$this->_drivers = array(
			'mcrypt' => extension_loaded('mcrypt'),
			// While OpenSSL is available for PHP 5.3.0, an IV parameter
			// for the encrypt/decrypt functions is only available since 5.3.3
			'openssl' => (is_php('5.3.3') && extension_loaded('openssl'))
		);

		if ( ! $this->_drivers['mcrypt'] && ! $this->_drivers['openssl'])
		{
			return show_error('Encryption: Unable to find an available encryption driver.');
		}
		// Our configuration validates against the existence of MCRYPT_MODE_* constants,
		// but MCrypt supports CTR mode without actually having a constant for it, so ...
		elseif ($this->_drivers['mcrypt'] && ! defined('MCRYPT_MODE_CTR'))
		{
			define('MCRYPT_MODE_CTR', 'ctr');
		}

		$this->initialize($params);

		isset($this->_key) OR $this->_key = config_item('encryption_key');
		if (empty($this->_key))
		{
			return show_error('Encryption: You are required to set an encryption key in your configuration.');
		}

		log_message('debug', 'Encryption Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	CI_Encryption
	 */
	public function initialize(array $params)
	{
		if ( ! empty($params['driver']))
		{
			if (isset($this->_drivers[$params['driver']]))
			{
				if ($this->_drivers[$params['driver']])
				{
					$this->_driver = $params['driver'];
				}
				else
				{
					log_message('error', "Encryption: Driver '".$params['driver']."' is not available.");
				}
			}
			else
			{
				log_message('error', "Encryption: Unknown driver '".$params['driver']."' cannot be configured.");
			}
		}

		if (empty($this->_driver))
		{
			$this->_driver = ($this->_drivers['mcrypt'] === TRUE)
				? 'mcrypt'
				: 'openssl';

			log_message('debug', "Encryption: Auto-configured driver '".$this->_driver."'.");
		}

		empty($params['key']) OR $this->_key = $params['key'];
		$this->{'_'.$this->_driver.'_initialize'}($params);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize MCrypt
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	protected function _mcrypt_initialize($params)
	{
		if ( ! empty($params['cipher']))
		{
			$params['cipher'] = strtolower($params['cipher']);
			$this->_cipher_alias($params['cipher']);

			if ( ! in_array($params['cipher'], mcrypt_list_algorithms(), TRUE))
			{
				log_message('error', 'Encryption: MCrypt cipher '.strtoupper($params['cipher']).' is not available.');
			}
			else
			{
				$this->_cipher = $params['cipher'];
			}
		}

		if ( ! empty($params['mode']))
		{
			if ( ! defined('MCRYPT_MODE_'.$params['mode']))
			{
				log_message('error', 'Encryption: MCrypt mode '.strtotupper($params['mode']).' is not available.');
			}
			else
			{
				$this->_mode = constant('MCRYPT_MODE_'.$params['mode']);
			}
		}

		if (isset($this->_cipher, $this->_mode))
		{
			if (is_resource($this->_handle)
				&& (strtolower(mcrypt_enc_get_algorithms_name($this->_handle)) !== $this->_cipher
					OR strtolower(mcrypt_enc_get_modes_name($this->_handle)) !== $this->_mode)
			)
			{
				mcrypt_module_close($this->_handle);
			}

			if ($this->_handle = mcrypt_module_open($this->_cipher, '', $this->_mode, ''))
			{
				log_message('debug', 'Encryption: MCrypt cipher '.strtoupper($this->_cipher).' initialized in '.strtoupper($this->_mode).' mode.');
			}
			else
			{
				log_message('error', 'Encryption: Unable to initialize MCrypt with cipher '.strtoupper($this->_cipher).' in '.strtoupper($this->_mode).' mode.');
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize OpenSSL
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	protected function _openssl_initialize($params)
	{
		if ( ! empty($params['cipher']))
		{
			$params['cipher'] = strtolower($params['cipher']);
			$this->_cipher_alias($params['cipher']);
			$this->_cipher = $params['cipher'];
		}

		if ( ! empty($params['mode']))
		{
			$this->_mode = strtolower($params['mode']);
		}

		if (isset($this->_cipher, $this->_mode))
		{
			// OpenSSL methods aren't suffixed with '-stream' for this mode
			$handle = ($this->_mode === 'stream')
				? $this->_cipher
				: $this->_cipher.'-'.$this->_mode;

			if ( ! in_array($handle, openssl_get_cipher_methods(), TRUE))
			{
				$this->_handle = NULL;
				log_message('error', 'Encryption: Unable to initialize OpenSSL with method '.strtoupper($handle).'.');
			}
			else
			{
				$this->_handle = $handle;
				log_message('debug', 'Encryption: OpenSSL initialized with method '.strtoupper($handle).'.');
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Encrypt
	 *
	 * @param	string	$data	Input data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	public function encrypt($data, array $params = NULL)
	{
		if (($params = $this->_get_params($params)) === FALSE)
		{
			return FALSE;
		}

		if (($data = $this->{'_'.$this->_driver.'_encrypt'}($data, $params)) === FALSE)
		{
			return FALSE;
		}

		if ($params['base64'])
		{
			$data = base64_encode($data);
		}

		if ($params['hmac'] !== FALSE)
		{
			if ( ! isset($params['hmac']['key']))
			{
				$params['hmac']['key'] = $this->hkdf(
					$params['key'],
					$params['hmac']['digest'],
					NULL,
					NULL,
					'authentication'
				);
			}

			return hash_hmac($params['hmac']['digest'], $data, $params['hmac']['key'], ! $params['base64']).$data;
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Encrypt via MCrypt
	 *
	 * @param	string	$data	Input data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	protected function _mcrypt_encrypt($data, $params)
	{
		if ( ! is_resource($params['handle']))
		{
			return FALSE;
		}
		elseif ( ! isset($params['iv']))
		{
			$params['iv'] = ($iv_size = mcrypt_enc_get_iv_size($params['handle']))
				? $this->_mcrypt_get_iv($iv_size)
				: NULL;
		}


		if (mcrypt_generic_init($params['handle'], $params['key'], $params['iv']) < 0)
		{
			if ($params['handle'] !== $this->_handle)
			{
				mcrypt_module_close($params['handle']);
			}

			return FALSE;
		}

		// Use PKCS#7 padding in order to ensure compatibility with OpenSSL
		// and other implementations outside of PHP
		$block_size = mcrypt_enc_get_block_size($params['handle']);
		$pad = $block_size - (strlen($data) % $block_size);
		$data .= str_repeat(chr($pad), $pad);

		// Work-around for yet another strange behavior in MCrypt.
		//
		// When encrypting in ECB mode, the IV is ignored. Yet
		// mcrypt_enc_get_iv_size() returns a value larger than 0
		// even if ECB is used AND mcrypt_generic_init() complains
		// if you don't pass an IV with length equal to the said
		// return value.
		//
		// This probably would've been fine (even though still wasteful),
		// but OpenSSL isn't that dumb and we need to make the process
		// portable, so ...
		$data = (mcrypt_enc_get_modes_name($params['handle']) !== 'ECB')
			? $params['iv'].mcrypt_generic($params['handle'], $data)
			: mcrypt_generic($params['handle'], $data);

		mcrypt_generic_deinit($params['handle']);
		if ($params['handle'] !== $this->_handle)
		{
			mcrypt_module_close($params['handle']);
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Encrypt via OpenSSL
	 *
	 * @param	string	$data	Input data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	protected function _openssl_encrypt($data, $params)
	{
		if (empty($params['handle']))
		{
			return FALSE;
		}
		elseif ( ! isset($params['iv']))
		{
			$params['iv'] = ($iv_size = openssl_cipher_iv_length($params['handle']))
				? $this->_openssl_get_iv($iv_size)
				: NULL;
		}

		$data = openssl_encrypt(
			$data,
			$params['handle'],
			$params['key'],
			1, // DO NOT TOUCH!
			$params['iv']
		);

		if ($data === FALSE)
		{
			return FALSE;
		}

		return $params['iv'].$data;
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt
	 *
	 * @param	string	$data	Encrypted data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	public function decrypt($data, array $params = NULL)
	{
		if (($params = $this->_get_params($params)) === FALSE)
		{
			return FALSE;
		}

		if ($params['hmac'] !== FALSE)
		{
			if ( ! isset($params['hmac']['key']))
			{
				$params['hmac']['key'] = $this->hkdf(
					$params['key'],
					$params['hmac']['digest'],
					NULL,
					NULL,
					'authentication'
				);
			}

			// This might look illogical, but it is done during encryption as well ...
			// The 'base64' value is effectively an inverted "raw data" parameter
			$digest_size = ($params['base64'])
				? $this->_digests[$params['hmac']['digest']] * 2
				: $this->_digests[$params['hmac']['digest']];
			$hmac = substr($data, 0, $digest_size);
			$data = substr($data, $digest_size);

			if ($hmac !== hash_hmac($params['hmac']['digest'], $data, $params['hmac']['key'], ! $params['base64']))
			{
				return FALSE;
			}
		}

		if ($params['base64'])
		{
			$data = base64_decode($data);
		}

		if (isset($params['iv']) && strncmp($params['iv'], $data, $iv_size = strlen($params['iv'])) === 0)
		{
			$data = substr($data, $iv_size);
		}

		return $this->{'_'.$this->_driver.'_decrypt'}($data, $params);
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt via MCrypt
	 *
	 * @param	string	$data	Encrypted data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	protected function _mcrypt_decrypt($data, $params)
	{
		if ( ! is_resource($params['handle']))
		{
			return FALSE;
		}
		elseif ( ! isset($params['iv']))
		{
			if ($iv_size = mcrypt_enc_get_iv_size($params['handle']))
			{
				if (mcrypt_enc_get_modes_name($params['handle']) !== 'ECB')
				{
					$params['iv'] = substr($data, 0, $iv_size);
					$data = substr($data, $iv_size);
				}
				else
				{
					// MCrypt is dumb and this is ignored, only size matters
					$params['iv'] = str_repeat("\x0", $iv_size);
				}
			}
			else
			{
				$params['iv'] = NULL;
			}
		}

		if (mcrypt_generic_init($params['handle'], $params['key'], $params['iv']) < 0)
		{
			if ($params['handle'] !== $this->_handle)
			{
				mcrypt_module_close($params['handle']);
			}

			return FALSE;
		}

		$data = mdecrypt_generic($params['handle'], $data);

		mcrypt_generic_deinit($params['handle']);
		if ($params['handle'] !== $this->_handle)
		{
			mcrypt_module_close($params['handle']);
		}

		// Remove PKCS#7 padding
		return substr($data, 0, -ord($data[strlen($data)-1]));
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt via OpenSSL
	 *
	 * @param	string	$data	Encrypted data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	protected function _openssl_decrypt($data, $params)
	{
		if ( ! isset($params['iv']))
		{
			if ($iv_size = openssl_cipher_iv_length($params['handle']))
			{
				$params['iv'] = substr($data, 0, $iv_size);
				$data = substr($data, $iv_size);
			}
			else
			{
				$params['iv'] = NULL;
			}
		}

		return empty($params['handle'])
			? FALSE
			: openssl_decrypt(
				$data,
				$params['handle'],
				$params['key'],
				1, // DO NOT TOUCH!
				$params['iv']
			);
	}

	// --------------------------------------------------------------------

	/**
	 * Get IV via MCrypt
	 *
	 * @param	int	$size
	 * @return	int
	 */
	protected function _mcrypt_get_iv($size)
	{
		// If /dev/urandom is available - use it, otherwise there's
		// also /dev/random, but it is highly unlikely that it would
		// be available while /dev/urandom is not and it is known to be
		// blocking anyway.
		if (defined(MCRYPT_DEV_URANDOM))
		{
			$source = MCRYPT_DEV_URANDOM;
		}
		else
		{
			$source = MCRYPT_RAND;
			is_php('5.3') OR srand(microtime(TRUE));
		}

		return mcrypt_create_iv($size, $source);
	}

	// --------------------------------------------------------------------

	/**
	 * Get IV via OpenSSL
	 *
	 * @param	int	$size	IV size
	 * @return	int
	 */
	protected function _openssl_get_iv($size)
	{
		return openssl_random_pseudo_bytes($size);
	}

	// --------------------------------------------------------------------

	/**
	 * Get params
	 *
	 * @param	array	$params	Input parameters
	 * @return	array
	 */
	protected function _get_params($params)
	{
		if (empty($params))
		{
			return isset($this->_cipher, $this->_mode, $this->_key, $this->_handle)
				? array(
					'handle' => $this->_handle,
					'cipher' => $this->_cipher,
					'mode' => $this->_mode,
					'key' => $this->_key,
					'base64' => TRUE,
					'hmac' => $this->_mode === 'gcm' ? FALSE : array('digest' => 'sha512',	'key' => NULL)
				)
				: FALSE;
		}
		elseif ( ! isset($params['cipher'], $params['mode'], $params['key']))
		{
			return FALSE;
		}

		if ($params['mode'] === 'gcm')
		{
			$params['hmac'] = FALSE;
		}
		elseif ( ! isset($params['hmac']) OR ( ! is_array($params['hmac']) && $params['hmac'] !== FALSE))
		{
			$params['hmac'] = array(
				'digest' => 'sha512',
				'key' => NULL
			);
		}
		elseif (is_array($params['hmac']))
		{
			if (isset($params['hmac']['digest']) && ! isset($this->_digests[$params['hmac']['digest']]))
			{
				return FALSE;
			}

			$params['hmac'] = array(
				'digest' => isset($params['hmac']['digest']) ? $params['hmac']['digest'] : 'sha512',
				'key' => isset($params['hmac']['key']) ? $params['hmac']['key'] : NULL
			);
		}

		$params = array(
			'handle' => NULL,
			'cipher' => isset($params['cipher']) ? $params['cipher'] : $this->_cipher,
			'mode' => isset($params['mode']) ? $params['mode'] : $this->_mode,
			'key' => isset($params['key']) ? $params['key'] : $this->_key,
			'iv' => isset($params['iv']) ? $params['iv'] : NULL,
			'base64' => isset($params['base64']) ? $params['base64'] : TRUE,
			'hmac' => $params['hmac']
		);

		$this->_cipher_alias($params['cipher']);
		$params['handle'] = ($params['cipher'] !== $this->_cipher OR $params['mode'] !== $this->_mode)
			? $this->{'_'.$this->_driver.'_get_handle'}($params['cipher'], $params['mode'])
			: $this->_handle;

		return $params;
	}

	// --------------------------------------------------------------------

	/**
	 * Get MCrypt handle
	 *
	 * @param	string	$cipher	Cipher name
	 * @param	string	$mode	Encryption mode
	 * @return	resource
	 */
	protected function _mcrypt_get_handle($cipher, $mode)
	{
		return mcrypt_module_open($cipher, '', $mode, '');
	}

	// --------------------------------------------------------------------

	/**
	 * Get OpenSSL handle
	 *
	 * @param	string	$cipher	Cipher name
	 * @param	string	$mode	Encryption mode
	 * @return	string
	 */
	protected function _openssl_get_handle($cipher, $mode)
	{
		// OpenSSL methods aren't suffixed with '-stream' for this mode
		return ($mode === 'stream')
			? $cipher
			: $cipher.'-'.$mode;
	}

	// --------------------------------------------------------------------

	/**
	 * Cipher alias
	 *
	 * Tries to translate cipher names between MCrypt and OpenSSL's "dialects".
	 *
	 * @param	string	$cipher	Cipher name
	 * @return	void
	 */
	protected function _cipher_alias(&$cipher)
	{
		static $dictionary;

		if (empty($dictionary))
		{
			$dictionary = array(
				'mcrypt' => array(
					'aes-128' => 'rijndael-128',
					'aes-192' => 'rijndael-128',
					'aes-256' => 'rijndael-128',
					'des3-ede3' => 'tripledes',
					'bf' => 'blowfish',
				),
				'openssl' => array(
					'rijndael-128' => 'aes-128',
					'tripledes' => 'des-ede3',
					'blowfish' => 'bf'
				)
			);

			// Notes:
			//
			// - Blowfish is said to be supporting key sizes between
			//   4 and 56 bytes, but it appears that between MCrypt and
			//   OpenSSL, only those of 16 and more bytes are compatible.
			//
			// Other seemingly matching ciphers between MCrypt, OpenSSL:
			//
			// - DES is compatible, but doesn't need an alias
			// - CAST-128/CAST5 is NOT compatible
			//	mcrypt: 'cast-128'
			//	openssl: 'cast5'
			// - RC2 is NOT compatible
			//	mcrypt: 'rc2'
			//	openssl: 'rc2', 'rc2-40', 'rc2-64'
			//
			// To avoid any other confusion due to a popular (but incorrect)
			// belief, it should also be noted that Rijndael-192/256 are NOT
			// the same ciphers as AES-192/256 like Rijndael-128 and AES-256 is.
			//
			// All compatibility tests were done in CBC mode.
		}

		if (isset($dictionary[$this->_driver][$cipher]))
		{
			$cipher = $dictionary[$this->_driver][$cipher];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * HKDF
	 *
	 * @link	https://tools.ietf.org/rfc/rfc5869.txt
	 * @param	$key	Input key
	 * @param	$digest	A SHA-2 hashing algorithm
	 * @param	$salt	Optional salt
	 * @param	$info	Optional context/application-specific info
	 * @param	$length	Output length (defaults to the selected digest size)
	 * @return	string	A pseudo-random key
	 */
	public function hkdf($key, $digest = 'sha512', $salt = NULL, $length = NULL, $info = '')
	{
		if ( ! isset($this->_digests[$digest]))
		{
			return FALSE;
		}

		if (empty($length) OR ! is_int($length))
		{
			$length = $this->_digests[$digest];
		}
		elseif ($length > (255 * $this->_digests[$digest]))
		{
			return FALSE;
		}

		isset($salt) OR $salt = str_repeat("\0", $this->_digests[$digest]);

		$prk = hash_hmac($digest, $key, $salt, TRUE);
		$key = '';
		for ($key_block = '', $block_index = 1; strlen($key) < $length; $block_index++)
		{
			$key_block = hash_hmac($digest, $key_block.$info.chr($block_index), $prk, TRUE);
			$key .= $key_block;
		}

		return substr($key, 0, $length);
	}

	// --------------------------------------------------------------------

	/**
	 * __get() magic
	 *
	 * @param	string	$key	Property name
	 * @return	mixed
	 */
	public function __get($key)
	{
		return in_array($key, array('cipher', 'mode', 'driver', 'drivers', 'digests'), TRUE)
			? $this->{'_'.$key}
			: NULL;
	}

}

/* End of file Encryption.php */
/* Location: ./system/libraries/Encryption.php */