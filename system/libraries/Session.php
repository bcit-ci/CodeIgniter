<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
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
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
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
	var $flashdata_key 	= 'flash';
	var $time_to_update	= 300;

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
		 * they can offset the "last_activity" time
		 * based on each user's locale.
		 *
		 */

		if (is_numeric($this->CI->config->item('sess_time_to_update')))
		{
			$this->time_to_update = $this->CI->config->item('sess_time_to_update');
		}

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
			if (($this->userdata['last_activity'] + $this->time_to_update) < $this->now)
			{
				$this->sess_update();
			}
		}
		
		// Delete expired sessions if necessary
		if ($this->use_database === TRUE)
		{		
			$this->sess_gc();
		}

		// Delete 'old' flashdata (from last request)
       	$this->_flashdata_sweep();
        
        // Mark all new flashdata as old (data will be deleted before next request)
       	$this->_flashdata_mark();
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
		else
		{	
			// encryption was not used, so we need to check the md5 hash
			$hash = substr($session, strlen($session)-32); // get last 32 chars
			$session = substr($session, 0, strlen($session)-32);

			// Does the md5 hash match?  This is to prevent manipulation of session data
			// in userspace
			if ($hash !==  md5($session.$this->CI->config->item('encryption_key')))
			{
				log_message('error', 'The session cookie data did not match what was expected. This could be a possible hacking attempt.');
				$this->sess_destroy();
				return FALSE;
			}
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
		if ($this->CI->config->item('sess_match_useragent') == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 50)))
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
		else
		{
			// if encryption is not used, we provide an md5 hash to prevent userside tampering
			$cookie_data = $cookie_data . md5($cookie_data.$this->CI->config->item('encryption_key'));
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
		// Save the old session id so we know which record to 
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		$new_sessid = '';
		while (strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}
		$new_sessid = md5(uniqid($new_sessid, TRUE));
		
        // Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;
		
		// Update the session in the DB if needed
		if ($this->use_database === TRUE)
		{		
			$this->CI->db->query($this->CI->db->update_string($this->session_table, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));
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
	 * Fetch a specific item from the session array
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
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	mixed
	 */	
	function all_userdata()
	{
        return ( ! isset($this->userdata)) ? FALSE : $this->userdata;
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

	
    // ------------------------------------------------------------------------

    /**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
    function set_flashdata($newdata = array(), $newval = '')
    {
        if (is_string($newdata))
        {
            $newdata = array($newdata => $newval);
        }
        
        if (count($newdata) > 0)
        {
            foreach ($newdata as $key => $val)
            {
                $flashdata_key = $this->flashdata_key.':new:'.$key;
                $this->set_userdata($flashdata_key, $val);
            }
        }
    } 
	
    // ------------------------------------------------------------------------

    /**
     * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
     */
    function keep_flashdata($key)
    {
		// 'old' flashdata gets removed.  Here we mark all 
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will return FALSE if the $key 
		// provided cannot be found
        $old_flashdata_key = $this->flashdata_key.':old:'.$key;
        $value = $this->userdata($old_flashdata_key);

        $new_flashdata_key = $this->flashdata_key.':new:'.$key;
        $this->set_userdata($new_flashdata_key, $value);
    }
	
    // ------------------------------------------------------------------------

	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
    function flashdata($key)
    {
        $flashdata_key = $this->flashdata_key.':old:'.$key;
        return $this->userdata($flashdata_key);
    }

    // ------------------------------------------------------------------------

    /**
     * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
     */
    function _flashdata_mark()
    {
		$userdata = $this->all_userdata();
        foreach ($userdata as $name => $value)
        {
            $parts = explode(':new:', $name);
            if (is_array($parts) && count($parts) === 2)
            {
                $new_name = $this->flashdata_key.':old:'.$parts[1];
                $this->set_userdata($new_name, $value);
                $this->unset_userdata($name);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
     */

    function _flashdata_sweep()
    {
		$userdata = $this->all_userdata();
        foreach ($userdata as $key => $value)
        {
            if (strpos($key, ':old:'))
            {
                $this->unset_userdata($key);
            }
        }

    }
	
}
// END Session Class

/* End of file Session.php */
/* Location: ./system/libraries/Session.php */