<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
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

	var $config = array();
	var $is_loaded = array();
	var $_config_paths = array(APPPATH);

	/**
	 * Constructor
	 *
	 * Sets the $config data from the primary config.php file as a class variable
	 *
	 * @access   public
	 * @param   string	the config file name
	 * @param   boolean  if configuration values should be loaded into their own section
	 * @param   boolean  true if errors should just return false, false if an error message should be displayed
	 * @return  boolean  if the file was successfully loaded or not
	 */
	function CI_Config()
	{
		$this->config =& get_config();	
		log_message('debug', "Config Class Initialized");
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @access	public
	 * @param	string	the config file name
	 * @return	boolean	if the file was loaded correctly
	 */	
	function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
		$loaded = FALSE;
	
		foreach($this->_config_paths as $path)
		{
			$file_path = $path.'config/'.$file.EXT;
			
			if (in_array($file_path, $this->is_loaded, TRUE))
			{
				$loaded = TRUE;
				continue;
			}

			if ( ! file_exists($path.'config/'.$file.EXT))
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
		}
		
		if ($loaded === FALSE)
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.EXT.' does not exist.');
		}

		return TRUE;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item
	 *
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @param	string	the index name
	 * @param	bool
	 * @return	string
	 */
	function item($item, $index = '')
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
	 * Fetch a config file item - adds slash after item
	 *
	 * The second parameter allows a slash to be added to the end of
	 * the item, in the case of a path.
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @param	bool
	 * @return	string
	 */
	function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}

		$pref = $this->config[$item];

		if ($pref != '' && substr($pref, -1) != '/')
		{	
			$pref .= '/';
		}

		return $pref;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Site URL
	 *
	 * @access	public
	 * @param	string	the URI string
	 * @return	string
	 */
	function site_url($uri = '')
	{
		if ($uri == '')
		{
			if ($this->item('base_url') == '')
			{
				return $this->item('index_page');
			}
			else
			{
				return $this->slash_item('base_url').$this->item('index_page');
			}
		}

		if ($this->item('enable_query_strings') == FALSE)
		{
			if (is_array($uri))
			{
				$uri = implode('/', $uri);
			}
	
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
			return $this->slash_item('base_url').$this->slash_item('index_page').trim($uri, '/').$suffix; 
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

			if ($this->item('base_url') == '')
			{
				return $this->item('index_page').'?'.$uri;
			}
			else
			{
				return $this->slash_item('base_url').$this->item('index_page').'?'.$uri;
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * System URL
	 *
	 * @access	public
	 * @return	string
	 */
	function system_url()
	{
		$x = explode("/", preg_replace("|/*(.+?)/*$|", "\\1", BASEPATH));
		return $this->slash_item('base_url').end($x).'/';
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Set a config file item
	 *
	 * @access	public
	 * @param	string	the config item key
	 * @param	string	the config item value
	 * @return	void
	 */
	function set_item($item, $value)
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
	function _assign_to_config($items = array())
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

// END CI_Config class

/* End of file Config.php */
/* Location: ./system/core/Config.php */