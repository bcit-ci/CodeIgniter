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
 * @since		Version 1.3
 * @filesource
 */
 
 
// ------------------------------------------------------------------------

/**
 * CI_BASE - For PHP 5
 * 
 * This file contains some code used only when Code Igniter is being
 * run under PHP 5.  It allows us to manage the CI super object more
 * gracefully than what is possible with PHP 4.
 * 
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	front-controller
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/
 */

class CI_Base {

	public function CI_Base()
	{
		$instance =& _load_class('Instance');
		$instance->set_instance($this);
	}
}

class Instance {
	public static $instance;

	public function set_instance(&$object)
	{
		self::$instance =& $object;
	}
	
	public function &get_instance()
	{
		return self::$instance;
	}
}

function &get_instance()
{
	$instance =& _load_class('Instance');
	return $instance->get_instance();	
}

?>