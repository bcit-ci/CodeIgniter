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
 * bundled with this package in the files license.txt / license.rst.  It is
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
 * CodeIgniter Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Hooks {

	/**
	 * Determines whether hooks are enabled
	 *
	 * @var bool
	 */
	public $enabled =	FALSE;

	/**
	 * List of all hooks set in config/hooks.php
	 *
	 * @var array
	 */
	public $hooks =	array();

	/**
	 * Determines whether hook is in progress, used to prevent infinte loops
	 *
	 * @var bool
	 */
	public $in_progress	=	FALSE;

	/**
	 * Initialize the Hooks Preferences
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$CFG =& load_class('Config', 'core');

		log_message('debug', 'Hooks Class Initialized');

		// If hooks are not enabled in the config file
		// there is nothing else to do
		if ($CFG->item('enable_hooks') === FALSE)
		{
			return;
		}

		// Grab the "hooks" definition file.
		if (defined('ENVIRONMENT') && is_file(APPPATH.'config/'.ENVIRONMENT.'/hooks.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
		}
		elseif (is_file(APPPATH.'config/hooks.php'))
		{
			include(APPPATH.'config/hooks.php');
		}

		// If there are no hooks, we're done.
		if ( ! isset($hook) OR ! is_array($hook))
		{
			return;
		}

		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Call Hook
	 *
	 * Calls a particular hook. Called by CodeIgniter.php.
	 *
	 * @param	string	the hook name
	 * @return	mixed
	 */
	public function call_hook($which = '')
	{
		if ( ! $this->enabled OR ! isset($this->hooks[$which]))
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

	// --------------------------------------------------------------------

	/**
	 * Run Hook
	 *
	 * Runs a particular hook
	 *
	 * @param	array	the hook details
	 * @return	bool
	 */
	protected function _run_hook($data)
	{
		if ( ! is_array($data))
		{
			return FALSE;
		}

		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------

		// If the script being called happens to have the same
		// hook call within it a loop can happen
		if ($this->in_progress === TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------

		if ( ! isset($data['filepath'], $data['filename']))
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

		if ( ! empty($data['class']))
		{
			$class = $data['class'];
		}

		if ( ! empty($data['function']))
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

}

/* End of file Hooks.php */
/* Location: ./system/core/Hooks.php */