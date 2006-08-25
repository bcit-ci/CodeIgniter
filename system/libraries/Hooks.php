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
 * Code Igniter Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.  Most of
 * this class is borrowed from Paul's Extension class in ExpressionEngine.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Hooks {
	
	var $enabled 		= FALSE;
	var $hooks   		= array();
	var $in_progress	= FALSE;
	
	/**
	 * Constructor
	 *
	 */
	function CI_Hooks()
	{
		log_message('debug', "Hooks Class Initialized");
	
		$CFG =& _load_class('CI_Config');
		
		// If hooks are not enabled in the config file
		// there is nothing else to do
		
		if ($CFG->item('enable_hooks') == FALSE)
		{
			return;
		}
		
		// Grab the "hooks" definition file.
		// If there are no hooks, we're done.
		
		@include(APPPATH.'config/hooks'.EXT);
		
		if ( ! isset($hook) OR ! is_array($hook))
		{
			return;
		}

		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}
  	// END CI_Hooks()
  	
	// --------------------------------------------------------------------

	/**
	 * Does a given hook exist?
	 *
	 * Returns TRUE/FALSE based on whether a given hook exists
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _hook_exists($which = '')
	{
		if ( ! $this->enabled)
		{
			return FALSE;
		}

		if ( ! isset($this->hooks[$which]))
		{
			return FALSE;
		}

		return TRUE;
	}
  	// END hook_exists()
  	
  	
	// --------------------------------------------------------------------

	/**
	 * Call Hook
	 *
	 * Calls a particular hook
	 *
	 * @access	private
	 * @param	string	the hook name
	 * @return	mixed
	 */
	function _call_hook($which = '')
	{
		if (isset($this->hooks[$which][0]) AND is_array($this->hooks[$which][0]))
		{
			foreach ($this->hooks[$which] as $val)
			{
				$this->_run_hook($val);
			}
		}
		else
		{
			$this->_run_hook($this->hooks[$which]);
		}
	}
  	// END hook_exists()

	// --------------------------------------------------------------------

	/**
	 * Run Hook
	 *
	 * Runs a particular hook
	 *
	 * @access	private
	 * @param	array	the hook details
	 * @return	bool
	 */
	function _run_hook($data)
	{
		if ( ! is_array($data))
		{
			return FALSE;
		}
		
		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------
	
		// If the script being called happens to have the same 
		// extension call within it a loop can happen
		
		if ($this->in_progress == TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------
		
		if ( ! isset($data['filepath']) OR ! isset($data['filename']))
		{
			return FALSE;
		}
		
		$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];
	
		if ( ! file_exists($filepath))
		{
			return FALSE;
		}
		
		// -----------------------------------
		// Set class/function name
		// -----------------------------------
		
		$class		= FALSE;
		$function	= FALSE;
		$params		= '';
		
		if (isset($data['class']) AND $data['class'] != '') 
		{
			$class = $data['class'];
		}

		if (isset($data['function'])) 
		{
			$function = $data['function'];
		}

		if (isset($data['params'])) 
		{
			$params = $data['params'];
		}
		
		if ($class === FALSE AND $function === FALSE)
		{
			return FALSE;
		}
		
		// -----------------------------------
		// Set the in_progress flag
		// -----------------------------------

		$this->in_progress = TRUE;
		
		// -----------------------------------
		// Call the requested class and/or function
		// -----------------------------------
		
		if ($class !== FALSE)
		{
			if ( ! class_exists($class))
			{
				require($filepath);
			}
		
			$HOOK = new $class;
			$HOOK->$function($params);
		}
		else
		{
			if ( ! function_exists($function))
			{
				require($filepath);
			}
		
			$function($params);
		}
	
		$this->in_progress = FALSE;
		return TRUE;
	}
  	// END _run_hook()


}

// END CI_Hooks class
?>