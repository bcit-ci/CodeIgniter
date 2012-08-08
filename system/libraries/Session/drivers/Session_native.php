<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine	Dev Team
 * @copyright	Copyright	(c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version	2.0
 * @filesource
 */


/**
 * Native PHP session management driver
 *
 * This is the driver that uses the native PHP $_SESSION array through the Session driver library.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team
 */
class CI_Session_native extends CI_Session_driver {
	/**
	 * Initialize session driver object
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function initialize()
	{
		// Get config parameters
		$config = array();
		$CI =& get_instance();
		$prefs = array(
			'sess_cookie_name',
			'sess_expire_on_close',
			'sess_expiration',
			'sess_match_ip',
			'sess_match_useragent',
			'sess_time_to_update',
			'cookie_prefix',
			'cookie_path',
			'cookie_domain'
		);
		foreach ($prefs as $key)
		{
			$config[$key] = isset($this->_parent->params[$key]) ? $this->_parent->params[$key] :
				$CI->config->item($key);
		}

		// Set session name, if specified
		if ($config['sess_cookie_name'])
		{
			$name = $config['sess_cookie_name'];
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
		session_set_cookie_params($config['sess_expire_on_close'] ? 0 : $expire, $path, $domain);

		// Start session
		session_start();

		// Check session expiration, ip, and agent
		$now = time();
		$destroy = FALSE;
		if (isset($_SESSION['last_activity']) && ($_SESSION['last_activity'] + $expire) < $now)
		{
			// Expired - destroy
			$destroy = TRUE;
		}
		else if ($config['sess_match_ip'] == TRUE && isset($_SESSION['ip_address']) &&
		$_SESSION['ip_address'] != $CI->input->ip_address())
		{
			// IP doesn't match - destroy
			$destroy = TRUE;
		}
		else if ($config['sess_match_useragent'] == TRUE && isset($_SESSION['user_agent']) &&
		$_SESSION['user_agent'] != trim(substr($CI->input->user_agent(), 0, 50)))
		{
			// Agent doesn't match - destroy
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
		if ($config['sess_time_to_update'] && isset($_SESSION['last_activity']) &&
		($_SESSION['last_activity'] + $config['sess_time_to_update']) < $now)
		{
			// Regenerate ID, but don't destroy session
			$this->sess_regenerate(FALSE);
		}

		// Set activity time
		$_SESSION['last_activity'] = $now;

		// Set matching values as required
		if ($config['sess_match_ip'] == TRUE && !isset($_SESSION['ip_address']))
		{
			// Store user IP address
			$_SESSION['ip_address'] = $CI->input->ip_address();
		}
		if ($config['sess_match_useragent'] == TRUE && !isset($_SESSION['user_agent']))
		{
			// Store user agent string
			$_SESSION['user_agent'] = trim(substr($CI->input->user_agent(), 0, 50));
		}

		// Make session ID available
		$_SESSION['session_id'] = session_id();
	}

	/**
	 * Save the session data
	 *
	 * @access	public
	 * @return	void
	 */
	public function sess_save()
	{
		// Nothing to do - changes to $_SESSION are automatically saved
	}

	/**
	 * Destroy the current session
	 *
	 * @access	public
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
			setcookie($name, '', time() - 42000, $params['path'], $params['domain']);
			unset($_COOKIE[$name]);
		}
		session_destroy();
	}

	/**
	 * Regenerate the current session
	 *
	 * Regenerate the session id
	 *
	 * @access	public
	 * @param	boolean	Destroy session data flag (default: FALSE)
	 * @return	void
	 */
	public function sess_regenerate($destroy = FALSE)
	{
		// Just regenerate id, passing destroy flag
		session_regenerate_id($destroy);
		$_SESSION['session_id'] = session_id();
	}

	/**
	 * Get a reference to user data array
	 *
	 * @access	public
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
