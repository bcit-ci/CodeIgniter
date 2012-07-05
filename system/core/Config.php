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
	 * @var array
	 */
	public $config = array();

	/**
	 * List of all loaded config files
	 *
	 * @var array
	 */
	public $is_loaded =	array();

	/**
	 * List of paths to search when trying to load a config file.
	 * This must be public as it's used by the Loader class.
	 *
	 * @var array
	 */
	public $_config_paths =	array(APPPATH);

	/**
	 * Constructor
	 *
	 * Sets the $config data from the primary config.php file as a class variable
	 */
	public function __construct()
	{
		$this->config =& get_config();
		log_message('debug', 'Config Class Initialized');

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
	}

	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @param	string	the config file name
	 * @param	bool	if configuration values should be loaded into their own section
	 * @param	bool	true if errors should just return false, false if an error message should be displayed
	 * @return	bool	if the file was loaded correctly
	 */
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file === '') ? 'config' : str_replace('.php', '', $file);
		$found = $loaded = FALSE;
		
		$check_locations = defined('ENVIRONMENT')
			? array(ENVIRONMENT.'/'.$file, $file)
			: array($file);

		foreach ($this->_config_paths as $path)
		{
			foreach ($check_locations as $location)
			{
				$file_path = $path.'config/'.$location.'.php';

				if (in_array($file_path, $this->is_loaded, TRUE))
				{
					$loaded = TRUE;
					continue 2;
				}

				if (file_exists($file_path))
				{
					$found = TRUE;
					break;
				}
			}

			if ($found === FALSE)
			{
				continue;
			}

			include($file_path);

			if ( ! isset($config) OR ! is_array($config))
			{
				if ($fail_gracefully === TRUE)
				{
					return FALSE;
				}
				show_error('Your '.$file_path.' file does not appear to contain a valid configuration array.');
			}

			if ($use_sections === TRUE)
			{
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
				$this->config = array_merge($this->config, $config);
			}

			$this->is_loaded[] = $file_path;
			unset($config);

			$loaded = TRUE;
			log_message('debug', 'Config file loaded: '.$file_path);
			break;
		}

		if ($loaded === FALSE)
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.'.php does not exist.');
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item
	 *
	 * @param	string	the config item name
	 * @param	string	the index name
	 * @return	string
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
	 * @param	string	the config item name
	 * @return	string
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
	 * @param	mixed	the URI string or an array of segments
	 * @return	string
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
	 * @param	string	$uri
	 * @return	string
	 */
	public function base_url($uri = '')
	{
		return $this->slash_item('base_url').ltrim($this->_uri_string($uri), '/');
	}

	// -------------------------------------------------------------

	/**
	 * Build URI string for use in Config::site_url() and Config::base_url()
	 *
	 * @param	mixed	$uri
	 * @return	string
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
	 * @return	string
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
	 * after the Config class is instantiated. It permits config items
	 * to be assigned or overriden by variables contained in the index.php file
	 *
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

}

/* End of file Config.php */
/* Location: ./system/core/Config.php */