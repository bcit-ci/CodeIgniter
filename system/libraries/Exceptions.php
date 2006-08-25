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
 * Exceptions Class
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions {
	var $action;
	var $severity;
	var $message;
	var $filename;
	var $line;

	var $levels = array(
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
	 * Constructor 
	 *
	 */	
	function CI_Exceptions()
	{
		log_message('debug', "Output Class Initialized");
	}
  	// END CI_Exceptions()
  	
	// --------------------------------------------------------------------

	/**
	 * Exception Logger
	 *
	 * This function logs PHP generated error messages
	 *
	 * @access	private
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
	 */
	function log_exception($severity, $message, $filepath, $line)
	{	
		$severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
		
		log_message('error', 'Severity: '.$severity.' '.$severity.' --> '.$message. ' '.$filepath.' '.$line, TRUE);
	}
	// END log_exception()
  
	// --------------------------------------------------------------------

	/**
	 * 404 Page Not Found Handler
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function show_404($page = '')
	{	
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found."; 

		log_message('error', '404 Page Not Found --> '.$page);
		echo $this->show_error($heading, $message, 'error_404');
		exit;
	}
  	// END show_404()
  	
	// --------------------------------------------------------------------

	/**
	 * General Error Page
	 *
	 * This function takes an error message as input
	 * (either as a string or an array) and displayes
	 * it using the specified template.
	 *
	 * @access	private
	 * @param	string	the heading
	 * @param	string	the message
	 * @param	string	the template name
	 * @return	string
	 */
	function show_error($heading, $message, $template = 'error_general')
	{
		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
				
		ob_start();
		include_once(APPPATH.'errors/'.$template.EXT);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
	// END show_error()

  
	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 *
	 * @access	private
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
	 */
	function show_php_error($severity, $message, $filepath, $line)
	{	
		$severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
	
		$filepath = str_replace("\\", "/", $filepath);
		
		// For safety reasons we do not show the full file path
		if (FALSE !== strpos($filepath, '/'))
		{
			$x = explode('/', $filepath);
			$filepath = $x[count($x)-2].'/'.end($x);
		}
	
		ob_start();
		include_once(APPPATH.'errors/error_php'.EXT);
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}
  	// END show_php_error()

// END Exceptions Class
}
?>