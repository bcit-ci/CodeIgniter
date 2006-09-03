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
 * System Front Controller
 *
 * Loads the base classes and executes the request. 
 * 
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Front-controller
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/
 */
 
define('APPVER', '1.4.0');

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require(BASEPATH.'codeigniter/Common'.EXT);

/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log errors
 * ------------------------------------------------------
 */
set_error_handler('_exception_handler');
set_magic_quotes_runtime(0); // Kill magic quotes

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */

$BM =& _load_class('CI_Benchmark');
$BM->mark('code_igniter_start');

/*
 * ------------------------------------------------------
 *  Instantiate the hooks classe
 * ------------------------------------------------------
 */

$EXT =& _load_class('CI_Hooks');

/*
 * ------------------------------------------------------
 *  Is there a "pre_system" hook?
 * ------------------------------------------------------
 */
if ($EXT->_hook_exists('pre_system'))
{
	$EXT->_call_hook('pre_system');
}

/*
 * ------------------------------------------------------
 *  Instantiate the base classes
 * ------------------------------------------------------
 */

$CFG =& _load_class('CI_Config');
$RTR =& _load_class('CI_Router');
$OUT =& _load_class('CI_Output');

/*
 * ------------------------------------------------------
 *	Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */
 
if ($EXT->_hook_exists('cache_override'))
{
	$EXT->_call_hook('cache_override');
}
else
{
	if ($OUT->_display_cache() == TRUE)
	{
		exit;
	}
}

/*
 * ------------------------------------------------------
 *  Load the remaining base classes
 * ------------------------------------------------------
 */

$IN		=& _load_class('CI_Input');
$URI	=& _load_class('CI_URI');
$LANG	=& _load_class('CI_Language');

/*
 * ------------------------------------------------------
 *  Load the app controller and local controller
 * ------------------------------------------------------
 * 
 *  Note: Due to the poor object handling in PHP 4 we'll
 *  contditionally load different versions of the base
 *  class.  Retaining PHP 4 compatibility requires a bit of a hack.
 *
 *  Note: The Loader class needs to be included first
 * 
 */
 
_load_class('CI_Loader', FALSE); 
  
if (floor(phpversion()) < 5)
{
	require(BASEPATH.'codeigniter/Base4'.EXT);
}
else
{
	require(BASEPATH.'codeigniter/Base5'.EXT);
}

_load_class('CI_Controller', FALSE); 

require(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT);

/*
 * ------------------------------------------------------
 *  Security check
 * ------------------------------------------------------
 * 
 *  None of the functions in the app controller or the
 *  loader class can be called via the URI, nor can 
 *  controller functions that begin with an underscore
 */
$class  = $RTR->fetch_class();
$method = $RTR->fetch_method();

if ( ! class_exists($class)
	OR $method == 'controller'
	OR substr($method, 0, 1) == '_' 
	OR in_array($method, get_class_methods('Controller'))
	)
{
	show_404();
}

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook?
 * ------------------------------------------------------
 */
if ($EXT->_hook_exists('pre_controller'))
{
	$EXT->_call_hook('pre_controller');
}

/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested method
 * ------------------------------------------------------
 */
$CI = new $class();

if ($RTR->scaffolding_request === TRUE)
{
	if ($EXT->_hook_exists('scaffolding_override'))
	{
		$EXT->_call_hook('scaffolding_override');
	}
	else
	{
		$CI->_ci_scaffolding();
	}
}
else
{
	if ($method == $class)
	{
		$method = 'index';
	}
	
	if (method_exists($CI, '_remap'))
	{
		$CI->_remap($method);
	}
	else
	{
		if ( ! method_exists($CI, $method))
		{
			show_404();
		}
	
		$CI->$method();
	}
}

/*
 * ------------------------------------------------------
 *  Is there a "post_controller" hook?
 * ------------------------------------------------------
 */
if ($EXT->_hook_exists('post_controller'))
{
	$EXT->_call_hook('post_controller');
}

/*
 * ------------------------------------------------------
 *  Send the final rendered output to the browser
 * ------------------------------------------------------
 */
 
if ($EXT->_hook_exists('display_override'))
{
	$EXT->_call_hook('display_override');
}
else
{
	$OUT->_display();
}

/*
 * ------------------------------------------------------
 *  Is there a "post_system" hook?
 * ------------------------------------------------------
 */
if ($EXT->_hook_exists('post_system'))
{
	$EXT->_call_hook('post_system');
}

/*
 * ------------------------------------------------------
 *  Close the DB connection of one exists
 * ------------------------------------------------------
 */
if ($CI->_ci_is_loaded('db'))
{
	$CI->db->close();
}


?>