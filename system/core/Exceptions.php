<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * bundled with this package in the files license.txt / license.rst. It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

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
		E_ERROR				=>	'Error',
		E_WARNING			=>	'Warning',
		E_PARSE				=>	'Parsing Error',
		E_NOTICE			=>	'Notice',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_STRICT			=>	'Runtime Notice'
	);

	/**
	 * Initialize execption class
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
	 * This function logs PHP generated error messages
	 *
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	void
	 */
	public function log_exception($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		log_message('error', 'Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * 404 Page Not Found Handler
	 *
	 * Calls the 404 override method if configured, or displays a generic 404 error.
	 *
	 * @param	string	the page
	 * @param 	bool	log error yes/no
	 * @return	string
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', '404 Page Not Found --> '.$page);
		}

		// Call show_error for the 404 - it will exit
		$this->show_error('404 Page Not Found', 'The page you requested was not found.', 'error_404', 404);
	}

	// --------------------------------------------------------------------

	/**
	 * General Error Page
	 *
	 * This function takes an error message as input and passes it to the error
	 * override method if configured, or displays it using the specified template.
	 * The override method will get the heading and message(s) as its first arguments,
	 * followed by any trailing segments of the override route. So, if the override
	 * route was "errclass/method/one/two", the effect would be to call:
	 *	errclass->method($heading, $message, "one", "two");
	 *
	 * @param	string	the heading
	 * @param	mixed	the message string or array of strings
	 * @param	string	the template name
	 * @param 	int	the status code
	 * @return	string
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		// Set status header
		set_status_header($status_code);

		// Clear any output buffering
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		// Check Router for an error (or 404) override
		$CI =& get_instance();
		if (isset($CI->router))
		{
			$route = $CI->router->get_error_route($status_code == 404);
			if ($route !== FALSE)
			{
				// Insert or append arguments
				if (count($route) > CI_Router::SEG_ARGS)
				{
					// Insert heading and message after path, subdir, class, and method and before other args
					$route = array_merge(
							array_slice($route, 0, CI_Router::SEG_ARGS),
							array($heading, $message),
							array_slice($route, CI_Router::SEG_ARGS)
							);
				}
				else
				{
					// Just append heading and message to the end
					$route[] = $heading;
					$route[] = $message;
				}

				// Ensure "routed" is not set
				if (isset($CI->routed))
				{
					unset($CI->routed);
				}

				// Load the error Controller as "routed" and call the method
				if ($CI->load->controller($route, 'routed'))
				{
					// Display the output and exit
					$CI->output->_display();
					exit;
				}
			}
		}

		// If the override didn't exit above, just display the generic error template
		ob_start();
		$message = '<p>'.implode('</p><p>', (array) $message).'</p>';
		include(VIEWPATH.'errors/'.$template.'.php');
		echo ob_get_clean();
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 *
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
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
		echo ob_get_clean();
	}
}

/* End of file Exceptions.php */
/* Location: ./system/core/Exceptions.php */
