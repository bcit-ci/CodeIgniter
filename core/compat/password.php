<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PHP ext/standard/password compatibility package
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Compatibility
 * @author		Andrey Andreev
 * @link		https://codeigniter.com/user_guide/
 * @link		https://secure.php.net/password
 */

// ------------------------------------------------------------------------

if (is_php('5.5') or ! defined('CRYPT_BLOWFISH') or CRYPT_BLOWFISH !== 1 or defined('HHVM_VERSION')) {
    return;
}

// ------------------------------------------------------------------------

defined('PASSWORD_BCRYPT') or define('PASSWORD_BCRYPT', 1);
defined('PASSWORD_DEFAULT') or define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

// ------------------------------------------------------------------------

if (! function_exists('password_get_info')) {
    /**
     * password_get_info()
     *
     * @link	https://secure.php.net/password_get_info
     * @param	string	$hash
     * @return	array
     */
    function password_get_info($hash)
    {
        return (strlen($hash) < 60 or sscanf($hash, '$2y$%d', $hash) !== 1)
            ? array('algo' => 0, 'algoName' => 'unknown', 'options' => array())
            : array('algo' => 1, 'algoName' => 'bcrypt', 'options' => array('cost' => $hash));
    }
}

// ------------------------------------------------------------------------

if (! function_exists('password_hash')) {
    /**
     * password_hash()
     *
     * @link	https://secure.php.net/password_hash
     * @param	string	$password
     * @param	int	$algo
     * @param	array	$options
     * @return	mixed
     */
    function password_hash($password, $algo, array $options = array())
    {
        static $func_overload;
        isset($func_overload) or $func_overload = (extension_loaded('mbstring') && ini_get('mbstring.func_overload'));

        if ($algo !== 1) {
            trigger_error('password_hash(): Unknown hashing algorithm: '.(int) $algo, E_USER_WARNING);
            return null;
        }

        if (isset($options['cost']) && ($options['cost'] < 4 or $options['cost'] > 31)) {
            trigger_error('password_hash(): Invalid bcrypt cost parameter specified: '.(int) $options['cost'], E_USER_WARNING);
            return null;
        }

        if (isset($options['salt']) && ($saltlen = ($func_overload ? mb_strlen($options['salt'], '8bit') : strlen($options['salt']))) < 22) {
            trigger_error('password_hash(): Provided salt is too short: '.$saltlen.' expecting 22', E_USER_WARNING);
            return null;
        } elseif (! isset($options['salt'])) {
            if (function_exists('random_bytes')) {
                try {
                    $options['salt'] = random_bytes(16);
                } catch (Exception $e) {
                    log_message('error', 'compat/password: Error while trying to use random_bytes(): '.$e->getMessage());
                    return false;
                }
            } elseif (defined('MCRYPT_DEV_URANDOM')) {
                $options['salt'] = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
            } elseif (DIRECTORY_SEPARATOR === '/' && (is_readable($dev = '/dev/arandom') or is_readable($dev = '/dev/urandom'))) {
                if (($fp = fopen($dev, 'rb')) === false) {
                    log_message('error', 'compat/password: Unable to open '.$dev.' for reading.');
                    return false;
                }

                // Try not to waste entropy ...
                stream_set_chunk_size($fp, 16);

                $options['salt'] = '';
                for ($read = 0; $read < 16; $read = ($func_overload) ? mb_strlen($options['salt'], '8bit') : strlen($options['salt'])) {
                    if (($read = fread($fp, 16 - $read)) === false) {
                        log_message('error', 'compat/password: Error while reading from '.$dev.'.');
                        return false;
                    }
                    $options['salt'] .= $read;
                }

                fclose($fp);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $is_secure = null;
                $options['salt'] = openssl_random_pseudo_bytes(16, $is_secure);
                if ($is_secure !== true) {
                    log_message('error', 'compat/password: openssl_random_pseudo_bytes() set the $cryto_strong flag to FALSE');
                    return false;
                }
            } else {
                log_message('error', 'compat/password: No CSPRNG available.');
                return false;
            }

            $options['salt'] = str_replace('+', '.', rtrim(base64_encode($options['salt']), '='));
        } elseif (! preg_match('#^[a-zA-Z0-9./]+$#D', $options['salt'])) {
            $options['salt'] = str_replace('+', '.', rtrim(base64_encode($options['salt']), '='));
        }

        isset($options['cost']) or $options['cost'] = 10;

        return (strlen($password = crypt($password, sprintf('$2y$%02d$%s', $options['cost'], $options['salt']))) === 60)
            ? $password
            : false;
    }
}

// ------------------------------------------------------------------------

if (! function_exists('password_needs_rehash')) {
    /**
     * password_needs_rehash()
     *
     * @link	https://secure.php.net/password_needs_rehash
     * @param	string	$hash
     * @param	int	$algo
     * @param	array	$options
     * @return	bool
     */
    function password_needs_rehash($hash, $algo, array $options = array())
    {
        $info = password_get_info($hash);

        if ($algo !== $info['algo']) {
            return true;
        } elseif ($algo === 1) {
            $options['cost'] = isset($options['cost']) ? (int) $options['cost'] : 10;
            return ($info['options']['cost'] !== $options['cost']);
        }

        // Odd at first glance, but according to a comment in PHP's own unit tests,
        // because it is an unknown algorithm - it's valid and therefore doesn't
        // need rehashing.
        return false;
    }
}

// ------------------------------------------------------------------------

if (! function_exists('password_verify')) {
    /**
     * password_verify()
     *
     * @link	https://secure.php.net/password_verify
     * @param	string	$password
     * @param	string	$hash
     * @return	bool
     */
    function password_verify($password, $hash)
    {
        if (strlen($hash) !== 60 or strlen($password = crypt($password, $hash)) !== 60) {
            return false;
        }

        $compare = 0;
        for ($i = 0; $i < 60; $i++) {
            $compare |= (ord($password[$i]) ^ ord($hash[$i]));
        }

        return ($compare === 0);
    }
}
