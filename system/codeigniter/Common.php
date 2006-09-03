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
 * Common Functions
 *
 * Loads the base classes and executes the request. 
 * 
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Common Functions
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/
 */
 
// ------------------------------------------------------------------------

/**
* Class registry
*
*
* @access	public
* @return	object
*/
function &_load_class($class, $instantiate = TRUE)
{
	static $objects;
	
	if ( ! isset($objects[$class]))
	{
		if (FALSE !== strpos($class, 'CI_'))
		{
			if (file_exists(APPPATH.'libraries/'.str_replace('CI_', '', $class).EXT))
			{
				require(APPPATH.'libraries/'.str_replace('CI_', '', $class).EXT);	
			}
			else
			{
				require(BASEPATH.'libraries/'.str_replace('CI_', '', $class).EXT);	
			}
		}
	
		if ($instantiate == TRUE)
		{
			if ($class == 'CI_Controller')
				$class = 'Controller';
				
			$objects[$class] =& new $class();
		}
		else
		{
			$objects[$class] = FALSE;
		}
	}
	
	return $objects[$class];
}

/**
* Loads the main config.php file
*
*
* @access	private
* @return	array
*/
function &_get_config()
{
	static $conf;
	
	if ( ! isset($conf))
	{
		require(APPPATH.'config/config'.EXT);
		
		if ( ! isset($config) OR ! is_array($config))
		{
			show_error('Your config file does not appear to be formatted correctly.');
		}

		$conf[0] =& $config;
	}
	return $conf[0];
}


/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located 
* in application/errors/errors.php
* This function will send the error page directly to the 
* browser and exit.
*
* @access	public
* @return	void
*/
function show_error($message)
{
	if ( ! class_exists('CI_Exceptions'))
	{
		include_once(BASEPATH.'libraries/Exceptions.php');
	}
	
	$error = new CI_Exceptions();
	echo $error->show_error('An Error Was Encountered', $message);
	exit;
}


/**
* 404 Page Handler
*
* This function is similar to the show_error() function above
* However, instead of the standard error template it displays
* 404 errors.
*
* @access	public
* @return	void
*/
function show_404($page = '')
{
	if ( ! class_exists('CI_Exceptions'))
	{
		include_once(BASEPATH.'libraries/Exceptions.php');
	}
	
	$error = new CI_Exceptions();
	$error->show_404($page);
	exit;
}


/**
* Error Logging Interface 
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
function log_message($level = 'error', $message, $php_error = FALSE)
{
	static $LOG;
	
	$config =& _get_config();
	if ($config['log_errors'] === FALSE)
	{
		return;
	}

	if ( ! class_exists('CI_Log'))
	{
		include_once(BASEPATH.'libraries/Log.php');		
	}
	
	if ( ! is_object($LOG))
	{
		$LOG = new CI_Log(
							$config['log_path'], 
							$config['log_threshold'], 
							$config['log_date_format']
						);
	}	
	
	$LOG->write_log($level, $message, $php_error);
}


/**
* Exception Handler
*
* This is the custom exception handler we defined at the
* top of this file. The main reason we use this is permit 
* PHP errors to be logged in our own log files since we may 
* not have access to server logs. Since this function
* effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level.
* We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
function _exception_handler($severity, $message, $filepath, $line)
{	
	 // We don't bother with "strict" notices since they will fill up
	 // the log file with information that isn't normally very
	 // helpful.  For example, if you are running PHP 5 and you
	 // use version 4 style class functions (without prefixes
	 // like "public", "private", etc.) you'll get notices telling
	 // you that these have been deprecated.
	 
	if ($severity == E_STRICT)
	{
		return;
	}

	// Send the PHP error to the log file...
	if ( ! class_exists('CI_Exceptions'))
	{
		include_once(BASEPATH.'libraries/Exceptions.php');
	}
	$error = new CI_Exceptions();

	// Should we display the error?  
	// We'll get the current error_reporting level and add its bits
	// with the severity bits to find out.
	
	if (($severity & error_reporting()) == $severity)
	{
		$error->show_php_error($severity, $message, $filepath, $line);
	}
	
	// Should we log the error?  No?  We're done...
	$config =& _get_config();
	if ($config['log_errors'] === FALSE)
	{
		return;
	}

	$error->log_exception($severity, $message, $filepath, $line);
}


?>