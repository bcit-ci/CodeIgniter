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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Cookie Class
 *
 * This class enables the creation of cookies
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Anders Persson
 * @link		http://codeigniter.com/user_guide/libraries/cookie.html
 */

 
$laws = array(
	'EU' => array(
		'general_rule' => 'not_before_accepted', //No cookies before acception
		'regional_exceptions' => array(
			'UK' => 'remove_after_if_not_accepted', //Start cookies allowed
			'Netherland' => 'never_create_unless_accepted'
		)
	),
	'USA' => 'no_law',
	'China' => '',
	'Russia' => ''
	
	//And so on
);
 
 
class Cookie {
		
		/**
		 * You can define cookie objects to either follow the law provided in the framework
		 * or not
		 * However, it's still possible to disable/enable cookies and therefore define your
		 * own laws
		 * 
		 * @var bool
		 * */
        protected $follow_law = TRUE;
		
		/**
		 * TRUE if PHPs setcookie function works
		 * FALSE if it doesn't
		 * 
		 * @var bool
		 * */
        protected $setcookie;
		
		/**
		 * 
		 * There's two levels of options.
		 * First is the site owner.
		 * -managed by the $disabled variable
		 * Second is the end-user
		 * -managed by the $end_user_accepted variable 
		 *
		 * 1 = This cookie is always disabled, no matter laws
		 * 0 = disabled/enabled depending
		 * -1 = This cookie is always enabled, no matter laws
		 * 
		 * @var int 
		 * */
        protected $disabled = 0;
		
		/**
		 * 
		 * There's two levels of options.
		 * First is the site owner.
		 * -managed by the $disabled variable
		 * Second is the end-user
		 * -managed by the $end_user_accepted variable
		 *
		 * TRUE if the end user agreed to store cookies
		 * 
		 * @var bool
		 * */
		protected $end_user_accepted = FALSE;
		
		/**
		 * Set the region
		 * 
		 * @var String
		 * */
		protected $region = '';
		
		/**
		 * See the switch case in this->create() for details of the laws
		 * 
		 * @var String
		 * */
		protected $law = '';
       
       
        /**
		 * Cookie name
		 * 
		 * @var String
		 * */
		protected $name;
		
		/**
		 * Cookie value
		 * 
		 * @var String
		 * */
		protected $value;
		
		/**
		 * Cookie expiration
		 * 
		 * @var int
		 * */
		protected $expiration;
		
		/**
		 * Cookie domain
		 * 
		 * @var String
		 * */
		protected $domain;
		
		/**
		 * Cookie path
		 * 
		 * @var String
		 * */
		protected $path;

		/**
		 * Cookie prefix
		 * 
		 * @var String
		 * */
		protected $prefix;
        
		/**
		 * Cookie secure
		 * 
		 * @var bool
		 * */
		protected $security;
		
		/**
		 * Cookie httponly
		 * 
		 * @var bool
		 * */
		protected $httponly;
       
 
        /*
        protected $laws = array(
                'region' => ''
        );
       
        //etc ->
        protected $laws = array(
                'EU' => array(
                        'general_rule' => 'not_before_accepted' //No cookies before acception
                        'regional_exceptions' => array(
                                'UK' => 'remove_after_not_accepted', //Start cookies allowed
                                'Netherland' 'other_law'
                        )
                ),
                'USA' => '',
                'China' => '',
                'Russia' => ''
        );
       
       
        In order to comply with legal frameworks in different countries,
        All cookies should perhaps be disabled by default for some countries.
       
        $this->disable() will keep the ccokies stored but not used until
        $this->enable() is called.
       
        $this->delete() will remove the cookie and remove the cookie object
       
        All the law values for each law-region can be loaded as an array with 'region' as key
        and 'law' as value; See above for example.
       
        The point of this is to make a flexible cookie where
        1. Some cookies can follow the law, others not.
        2. It should be as easy as possible to modify the class when a region change it laws, it should also
        3. Be possible to modify the behaviour in the objects itself.
        */

