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
 * Code Igniter Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config {

	var $config = array();
	var $is_loaded = array();

	/**
	 * Constructor
	 *
	 * Sets the $config data from the primary config.php file as a class variable
	 *
	 * @access	public
	 */
	function CI_Config()
	{
		$this->config =& _get_config();
		log_message('debug', "Config Class Initialized");
	}
  	// END CI_Config()
  	
  	
	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @access	public
	 * @param	string	the config file name
	 * @return	void
	 */	
	function load($file = '')
	{
		$file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
	
		if (in_array($file, $this->is_loaded))
		{                
			return;
		}
	
		include_once(APPPATH.'config/'.$file.EXT);

		if ( ! isset($config) OR ! is_array($config))
		{
			show_error('Your '.$file.EXT.' file does not appear to contain a valid configuration array.');
		}
		
		$this->config = array_merge($this->config, $config);

		$this->is_loaded[] = $file;
		unset($config);

		log_message('debug', 'Config file loaded: config/'.$file.EXT);
	}
  	// END load()
  	
	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item
	 *
	 * The second parameter allows a slash to be added to the end of
	 * the item, in the case of a path.
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @param	bool
	 * @return	string
	 */		
	function item($item, $slash = FALSE)
	{
		if ( ! isset($this->config[$item])) 
		{
			return FALSE;
		}
		
		$pref = $this->config[$item];
		
		if ($pref == '')
		{
			return $pref;
		}
			
        if ($slash !== FALSE AND ereg("/$", $pref) === FALSE)
        {
			$pref .= '/';
        }
        
        return $pref;
	}
  	// END item()
  	
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
		if (is_array($uri))
		{ 
			$uri = implode('/', $uri);
		}
		
		if ($uri == '')
		{
			return $this->item('base_url', 1).$this->item('index_page');
		}
		else
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');		
			return $this->item('base_url', 1).$this->item('index_page', 1).preg_replace("|^/*(.+?)/*$|", "\\1", $uri).$suffix;
		}
	}
  	// END site_url()
  	
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
		return $this->item('base_url', 1).end($x).'/';
	}
  	// END system_url()
  	
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
	// END set_item()

}

// END CI_Config class
?>