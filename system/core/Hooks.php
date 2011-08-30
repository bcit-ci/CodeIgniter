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
 * CodeIgniter Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.
 * The base class, CI_CoreShare, is defined in CodeIgniter.php and allows
 * Loader access to protected loading methods in CodeIgniter.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Hooks extends CI_CoreShare {
	/**
	 * List of all hooks set in config/hooks.php
	 *
	 * @var array
	 * @access	protected
	 */
	protected $hooks = array();

	/**
	 * Determines wether hook is in progress, used to prevent infinte loops
	 *
	 * @var bool
	 * @access	protected
	 */
	protected $in_progress = FALSE;

	/**
	 * Constructor
	 *
	 * @param	object	parent reference
	 * @param	array	hooks config
	 */
	public function __construct(CodeIgniter $CI, array $hooks)
	{
		// No need for parent reference - just install hooks
		$this->hooks = $hooks;
		$CI->log_message('debug', 'Hooks Class Initialized');
	}

	/**
	 * Call Hook
	 *
	 * Calls a particular hook.
	 * The CodeIgniter object calls this protected function via CI_CoreShare.
	 *
	 * @access	protected
	 * @param	string	the hook name
	 * @return	mixed
	 */
	protected function _call_hook($which = '')
	{
		if (!isset($this->hooks[$which]))
		{
			return FALSE;
		}

		if (isset($this->hooks[$which][0]) && is_array($this->hooks[$which][0]))
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

		return TRUE;
	}

	/**
	 * Run Hook
	 *
	 * This helper function runs a particular hook.
	 * It should only be called internally.
	 *
	 * @access	protected
	 * @param	array	the hook details
	 * @return	bool
	 */
	protected function _run_hook($data)
	{
		if (!is_array($data))
		{
			return FALSE;
		}

		// Safety - Prevents run-away loops
		// If the script being called happens to have the same hook call within it a loop can happen
		if ($this->in_progress == TRUE)
		{
			return;
		}

		// Set file path
		if (!isset($data['filepath']) || !isset($data['filename']))
		{
			return FALSE;
		}
		$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];

		if (!file_exists($filepath))
		{
			return FALSE;
		}

		// Set class/function name
		$class = FALSE;
		$function = FALSE;
		$params = '';
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

		if ($class === FALSE && $function === FALSE)
		{
			return FALSE;
		}

		// Set the in_progress flag
		$this->in_progress = TRUE;

		// Call the requested class and/or function
		if ($class !== FALSE)
		{
			if (!class_exists($class))
			{
				require($filepath);
			}

			$HOOK = new $class;
			$HOOK->$function($params);
		}
		else
		{
			if (!function_exists($function))
			{
				require($filepath);
			}

			$function($params);
		}

		$this->in_progress = FALSE;
		return TRUE;
	}
}
// END CI_Hooks class

/* End of file Hooks.php */
/* Location: ./system/core/Hooks.php */
