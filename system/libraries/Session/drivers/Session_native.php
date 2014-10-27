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
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Native PHP session management driver
 *
 * This is the driver that uses the native PHP $_SESSION array through the Session driver library.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		EllisLab Dev Team
 */
class CI_Session_native extends CI_Session_driver {

	/**
	 * Initialize session driver object
	 *
	 * @return	void
	 */
	protected function initialize()
	{
		// Get config parameters
		$config = array();
		$prefs = array(
			'sess_cookie_name',
			'sess_expire_on_close',
			'sess_expiration',
			'sess_match_ip',
			'sess_match_useragent',
			'sess_time_to_update',
			'cookie_prefix',
			'cookie_path',
			'cookie_domain',
			'cookie_secure',
			'cookie_httponly'
		);

		foreach ($prefs as $key)
		{
			$config[$key] = isset($this->_parent->params[$key])
				? $this->_parent->params[$key]
				: $this->CI->config->item($key);
		}

		// Set session name, if specified
		if ($config['sess_cookie_name'])
		{
			// Differentiate name from cookie driver with '_id' suffix
			$name = $config['sess_cookie_name'].'_id';
			if ($config['cookie_prefix'])
			{
				// Prepend cookie prefix
				$name = $config['cookie_prefix'].$name;
			}
			session_name($name);
		}

		// Set expiration, path, and domain
		$expire = 7200;
		$path = '/';
		$domain = '';
		$secure = (bool) $config['cookie_secure'];
		$http_only = (bool) $config['cookie_httponly'];

		if ($config['sess_expiration'] !== FALSE)
		{
			// Default to 2 years if expiration is "0"
			$expire = ($config['sess_expiration'] == 0) ? (60*60*24*365*2) : $config['sess_expiration'];
		}

		if ($config['cookie_path'])
		{
			// Use specified path
			$path = $config['cookie_path'];
		}

		if ($config['cookie_domain'])
		{
			// Use specified domain
			$domain = $config['cookie_domain'];
		}

		session_set_cookie_params($config['sess_expire_on_close'] ? 0 : $expire, $path, $domain, $secure, $http_only);

		// Start session
		session_start();

		// Check session expiration, ip, and agent
		$now = time();
		$destroy = FALSE;
		if (isset($_SESSION['last_activity']) && (($_SESSION['last_activity'] + $expire) < $now OR $_SESSION['last_activity'] > $now))
		{
			// Expired - destroy
			log_message('debug', 'Session: Expired');
			$destroy = TRUE;
		}
		elseif ($config['sess_match_ip'] === TRUE && isset($_SESSION['ip_address'])
			&& $_SESSION['ip_address'] !== $this->CI->input->ip_address())
		{
			// IP doesn't match - destroy
			log_message('debug', 'Session: IP address mismatch');
			$destroy = TRUE;
		}
		elseif ($config['sess_match_useragent'] === TRUE && isset($_SESSION['user_agent'])
			&& $_SESSION['user_agent'] !== trim(substr($this->CI->input->user_agent(), 0, 50)))
		{
			// Agent doesn't match - destroy
			log_message('debug', 'Session: User Agent string mismatch');
			$destroy = TRUE;
		}

		// Destroy expired or invalid session
		if ($destroy)
		{
			// Clear old session and start new
			$this->sess_destroy();
			session_start();
		}

		// Check for update time
		if ($config['sess_time_to_update'] && isset($_SESSION['last_activity'])
			&& ($_SESSION['last_activity'] + $config['sess_time_to_update']) < $now)
		{
			// Changing the session ID amidst a series of AJAX calls causes problems
			if ( ! $this->CI->input->is_ajax_request())
			{
				// Regenerate ID, but don't destroy session
				log_message('debug', 'Session: Regenerate ID');
				$this->sess_regenerate(FALSE);
			}
		}

		// Set activity time
		$_SESSION['last_activity'] = $now;

		// Set matching values as required
		if ($config['sess_match_ip'] === TRUE && ! isset($_SESSION['ip_address']))
		{
			// Store user IP address
			$_SESSION['ip_address'] = $this->CI->input->ip_address();
		}

		if ($config['sess_match_useragent'] === TRUE && ! isset($_SESSION['user_agent']))
		{
			// Store user agent string
			$_SESSION['user_agent'] = trim(substr($this->CI->input->user_agent(), 0, 50));
		}

		// Make session ID available
		$_SESSION['session_id'] = session_id();
	}

	// ------------------------------------------------------------------------

	/**
	 * Save the session data
	 *
	 * @return	void
	 */
	public function sess_save()
	{
		// Nothing to do - changes to $_SESSION are automatically saved
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy the current session
	 *
	 * @return	void
	 */
	public function sess_destroy()
	{
		// Cleanup session
		$_SESSION = array();
		$name = session_name();
		if (isset($_COOKIE[$name]))
		{
			// Clear session cookie
			$params = session_get_cookie_params();
			setcookie($name, '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
			unset($_COOKIE[$name]);
		}
		session_destroy();
	}

	// ------------------------------------------------------------------------

	/**
	 * Regenerate the current session
	 *
	 * Regenerate the session id
	 *
	 * @param	bool	Destroy session data flag (default: FALSE)
	 * @return	void
	 */
	public function sess_regenerate($destroy = FALSE)
	{
		// Just regenerate id, passing destroy flag
		session_regenerate_id($destroy);
		$_SESSION['session_id'] = session_id();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get a reference to user data array
	 *
	 * @return	array	Reference to userdata
	 */
	public function &get_userdata()
	{
		// Just return reference to $_SESSION
		return $_SESSION;
	}

}

/* End of file Session_native.php */
/* Location: ./system/libraries/Session/drivers/Session_native.php */