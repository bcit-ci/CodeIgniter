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
 
 
public class Cookie {
        	
		/**
		 * Log all errors so it can be displayed for debug purposes
		 * 
		 * @var array
		 * */
		private $log = array();
		
		/**
		 * You can define cookie objects to either follow the law provided in the framework
		 * or not
		 * However, it's still possible to disable/enable cookies and therefore define your
		 * own laws
		 * 
		 * @var bool
		 * */
        private $follow_law = true;
		
		/**
		 * True if PHPs setcookie function works
		 * False if it doesn't
		 * 
		 * @var bool
		 * */
        private $success;
		
		/**
		 * 1 = This cookie is allways disabled, no matter laws
		 * 0 = disabled/enabled depending
		 * -1 = This cookie is allways disabled, no matter laws
		 * 
		 * @var number 
		 * */
        private $disabled = 0;
		
		/**
		 * true if the end user agreed to store cookies
		 * 
		 * @var bool
		 * */
		private $end_user_accepted = false;
		
		/**
		 * Set the region
		 * 
		 * @var String
		 * */
		private $region = "";
		
		/**
		 * See the switch case in this->create() for details of the laws
		 * 
		 * @var String
		 * */
		private $law = "";
       
       
        /**
		 * Cookie name
		 * 
		 * @var String
		 * */
		private $name;
		
		/**
		 * Cookie value
		 * 
		 * @var String
		 * */
		private $value;
		
		/**
		 * Cookie expiration
		 * 
		 * @var 
		 * */
		private $expiration;
		
		/**
		 * Cookie domain
		 * 
		 * @var 
		 * */
		private $domain;
		
		/**
		 * Cookie path
		 * 
		 * @var 
		 * */
		private $path;

		/**
		 * Cookie prefix
		 * 
		 * @var 
		 * */
		private $prefix;
        
		/**
		 * Cookie secure
		 * 
		 * @var 
		 * */
		private $security;
		
		/**
		 * Cookie httponly
		 * 
		 * @var 
		 * */
		private $httponly;
       
 
        /*
        private $laws = array(
                'region' => ''
        );
       
        //etc ->
        private $laws = array(
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
 

 
        private function set_cookie($expire = this->expire)
        {
                if(!setcookie($this->prefix.$this->name,
                        $this->value,
                        $expire,
                        $this->path,
                        $this->domain,
                        $this->secure,
                        $this->httponly
                ))
				{
					$this->success=false;
					$this->log[] = 'Cookies cannot be set by Cookie::set_cookie()';
				}
        }
		
		
		private function register_law($laws, $region)
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
			return false;
		}
 
 
        /*
        *       Public
        */
        public function set_accept($b)
        {
        	$this->end_user_accepted = $b;
        }


		public function get_accept()
		{
			return $this->end_user_accepted;
		}
        
        
        public function set_law()
        {
        	global $laws;
			$this->law = $this->register_law($laws, $this->region);
        }


		public function get_law()
		{
			return $this->law;
		}
        
        
        public function create()
        {
        	if($this->disabled === false)
			{
				elseif($this->disabled === false && $this->follow_law === true)
				{
					switch($this->law)
					{
						case 'no_law' : 
						case 'remove_after_if_not_accepted' :
							//$this->set_cookie();
							break;
						case 'never_create_unless_accepted' :
							if(this->end_user_accepted === true)
							{
								//$this->set_cookie();
								break;
							}
							else
							{
								$this->log[] = "The law is followed and making this cookie is illegal";
								return false;
								break;
							}
						default : 
							//$this->set_cookie();
							break;
					}
					$this->set_cookie();
					return true;
				}
			}
			if($this->disabled === true)
			{
				$this->log[] = "Cannot set a new cookie, since disabled is true";
			}
			else
			{
				$this->log[] = "Unknown error in 'Cookie::create();'";	
			}
			
			return false;
        }
        
        
        public function refresh()
        {
        	$this->delete();
			$this->set_cookie();
        }
        
        
        public function disable()
        {
			$this->disable == true; //
        }
       
       
        public function enable()
        {
			$this->disable == false; //
        }
		
		
        public function delete()
        {
                $this->set_cookie(time()-3600);
                $this->__destruct();
        }
		
		
		public function set_region($region)
		{
			$this->region = $region;
		}
		
		
		public function get_region()
		{
			return $this->region;
		}
		
		
		public function display_log()
		{
			return var_dump($this->log);
		}
		
		
        public function set_name($name)
        {
			$this->name = $name;
        }
       
       
        public function set_value($value)
        {
                $this->value = $value;
        }
       
       
        public function set_expiration($expire)
        {
                $this->expire = $expire;
        }
       
       
        public function set_domain($domain)
        {
                $this->domain = $domain;
        }
       
       
        public function set_path($path)
        {
                $this->path = $path;
        }
       
       
        public function set_prefix($prefix)
        {
                $this->prefix = $prefix;
        }
       
       
        public function set_security($security)
        {
                $this->security = $security;
        }
       
       
        public function set_httponly($httponly)
        {
                $this->httponly = $httponly;
        }
       
       
        public get_follow_law()
        {
                return $this->follow_law;
        }
 
 
        public get_success()
        {
                return $this->success;
        }
 
 
        public get_disabled()
        {
                return $this->disabled;
        }
 
 
        public function get_name()
        {
                return $this->name;
        }
 
 
        public function get_value()
        {
                return $this->value;
        }
 
 
        public function get_expiration()
        {
                return $this->expiration;
        }
 
 
        public function get_domain()
        {
                return $this->domain;
        }
 
 
        public function get_path()
        {
                return $this->path;
        }
 
 
        public function get_prefix()
        {
                return $this->prefix;
        }
 
 
        public function get_security()
        {
                return $this->security;
        }
 
 
        public function get_httponly()
        {
                return $this->httponly;
        }
 
 
        public function __construct($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
        {
                if (is_array($name))
                {
                        // always leave 'name' in last place, as the loop will break otherwise, due to $$item
                        foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name') as $item)
                        {
                                if (isset($name[$item]))
                                {
                                        $$item = $name[$item];
                                }
                        }
                }
 
                if ($prefix === '' && config_item('cookie_prefix') !== '')
                {
                        $this->prefix = config_item('cookie_prefix');
                }
 
                if ($domain == '' && config_item('cookie_domain') != '')
                {
                        $this->domain = config_item('cookie_domain');
                }
 
                if ($path === '/' && config_item('cookie_path') !== '/')
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

 				if($name !== '')
					$this->setcookie($prefix.$name, $value, $expire, $path, $domain, $secure, $httponly);
        }
 
 
        public function __destruct()
        {
                // is called by the $this-delete() method
        }
}