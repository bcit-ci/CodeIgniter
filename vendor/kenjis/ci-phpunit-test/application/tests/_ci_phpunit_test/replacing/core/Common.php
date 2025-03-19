<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

// Load this file before loading "system/core/Common.php"

/**
 * Class registry
 * 
 * @staticvar array $_classes
 * 
 * @param string $class
 * @param string $directory
 * @param array  $param
 * @param bool   $reset
 * @param object $obj
 * 
 * @return object
 */
function &load_class(
	$class,
	$directory = 'libraries',
	$param = NULL,
	$reset = FALSE,
	$obj = NULL
)
{
	static $_classes = array();

	if ($reset)
	{
		// If Utf8 is instantiated twice,
		// error "Constant UTF8_ENABLED already defined" occurs
		$UTF8 = $_classes['Utf8'];
		$_classes = array(
			'Utf8' => $UTF8
		);
		$obj = new stdClass();
		return $obj;
	}

	// Register object directly
	if ($obj)
	{
		is_loaded($class);

		$_classes[$class] = $obj;
		return $_classes[$class];
	}

	// Does the class exist? If so, we're done...
	if (isset($_classes[$class]))
	{
		return $_classes[$class];
	}

	$name = FALSE;

	// Look for the class first in the local application/libraries folder
	// then in the native system/libraries folder
	foreach (array(APPPATH, BASEPATH) as $path)
	{
		if (file_exists($path.$directory.'/'.$class.'.php'))
		{
			$name = 'CI_'.$class;

			if (class_exists($name, FALSE) === FALSE)
			{
				require_once($path.$directory.'/'.$class.'.php');
			}

			break;
		}
	}

	// Is the request a class extension? If so we load it too
	if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
	{
		$name = config_item('subclass_prefix').$class;

		if (class_exists($name, FALSE) === FALSE)
		{
			require_once(APPPATH.$directory.'/'.$name.'.php');
		}
	}

	// Did we find the class?
	if ($name === FALSE)
	{
		// Note: We use exit() rather then show_error() in order to avoid a
		// self-referencing loop with the Exceptions class
		set_status_header(503);

		// changed by ci-phpunit-test
		$msg = 'Unable to locate the specified class: '.$class.'.php';
//		exit(5); // EXIT_UNK_CLASS
		throw new CIPHPUnitTestExitException($msg);
	}

	// Keep track of what we just loaded
	is_loaded($class);

	$_classes[$class] = isset($param)
		? new $name($param)
		: new $name();
	return $_classes[$class];
}

/**
 * Keeps track of which libraries have been loaded.
 * 
 * @staticvar array $_is_loaded
 * 
 * @param string $class
 * @param bool   $reset
 * 
 * @return array
 */
function &is_loaded($class = '', $reset = FALSE)
{
	static $_is_loaded = array();

	if ($reset)
	{
		$_is_loaded = array();
		return $_is_loaded;
	}

	if ($class !== '')
	{
		$_is_loaded[strtolower($class)] = $class;
	}

	return $_is_loaded;
}

function is_cli($return = null)
{
	static $_return = TRUE;

	if ($return !== null)
	{
		$_return = $return;
	}

	return $_return;
}

function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
{
	$status_code = abs($status_code);
	if ($status_code < 100)
	{
		$exit_status = $status_code + 9; // 9 is EXIT__AUTO_MIN
		if ($exit_status > 125) // 125 is EXIT__AUTO_MAX
		{
			$exit_status = 1; // EXIT_ERROR
		}

		$status_code = 500;
	}
	else
	{
		$exit_status = 1; // EXIT_ERROR
	}

	while (ob_get_level() > 1)
	{
		ob_end_clean();
	}

	throw new CIPHPUnitTestShowErrorException($message, $status_code);
}

function show_404($page = '', $log_error = TRUE)
{
	while (ob_get_level() > 1)
	{
		ob_end_clean();
	}

	throw new CIPHPUnitTestShow404Exception($page, 404);
}

