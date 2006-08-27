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
 * Language Class
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/language.html
 */
class CI_Language {

	var $language	= array();
	var $is_loaded	= array();

	/**
	 * Constructor 
	 *
	 * @access	public
	 */	
	function CI_Language()
	{
		log_message('debug', "Language Class Initialized");
	}
	// END CI_Language()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @return	void
	 */
	function load($langfile = '', $idiom = '', $return = FALSE)
	{	
		$langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang'.EXT;
		
		if (in_array($langfile, $this->is_loaded))
		{
			return;
		}
		
		if ($idiom == '')
		{
			$obj =& get_instance();
			$deft_lang = $obj->config->item('language');
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
		}
	
		if ( ! file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile))
		{
			show_error('Unable to load the requested language file: language/'.$langfile);
		}

		include_once(BASEPATH.'language/'.$idiom.'/'.$langfile);
		            
		if ( ! isset($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}
		
		if ($return == TRUE)
		{
			return $lang;
		}
		
		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);
		
		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}
	// END load()
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	the language line
	 * @return	string
	 */
	function line($line = '')
	{
		return ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];
	}
	// END line()

}
// END Language Class
?>