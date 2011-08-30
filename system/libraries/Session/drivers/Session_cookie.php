<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource
 */


/**
 * Cookie-based session management driver
 *
 * This is the CI_Session functionality, as written by EllisLab, abstracted out to a driver.
 * I have done a little updating for PHP5, and made minor changes to extract this functionality from
 * the public interface (now in the Session Library), but effectively this code is unchanged.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team and Darren Hill (DChill42)
 */
class Session_Cookie extends SessionDriver {
	private $sess_encrypt_cookie	= FALSE;
	private $sess_use_database		= FALSE;
	private $sess_table_name		= '';
	private $sess_expiration		= 7200;
	private $sess_expire_on_close	= FALSE;
	private $sess_match_ip			= FALSE;
	private $sess_match_useragent	= TRUE;
	private $sess_cookie_name		= 'ci_session';
	private $cookie_prefix			= '';
	private $cookie_path			= '';
	private $cookie_domain			= '';
	private $sess_time_to_update	= 300;
	private $encryption_key		 	= '';
	private $time_reference		 	= 'time';
	private $userdata				= array();
	private $CI					 	= null;
	private $now					= 0;

	const gc_probability			= 5;

	/**
	 * Initialize session driver object
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function initialize()
	{
		// Set the super object to a local variable for use throughout the class
		$this->CI =& get_instance();

		// Set all the session preferences, which can either be set
		// manually via the $params array above or via the config file
		foreach (array('sess_encrypt_cookie', 'sess_use_database', 'sess_table_name', 'sess_expiration',
		'sess_expire_on_close', 'sess_match_ip', 'sess_match_useragent', 'sess_cookie_name', 'cookie_path',
		'cookie_domain', 'sess_time_to_update', 'time_reference', 'cookie_prefix', 'encryption_key') as $key)
		{
			$this->$key = (isset($this->parent->params[$key])) ? $this->parent->params[$key] : $this->CI->config->item($key);
		}

		if ($this->encryption_key == '')
		{
			show_error('In order to use the Cookie Session driver you are required to set an encryption key '.
				'in your config file.');
		}

		// Load the string helper so we can use the strip_slashes() function
		$this->CI->load->helper('string');

		// Do we need encryption? If so, load the encryption class
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$this->CI->load->library('encrypt');
		}

		// Are we using a database? If so, load it
		if ($this->sess_use_database === TRUE && $this->sess_table_name != '')
		{
			$this->CI->load->database();
		}

		// Set the "now" time. Can either be GMT or server time, based on the config prefs.
		// We use this to set the "last activity" time
		$this->now = $this->_get_time();

		// Set the session length. If the session expiration is
		// set to zero we'll set the expiration two years from now.
		if ($this->sess_expiration == 0)
		{
			$this->sess_expiration = (60*60*24*365*2);
		}

		// Set the cookie name
		$this->sess_cookie_name = $this->cookie_prefix.$this->sess_cookie_name;

		// Run the Session routine. If a session doesn't exist we'll
		// create a new one. If it does, we'll update it.
		if ( ! $this->_sess_read())
		{
			$this->_sess_create();
		}
		else
		{
			$this->_sess_update();
		}

		// Delete expired sessions if necessary
		$this->_sess_gc();
	}

	/**
	 * Write the session data
	 *
	 * @return	void
	 */
	public function sess_save()
	{
		// Are we saving custom data to the DB? If not, all we do is update the cookie
		if ($this->sess_use_database === FALSE)
		{
			$this->_set_cookie();
			return;
		}

		// set the custom userdata, the session data we will set in a second
		$custom_userdata = $this->all_userdata();
		$cookie_userdata = array();

		// Before continuing, we need to determine if there is any custom data to deal with.
		// Let's determine this by removing the default indexes to see if there's anything left in the array
		// and set the session data while we're at it
		foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
		{
			unset($custom_userdata[$val]);
			$cookie_userdata[$val] = $this->userdata($val);
		}

		// Did we find any custom data? If not, we turn the empty array into a string
		// since there's no reason to serialize and store an empty array in the DB
		if (count($custom_userdata) === 0)
		{
			$custom_userdata = '';
		}
		else
		{
			// Serialize the custom data array so we can store it
			$custom_userdata = $this->_serialize($custom_userdata);
		}

		// Run the update query
		$this->CI->db->where('session_id', $this->userdata('session_id'));
		$this->CI->db->update($this->sess_table_name,
			array('last_activity' => $this->userdata('last_activity'), 'user_data' => $custom_userdata));

		// Write the cookie. Notice that we manually pass the cookie data array to the
		// _set_cookie() function. Normally that function will store $this->userdata, but
		// in this case that array contains custom data, which we do not want in the cookie.
		$this->_set_cookie($cookie_userdata);
	}

