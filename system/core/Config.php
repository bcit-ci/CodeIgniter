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
 * CodeIgniter Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config {
	/**
	 * List of all loaded config values
	 *
	 * @var		array
	 */
	public $config = array();

	/**
	 * List of all loaded config files
	 *
	 * @var		array
	 */
	public $is_loaded =	array();

	/**
	 * List of paths to search when trying to load a config file.
	 * This must be public as it's used by the Loader class.
	 *
	 * @var		array
	 */
	public $_config_paths = array();

	/**
	 * Callback to array merging function
	 *
	 * @var		callback
	 */
	protected $merge_arrays;

	/**
	 * Constructor
	 *
	 * Sets the $config data from CodeIgniter::_core_config as a class variable.
	 * This is the contents of the primary config.php file with $assign_to_config
	 * overrides applied.
	 */
	public function __construct()
	{
		// Take over core config
		$CI = get_instance();
		$this->config = $CI->_core_config;
		unset($CI->_core_config);

		// Get config paths with autoloaded package paths
		$this->_config_paths = (isset($CI->app_paths) && is_array($CI->app_paths)) ? $CI->app_paths : array(APPPATH);

		// Determine array merge function
		$this->merge_arrays = is_php('5.3') ? 'array_replace_recursive' : array($this, '_merge_arrays');

		// Establish any configured constants
		$this->get('constants.php', NULL);

		// Autoload any other config files
		$autoload = $CI->_autoload;
		if (is_array($autoload) && isset($autoload['config']))
		{
			foreach ((array)$autoload['config'] as $file)
			{
				$this->load($file);
			}
		}

		// Set the base_url automatically if none was provided
		if (empty($this->config['base_url']))
		{
			if (isset($_SERVER['HTTP_HOST']))
			{
				$base_url = ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
				$base_url .= '://'.$_SERVER['HTTP_HOST']
					.str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
			}
			else
			{
				$base_url = 'http://localhost/';
			}

			$this->set_item('base_url', $base_url);
		}

		log_message('debug', 'Config Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @param	string	Config file name
	 * @param	bool	If configuration values should be loaded into their own section
	 * @param	bool	TRUE if errors should just return FALSE, FALSE if an error message should be displayed
	 * @return	bool	If the file was loaded correctly
	 */
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		// Strip .php from file
		$file = ($file === '') ? 'config' : str_replace('.php', '', $file);

		// Make sure file isn't already loaded
		if (in_array($file, $this->is_loaded))
		{
			return TRUE;
		}

		// Get config array and check result
		$config = $this->get($file.'.php', 'config');
		if ($config === FALSE)
		{
			if ($fail_gracefully)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.'.php does not exist.');
		}
		else if (is_string($config))
		{
			if ($fail_gracefully)
			{
				return FALSE;
			}
			show_error('Your '.$config.' file does not appear to contain a valid configuration array.');
		}

		// Check for sections
		if ($use_sections === TRUE)
		{
			// Merge or set section
			if (isset($this->config[$file]))
			{
				$this->config[$file] = array_merge($this->config[$file], $config);
			}
			else
			{
				$this->config[$file] = $config;
			}
		}
		else
		{
			// Merge config
			$this->config = array_merge($this->config, $config);
		}

		// Mark file as loaded
		$this->is_loaded[] = $file;
		log_message('debug', 'Config file loaded: '.$file.'.php');
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get config file contents
	 *
	 * Reads and merges config arrays from named config files
	 *
	 * @param	string	Config file name
	 * @param	mixed	Array name to look for, NULL for return value, FALSE for no output
	 * @return	mixed	Merged config if found, otherwise FALSE
	 */
	public function get($file, $name = NULL)
	{
		$extras = FALSE;
		return $this->get_ext($file, $name, $extras);
	}

	// --------------------------------------------------------------------

	/**
	 * Get config file contents with extra vars
	 *
	 * Reads and merges config arrays from named config files.
	 * Any standalone variables not starting with an underscore are gathered
	 * and returned via $_extras. For this reason, all local variables start
	 * with an underscore.
	 *
	 * @param	string	Config file name
	 * @param	mixed	Array name to look for, NULL for return value, FALSE for no output
	 * @param	array	Reference to extras array
	 * @return	mixed	Merged config if found, otherwise FALSE
	 */
	public function get_ext($_file, $_name, &$_extras)
	{
		// Ensure file ends with .php
		if (!preg_match('/\.php$/', $_file))
		{
			$_file .= '.php';
		}

		// Merge arrays from all viable config paths
		$_merged = array();
		$_check_locations = defined('ENVIRONMENT') ? array($_file, ENVIRONMENT.'/'.$_file) : array($_file);
		foreach ($this->_config_paths as $_path)
		{
			// Check with/without ENVIRONMENT
			foreach ($_check_locations as $_location)
			{
				// Determine if file exists here
				$_file_path = $_path.'config/'.$_location;
				if (file_exists($_file_path))
				{
					// Include file
					$_return = include($_file_path);

					// See if we're gathering extra variables
					if ($_extras !== FALSE)
					{
						// Get associative array of public vars
						foreach (get_defined_vars() as $_key => $_var)
						{
							if (substr($_key, 0, 1) != '_' && $_key != $_name && $_key != 'this')
							{
								$_extras[$_key] = $_var;
							}
						}
					}

					// See if we have an array name to check for
					if ($_name === NULL)
					{
						// Use the return value of the file we captured above
						$_name = '_return';
					}
					else if (empty($_name))
					{
						// Nope - just note we found something
						$_merged = TRUE;
						continue;
					}

					// Check for config array
					if ( ! isset($$_name) OR ! is_array($$_name))
					{
						// Invalid - return bad filename
						return $_file_path;
					}

					// Merge config and unset local
					$_merged = call_user_func_array($this->merge_arrays, array($_merged, &$$_name));
					unset($$_name);
				}
			}
		}

		// Test for merged config
		if (empty($_merged))
		{
			// None - quit
			return FALSE;
		}

		// Return merged config
		return $_merged;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item
	 *
	 *
	 * @param	string	Config item name
	 * @param	string	Index name
	 * @return	mixed	Config item value or FALSE on failure
	 */
	public function item($item, $index = '')
	{
		if ($index == '')
		{
			return isset($this->config[$item]) ? $this->config[$item] : FALSE;
		}

		return isset($this->config[$index], $this->config[$index][$item]) ? $this->config[$index][$item] : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item - adds slash after item (if item is not empty)
	 *
	 * @param	string	Config item name
	 * @return	mixed	Config item value or FALSE on failure
	 */
	public function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}
		elseif (trim($this->config[$item]) === '')
		{
			return '';
		}

		return rtrim($this->config[$item], '/').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Site URL
	 * Returns base_url . index_page [. uri_string]
	 *
	 * @param	mixed	URI string or an array of segments
	 * @return	string	URL string
	 */
	public function site_url($uri = '')
	{
		if (empty($uri))
		{
			return $this->slash_item('base_url').$this->item('index_page');
		}

		$uri = $this->_uri_string($uri);

		if ($this->item('enable_query_strings') === FALSE)
		{
			$suffix = ($this->item('url_suffix') === FALSE) ? '' : $this->item('url_suffix');

			if ($suffix !== '' && ($offset = strpos($uri, '?')) !== FALSE)
			{
				$uri = substr($uri, 0, $offset).$suffix.substr($uri, $offset);
			}
			else
			{
				$uri .= $suffix;
			}

			return $this->slash_item('base_url').$this->slash_item('index_page').$uri;
		}
		elseif (strpos($uri, '?') === FALSE)
		{
			$uri = '?'.$uri;
		}

		return $this->slash_item('base_url').$this->item('index_page').$uri;
	}

	// -------------------------------------------------------------

	/**
	 * Base URL
	 * Returns base_url [. uri_string]
	 *
	 * @param	string	URI
	 * @return	string	URL string
	 */
	public function base_url($uri = '')
	{
		return $this->slash_item('base_url').ltrim($this->_uri_string($uri), '/');
	}

	// -------------------------------------------------------------

	/**
	 * Build URI string for use in Config::site_url() and Config::base_url()
	 *
	 * @param	mixed	URI
	 * @return	string	URI string
	 */
	protected function _uri_string($uri)
	{
		if ($this->item('enable_query_strings') === FALSE)
		{
			if (is_array($uri))
			{
				$uri = implode('/', $uri);
			}
			return trim($uri, '/');
		}
		elseif (is_array($uri))
		{
			return http_build_query($uri);
		}

		return $uri;
	}

	// --------------------------------------------------------------------

	/**
	 * System URL
	 *
	 * @return	string	URL string
	 */
	public function system_url()
	{
		$x = explode('/', preg_replace('|/*(.+?)/*$|', '\\1', BASEPATH));
		return $this->slash_item('base_url').end($x).'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Set a config file item
	 *
	 * @param	string	Config item key
	 * @param	string	Config item value
	 * @return	void
	 */
	public function set_item($item, $value)
	{
		$this->config[$item] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Merge config arrays recursively
	 *
	 * This function recursively merges the values from a new array into an existing array.
	 * It is a substitute for array_replace_recursive() in PHP < 5.3; accepting only two arrays.
	 * The main (existing) array is copied as a parameter, modified with the contents of
	 * the new array (which is referenced), and returned.
	 *
	 * @param	array	Main array
	 * @param	array	New array of values to merge in
	 * @return	array	Merged array
	 */
	protected function _merge_arrays(array $main, array &$new)
	{
		// Iterate values of new array
		foreach ($new as $key => &$value)
		{
			// Merge sub-arrays recursively, add/replace all others
			$main[$key] = (is_array($value) && isset($main[$key]) && is_array($main[$key])) ?
				$this->_merge_arrays($main[$key], $value) : $value;
		}

		// Return merged array
		return $main;
	}
}

/* End of file Config.php */
/* Location: ./system/core/Config.php */
