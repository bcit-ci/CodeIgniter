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

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the base class that connects each controller to the core object
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {
	/**
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI;

	/**
	 * Constructor
	 *
	 * @param	object	parent reference
	 */
	public function __construct(CodeIgniter $CI)
	{
		// Attach parent reference
		$this->CI =& $CI;
		$CI->log_message('debug', get_class($this).' Controller Class Initialized');
	}

	/**
	 * Show 404
	 *
	 * This function exits Controller operation and informs the user that the
	 * requested page could not be found. It is mainly for use inside _remap().
	 *
	 * @param	string	requested page
	 * @param	boolean	FALSE to skip logging
	 * @return	void
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		// Just throw the exception - CodeIgniter will catch it
		$log_msg = ($page && $log_error) ? '404 Page Not Found --> '.$page : '';
		throw new CI_ShowError('The page you requested was not found.', '404 Page Not Found', 404, $log_msg,
			'error_404');
	}

	/**
	 * End operation
	 *
	 * This function exits Controller operation and goes directly to
	 * the final hooks and output display in CodeIgniter.
	 *
	 * @return void
	 */
	public function end_run()
	{
		// Just throw the exception - CodeIgniter will catch it
		throw new CI_EndRun();
	}

	/**
	 * Get magic method
	 *
	 * Exposes core object members
	 *
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
	 * Tests core object member existence
	 *
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
	 * Returns reference to core object
	 *
	 * @return object	Core instance
	 */
	public static function &instance()
	{
		// Return core instance
		return CodeIgniter::instance();
	}
}
// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
