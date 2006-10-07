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
 
define('APPVER', '1.5.0');

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require(BASEPATH.'codeigniter/Common'.EXT);

/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
set_error_handler('_exception_handler');
set_magic_quotes_runtime(0); // Kill magic quotes

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */

$BM =& _load_class('Benchmark');
$BM->mark('total_execution_time_start');
$BM->mark('loading_time_base_clases_start');

/*
 * ------------------------------------------------------
 *  Instantiate the hooks classe
 * ------------------------------------------------------
 */

$EXT =& _load_class('Hooks');

/*
 * ------------------------------------------------------
 *  Is there a "pre_system" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('pre_system');

/*
 * ------------------------------------------------------
 *  Instantiate the base classes
 * ------------------------------------------------------
 */

$CFG =& _load_class('Config');
$RTR =& _load_class('Router');
$OUT =& _load_class('Output');

/*
 * ------------------------------------------------------
 *	Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */
 
if ($EXT->_call_hook('cache_override') === FALSE)
{
	if ($OUT->_display_cache($CFG, $RTR) == TRUE)
	{
		exit;
	}
}

/*
 * ------------------------------------------------------
 *  Load the remaining base classes
 * ------------------------------------------------------
 */

$IN		=& _load_class('Input');
$URI	=& _load_class('URI');
$LANG	=& _load_class('Language');

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
 
_load_class('Loader', FALSE); 
  
if (floor(phpversion()) < 5)
{
	require(BASEPATH.'codeigniter/Base4'.EXT);
}
else
{
	require(BASEPATH.'codeigniter/Base5'.EXT);
}

_load_class('Controller', FALSE); 

require(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT);

// Set a mark point for benchmarking
$BM->mark('loading_time_base_clases_end');


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
	OR in_array($method, get_class_methods('Controller'), TRUE)
	)
{
	show_404();
}

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('pre_controller');

/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested method
 * ------------------------------------------------------
 */

// Mark a start point so we can benchmark the controller
$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

$CI = new $class();

if ($RTR->scaffolding_request === TRUE)
{
	if ($EXT->_call_hook('scaffolding_override') === FALSE)
	{
		if ($CI->_ci_scaffolding === FALSE OR $CI->_ci_scaff_table === FALSE)
		{
			show_404('Scaffolding unavailable');
		}
		
		if ( ! class_exists('Scaffolding'))
		{			
			if ( ! in_array($CI->uri->segment(3), array('add', 'insert', 'edit', 'update', 'view', 'delete', 'do_delete'), TRUE))
			{
				$method = 'view';
			}
			else
			{
				$method = $CI->uri->segment(3);
			}
			
			require_once(BASEPATH.'scaffolding/Scaffolding'.EXT);
			$scaff = new Scaffolding($CI->_ci_scaff_table);
			$scaff->$method();
		}
	}
}
else
{
	/*
	 * ------------------------------------------------------
	 *  Is there a "post_controller_constructor" hook?
	 * ------------------------------------------------------
	 */
	$EXT->_call_hook('post_controller_constructor');
	
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

		// Call the requested method.  
		// Any URI segments present (besides the class/function) will be passed to the method for convenience		
		call_user_func_array(array(&$CI, $method), array_slice($RTR->rsegments, (($RTR->fetch_directory() == '') ? 2 : 3)));		
	}
}

// Mark a benchmark end point
$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');

/*
 * ------------------------------------------------------
 *  Is there a "post_controller" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('post_controller');

/*
 * ------------------------------------------------------
 *  Send the final rendered output to the browser
 * ------------------------------------------------------
 */
 
if ($EXT->_call_hook('display_override') === FALSE)
{
	$OUT->_display();
}

/*
 * ------------------------------------------------------
 *  Is there a "post_system" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('post_system');

/*
 * ------------------------------------------------------
 *  Close the DB connection of one exists
 * ------------------------------------------------------
 */
if (class_exists('CI_DB'))
{
	$CI->db->close();
}


?>