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

// ------------------------------------------------------------------------

/**
 * CodeIgniter Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config {
	/**
	 * List of all loaded config values
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $config = array();

	/**
	 * List of all loaded config files
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $is_loaded = array();

	/**
	 * Callback to array merging function
	 *
	 * @var		callback
	 * @access	protected
	 */
	protected $merge_arrays;

	/**
	 * List of paths to search when trying to load a config file
	 *
	 * @var		array
	 */
	public $_config_paths = array(APPPATH);

	/**
	 * Constructor
	 *
	 * Sets the $config data from the primary config.php file as a class variable
	 */
	public function __construct()
	{
		$this->config =& get_config();
		log_message('debug', 'Config Class Initialized');

		// Determine array merge function
		$this->merge_arrays = is_php('5.3') ? 'array_replace_recursive' : array($this, '_merge_arrays');

		// Set the base_url automatically if none was provided
		if ($this->config['base_url'] == '')
		{
			if (isset($_SERVER['HTTP_HOST']))
			{
				$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
				$base_url .= '://'. $_SERVER['HTTP_HOST'];
				$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
			}
			else
			{
				$base_url = 'http://localhost/';
			}

			$this->set_item('base_url', $base_url);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @param	string	the config file name
	 * @param	boolean	if configuration values should be loaded into their own section
	 * @param	boolean	true if errors should just return false, false if an error message should be displayed
	 * @return	boolean	if the file was loaded correctly
	 */
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		// Strip .php from file
		$file = str_replace('.php', '', $file);

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
	 * @param	string	the config file name
	 * @param	string	the array name to look for
	 * @return	mixed	merged config if found, otherwise FALSE
	 */
	public function get($file, $name)
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
	 * @param	string	the config file name
	 * @param	string	the array name to look for
	 * @param	array	reference to extras array
	 * @return	mixed	merged config if found, otherwise FALSE
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
		$_check_locations = defined('ENVIRONMENT') ? array(ENVIRONMENT.'/'.$_file, $_file) : array($_file);
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
					include($_file_path);

					// See if we're gathering extra variables
					if ($_extras !== FALSE)
					{
						// Get associative array of public vars
						foreach (get_defined_vars() as $_key => $_var)
						{
							if (substr($_key, 0, 1) != '_' && $_key != $_name)
							{
								$_extras[$_key] = $_var;
							}
						}
					}

					// See if we have an array name to check for
					if (empty($_name))
					{
						// Nope - just note we found something
						$_merged = TRUE;
						continue;
					}

					// Check for config array
					if ( ! isset($$_name) || ! is_array($$_name))
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
	 * @param	string	the config item name
	 * @param	string	the index name
	 * @param	bool
	 * @return	string
	 */
	public function item($item, $index = '')
	{
		if ($index == '')
		{
			if ( ! isset($this->config[$item]))
			{
				return FALSE;
			}

			$pref = $this->config[$item];
		}
		else
		{
			if ( ! isset($this->config[$index]))
			{
				return FALSE;
			}

			if ( ! isset($this->config[$index][$item]))
			{
				return FALSE;
			}

			$pref = $this->config[$index][$item];
		}

		return $pref;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item - adds slash after item (if item is not empty)
	 *
	 * @param	string	the config item name
	 * @param	bool
	 * @return	string
	 */
	public function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}
		if( trim($this->config[$item]) == '')
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
	 * @param	string	the URI string
	 * @return	string
	 */
	public function site_url($uri = '')
	{
		if ($uri == '')
		{
			return $this->slash_item('base_url').$this->item('index_page');
		}

		if ($this->item('enable_query_strings') == FALSE)
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
			return $this->slash_item('base_url').$this->slash_item('index_page').$this->_uri_string($uri).$suffix;
		}
		else
		{
			return $this->slash_item('base_url').$this->item('index_page').'?'.$this->_uri_string($uri);
		}
	}

	// -------------------------------------------------------------

	/**
	 * Base URL
	 * Returns base_url [. uri_string]
	 *
	 * @param string $uri
	 * @return string
	 */
	public function base_url($uri = '')
	{
		return $this->slash_item('base_url').ltrim($this->_uri_string($uri),'/');
	}

	// -------------------------------------------------------------

	/**
	 * Build URI string for use in Config::site_url() and Config::base_url()
	 *
	 * @access protected
	 * @param  $uri
	 * @return string
	 */
	protected function _uri_string($uri)
	{
		if ($this->item('enable_query_strings') == FALSE)
		{
			if (is_array($uri))
			{
				$uri = implode('/', $uri);
			}
			$uri = trim($uri, '/');
		}
		else
		{
			if (is_array($uri))
			{
				$i = 0;
				$str = '';
				foreach ($uri as $key => $val)
				{
					$prefix = ($i == 0) ? '' : '&';
					$str .= $prefix.$key.'='.$val;
					$i++;
				}
				$uri = $str;
			}
		}
	    return $uri;
	}

	// --------------------------------------------------------------------

	/**
	 * System URL
	 *
	 * @return	string
	 */
	public function system_url()
	{
		$x = explode("/", preg_replace("|/*(.+?)/*$|", "\\1", BASEPATH));
		return $this->slash_item('base_url').end($x).'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Set a config file item
	 *
	 * @param	string	the config item key
	 * @param	string	the config item value
	 * @return	void
	 */
	public function set_item($item, $value)
	{
		$this->config[$item] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Assign to Config
	 *
	 * This function is called by the front controller (CodeIgniter.php)
	 * after the Config class is instantiated.  It permits config items
	 * to be assigned or overriden by variables contained in the index.php file
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	public function _assign_to_config($items = array())
	{
		if (is_array($items))
		{
			foreach ($items as $key => $val)
			{
				$this->set_item($key, $val);
			}
		}
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
	 * @access	protected
	 * @param	array	main array
	 * @param	array	new array of values to merge in
	 * @return	array	merged array
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

// END CI_Config class

/* End of file Config.php */
/* Location: ./system/core/Config.php */
