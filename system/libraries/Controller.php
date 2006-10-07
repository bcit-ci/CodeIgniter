<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Code Igniter Application Controller Class
 *
 * This class object is the the super class the every library in 
 * Code Igniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/controllers.html
 */
class Controller extends CI_Base {

	var $_ci_scaffolding	= FALSE;
	var $_ci_scaff_table	= FALSE;
	
	/**
	 * Constructor
	 *
	 * Calls the initialize() function
	 */
	function Controller()
	{	
		parent::CI_Base();
		
		$this->_ci_initialize();
		
		log_message('debug', "Controller Class Initialized");
	}
  
	// --------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Assigns all the bases classes loaded by the front controller to
	 * variables in this class.  Also calls the autoload routine.
	 *
	 * @access	private
	 * @return	void
	 */  
	function _ci_initialize()
	{
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables so that CI can be 
		// run as one big super object.
		foreach (array('Config', 'Input', 'Benchmark', 'URI', 'Output') as $val)
		{
			$class = strtolower($val);
			$this->$class =& _load_class($val);
		}
		
		$this->lang	=& _load_class('Language');
	
		// In PHP 4 the Controller class is a child of CI_Loader.
		// In PHP 5 we run it as its own class.
		if (floor(phpversion()) >= 5)
		{
			$this->load = new CI_Loader();
		}

		
		// Load everything specified in the autoload.php file
		$this->load->_ci_autoloader();

		// This allows anything loaded using $this->load (viwes, files, etc.)
		// to become accessible from within the Controller class functions.
		foreach (get_object_vars($this) as $key => $var)
		{
			if (is_object($var))
			{
				$this->load->$key =& $this->$key;
			}
		}	
	}
    

}
// END _Controller class
?>