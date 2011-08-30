<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * Exceptions Class
 *
 * The base class, CI_CoreShare, is defined in CodeIgniter.php and allows
 * Loader access to protected loading methods in CodeIgniter.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions extends CI_CoreShare {
	/**
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI		= NULL;

	/**
	 * Initial output buffer level
	 *
	 * @var	int
	 * @access	protected
	 */
	protected $ob_level	= 0;

	/**
	 * Constructor
	 *
	 * @param	object	parent reference
	 * @param	int		initial output buffer level
	 */
	public function __construct(CodeIgniter $CI, $ob_level) {
		// Attach parent reference
		$this->CI =& $CI;
		$this->ob_level = $ob_level;
		// Note: Do not log messages from this constructor.
	}

	/**
	 * General Error Page
	 *
	 * This function displays an error using the specified template unless an override is found.
	 * The override method will get the exception as its first argument,
	 * followed by any trailing segments of the override route. So, if the override
	 * route was "errclass/method/one/two", the effect would be to call:
	 *	errclass->method($exception, "one", "two");
	 * The CodeIgniter object calls this protected function via CI_CoreShare.
	 *
	 * @access	protected
	 * @param	object	ShowError exception
	 * @return	void
	 */
	protected function _show_error(CI_ShowError $error) {
		// Get template
		$template = $error->getTemplate();

		try {
			// Ensure Output is loaded and set status header
			if (!isset($this->CI->output)) {
				$this->_call_core($this->CI, '_load', 'core', 'Output');
			}
			$this->CI->output->set_status_header($error->getCode());

			// Clear any output buffering
			if (ob_get_level() > $this->ob_level + 1) {
				ob_end_flush();
			}

			// Ensure Router is loaded
			if (!isset($this->CI->router)) {
				$this->_call_core($this->CI, '_load', 'core', 'Router');
			}

			// Check Router for an override
			$route = $this->CI->router->get_error_route(str_replace('error_', '', $template));
			if ($route !== FALSE) {
				// Extract segment parts
				$path = array_shift($route);
				$subdir = array_shift($route);
				$class = array_shift($route);
				$method = array_shift($route);

				// Prepend exception to any remaining args
				array_unshift($route, $error);

				// Load object in core as routed
				if (isset($this->CI->routed)) {
					unset($this->CI->routed);
				}
				$this->_call_core($this->CI, '_load', 'controller', $class, 'routed', NULL, $subdir, $path);

				// Call controller method
				if ($this->_call_core($this->CI, '_call_controller', 'routed', $method, $route)) {
					// Display the output and exit
					$this->_call_core($this->CI->output, '_display');
					return;
				}
			}
		}
		catch (CI_ShowError $ex) {
			// Just add the failure to the existing messages and move on
			$error->addMessage($ex->getMessage());
		}

		// If the override didn't exit above, just display the generic error template
		// The output buffering here prevents displaying any partially buffered output
		// from the pre-error operation
		ob_start();
		include(APPPATH.'errors/'.$template.'.php');
		echo ob_get_clean();
	}
}
// END Exceptions Class

/* End of file Exceptions.php */
/* Location: ./system/core/Exceptions.php */
