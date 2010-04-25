<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class Controller extends CI_Base {
	
	/**
	 * Constructor
	 *
	 * Calls the initialize() function
	 */
	function Controller()
	{	
		parent::CI_Base();

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables 
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		// In PHP 5 the Loader class is run as a discreet
		// class.  In PHP 4 it extends the Controller @PHP4
	 	if (is_php('5.0.0') == TRUE)
		{
			$this->load =& load_class('Loader', 'core');
			
			$this->load->_base_classes =& is_loaded();
			
			$this->load->_ci_autoloader();
		}
		else
		{
			$this->_ci_autoloader();
			
			// sync up the objects since PHP4 was working from a copy
			foreach (array_keys(get_object_vars($this)) as $attribute)
			{
				if (is_object($this->$attribute))
				{
					$this->load->$attribute =& $this->$attribute;
				}
			}
		}

		log_message('debug', "Controller Class Initialized");
		
	}

}
// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */