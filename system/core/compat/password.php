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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PHP ext/standard/password compatibility package
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Compatibility
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/
 * @link		http://php.net/password
 */

// ------------------------------------------------------------------------

if (is_php('5.5') OR ! is_php('5.3.7') OR ! defined('CRYPT_BLOWFISH') OR CRYPT_BLOWFISH !== 1 OR defined('HHVM_VERSION'))
{
	return;
}

// ------------------------------------------------------------------------

defined('PASSWORD_BCRYPT') OR define('PASSWORD_BCRYPT', 1);
defined('PASSWORD_DEFAULT') OR define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

// ------------------------------------------------------------------------

if ( ! function_exists('password_get_info'))
{
	/**
	 * password_get_info()
	 *
	 * @link	http://php.net/password_get_info
	 * @param	string	$hash
	 * @return	array
	 */
	function password_get_info($hash)
	{
		return (strlen($hash) < 60 OR sscanf($hash, '$2y$%d', $hash) !== 1)
			? array('algo' => 0, 'algoName' => 'unknown', 'options' => array())
			: array('algo' => 1, 'algoName' => 'bcrypt', 'options' => array('cost' => $hash));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('password_hash'))
{
	/**
	 * password_hash()
	 *
	 * @link	http://php.net/password_hash
	 * @param	string	$password
	 * @param	int	$algo
	 * @param	array	$options
	 * @return	mixed
	 */
	function password_hash($password, $algo, array $options = array())
	{
		if ($algo !== 1)
		{
			trigger_error('password_hash(): Unknown hashing algorithm: '.(int) $algo, E_USER_WARNING);
			return NULL;
		}

		if (isset($options['cost']) && ($options['cost'] < 4 OR $options['cost'] > 31))
		{
			trigger_error('password_hash(): Invalid bcrypt cost parameter specified: '.(int) $options['cost'], E_USER_WARNING);
			return NULL;
		}

		if (isset($options['salt']) && strlen($options['salt']) < 22)
		{
			trigger_error('password_hash(): Provided salt is too short: '.strlen($options['salt']).' expecting 22', E_USER_WARNING);
			return NULL;
		}
		elseif ( ! isset($options['salt']))
		{
			if (defined('MCRYPT_DEV_URANDOM'))
			{
				$options['salt'] = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
			}
			elseif (function_exists('openssl_random_pseudo_bytes'))
			{
				$options['salt'] = openssl_random_pseudo_bytes(16);
			}
			elseif (DIRECTORY_SEPARATOR === '/' && (is_readable($dev = '/dev/arandom') OR is_readable($dev = '/dev/urandom')))
			{
				if (($fp = fopen($dev, 'rb')) === FALSE)
				{
					log_message('error', 'compat/password: Unable to open '.$dev.' for reading.');
					return FALSE;
				}

				$options['salt'] = '';
				for ($read = 0; $read < 16; $read = strlen($options['salt']))
				{
					if (($read = fread($fp, 16 - $read)) === FALSE)
					{
						log_message('error', 'compat/password: Error while reading from '.$dev.'.');
						return FALSE;
					}
					$options['salt'] .= $read;
				}

				fclose($fp);
			}
			else
			{
				log_message('error', 'compat/password: No CSPRNG available.');
				return FALSE;
			}

			$options['salt'] = str_replace('+', '.', rtrim(base64_encode($options['salt']), '='));
		}
		elseif ( ! preg_match('#^[a-zA-Z0-9./]+$#D', $options['salt']))
		{
			$options['salt'] = str_replace('+', '.', rtrim(base64_encode($options['salt']), '='));
		}

		isset($options['cost']) OR $options['cost'] = 10;
		return crypt($password, sprintf('$2y$%02d$%s', $options['cost'], $options['salt']));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('password_needs_rehash'))
{
	/**
	 * password_needs_rehash()
	 *
	 * @link	http://php.net/password_needs_rehash
	 * @param	string	$hash
	 * @param	int	$algo
	 * @param	array	$options
	 * @return	bool
	 */
	function password_needs_rehash($hash, $algo, array $options = array())
	{
		$info = password_get_info($hash);

		if ($algo !== $info['algo'])
		{
			return TRUE;
		}
		elseif ($algo === 1)
		{
			$options['cost'] = isset($options['cost']) ? (int) $options['cost'] : 10;
			return ($info['options']['cost'] !== $options['cost']);
		}

		// Odd at first glance, but according to a comment in PHP's own unit tests,
		// because it is an unknown algorithm - it's valid and therefore doesn't
		// need rehashing.
		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('password_verify'))
{
	/**
	 * password_verify()
	 *
	 * @link	http://php.net/password_verify
	 * @param	string	$password
	 * @param	string	$hash
	 * @return	bool
	 */
	function password_verify($password, $hash)
	{
		if (strlen($hash) !== 60 OR strlen($password = crypt($password, $hash)) !== 60)
		{
			return FALSE;
		}

		$compare = 0;
		for ($i = 0; $i < 60; $i++)
		{
			$compare |= (ord($password[$i]) ^ ord($hash[$i]));
		}

		return ($compare === 0);
	}
}

/* End of file password.php */
/* Location: ./system/core/compat/password.php */