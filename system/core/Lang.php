<?php
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class CI_Lang {

	/**
	 * List of loaded translated strings
	 *
	 * @var	array
	 */
	public $language =	array();

	/**
	 * List of loaded language files
	 *
	 * @var	array
	 */
	public $is_loaded =	array();

	/**
	 * The code of the currently loaded language
	 *
	 * @var string
	 */
	protected $_current_language =	NULL;

	/**
	 * The supported language codes
	 *
	 * @var array
	 */
	protected $_supported_languages =	NULL;

	/**
	 * The directory name of the currently loaded language.
	 * If NULL, defaults to $_current_language.
	 *
	 * @var string
	 */
	protected $_language_folder =	NULL;

	/**
	 * The key used to keep track of the current language in Session or in $_GET
	 *
	 * @var string
	 */
	protected $_language_key;

	/**
	 * The list of supported languages.
	 *
	 * @return array
	 */
	public function supported()
	{
		return $this->_supported_languages;
	}

	/**
	 * The language code currently in use.
	 *
	 * @return string
	 */
	public function current()
	{
		if ($this->_current_language)
		{
			return $this->_current_language;
		}

		$CI = &get_instance();

		// First check POST, GET, then the session
		$user_lang = $CI->input->post_get($this->_language_key);
		$session_lang = $CI->session->userdata($this->_language_key);

		$lang = $user_lang ? $user_lang : $session_lang;

		// Try to auto-detect from the browser's settings
		if (!$lang)
		{
			$lang = $this->_detect_language();
		}

		$lang = strtolower($lang);

		if ( ! $lang || ! in_array($lang, $this->_supported_languages))
		{
			// No appropriate language was detected, default to the first one
			// in our supported language list.
			$first = array_slice($this->_supported_languages, 0, 1, TRUE);
			$first_key = key($first);
			if (is_int($first_key)) {
				$this->_current_language = $this->_supported_languages[0];
			} else {
				$this->_current_language = $first_key;
				$this->_language_folder = $this->_supported_languages[$first_key];
			}
		}
		else {
			$this->_current_language = $lang;
			$this->_language_folder = isset($this->_supported_languages[$lang]) ? $this->_supported_languages[$lang] : NULL;
		}

		// Store the detected language in the session
		if ($CI->session->userdata($this->_language_key) != $lang)
		{
			$CI->session->set_userdata($this->_language_key, $lang);
		}

		return $this->_current_language;
	}

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$config = &get_config();
		$this->_supported_languages = $config['language'];
		$this->_language_key = $config['language_key'];

		log_message('debug', 'Language Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Load a language file
	 *
	 * @param	mixed	$langfile	Language file name
	 * @param	string	$idiom		Language code (en-US, etc.)
	 * @param	bool	$return		Whether to return the loaded array of translations
	 * @param	bool	$add_suffix	Whether to add suffix to $langfile
	 * @param	string	$alt_path	Alternative path to look for the language file
	 *
	 * @return	void|string[]	Array containing translations, if $return is set to TRUE
	 */
	public function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix === TRUE)
		{
			$langfile = str_replace('_lang', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (empty($idiom) OR ! ctype_alpha($idiom))
		{
			$config =& get_config();
			$idiom = $this->current();
		}

		if ($return === FALSE && isset($this->is_loaded[$langfile]) && $this->is_loaded[$langfile] === $idiom)
		{
			return;
		}

		// Load the base file, so any others found can override it
		$basepath = BASEPATH.'language/'.$idiom.'/'.$langfile;
		if (($found = file_exists($basepath)) === TRUE)
		{
			include($basepath);
		}

		// Do we have an alternative path to look in?
		if ($alt_path !== '')
		{
			$alt_path .= 'language/'.$idiom.'/'.$langfile;
			if (file_exists($alt_path))
			{
				include($alt_path);
				$found = TRUE;
			}
		}
		else
		{
			foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
			{
				$package_path .= 'language/'.$idiom.'/'.$langfile;
				if ($basepath !== $package_path && file_exists($package_path))
				{
					include($package_path);
					$found = TRUE;
					break;
				}
			}
		}

		if ($found !== TRUE)
		{
			show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
		}

		if ( ! isset($lang) OR ! is_array($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);

			if ($return === TRUE)
			{
				return array();
			}
			return;
		}

		if ($return === TRUE)
		{
			return $lang;
		}

		$this->is_loaded[$langfile] = $idiom;
		$this->language = array_merge($this->language, $lang);

		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Language line
	 *
	 * Fetches a single line of text from the language array
	 *
	 * @param	string	$line		Language line key
	 * @param	bool	$log_errors	Whether to log an error message if the line is not found
	 * @return	string	Translation
	 */
	public function line($line, $log_errors = TRUE)
	{
		$value = isset($this->language[$line]) ? $this->language[$line] : FALSE;

		// Because killer robots like unicorns!
		if ($value === FALSE && $log_errors === TRUE)
		{
			log_message('error', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}

	/**
	 * Detect the language which best suits the client
	 */
	private function _detect_language()
	{
		$lang = NULL;
		foreach ($this->_accepted_languages() as $browser_lang => $order)
		{
			if (array_key_exists($browser_lang, $this->_supported_languages))
			{
				$lang = $browser_lang;
				break;
			}
		}

		$lang = strtolower($lang);

		log_message('debug', "Client supports languages : " . implode(", ", array_keys($this->_accepted_languages())));
		log_message('debug', "Selected language '{$lang}' from browser");

		return $lang;
	}

	/**
	 * Parse and sort the client's accepted languages
	 */
	private function _accepted_languages()
	{
		$locales = NULL;
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			// Client sent us an HTTP header, extract its data
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
			if (count($lang_parse[1]) !== 0)
			{
				$locales = array_combine($lang_parse[1], $lang_parse[4]);
				foreach ($locales as $lang => $val)
				{
					$locales[$lang] = ($val === '' ? 1.0 : floatval($locales[$lang]));
				}

				// Sort our locales by their preferred rank
				arsort($locales, SORT_NUMERIC);
			}
		}

		return $locales;
	}
}

/* End of file Lang.php */
/* Location: ./system/core/Lang.php */