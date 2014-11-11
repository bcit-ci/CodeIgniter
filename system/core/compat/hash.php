<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PHP ext/hash compatibility package
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Compatibility
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/
 * @link		http://php.net/hash
 */

// ------------------------------------------------------------------------

if (is_php('5.6'))
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('hash_equals'))
{
	/**
	 * hash_equals()
	 *
	 * @link	http://php.net/hash_equals
	 * @param	string	$known_string
	 * @param	string	$user_string
	 * @return	bool
	 */
	function hash_equals($known_string, $user_string)
	{
		if ( ! is_string($known_string))
		{
			trigger_error('hash_equals(): Expected known_string to be a string, '.strtolower(gettype($known_string)).' given', E_USER_WARNING);
			return FALSE;
		}
		elseif ( ! is_string($user_string))
		{
			trigger_error('hash_equals(): Expected user_string to be a string, '.strtolower(gettype($user_string)).' given', E_USER_WARNING);
			return FALSE;
		}
		elseif (($length = strlen($known_string)) !== strlen($user_string))
		{
			return FALSE;
		}

		$diff = 0;
		for ($i = 0; $i < $length; $i++)
		{
			$diff |= ord($known_string[$i]) ^ ord($user_string[$i]);
		}

		return ($diff === 0);
	}
}

// ------------------------------------------------------------------------

if (is_php('5.5'))
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('hash_pbkdf2'))
{
	/**
	 * hash_pbkdf2()
	 *
	 * @link	http://php.net/hash_pbkdf2
	 * @param	string	$algo
	 * @param	string	$password
	 * @param	string	$salt
	 * @param	int	$iterations
	 * @param	int	$length
	 * @param	bool	$raw_output
	 * @return	string
	 */
	function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = FALSE)
	{
		if ( ! in_array($algo, hash_algos(), TRUE))
		{
			trigger_error('hash_pbkdf2(): Unknown hashing algorithm: '.$algo, E_USER_WARNING);
			return FALSE;
		}

		if (($type = gettype($iterations)) !== 'integer')
		{
			if ($type === 'object' && method_exists($iterations, '__toString'))
			{
				$iterations = (string) $iterations;
			}

			if (is_string($iterations) && is_numeric($iterations))
			{
				$iterations = (int) $iterations;
			}
			else
			{
				trigger_error('hash_pbkdf2() expects parameter 4 to be long, '.$type.' given', E_USER_WARNING);
				return NULL;
			}
		}

		if ($iterations < 1)
		{
			trigger_error('hash_pbkdf2(): Iterations must be a positive integer: '.$iterations, E_USER_WARNING);
			return FALSE;
		}

		if (($type = gettype($length)) !== 'integer')
		{
			if ($type === 'object' && method_exists($length, '__toString'))
			{
				$length = (string) $length;
			}

			if (is_string($length) && is_numeric($length))
			{
				$length = (int) $length;
			}
			else
			{
				trigger_error('hash_pbkdf2() expects parameter 5 to be long, '.$type.' given', E_USER_WARNING);
				return NULL;
			}
		}

		if ($length < 0)
		{
			trigger_error('hash_pbkdf2(): Length must be greater than or equal to 0: '.$length, E_USER_WARNING);
			return FALSE;
		}

		$hash_length = strlen(hash($algo, NULL, TRUE));
		if (empty($length))
		{
			$length = $hash_length;
		}

		$hash = '';
		// Note: Blocks are NOT 0-indexed
		for ($bc = ceil($length / $hash_length), $bi = 1; $bi <= $bc; $bi++)
		{
			$key = $derived_key = hash_hmac($algo, $salt.pack('N', $bi), $password, TRUE);
			for ($i = 1; $i < $iterations; $i++)
			{
				$derived_key ^= $key = hash_hmac($algo, $key, $password, TRUE);
			}

			$hash .= $derived_key;
		}

		// This is not RFC-compatible, but we're aiming for natural PHP compatibility
		return substr($raw_output ? $hash : bin2hex($hash), 0, $length);
	}
}

/* End of file hash.php */
/* Location: ./system/core/compat/hash.php */