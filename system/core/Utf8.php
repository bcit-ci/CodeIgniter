<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 2.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Utf8 Class
 *
 * Provides support for UTF-8 environments
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	UTF-8
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/utf8.html
 */
class CI_Utf8 {

	/**
	 * Class constructor
	 *
	 * Determines if UTF-8 support is to be enabled.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		if (defined('UTF8_ENABLED')) {
			return;
		}

		if (
			defined('PREG_BAD_UTF8_ERROR')				// PCRE must support UTF-8
			&& (ICONV_ENABLED === TRUE OR MB_ENABLED === TRUE)	// iconv or mbstring must be installed
			&& strtoupper(config_item('charset')) === 'UTF-8'	// Application charset must be UTF-8
			)
		{
			define('UTF8_ENABLED', TRUE);
			log_message('debug', 'UTF-8 Support Enabled');
		}
		else
		{
			define('UTF8_ENABLED', FALSE);
			log_message('debug', 'UTF-8 Support Disabled');
		}

		log_message('info', 'Utf8 Class Initialized');
	}

	// --------------------------------------------------------------------


	/**
	 * normalize whitespace
	 *
	 * @param string $str The string to be normalized.
	 *
	 * @return string
	 */
	public function normalize_whitespace($str)
	{
		$whitespaces = implode('|', $this->whitespace_table());
		$regx = '/(' . $whitespaces . ')/s';
		return preg_replace($regx, ' ', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * returns an array with all utf8 whitespace characters as per
	 * http://www.bogofilter.org/pipermail/bogofilter/2003-March/001889.html
	 *
	 * @author: Derek E. derek.isname@gmail.com
	 *
	 * @return array an array with all known whitespace characters as values and the type of whitespace as keys
	 *         as defined in above URL
	 */
	public function whitespace_table()
	{
		$whitespace = array(
				'SPACE'                     => "\x20",
				'NO-BREAK SPACE'            => "\xc2\xa0",
				'OGHAM SPACE MARK'          => "\xe1\x9a\x80",
				'EN QUAD'                   => "\xe2\x80\x80",
				'EM QUAD'                   => "\xe2\x80\x81",
				'EN SPACE'                  => "\xe2\x80\x82",
				'EM SPACE'                  => "\xe2\x80\x83",
				'THREE-PER-EM SPACE'        => "\xe2\x80\x84",
				'FOUR-PER-EM SPACE'         => "\xe2\x80\x85",
				'SIX-PER-EM SPACE'          => "\xe2\x80\x86",
				'FIGURE SPACE'              => "\xe2\x80\x87",
				'PUNCTUATION SPACE'         => "\xe2\x80\x88",
				'THIN SPACE'                => "\xe2\x80\x89",
				'HAIR SPACE'                => "\xe2\x80\x8a",
				'ZERO WIDTH SPACE'          => "\xe2\x80\x8b",
				'NARROW NO-BREAK SPACE'     => "\xe2\x80\xaf",
				'MEDIUM MATHEMATICAL SPACE' => "\xe2\x81\x9f",
				'IDEOGRAPHIC SPACE'         => "\xe3\x80\x80"
		);

		return $whitespace;
	}

	// --------------------------------------------------------------------

	/**
	 * Clean UTF-8 strings
	 *
	 * Ensures strings contain only valid UTF-8 characters.
	 *
	 * @param	string	$str	String to clean
	 * @return	string
	 */
	public function clean_string($str)
	{
		if ($this->is_ascii($str) === FALSE)
		{
			if (MB_ENABLED)
			{
				$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
			}
			elseif (ICONV_ENABLED)
			{
				$str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
			}
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Remove ASCII control characters
	 *
	 * Removes all ASCII control characters except horizontal tabs,
	 * line feeds, and carriage returns, as all others can cause
	 * problems in XML.
	 *
	 * @param	string	$str	String to clean
	 * @return	string
	 */
	public function safe_ascii_for_xml($str)
	{
		return remove_invisible_characters($str, FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert to UTF-8
	 *
	 * Attempts to convert a string to UTF-8.
	 *
	 * @param	string	$str		Input string
	 * @param	string	$encoding	Input encoding
	 * @return	string	$str encoded in UTF-8 or FALSE on failure
	 */
	public function convert_to_utf8($str, $encoding)
	{
		if (MB_ENABLED)
		{
			return mb_convert_encoding($str, 'UTF-8', $encoding);
		}
		elseif (ICONV_ENABLED)
		{
			return @iconv($encoding, 'UTF-8', $str);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Is ASCII?
	 *
	 * Tests if a string is standard 7-bit ASCII or not.
	 *
	 * @param	string	$str	String to check
	 * @return	bool
	 */
	public function is_ascii($str)
	{
		return (preg_match('/[^\x00-\x7F]/S', $str) === 0);
	}

}
