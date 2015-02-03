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
	
	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);
		
		// Sanitize session_data cookie name
		if (isset($this->_config['save_path']))
		{
			$this->_config['save_path'] = preg_replace("/[^a-zA-Z0-9\_\-\.]+/", "", $this->_config['save_path']);
		}
		else
		{
			$this->_config['save_path'] = 'ci_session_data';
		}
		
		$this->_encryption_key = $this->_config['encryption_key'];
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
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID, unused
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
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
									
			$session_data = $this->_unserialize($session);
			
			// Does IP match?
			if ($this->_config['match_ip'] && ( ! isset($session_data['ip_address']) OR $session_data['ip_address'] !== $_SERVER['REMOTE_ADDR']))
				return FALSE;
			
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return FALSE;
		
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
		$hmac_check = hash_hmac('sha1', $session, $this->encryption_key);
		$diff = 0;
		for ($i = 0; $i < 40; $i++)
		{
			$xor = ord($hmac[$i]) ^ ord($hmac_check[$i]);
			$diff |= $xor;
		}
		if ($diff !== 0)
		{
			log_message('error', 'Session: HMAC mismatch. The session cookie data did not match what was expected.');
			$this->sess_destroy();
			return FALSE;
		}
		// Decrypt the cookie data
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$session = $this->CI->encrypt->decode($session);
		}
		// Unserialize the session array
		$session = $this->_unserialize($session);
		// Is the session data we unserialized an array with the correct format?
		if ( ! is_array($session) OR ! isset($session['session_id']) OR ! isset($session['ip_address']) OR ! isset($session['user_agent']) OR ! isset($session['last_activity']))
		{
			$this->sess_destroy();
			return FALSE;
		}
		// Is the session current?
		if (($session['last_activity'] + $this->sess_expiration) < $this->now)
		{
			$this->sess_destroy();
			return FALSE;
		}
		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}
		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 120)))
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Session is valid!
		$this->userdata = $session;
		unset($session);
		return TRUE;
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
		// If the two IDs don't match, we have a session_regenerate_id() call
		// and we need to close the old handle and open a new one
		if ($session_id !== $this->_session_id && ( ! $this->close() OR $this->read($session_id) === FALSE))
		{
			return FALSE;
		}

		if ( ! is_resource($this->_file_handle))
		{
			return FALSE;
		}
		elseif ($this->_fingerprint === md5($session_data))
		{
			return ($this->_file_new)
				? TRUE
				: touch($this->_file_path.$session_id);
		}

		if ( ! $this->_file_new)
		{
			ftruncate($this->_file_handle, 0);
			rewind($this->_file_handle);
		}

		if (($length = strlen($session_data)) > 0)
		{
			for ($written = 0; $written < $length; $written += $result)
			{
				if (($result = fwrite($this->_file_handle, substr($session_data, $written))) === FALSE)
				{
					break;
				}
			}

			if ( ! is_int($result))
			{
				$this->_fingerprint = md5(substr($session_data, 0, $written));
				log_message('error', 'Session: Unable to write data.');
				return FALSE;
			}
		}

		$this->_fingerprint = md5($session_data);
		return TRUE;
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
		if (is_resource($this->_file_handle))
		{
			flock($this->_file_handle, LOCK_UN);
			fclose($this->_file_handle);

			$this->_file_handle = $this->_file_new = $this->_session_id = NULL;
			return TRUE;
		}

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
		if ($this->close())
		{
			return unlink($this->_file_path.$session_id) && $this->_cookie_destroy();
		}
		elseif ($this->_file_path !== NULL)
		{
			clearstatcache();
			return file_exists($this->_file_path.$session_id)
				? (unlink($this->_file_path.$session_id) && $this->_cookie_destroy())
				: TRUE;
		}

		return FALSE;
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
		if ( ! is_dir($this->_config['save_path']) OR ($files = scandir($this->_config['save_path'])) === FALSE)
		{
			log_message('debug', "Session: Garbage collector couldn't list files under directory '".$this->_config['save_path']."'.");
			return FALSE;
		}

		$ts = time() - $maxlifetime;

		foreach ($files as $file)
		{
			// If the filename doesn't match this pattern, it's either not a session file or is not ours
			if ( ! preg_match('/(?:[0-9a-f]{32})?[0-9a-f]{40}$/i', $file)
				OR ! is_file($this->_config['save_path'].DIRECTORY_SEPARATOR.$file)
				OR ($mtime = filemtime($this->_config['save_path'].DIRECTORY_SEPARATOR.$file)) === FALSE
				OR $mtime > $ts)
			{
				continue;
			}

			unlink($this->_config['save_path'].DIRECTORY_SEPARATOR.$file);
		}

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
		$data = @unserialize(strip_slashes($data));
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
