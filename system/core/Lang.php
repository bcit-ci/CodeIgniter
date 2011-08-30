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
	 * Reference to CodeIgniter object
	 *
	 * @var object
	 * @access	protected
	 */
	protected $CI		= NULL;

	/**
	 * List of translations
	 *
	 * @var array
	 * @access	protected
	 */
	protected $language	= array();

	/**
	 * List of loaded language files
	 *
	 * @var array
	 * @access	protected
	 */
	protected $is_loaded	= array();

	/**
	 * Constructor
	 *
	 * @param	object	parent reference
	 */
	public function __construct(CodeIgniter $CI) {
		// Attach parent reference
		$this->CI =& $CI;
		$CI->log_message('debug', 'Language Class Initialized');
	}

	/**
	 * Load a language file
	 *
	 * @throws	CI_ShowError	if language file is not found
	 * @param	mixed	the	name of the language file to be loaded. Can be an array
	 * @param	string	the	language (english, etc.)
	 * @return	mixed
	 */
	public function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix == TRUE) {
			$langfile = str_replace('_lang.', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (in_array($langfile, $this->is_loaded, TRUE)) {
			return;
		}

		if ($idiom == '') {
			$cfg_lang = $this->CI->config->item('language');
			$deft_lang = $cfg_lang ? $cfg_lang : 'english';
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
		}

		// Determine where the language file is and load it
		if ($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile)) {
			include($alt_path.'language/'.$idiom.'/'.$langfile);
		}
		else {
			$found = FALSE;

			foreach ($this->CI->get_package_paths(TRUE) as $package_path) {
				if (file_exists($package_path.'language/'.$idiom.'/'.$langfile)) {
					include($package_path.'language/'.$idiom.'/'.$langfile);
					$found = TRUE;
					break;
				}
			}

			if ($found !== TRUE) {
				throw new CI_ShowError('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
			}
		}

		if (!isset($lang) || !is_array($lang)) {
			$this->CI->log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}

		if ($return == TRUE) {
			return $lang;
		}

		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		$this->CI->log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}

	/**
	 * Fetch a single line of text from the language array
	 *
	 * @param	string	$line	the	language line
	 * @return	string
	 */
	public function line($line = '') {
		// Validate line
		if ($line == '' || ! isset($this->language[$line])) {
			$this->CI->log_message('error', 'Could not find the language line "'.$line.'"');
		}

		// Return line
		return $this->language[$line];
	}
}
// END Language Class

/* End of file Lang.php */
/* Location: ./system/core/Lang.php */
