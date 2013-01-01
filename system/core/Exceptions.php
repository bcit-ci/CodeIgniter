<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Exceptions Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions {

	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var	int
	 */
	public $ob_level;

	/**
	 * List if available error levels
	 *
	 * @var	array
	 */
	public $levels = array(
		E_ERROR			=>	'Error',
		E_WARNING		=>	'Warning',
		E_PARSE			=>	'Parsing Error',
		E_NOTICE		=>	'Notice',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_STRICT		=>	'Runtime Notice'
	);

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->ob_level = ob_get_level();
		// Note: Do not log messages from this constructor.
	}

	// --------------------------------------------------------------------

	/**
	 * Exception Logger
	 *
	 * Logs PHP generated error messages
	 *
	 * @param	int	$severity	Log level
	 * @param	string	$message	Error message
	 * @param	string	$filepath	File path
	 * @param	int	$line		Line number
	 * @return	void
	 */
	public function log_exception($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		log_message('error', 'Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * 404 Error Handler
	 *
	 * @uses	CI_Exceptions::show_error()
	 *
	 * @param	string	$page		Page URI
	 * @param 	bool	$log_error	Whether to log the error
	 * @return	void
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		$heading = '404 Page Not Found';
		$message = 'The page you requested was not found.';

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', '404 Page Not Found --> '.$page);
		}

		echo $this->show_error($heading, $message, 'error_404', 404);
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * General Error Page
	 *
	 * Takes an error message as input (either as a string or an array)
	 * and displays it using the specified template.
	 *
	 * @param	string		$heading	Page heading
	 * @param	string|string[]	$message	Error message
	 * @param	string		$template	Template name
	 * @param 	int		$status_code	(default: 500)
	 *
	 * @return	string	Error page output
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		set_status_header($status_code);

		$message = '<p>'.implode('</p><p>', is_array($message) ? $message : array($message)).'</p>';

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include(VIEWPATH.'errors/'.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 *
	 * @param	int	$severity	Error level
	 * @param	string	$message	Error message
	 * @param	string	$filepath	File path
	 * @param	int	$line		Line number
	 * @return	string	Error page output
	 */
	public function show_php_error($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		$filepath = str_replace('\\', '/', $filepath);

		// For safety reasons we do not show the full file path
		if (FALSE !== strpos($filepath, '/'))
		{
			$x = explode('/', $filepath);
			$filepath = $x[count($x)-2].'/'.end($x);
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include(VIEWPATH.'errors/error_php.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}

}

/* End of file Exceptions.php */
/* Location: ./system/core/Exceptions.php */