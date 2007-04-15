<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {

	var $CI;
	var $now;
	var $encryption		= TRUE;
	var $use_database	= FALSE;
	var $session_table	= FALSE;
	var $sess_length	= 7200;
	var $sess_cookie	= 'ci_session';
	var $userdata		= array();
	var $gc_probability	= 5;


	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */		
	function CI_Session()
	{
		$this->CI =& get_instance();

		log_message('debug', "Session Class Initialized");
		$this->sess_run();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Run the session routines
	 *
	 * @access	public
	 * @return	void
	 */		
	function sess_run()
	{
		/*
		 *  Set the "now" time
		 *
		 * It can either set to GMT or time(). The pref
		 * is set in the config file.  If the developer
		 * is doing any sort of time localization they
		 * might want to set the session time to GMT so
		 * they can offset the "last_activity" and
		 * "last_visit" times based on each user's locale.
		 *
		 */
		if (strtolower($this->CI->config->item('time_reference')) == 'gmt')
		{
			$now = time();
			$this->now = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
	
			if (strlen($this->now) < 10)
			{
				$this->now = time();
				log_message('error', 'The session class could not set a proper GMT timestamp so the local time() value was used.');
			}
		}
		else
		{
			$this->now = time();
		}
		
		/*
		 *  Set the session length
		 *
		 * If the session expiration is set to zero in
		 * the config file we'll set the expiration
		 * two years from now.
		 *
		 */
		$expiration = $this->CI->config->item('sess_expiration');
		
		if (is_numeric($expiration))
		{
			if ($expiration > 0)
			{
				$this->sess_length = $this->CI->config->item('sess_expiration');
			}
			else
			{
				$this->sess_length = (60*60*24*365*2);
			}
		}
		
		// Do we need encryption?
		$this->encryption = $this->CI->config->item('sess_encrypt_cookie');
	
		if ($this->encryption == TRUE)	
		{
			$this->CI->load->library('encrypt');
		}		

		// Are we using a database?
		if ($this->CI->config->item('sess_use_database') === TRUE AND $this->CI->config->item('sess_table_name') != '')
		{
			$this->use_database = TRUE;
			$this->session_table = $this->CI->config->item('sess_table_name');
			$this->CI->load->database();
		}
		
		// Set the cookie name
		if ($this->CI->config->item('sess_cookie_name') != FALSE)
		{
			$this->sess_cookie = $this->CI->config->item('cookie_prefix').$this->CI->config->item('sess_cookie_name');
		}
	
		/*
		 *  Fetch the current session
		 *
		 * If a session doesn't exist we'll create
		 * a new one.  If it does, we'll update it.
		 *
		 */
		if ( ! $this->sess_read())
		{
			$this->sess_create();
		}
		else
		{	
			// We only update the session every five minutes
			if (($this->userdata['last_activity'] + 300) < $this->now)
			{
				$this->sess_update();
			}
		}
		
		// Delete expired sessions if necessary
		if ($this->use_database === TRUE)
		{		
			$this->sess_gc();
		}	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_read()
	{	
		// Fetch the cookie
		$session = $this->CI->input->cookie($this->sess_cookie);
		
		if ($session === FALSE)
		{
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}
		
		// Decrypt and unserialize the data
		if ($this->encryption == TRUE)
		{
			$session = $this->CI->encrypt->decode($session);
		}

		$session = @unserialize($this->strip_slashes($session));
		
		if ( ! is_array($session) OR ! isset($session['last_activity']))
		{
			log_message('error', 'The session cookie data did not contain a valid array. This could be a possible hacking attempt.');
			return FALSE;
		}
		
		// Is the session current?
		if (($session['last_activity'] + $this->sess_length) < $this->now)
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->CI->config->item('sess_match_ip') == TRUE AND $session['ip_address'] != $this->CI->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Does the User Agent Match?
		if ($this->CI->config->item('sess_match_useragent') == TRUE AND $session['user_agent'] != substr($this->CI->input->user_agent(), 0, 50))
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Is there a corresponding session in the DB?
		if ($this->use_database === TRUE)
		{
			$this->CI->db->where('session_id', $session['session_id']);
					
			if ($this->CI->config->item('sess_match_ip') == TRUE)
			{
				$this->CI->db->where('ip_address', $session['ip_address']);
			}

			if ($this->CI->config->item('sess_match_useragent') == TRUE)
			{
				$this->CI->db->where('user_agent', $session['user_agent']);
			}
			
			$query = $this->CI->db->get($this->session_table);

			if ($query->num_rows() == 0)
			{
				$this->sess_destroy();
				return FALSE;
			}
			else
			{
				$row = $query->row();
				if (($row->last_activity + $this->sess_length) < $this->now)
				{
					$this->CI->db->where('session_id', $session['session_id']);
					$this->CI->db->delete($this->session_table);
					$this->sess_destroy();
					return FALSE;
				}
			}
		}
	
		// Session is valid!
		$this->userdata = $session;
		unset($session);
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{								
		$cookie_data = serialize($this->userdata);
		
		if ($this->encryption == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);
		}

		setcookie(
					$this->sess_cookie,
					$cookie_data,
					$this->sess_length + time(),
					$this->CI->config->item('cookie_path'),
					$this->CI->config->item('cookie_domain'),
					0
				);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{	
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}
	
		$this->userdata = array(
							'session_id' 	=> md5(uniqid($sessid, TRUE)),
							'ip_address' 	=> $this->CI->input->ip_address(),
							'user_agent' 	=> substr($this->CI->input->user_agent(), 0, 50),
							'last_activity'	=> $this->now
							);
		
		
		// Save the session in the DB if needed
		if ($this->use_database === TRUE)
		{
			$this->CI->db->query($this->CI->db->insert_string($this->session_table, $this->userdata));
		}
			
		// Write the cookie
		$this->userdata['last_visit'] = 0;		
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{	
		if (($this->userdata['last_activity'] + $this->sess_length) < $this->now)
		{
			$this->userdata['last_visit'] = $this->userdata['last_activity'];
		}
	
		$this->userdata['last_activity'] = $this->now;
		
		// Update the session in the DB if needed
		if ($this->use_database === TRUE)
		{		
			$this->CI->db->query($this->CI->db->update_string($this->session_table, array('last_activity' => $this->now), array('session_id' => $this->userdata['session_id'])));
		}
		
		// Write the cookie
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		setcookie(
					$this->sess_cookie,
					addslashes(serialize(array())),
					($this->now - 31500000),
					$this->CI->config->item('cookie_path'),
					$this->CI->config->item('cookie_domain'),
					0
				);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database
	 * if the probability percentage is met
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_gc()
	{
		srand(time());
		if ((rand() % 100) < $this->gc_probability)
		{
			$expire = $this->now - $this->sess_length;
			
			$this->CI->db->where("last_activity < {$expire}");
			$this->CI->db->delete($this->session_table);

			log_message('debug', 'Session garbage collection performed.');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a specific item form  the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function userdata($item)
	{
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */		
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}
	
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}
	
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */		
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}
	
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}
	
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Strip slashes
	 *
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	 function strip_slashes($vals)
	 {
	 	if (is_array($vals))
	 	{	
	 		foreach ($vals as $key=>$val)
	 		{
	 			$vals[$key] = $this->strip_slashes($val);
	 		}
	 	}
	 	else
	 	{
	 		$vals = stripslashes($vals);
	 	}
	 	
	 	return $vals;
	}

}
// END Session Class
?>