	public function __construct($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
	{
		if($name != '')
		{
			$this->name = $name;
		}
		else 
		{
			/*
			 * Check all setted cookies and then create a name
			 * Ex CI_no_name_6
			 * */
		}

		if ($prefix == '' && config_item('cookie_prefix') !== '')
		{
			$this->prefix = config_item('cookie_prefix');
		}


		if ($domain == '' && config_item('cookie_domain') != '')
		{
			$this->domain = config_item('cookie_domain');
		}
 
		if ($path == '/' && config_item('cookie_path') !== '/')
		{
			$this->path = config_item('cookie_path');
		}
		
		if ($secure === FALSE && config_item('cookie_secure') !== FALSE)
		{
			$this->secure = config_item('cookie_secure');
		}

		if ($httponly === FALSE && config_item('cookie_httponly') !== FALSE)
		{
			$this->httponly = config_item('cookie_httponly');
		}

		if ( ! is_numeric($expire))
		{
			$this->expire = time() - 86500; // a week
		}
		else
		{
			$this->expire = ($expire > 0) ? time() + $expire : 0;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Class destructor
	 *
	 * Do not delete the cookie here, since this allways called in the end. This is
	 * however called by the $this-delete() method
	 * Instead a copy of the cookie could be stored in the database, so the same object
	 * can be recreated without hassle
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		// Store the cookie somewhere
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the cookie
	 *
	 * @param	int expire
	 * @return	bool
	 */
	protected function set_cookie($expire=NULL)
	{
		$expire = ($expire===NULL) ? $this->expire : $expire;
		
		if(!setcookie($this->prefix.$this->name,
			$this->value,
				$expire,
				$this->path,
				$this->domain,
				$this->secure,
				$this->httponly
		))
		{
			$this->setcookie=FALSE;
			log_message('error', 'Cookies cannot be set by Cookie::set_cookie()');
			return FALSE;
		}
		else
			return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the local law
	 *
	 * @param	array laws
	 * @param	String region
	 * @return	bool
	 */
	protected function register_law($laws, $region)
	{
		foreach($laws as $el => $key)
		{
			if($key == $region)
			{
				return $el[$key];
			}
			elseif(is_array($el[$key]))
			{
				$this->register_law($el[$key], $region);
			}
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Change end user agreement
	 *
	 * 1 = End user agreed to store cookies(this cookie)
	 * 0 = End user havn't made any response yet
	 * -1 = End user disagreed to store cookies(this cookie)
	 * 
	 * @param	int level of agreement
	 * @return	bool
	 */
	public function set_accept($b)
	{
		if(is_numeric($b) && ($b==-1 OR $b==0 OR $b==1))
		{
			$this->end_user_accepted = $b;
			return TRUE;
		}
		
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get level of agreement
	 *
	 * @return	int
	 */
	public function get_accept()
	{
		return $this->end_user_accepted;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set laws to follow
	 * the global $laws etc
	 * 
	 * @param	array
	 * @return	int
	 */
	public function set_law($laws)
	{
		if($this->law = $this->register_law($laws, $this->region))
		{
			return TRUE;
		}
		else
		{
			log_message('error', 'Laws could not be set. Consider to edit your laws-array.');
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get the current used law 
	 * the global $laws etc
	 *
	 * @return	String
	 */

	public function get_law()
	{
		return $this->law;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Create with the cookie with the $this->set_cookie method
	 * IF it's good to go
	 *
	 * @return	bool
	 */
	public function create()
	{
		if($this->disabled === FALSE)
		{
			if($this->follow_law === TRUE)
			{
				switch($this->law)
				{
					case 'no_law' : 
					case 'remove_after_if_not_accepted' :
						//$this->set_cookie();
						break;
					case 'never_create_unless_accepted' :
						if($this->end_user_accepted === TRUE)
						{
							//$this->set_cookie();
							break;
						}
						else
						{
							log_message('error', 'The law is followed and making this cookie is illegal');
							return FALSE;
							break;
						}
					default : 
						//$this->set_cookie();
						break;
				}
				$this->set_cookie();
				return TRUE;
			}
		}

		if($this->disabled === TRUE)
		{
			log_message('error', 'Cannot set a new cookie, since disabled is TRUE');
		}
		else
		{
			log_message('error', 'Unknown error in "Cookie::create();"');	
		}

		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Refresh the cookie
	 *
	 * @return	bool
	 */
	public function refresh()
	{
		if($this->delete() === TRUE)
		{
			if($this->set_cookie() === TRUE)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Delete this cookie object!
	 *
	 * @return	(FALSE)bool
	 */

	public function delete()
	{
		if($this->set_cookie(time()-3600))
		{
			$this->__destruct();
		}
		else
		{
			log_message('error', 'Could not delete the ' .$this->name. ' cookie');
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Delete this cookie object!
	 *
	 * @param Local law-region(that defines the location of the site)
	 * @return	(FALSE)bool
	 */
	public function set_region($region)
	{
		if(is_string($region))
		{
			$this->region = $region;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the curren law-region
	 *
	 * @return	String	region
	 */
	public function get_region()
	{
		return $this->region;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set a disabled value (site-managers own decision)
	 *
	 * 1 = total disabled
	 * 0 = depends on laws
	 * -1 = total enabled, overrides laws
	 * 
	 * @return	int	property
	 */
	public function set_disabled($site_owner_enable)
	{
		if(is_numeric($b) && ($b==-1 OR $b==0 OR $b==1))
		{
			if($this->disabled != $site_owner_enable)
			{
				$this->disabled = $site_owner_enable;
				if($this->refresh() === FALSE)
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		
		return FALSE;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Set a name for the cookie
	 * 
	 * @param	String	name
	 * @return	bool
	 */
	public function set_name($name)
	{
		if(is_string($name))
		{
			$this->name = $name;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set a value for the cookie
	 * 
	 * @param	String	value
	 * @return	bool
	 */
	public function set_value($value)
	{
		if(is_string($value))
		{
			$this->value = $value;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set an expiration date for the cookie
	 * 
	 * @param	String	value
	 * @return	bool
	 */
	public function set_expiration($expire)
	{
		if(is_numeric($expire))
		{
			$this->expire = $expire;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set domain for the cookie
	 * 
	 * @param	String	domain
	 * @return	bool
	 */
	public function set_domain($domain)
	{
		if(is_string($domain))
		{
			$this->domain = $domain;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set domain for the cookie
	 * 
	 * @param	String	path
	 * @return	bool
	 */
	public function set_path($path)
	{
		if(is_string($path))
		{
			$this->path = $path;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set prefix for the cookie
	 * 
	 * @param	String	prefix
	 * @return	bool
	 */
	public function set_prefix($prefix)
	{
		if(is_string($prefix))
		{
			$this->prefix = $prefix;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set security bool for the cookie
	 * 
	 * @param	String	security
	 * @return	bool
	 */
	public function set_security($security)
	{
		if(is_string($security))
		{
			$this->security = $security;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set httponly bool for the cookie
	 * 
	 * @param	String	httponly
	 * @return	bool
	 */
	public function set_httponly($httponly)
	{
		if(is_string($httponly))
		{
			$this->httponly = $httponly;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get info about wether the law is followed or not
	 * 
	 * @return	bool follow
	 */
	public function get_follow_law()
	{
		return $this->follow_law;
	}

	// --------------------------------------------------------------------

	/**
	 * Get info about wether the cookie has been set or not
	 * 
	 * @return	bool setcookie
	 */
	public function get_success()
	{
		return $this->setcookie;
	}

	// --------------------------------------------------------------------
	

	/**
	 * Get info about wether the cookie has been disabled by
	 * the site_owner
	 * 
	 * 1 = total disabled
	 * 0 = depends on laws
	 * -1 = total enabled, overrides laws
	 * 
	 * return int disabled
	 */
	public function get_disabled()
	{
		return $this->disabled;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get name
	 * 
	 * return String name
	 */
	public function get_name()
	{
		return $this->name;
	}

	// --------------------------------------------------------------------

	/**
	 * Get value
	 * 
	 * return String value
	 */
	public function get_value()
	{
		return $this->value;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get expiration time in seconds
	 * 
	 * return int expiration
	 */
	public function get_expiration()
	{
		return $this->expiration;
	}

	// --------------------------------------------------------------------

	/**
	 * Get domain
	 * 
	 * return String domain
	 */
	public function get_domain()
	{
		return $this->domain;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get path
	 * 
	 * return String path
	 */
	public function get_path()
	{
		return $this->path;
	}

	// --------------------------------------------------------------------

	/**
	 * Get prefix
	 * 
	 * return String prefix
	 */
	public function get_prefix()
	{
		return $this->prefix;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get security
	 * 
	 * return security bool
	 */
	public function get_security()
	{
		return $this->security;
	}

	// --------------------------------------------------------------------

	/**
	 * Get httponly
	 * 
	 * return bool httponly
	 */
	public function get_httponly()
	{
		return $this->httponly;
	}
}