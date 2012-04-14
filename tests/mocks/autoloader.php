<?php

// This autoloader provide convinient way to working with mock object
// make the test looks natural. This autoloader support cascade file loading as well
// within mocks directory.
//
// Prototype :
//
// include_once('Mock_Core_Loader') 					// Will load ./mocks/core/loader.php
// $mock_table = new Mock_Libraries_Table(); 			// Will load ./mocks/libraries/table.php
// $mock_database_driver = new Mock_Database_Driver();	// Will load ./mocks/database/driver.php 
// and so on...
function autoload($class) 
{
	$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;

	$ci_core = array(
		'Benchmark', 'Config', 'Controller',
		'Exceptions', 'Hooks', 'Input',
		'Lang', 'Loader', 'Model',
		'Output', 'Router', 'Security',
		'URI', 'Utf8',
	);

	$ci_libraries = array(
		'Calendar', 'Cart', 'Driver',
		'Email', 'Encrypt', 'Form_validation',
		'Ftp', 'Image_lib', 'Javascript',
		'Log', 'Migration', 'Pagination',
		'Parser', 'Profiler', 'Session',
		'Table', 'Trackback', 'Typography',
		'Unit_test', 'Upload', 'User_agent',
		'Xmlrpc', 'Zip',
	);

	if (strpos($class, 'Mock_') === 0)
	{
		$class = str_replace(array('Mock_', '_'), array('', DIRECTORY_SEPARATOR), $class);
		$class = strtolower($class);
	}
	elseif (strpos($class, 'CI_') === 0)
	{
		$fragments = explode('_', $class, 2);
		$subclass = next($fragments);

		if (in_array($subclass, $ci_core))
		{
			$dir = BASEPATH.'core'.DIRECTORY_SEPARATOR;
			$class = $subclass;
		}
		elseif (in_array($subclass, $ci_libraries))
		{
			$dir = BASEPATH.'libraries'.DIRECTORY_SEPARATOR;
			$class = $subclass;
		}
		else
		{
			$class = strtolower($class);
		}
	}

	$file = $dir.$class.'.php';

	if ( ! file_exists($file))
	{
		$trace = debug_backtrace();

		// If the autoload call came from `class_exists` or `file_exists`, 
		// we skipped and return FALSE
		if ($trace[2]['function'] == 'class_exists' OR $trace[2]['function'] == 'file_exists')
		{
			return FALSE;
		}

	    throw new InvalidArgumentException("Unable to load $class.");
	}

	include_once($file);
}