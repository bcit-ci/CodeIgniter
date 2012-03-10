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
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class CI_Lang {

	/**
	 * List of translations
	 *
	 * @var array
	 */
	var $language	= array();
	/**
	 * List of loaded language files
	 *
	 * @var array
	 */
	var $is_loaded	= array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
		log_message('debug', "Language Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @param	bool	return loaded array of translations
	 * @param 	bool	add suffix to $langfile
	 * @param 	string	alternative path to look for language file
	 * @return	mixed
	 */
	function load($langfiles = array(), $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		if ( ! is_array($langfiles))
		{
			$langfiles = array($langfiles);
		}
		
		if ($return == TRUE)
		{		
			$return_array = array();
		}
		
		foreach($langfiles as $langfile)
		{
			$langfile = str_replace('.php', '', $langfile);
	
			if ($add_suffix == TRUE)
			{
				$langfile .= '_lang';
			}
	
			$langfile .= '.php';
	
			if (in_array($langfile, $this->is_loaded, TRUE))
			{
				continue;
			}
	
			$config =& get_config();
	
			if ($idiom == '')
			{
				$deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
				$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
			}
	
			// Determine where the language file is and load it
			if ($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile))
			{
				include($alt_path.'language/'.$idiom.'/'.$langfile);
			}
			else
			{
				$found = FALSE;
	
				foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
				{
					if (file_exists($package_path.'language/'.$idiom.'/'.$langfile))
					{
						include($package_path.'language/'.$idiom.'/'.$langfile);
						$found = TRUE;
						break;
					}
				}
	
				if ($found !== TRUE)
				{
					show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
				}
			}
	
	
			if ( ! isset($lang))
			{
				log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
				return;
			}
	
			if ($return == TRUE)
			{			
				$return_array = array_merge($return_array, $lang);
			}
	
			$this->is_loaded[] = $langfile;
			$this->language = array_merge($this->language, $lang);
			unset($lang);
			log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		}

		if ($return == TRUE)
		{
			return $return_array;
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	$line	the language line
	 * @return	string
	 */
	function line($line = '')
	{
		$value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];

		// Because killer robots like unicorns!
		if ($value === FALSE)
		{
			log_message('error', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Unload a single language file or all language files
	 *
	 * @access	public
	 * @param	mixed	the name of the specific language file to be removed from the 'is_loaded' array. Can be an array
	 * @param 	bool	add suffix to $langfile
	 * @return	mixed
	 */	
	function clear($langfiles = array(), $add_suffix = TRUE)
	{
		if (empty($langfiles))
		{
			$this->is_loaded = array();
			return TRUE;
		}
		
		if ( ! is_array($langfiles))
		{
			$langfiles = array($langfiles);
		}

		foreach($langfiles as $langfile)
		{		
			$langfile = str_replace('.php', '', $langfile);	
		
			if ($add_suffix == TRUE)
			{
				$langfile .= '_lang';
			}

			$langfile .= '.php';
			
			// Determine if the language file is loaded
			$key = array_search($langfile, $this->is_loaded);
		
			if ($key === FALSE)
			{
				log_message('error', 'Could not find the language file "'.$langfile.'"');
				return;
			}
		
			unset($this->is_loaded[$key]);	
		}
		return TRUE;		
	}

}
// END Language Class

/* End of file Lang.php */
/* Location: ./system/core/Lang.php */