	/**
	 * Destroy the current session
	 *
	 * @return	void
	 */
	public function sess_destroy()
	{
		// Kill the session DB row
		if ($this->sess_use_database === TRUE && $this->has_userdata('session_id'))
		{
			$this->CI->db->where('session_id', $this->userdata['session_id']);
			$this->CI->db->delete($this->sess_table_name);
		}

		// Kill the cookie
		setcookie($this->sess_cookie_name, addslashes(serialize(array())), ($this->now - 31500000),
			$this->cookie_path, $this->cookie_domain, 0);
	}

	/**
	 * Regenerate the current session
	 *
	 * Regenerate the session id
	 *
	 * @param	boolean	Destroy session data flag (default: false)
	 * @return	void
	 */
	public function sess_regenerate($destroy = false)
	{
		// Check destroy flag
		if ($destroy)
		{
			// Destroy old session and create new one
			$this->sess_destroy();
			$this->_sess_create();
		}
		else
		{
			// Just force an update to recreate the id
			$this->_sess_update(true);
		}
	}

	/**
	 * Get a reference to user data array
	 *
	 * @return	array - Reference to userdata
	 */
	public function &get_userdata()
	{
		// Return reference to array
		return $this->userdata;
	}

	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _sess_read()
	{
		// Fetch the cookie
		$session = $this->CI->input->cookie($this->sess_cookie_name);

		// No cookie? Goodbye cruel world!...
		if ($session === FALSE)
		{
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}

		// Decrypt the cookie data
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$session = $this->CI->encrypt->decode($session);
		}
		else
		{
			// encryption was not used, so we need to check the md5 hash
			$hash	= substr($session, strlen($session)-32); // get last 32 chars
			$session = substr($session, 0, strlen($session)-32);

			// Does the md5 hash match? This is to prevent manipulation of session data in userspace
			if ($hash !== md5($session.$this->encryption_key))
			{
				log_message('error', 'The session cookie data did not match what was expected. '.
					'This could be a possible hacking attempt.');
				$this->sess_destroy();
				return FALSE;
			}
		}

		// Unserialize the session array
		$session = $this->_unserialize($session);