function set_status_header($code = 200, $text = '')
{
//	if (is_cli())
//	{
//		return;
//	}

	if (empty($code) OR ! is_numeric($code))
	{
		show_error('Status codes must be numeric', 500);
	}

	if (empty($text))
	{
		is_int($code) OR $code = (int) $code;
		$stati = array(
			100	=> 'Continue',
			101	=> 'Switching Protocols',

			200	=> 'OK',
			201	=> 'Created',
			202	=> 'Accepted',
			203	=> 'Non-Authoritative Information',
			204	=> 'No Content',
			205	=> 'Reset Content',
			206	=> 'Partial Content',

			300	=> 'Multiple Choices',
			301	=> 'Moved Permanently',
			302	=> 'Found',
			303	=> 'See Other',
			304	=> 'Not Modified',
			305	=> 'Use Proxy',
			307	=> 'Temporary Redirect',

			400	=> 'Bad Request',
			401	=> 'Unauthorized',
			402	=> 'Payment Required',
			403	=> 'Forbidden',
			404	=> 'Not Found',
			405	=> 'Method Not Allowed',
			406	=> 'Not Acceptable',
			407	=> 'Proxy Authentication Required',
			408	=> 'Request Timeout',
			409	=> 'Conflict',
			410	=> 'Gone',
			411	=> 'Length Required',
			412	=> 'Precondition Failed',
			413	=> 'Request Entity Too Large',
			414	=> 'Request-URI Too Long',
			415	=> 'Unsupported Media Type',
			416	=> 'Requested Range Not Satisfiable',
			417	=> 'Expectation Failed',
			422	=> 'Unprocessable Entity',

			500	=> 'Internal Server Error',
			501	=> 'Not Implemented',
			502	=> 'Bad Gateway',
			503	=> 'Service Unavailable',
			504	=> 'Gateway Timeout',
			505	=> 'HTTP Version Not Supported'
		);

		if (isset($stati[$code]))
		{
			$text = $stati[$code];
		}
		else
		{
			show_error('No status text available. Please check your status code number or supply your own message text.', 500);
		}
	}

	// Save status code in Output object
	// added by ci-phpunit-test
	$CI =& get_instance();
	$output = $CI->output;
	$output->_status = [
		'code'     => $code,
		'text'     => $text,
		'redirect' => null,
	];

	// Everything is done, so return
	// added by ci-phpunit-test
	if (ENVIRONMENT === 'testing')
	{
		return;
	}

	if (strpos(PHP_SAPI, 'cgi') === 0)
	{
		header('Status: '.$code.' '.$text, TRUE);
	}
	else
	{
		$server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
		header($server_protocol.' '.$code.' '.$text, TRUE, $code);
	}
}

/**
 * Loads the main config.php file
 * 
 * @staticvar array $config
 * 
 * @param array $replace
 * @param bool  $reset
 * 
 * @return array
 */
function &get_config(Array $replace = array(), $reset = FALSE)
{
	static $config;

	// Reset static variable
	// added by ci-phpunit-test
	if ($reset)
	{
		$config = null;
		return $config;
	}

	if (empty($config))
	{
		$file_path = APPPATH.'config/config.php';
		$found = FALSE;
		if (file_exists($file_path))
		{
			$found = TRUE;
			require($file_path);
		}

		// Is the config file in the environment folder?
		if (file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/config.php'))
		{
			require($file_path);
		}
		elseif ( ! $found)
		{
			set_status_header(503);
			echo 'The configuration file does not exist.';
			exit(3); // EXIT_CONFIG
		}

		// Does the $config array exist in the file?
		if ( ! isset($config) OR ! is_array($config))
		{
			set_status_header(503);
			echo 'Your config file does not appear to be formatted correctly.';
			exit(3); // EXIT_CONFIG
		}
	}

	// Are any values being dynamically added or replaced?
	foreach ($replace as $key => $val)
	{
		$config[$key] = $val;
	}

	return $config;
}

/**
 * Returns the specified config item
 * 
 * @staticvar array $_config
 * 
 * @param string $item
 * @param bool   $reset
 * 
 * @return type
 */
function config_item($item, $reset = FALSE)
{
	static $_config;

	// Reset static variable
	// added by ci-phpunit-test
	if ($reset)
	{
		$config = null;
		return;
	}

	if (empty($_config))
	{
		// references cannot be directly assigned to static variables, so we use an array
		$_config[0] =& get_config();
	}

	return isset($_config[0][$item]) ? $_config[0][$item] : NULL;
}
