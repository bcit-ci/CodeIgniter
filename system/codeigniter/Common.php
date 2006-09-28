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
* This function acts as a singleton.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has
* previously been instantiated the variable is returned.
* 
* @access	public
* @return	object
*/
function &_load_class($class, $instantiate = TRUE)
{
	static $objects = array();

	// Does the class exist?  If so, we're done...
	if (isset($objects[$class]))
	{
		return $objects[$class];
	}
	
	// This is a special case.  It's a class in the Base5.php file
	// which we don't need to load.  We only instantiate it.
	if ($class == 'Instance')
	{
		return $objects[$class] =& new $class();	
	}
		
	// If the requested class does not exist in the application/libraries
	// folder we'll load the native class from the system/libraries folder.

	$is_subclass = FALSE;	
	if ( ! file_exists(APPPATH.'libraries/'.$class.EXT))
	{
		require(BASEPATH.'libraries/'.$class.EXT);		
	}
	else
	{
		// A core class can either be extended or replaced by putting an
		// identially named file in the application/libraries folder.  
		// We need to need to determine if the class being requested is 
		// a sub-class or an independent instance so we'll open the file,
		// read the top portion of it. If the class extends the base class
		// we need to load it's parent. If it doesn't extend the base we'll
		// only load the requested class.
		
		// Note: I'm not thrilled with this approach since it requires us to
		// read the file, but I can't think of any other way to allow classes
		// to be extended on-the-fly.  I did benchmark the difference with and
		// without the file reading and I'm not seeing a perceptable difference.
		
		$fp	= fopen(APPPATH.'libraries/'.$class.EXT, "rb");
		if (preg_match("/MY_".$class."\s+extends\s+CI_".$class."/", fread($fp, '8000')))
		{
			require(BASEPATH.'libraries/'.$class.EXT);	
			require(APPPATH.'libraries/'.$class.EXT);
			$is_subclass = TRUE;
		}
		else
		{
			require(APPPATH.'libraries/'.$class.EXT);
		}
		fclose($fp);	
	}

	if ($instantiate == FALSE)
	{
		return $objects[$class] = TRUE;
	}
		
	if ($is_subclass == TRUE)
	{
		$name = 'MY_'.$class;
		return $objects[$class] =& new $name();
	}

	$name = ($class != 'Controller') ? 'CI_'.$class : $class;
	
	return $objects[$class] =& new $name();	
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
	static $main_conf;
		
	if ( ! isset($main_conf))
	{
		if ( ! file_exists(APPPATH.'config/config'.EXT))
		{
			show_error('The configuration file config'.EXT.' does not exist.');
		}
		
		require(APPPATH.'config/config'.EXT);
		
		if ( ! isset($config) OR ! is_array($config))
		{
			show_error('Your config file does not appear to be formatted correctly.');
		}

		$main_conf[0] =& $config;
	}
	return $main_conf[0];
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
	$error =& _load_class('Exceptions');
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
	$error =& _load_class('Exceptions');
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

	$LOG =& _load_class('Log');	
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

	$error =& _load_class('Exceptions');

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