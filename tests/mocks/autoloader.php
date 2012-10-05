<?php

// This autoloader provide convinient way to working with mock object
// make the test looks natural. This autoloader support cascade file loading as well
// within mocks directory.
//
// Prototype :
//
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
		'Calendar', 'Cart', 'Driver_Library',
		'Email', 'Encrypt', 'Form_validation',
		'Ftp', 'Image_lib', 'Javascript',
		'Log', 'Migration', 'Pagination',
		'Parser', 'Profiler', 'Table',
	   	'Trackback', 'Typography', 'Unit_test',
	   	'Upload', 'User_agent', 'Xmlrpc',
	   	'Zip',
	);

	$ci_drivers = array(
		'Session',
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
			$class = ($subclass === 'Driver_Library') ? 'Driver' : $subclass;
		}
		elseif (in_array($subclass, $ci_drivers))
		{
			$dir = BASEPATH.'libraries'.DIRECTORY_SEPARATOR.$subclass.DIRECTORY_SEPARATOR;
			$class = $subclass;
		}
		elseif (in_array(($parent = strtok($subclass, '_')), $ci_drivers)) {
			$dir = BASEPATH.'libraries'.DIRECTORY_SEPARATOR.$parent.DIRECTORY_SEPARATOR.'drivers'.DIRECTORY_SEPARATOR;
			$class = $subclass;
		}
		elseif (preg_match('/^CI_DB_(.+)_(driver|forge|result|utility)$/', $class, $m) && count($m) === 3)
		{
			$driver_path = BASEPATH.'database'.DIRECTORY_SEPARATOR.'drivers'.DIRECTORY_SEPARATOR;
			$dir = $driver_path.$m[1].DIRECTORY_SEPARATOR;
			$file = $dir.$m[1].'_'.$m[2].'.php';
		}
		elseif (strpos($class, 'CI_DB') === 0)
		{
			$dir = BASEPATH.'database'.DIRECTORY_SEPARATOR;
			$file = $dir.str_replace(array('CI_DB','active_record'), array('DB', 'active_rec'), $subclass).'.php';
		}
		else
		{
			$class = strtolower($class);
		}
	}

	$file = (isset($file)) ? $file : $dir.$class.'.php';

	if ( ! file_exists($file))
	{
		$trace = debug_backtrace();

		if ($trace[2]['function'] === 'class_exists' OR $trace[2]['function'] === 'file_exists')
		{
			// If the autoload call came from `class_exists` or `file_exists`,
			// we skipped and return FALSE
			return FALSE;
		}
		elseif (($autoloader = spl_autoload_functions()) && end($autoloader) !== __FUNCTION__)
		{
			// If there was other custom autoloader, passed away
			return FALSE;
		}

		throw new InvalidArgumentException("Unable to load {$class}.");
	}

	include_once($file);
}
