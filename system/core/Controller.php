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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the base class that connects each controller to the root object
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {
	/**
	 * Reference to the global CI instance
	 *
	 * @var	object
	 */
	protected $CI = NULL;

	/**
	 * Set up controller properties and methods
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->CI = get_instance();
		log_message('debug', 'Controller Class Initialized');
	}
		
	/**
	 * Get magic method
	 *
	 * Exposes root object members
	 * @param	string	member name
	 * @return	mixed
	 */
	public function __get($key)
	{
		if (isset($this->CI->$key))
		{
			return $this->CI->$key;
		}
	}

	/**
	 * Isset magic method
	 *
	 * Tests root object member existence
	 * @param	string	member name
	 * @return	boolean
	 */
	public function __isset($key)
	{
		return isset($this->CI->$key);
	}

	/**
	 * Get instance
	 *
	 * Returns reference to root object
	 *
	 * @return object	Root instance
	 */
	public static function &instance()
	{
		// Return root instance
		return get_instance();
	}
}

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
