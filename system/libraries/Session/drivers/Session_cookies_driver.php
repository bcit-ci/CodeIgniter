<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Files Driver
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Kakysha
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_cookies_driver extends CI_Session_driver implements SessionHandlerInterface {
	
	/*
	 * Encryption key
	 * 
	 * @var string
	 */
	protected $_encryption_key;
	
	/*
	 * Encrypt data cookie?
	 * 
	 * @var bool
	 */
	protected $_encrypt_data_cookie = FALSE;
	
	/*
	 * Reference to CodeIgniter core object
	 * 
	 * @var object
	 */
	protected $_CI;
	
	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);
		
		// Load additional config parameters specific to this driver
		isset($this->_config['encryption_key']) OR $this->_config['encryption_key'] = config_item('encryption_key');
		isset($this->_config['encrypt_data_cookie']) OR $this->_config['encrypt_data_cookie'] = config_item('sess_encrypt_data_cookie');
		
		// Sanitize session_data cookie name
		if (isset($this->_config['save_path']))
		{
			$this->_config['save_path'] = preg_replace("/[^a-zA-Z0-9\_\-]+/", "", $this->_config['save_path']);
		}
		else
		{
			$this->_config['save_path'] = 'ci_session_data';
		}
		
		if (empty($this->_config['encryption_key']))
		{
			log_message('error', 'Session: encryption_key is not set, aborting.');
		} else 
		{
			$this->_encryption_key = $this->_config['encryption_key'];
		}
		
		if ( ! empty($this->_config['encrypt_data_cookie']))
		{
			$this->_CI =& get_instance();
			$this->_CI->load->library('encryption');
			$this->_encrypt_data_cookie = (bool) $this->_config['encrypt_data_cookie'];
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 *
	 * Cookies are already received, nothing to do here
	 *
	 * @param	string	$save_path	Path to session files' directory
	 * @param	string	$name		Session cookie name, unused
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		// Driver is unusable if no encryption key is set
		return isset($this->_encryption_key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
		if ( ! isset($this->_encryption_key))
			return FALSE;
		
		if (isset($_COOKIE[$this->_config['save_path']]))
		{
			// Needed by write() to detect session_regenerate_id() calls
			$this->_session_id = $session_id;
			
			// Load session data from cookie
			$session = $_COOKIE[$this->_config['save_path']];
			
			// HMAC authentication
			$len = strlen($session) - 40;
			if ($len <= 0)
			{
				log_message('error', 'Session: The session cookie was not signed.');
				return FALSE;
			}
			// Check cookie authentication
			$hmac = substr($session, $len);
			$session = substr($session, 0, $len);
			// Time-attack-safe comparison
			$hmac_check = hash_hmac('sha1', $session, $this->_encryption_key);
			$diff = 0;
			for ($i = 0; $i < 40; $i++)
			{
				$xor = ord($hmac[$i]) ^ ord($hmac_check[$i]);
				$diff |= $xor;
			}
			if ($diff !== 0)
			{
				log_message('error', 'Session: HMAC mismatch. The session cookie data did not match what was expected.');
				return FALSE;
			}
			
			// Decrypt the cookie data
			if ($this->_encrypt_data_cookie == TRUE)
			{
				$session = $this->_CI->encryption->decrypt($session);
			}
			
			$session = $this->_unserialize($session);
			
			// Does IP match?
			if ($this->_config['match_ip'] && ( ! isset($session['ip_address']) OR $session['ip_address'] !== $_SERVER['REMOTE_ADDR']))
				return FALSE;
			
			$session_data = $session['data'];
			
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return '';
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 *
	 * Writes (create / update) session data
	 *
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		if ( ! isset($this->_encryption_key))
			return FALSE;
		
		// Was the ID regenerated?
		if ($session_id !== $this->_session_id)
		{
			$this->_fingerprint = md5('');
			$this->_session_id = $session_id;
		}
		
		if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
		{
			// Serialize the userdata for the cookie
			$session = array('data' => $session_data);
			if ($this->_config['match_ip'])
			{
				$session['ip_address'] = $_SERVER['REMOTE_ADDR'];
			}
			$session = $this->_serialize($session);
			
			// Encrypt the cookie data
			if ($this->_encrypt_data_cookie == TRUE)
			{
				$session = $this->_CI->encryption->encrypt($session);
			}

			// Sign session data
			$session .= hash_hmac('sha1', $session, $this->_encryption_key);
			
			// Set the cookie
			$expiration = $this->_config['expiration'] + time();
			setcookie(
				$this->_config['save_path'],
				$session,
				$expiration,
				$this->_config['cookie_path'],
				$this->_config['cookie_domain'],
				$this->_config['cookie_secure'],
				TRUE
			);
			
			$this->_fingerprint = $fingerprint;
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Releases locks and closes file descriptor.
	 *
	 * @return	void
	 */
	public function close()
	{
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 *
	 * Destroys the current session.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		return setcookie(
			$this->_config['save_path'],
			NULL,
			1,
			$this->_config['cookie_path'],
			$this->_config['cookie_domain'],
			$this->_config['cookie_secure'],
			TRUE
		) && $this->_cookie_destroy();
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 *
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		// Not necessary, browser takes care of that.
		return TRUE;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Serialize an array
	 *
	 * This function first converts any slashes found in the array to a temporary
	 * marker, so when it gets unserialized the slashes will be preserved
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _serialize($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else
		{
			if (is_string($data))
			{
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}
		return serialize($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _unserialize($data)
	{
		$data = @unserialize(stripslashes($data));
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}
			return $data;
		}
		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}
}