		// Is the session data we unserialized an array with the correct format?
		if ( ! is_array($session) || ! isset($session['session_id']) || ! isset($session['ip_address']) ||
		! isset($session['user_agent']) || ! isset($session['last_activity']))
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Is the session current?
		if (($session['last_activity'] + $this->sess_expiration) < $this->now())
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->sess_match_ip == TRUE && $session['ip_address'] != $this->CI->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE &&
		trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 50)))
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Is there a corresponding session in the DB?
		if ($this->sess_use_database === TRUE)
		{
			$this->CI->db->where('session_id', $session['session_id']);

			if ($this->sess_match_ip == TRUE)
			{
				$this->CI->db->where('ip_address', $session['ip_address']);
			}

			if ($this->sess_match_useragent == TRUE)
			{
				$this->CI->db->where('user_agent', $session['user_agent']);
			}

			$query = $this->CI->db->get($this->sess_table_name);

			// No result? Kill it!
			if ($query->num_rows() == 0)
			{
				$this->sess_destroy();
				return FALSE;
			}

			// Is there custom data? If so, add it to the main session array
			$row = $query->row();
			if (isset($row->user_data) && $row->user_data != '')
			{
				$custom_data = $this->_unserialize($row->user_data);

				if (is_array($custom_data))
				{
					foreach ($custom_data as $key => $val)
					{
						$session[$key] = $val;
					}
				}
			}
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return TRUE;
	}

	/**
	 * Create a new session
	 *
	 * @access	private
	 * @return	void
	 */
	private function _sess_create()
	{
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$sessid .= $this->CI->input->ip_address();

		$this->set_userdata('session_id', md5(uniqid($sessid, TRUE)));
		$this->set_userdata('ip_address', $this->CI->input->ip_address());
		$this->set_userdata('user_agent', substr($this->CI->input->user_agent(), 0, 50));
		$this->set_userdata('last_activity',$this->now());


		// Save the data to the DB if needed
		if ($this->sess_use_database === TRUE)
		{
			$this->CI->db->query($this->CI->db->insert_string($this->sess_table_name, $this->all_userdata()));
		}

		// Write the cookie
		$this->_set_cookie();
	}

	/**
	 * Update an existing session
	 *
	 * @access	private
	 * @param	boolean	Force update flag (default: false)
	 * @return	void
	 */
	private function _sess_update($force = false)
	{
		// We only update the session every five minutes by default (unless forced)
		if (!$force && ($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now())
		{
			return;
		}

		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		$new_sessid = '';
		while (strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$new_sessid .= $this->CI->input->ip_address();

		// Turn it into a hash
		$new_sessid = md5(uniqid($new_sessid, TRUE));

		// Update the session data in the session data array
		$this->set_userdata('session_id', $new_sessid);
		$this->set_userdata('last_activity', $this->now());

		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.
		$cookie_data = NULL;

		// Update the session ID and last_activity field in the DB if needed
		if ($this->sess_use_database === TRUE)
		{
			// set cookie explicitly to only have our session data
			$cookie_data = array();
			foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
			{
				$cookie_data[$val] = $this->userdata[$val];
			}

			$this->CI->db->query($this->CI->db->update_string($this->sess_table_name,
				array('last_activity' => $this->now(), 'session_id' => $new_sessid),
				array('session_id' => $old_sessid)));
		}

		// Write the cookie
		$this->_set_cookie($cookie_data);
	}

	/**
	 * Get the "now" time
	 *
	 * @access	private
	 * @return	int
	 */
	private function _get_time()
	{
		if (strtolower($this->time_reference) == 'gmt')
		{
			$now = time();
			$time = mktime(gmdate('H', $now), gmdate('i', $now), gmdate('s', $now), gmdate('m', $now),
				gmdate('d', $now), gmdate('Y', $now));
		}
		else
		{
			$time = time();
		}

		return $time;
	}

	/**
	 * Write the session cookie
	 *
	 * @access	private
	 * @param	array	Cookie name/value pairs
	 * @return	void
	 */
	private function _set_cookie(array $cookie_data = NULL)
	{
		if (is_null($cookie_data))
		{
			$cookie_data = $this->all_userdata();
		}

		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);

		if ($this->sess_encrypt_cookie == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);
		}
		else
		{
			// if encryption is not used, we provide an md5 hash to prevent userside tampering
			$cookie_data = $cookie_data.md5($cookie_data.$this->encryption_key);
		}

		$expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

		// Set the cookie
		setcookie($this->sess_cookie_name, $cookie_data, $expire, $this->cookie_path, $this->cookie_domain, 0);
	}

	/**
	 * Serialize an array
	 *
	 * This function first converts any slashes found in the array to a temporary
	 * marker, so when it gets unserialized the slashes will be preserved
	 *
	 * @access	private
	 * @param	mixed	Data to serialize
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

	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	string	Data to unserialize
	 * @return	mixed
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

	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database
	 * if the probability percentage is met
	 *
	 * @access	private
	 * @return	void
	 */
	private function _sess_gc()
	{
		if ($this->sess_use_database != TRUE)
		{
			return;
		}

		srand(time());
		if ((rand() % 100) < self::gc_probability)
		{
			$expire = $this->now() - $this->sess_expiration;

			$this->CI->db->where('last_activity < '.$expire);
			$this->CI->db->delete($this->sess_table_name);

			log_message('debug', 'Session garbage collection performed.');
		}
	}
}
// END Session_Cookie Class

/* End of file Session_cookie.php */
/* Location: ./system/libraries/Session/Session.php */
?>
