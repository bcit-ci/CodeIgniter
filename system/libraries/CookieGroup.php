<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

/**
 * CodeIgniter CookieGroup Class
 *
 * This class enables creating several lists of cookies
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Anders Persson
 * @link		http://codeigniter.com/user_guide/libraries/cookie.html
 *
 * */
class CookieGroup {
	
	/**
	 * Store the cookies in this array, with $name as key
	 * or
	 * array id
	 * 
	 * @var bool
	 * */
	public $cookies = array();
	
	/**
	 * Constructor
	 * 
	 * No content
	 * 
	 * @return void
	 * */
	public function __construct(){}
	
	// --------------------------------------------------------------------
	
	/**
	 * Destructor
	 * 
	 * No content
	 * 
	 * @return void
	 * */
	public function __destruct(){}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add
	 * 
	 * Adds a cookie to the list
	 * 
	 * @return void
	 * */
	public function add($cookie)
	{
		if($cookie->get_name() == '')
		{
			$this->cookies[] = $cookie;
		}
		else
		{
			$this->cookies[$cookie->get_name()] = $cookie;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get
	 * 
	 * Returns a specific cookie from the list
	 * 
	 * @return Cookie
	 * */
	public function get($name)
	{
		return $this->cookies[$name];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete group
	 * 
	 * Calls the destructor
	 * 
	 * @return void
	 * */
	public function delete_group()
	{
		//Remove it from the db if stored also
		__destruct();
	}
	/* Suggestion on methods that could be implemented:
	 * 
	public function refresh_all()
	{
		
	}
	public function remove_all()
	{
		
	}
	
	 * Maybe it could be necessary to store all properties from
	 * the cookie objects in the database, 
	 * to etc better keep track on which end_users 
	 * have full working cookies and not.
	 * 
	 * public function store_in_db(){}
	 */
}
$CI_cookie_list = new CookieGroup();
/*
 * And then you can add cookie-management-groups
 * $blog_cookies, $cms_cookies, $addon_track_cookies
 * etcetc
 * */