<?php

// This autoloader provide convinient way to working with mock object
// make the test looks natural. This autoloader support cascade file loading as well
// within mocks directory.
//
// Prototype :
//
// include_once('Mock_Core_Common') 						// Will load ./mocks/core/common.php
// $mock_loader = new Mock_Core_Loader(); 					// Will load ./mocks/core/loader.php
// $mock_database_driver = new Mock_Database_Driver();		// Will load ./mocks/database/driver.php 
function autoload($class) 
{
	$class = (strpos($class, 'Mock_') === 0) ? str_replace(array('Mock_', '_'), array('', DIRECTORY_SEPARATOR), $class) : $class;
	$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;
	$file = $dir.strtolower($class).'.php';

	if ( ! file_exists($file))
	{
		$trace = debug_backtrace();

		// If the autoload call came from `class_exists`, we skipped
		// and return FALSE
		if ($trace[2]['function'] == 'class_exists')
		{
			return FALSE;
		}

	    throw new InvalidArgumentException("Unable to load $class.");
	}

	include_once($file);
}