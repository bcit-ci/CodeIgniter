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
		$heading = '404 Page Not Found';
		if ($log_error)
		{
			log_message('error', $heading.' --> '.$page);
		}

		// Set status header
		set_status_header(404);

		// Route the error to a controller or 404 template and exit
		$args = array(
			'heading' => $heading,
			'message' => 'The page you requested was not found.'
		);
		$this->_route_error('404_override', 'error_404', $args);
		exit;
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
		set_status_header(500);

		// Route the error to a controller or error template and exit
		$args = array(
			'heading' => $heading,
			'message' => $message
		);
		$this->_route_error('error_override', 'error_general', $args);
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

		// Route the error to a controller or exception template
		$args = array(
			'severity'	=> $severity,
			'message'	=> $message,
			'filepath'	=> $filepath,
			'line'		=> $line
		);
		$this->_route_error('exception_override', 'error_php', $args);
	}

	// --------------------------------------------------------------------

	/**
	 * Route error to an override controller or a template
	 *
	 * @param	string	Override route name
	 * @param	string	Template name
	 * @param	array	Route/template arguments
	 * @return	void
	 */
	protected function _route_error($route, $template, $args = NULL)
	{
		// Clear any output buffering
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		// Check Router for an override
		$CI =& get_instance();
		if (isset($CI->router))
		{
			$stack = $CI->router->get_error_route($route);
			if ($stack !== FALSE)
			{
				// Check for arguments
				if ( ! empty($args))
				{
					// Insert or append arguments
					if (count($stack) > CI_Router::SEG_ARGS)
					{
						// Insert args after path, subdir, class, and method and before other args
						$stack = array_merge(
							array_slice($stack, 0, CI_Router::SEG_ARGS),
							$args,
							array_slice($stack, CI_Router::SEG_ARGS)
						);
					}
					else
					{
						// Just append args to the end
						$stack = array_merge($stack, $args);
					}
				}

				// Ensure "routed" is not set
				if (isset($CI->routed))
				{
					unset($CI->routed);
				}

				// Load the error Controller as "routed" and call the method
				if ($CI->load->controller($stack, 'routed'))
				{
					// Display the output and return
					$CI->output->_display();
					return;
				}
			}
		}

		// If the override didn't exit above, just export the args and display the template
		if (isset($args['message']))
		{
			// Wrap message(s) in P tags
			$args['message'] = '<p>'.implode('</p><p>', (array) $args['message']).'</p>';
		}
		extract($args);
		ob_start();
		include(VIEWPATH.'errors/'.$template.'.php');
		echo ob_get_clean();
	}
}

/* End of file Exceptions.php */
/* Location: ./system/core/Exceptions.php */
