<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the base class that connects each controller to the root object
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {
	protected $CI = NULL;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI = CodeIgniter::instance();
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
		return CodeIgniter::instance();
	}
}
// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
