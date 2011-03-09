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
 * Security Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Security
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Security {

	public $xss_hash			= '';
	public $csrf_hash			= '';
	public $csrf_expire			= 7200;  // Two hours (in seconds)
	public $csrf_token_name		= 'ci_csrf_token';
	public $csrf_cookie_name	= 'ci_csrf_token';

	/* never allowed, string replacement */
	public $never_allowed_str = array(
									'document.cookie'	=> '[removed]',
									'document.write'	=> '[removed]',
									'.parentNode'		=> '[removed]',
									'.innerHTML'		=> '[removed]',
									'window.location'	=> '[removed]',
									'-moz-binding'		=> '[removed]',
									'<!--'				=> '&lt;!--',
									'-->'				=> '--&gt;',
									'<![CDATA['			=> '&lt;![CDATA['
									);
	/* never allowed, regex replacement */
	public $never_allowed_regex = array(
										"javascript\s*:"			=> '[removed]',
										"expression\s*(\(|&\#40;)"	=> '[removed]', // CSS and IE
										"vbscript\s*:"				=> '[removed]', // IE, surprise!
										"Redirect\s+302"			=> '[removed]'
									);

	public function __construct()
	{
		$this->csrf_token_name = (config_item('csrf_token_name')) ? config_item('csrf_token_name') : 'csrf_token_name';
		$this->csrf_cookie_name = (config_item('csrf_cookie_name')) ? config_item('csrf_cookie_name') : 'csrf_cookie_name';
		$this->csrf_expire = (config_item('csrf_expire')) ? config_item('csrf_expire') : 7200;

		// Append application specific cookie prefix to token name
		$this->csrf_cookie_name = (config_item('cookie_prefix')) ? config_item('cookie_prefix').$this->csrf_token_name : $this->csrf_token_name;

		// Set the CSRF hash
		$this->_csrf_set_hash();

		log_message('debug', "Security Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Verify Cross Site Request Forgery Protection
	 *
	 * @access	public
	 * @return	null
	 */
	public function csrf_verify()
	{
		// If no POST data exists we will set the CSRF cookie
		if (count($_POST) == 0)
		{
			return $this->csrf_set_cookie();
		}

		// Do the tokens exist in both the _POST and _COOKIE arrays?
		if ( ! isset($_POST[$this->csrf_token_name]) OR ! isset($_COOKIE[$this->csrf_cookie_name]))
		{
			$this->csrf_show_error();
		}

		// Do the tokens match?
		if ($_POST[$this->csrf_token_name] != $_COOKIE[$this->csrf_cookie_name])
		{
			$this->csrf_show_error();
		}

		// We kill this since we're done and we don't want to polute the _POST array
		unset($_POST[$this->csrf_token_name]);

		// Nothing should last forever
		unset($_COOKIE[$this->csrf_cookie_name]);
		$this->_csrf_set_hash();
		$this->csrf_set_cookie();

		log_message('debug', "CSRF token verified ");
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cross Site Request Forgery Protection Cookie
	 *
	 * @access	public
	 * @return	null
	 */
	public function csrf_set_cookie()
	{
		$expire = time() + $this->csrf_expire;
		$secure_cookie = (config_item('cookie_secure') === TRUE) ? 1 : 0;

		setcookie($this->csrf_cookie_name, $this->csrf_hash, $expire, config_item('cookie_path'), config_item('cookie_domain'), $secure_cookie);

		log_message('debug', "CRSF cookie Set");
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cross Site Request Forgery Protection Cookie
	 *
	 * @access	private
	 * @return	null
	 */
	private function _csrf_set_hash()
	{
		if ($this->csrf_hash == '')
		{
			// If the cookie exists we will use it's value.  We don't necessarily want to regenerate it with
			// each page load since a page could contain embedded sub-pages causing this feature to fail
			if (isset($_COOKIE[$this->csrf_cookie_name]) AND $_COOKIE[$this->csrf_cookie_name] != '')
			{
				$this->csrf_hash = $_COOKIE[$this->csrf_cookie_name];
			}
			else
			{
				$this->csrf_hash = md5(uniqid(rand(), TRUE));
			}
		}

		return $this->csrf_hash;
	}

	// --------------------------------------------------------------------

	/**
	 * Show CSRF Error
	 *
	 * @access	public
	 * @return	null
	 */
	public function csrf_show_error()
	{
		show_error('The action you have requested is not allowed.');
	}

	// --------------------------------------------------------------------

	/**
	 * XSS Clean
	 *
	 * Sanitizes data so that Cross Site Scripting Hacks can be
	 * prevented.  This function does a fair amount of work but
	 * it is extremely thorough, designed to prevent even the
	 * most obscure XSS attempts.  Nothing is ever 100% foolproof,
	 * of course, but I haven't been able to get anything passed
	 * the filter.
	 *
	 * Note: This function should only be used to deal with data
	 * upon submission.  It's not something that should
	 * be used for general runtime processing.
	 *
	 * This function was based in part on some code and ideas I
	 * got from Bitflux: http://channel.bitflux.ch/wiki/XSS_Prevention
	 *
	 * To help develop this script I used this great list of
	 * vulnerabilities along with a few other hacks I've
	 * harvested from examining vulnerabilities in other programs:
	 * http://ha.ckers.org/xss.html
	 *
	 * @access	public
	 * @param	mixed	string or array
	 * @return	string
	 */
	public function xss_clean($str, $is_image = FALSE)
	{
		/*
		 * Is the string an array?
		 *
		 */
		if (is_array($str))
		{
			while (list($key) = each($str))
			{
				$str[$key] = $this->xss_clean($str[$key]);
			}

			return $str;
		}

		/*
		 * Remove Invisible Characters
		 */
		$str = remove_invisible_characters($str);

		/*
		 * Protect GET variables in URLs
		 */

		 // 901119URL5918AMP18930PROTECT8198

		$str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $this->xss_hash()."\\1=\\2", $str);

		/*
		 * Validate standard character entities
		 *
		 * Add a semicolon if missing.  We do this to enable
		 * the conversion of entities to ASCII later.
		 *
		 */
		$str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);

		/*
		 * Validate UTF16 two byte encoding (x00)
		 *
		 * Just as above, adds a semicolon if missing.
		 *
		 */
		$str = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;",$str);

		/*
		 * Un-Protect GET variables in URLs
		 */
		$str = str_replace($this->xss_hash(), '&', $str);

		/*
		 * URL Decode
		 *
		 * Just in case stuff like this is submitted:
		 *
		 * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
		 *
		 * Note: Use rawurldecode() so it does not remove plus signs
		 *
		 */
		$str = rawurldecode($str);

		/*
		 * Convert character entities to ASCII
		 *
		 * This permits our tests below to work reliably.
		 * We only convert entities that are within tags since
		 * these are the ones that will pose security problems.
		 *
		 */

		$str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);

		$str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, '_decode_entity'), $str);

		/*
		 * Remove Invisible Characters Again!
		 */
		$str = remove_invisible_characters($str);

		/*
		 * Convert all tabs to spaces
		 *
		 * This prevents strings like this: ja	vascript
		 * NOTE: we deal with spaces between characters later.
		 * NOTE: preg_replace was found to be amazingly slow here on large blocks of data,
		 * so we use str_replace.
		 *
		 */

		if (strpos($str, "\t") !== FALSE)
		{
			$str = str_replace("\t", ' ', $str);
		}

		/*
		 * Capture converted string for later comparison
		 */
		$converted_string = $str;

		/*
		 * Not Allowed Under Any Conditions
		 */

		foreach ($this->never_allowed_str as $key => $val)
		{
			$str = str_replace($key, $val, $str);
		}

		foreach ($this->never_allowed_regex as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		/*
		 * Makes PHP tags safe
		 *
		 *  Note: XML tags are inadvertently replaced too:
		 *
		 *	<?xml
		 *
		 * But it doesn't seem to pose a problem.
		 *
		 */
		if ($is_image === TRUE)
		{
			// Images have a tendency to have the PHP short opening and closing tags every so often
			// so we skip those and only do the long opening tags.
			$str = preg_replace('/<\?(php)/i', "&lt;?\\1", $str);
		}
		else
		{
			$str = str_replace(array('<?', '?'.'>'),  array('&lt;?', '?&gt;'), $str);
		}

		/*
		 * Compact any exploded words
		 *
		 * This corrects words like:  j a v a s c r i p t
		 * These words are compacted back to their correct state.
		 *
		 */
		$words = array('javascript', 'expression', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');
		foreach ($words as $word)
		{
			$temp = '';

			for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++)
			{
				$temp .= substr($word, $i, 1)."\s*";
			}

			// We only want to do this when it is followed by a non-word character
			// That way valid stuff like "dealer to" does not become "dealerto"
			$str = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', array($this, '_compact_exploded_words'), $str);
		}

		/*
		 * Remove disallowed Javascript in links or img tags
		 * We used to do some version comparisons and use of stripos for PHP5, but it is dog slow compared
		 * to these simplified non-capturing preg_match(), especially if the pattern exists in the string
		 */
		do
		{
			$original = $str;

			if (preg_match("/<a/i", $str))
			{
				$str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", array($this, '_js_link_removal'), $str);
			}

			if (preg_match("/<img/i", $str))
			{
				$str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", array($this, '_js_img_removal'), $str);
			}

			if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str))
			{
				$str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
			}
		}
		while ($original != $str);

		unset($original);

		/*
		 * Remove JavaScript Event Handlers
		 *
		 * Note: This code is a little blunt.  It removes
		 * the event handler and anything up to the closing >,
		 * but it's unlikely to be a problem.
		 *
		 */
		$event_handlers = array('[^a-z_\-]on\w*','xmlns');

		if ($is_image === TRUE)
		{
			/*
			 * Adobe Photoshop puts XML metadata into JFIF images, including namespacing,
			 * so we have to allow this for images. -Paul
			 */
			unset($event_handlers[array_search('xmlns', $event_handlers)]);
		}

		$str = preg_replace("#<([^><]+?)(".implode('|', $event_handlers).")(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);

		/*
		 * Sanitize naughty HTML elements
		 *
		 * If a tag containing any of the words in the list
		 * below is found, the tag gets converted to entities.
		 *
		 * So this: <blink>
		 * Becomes: &lt;blink&gt;
		 *
		 */
		$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
		$str = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', array($this, '_sanitize_naughty_html'), $str);

		/*
		 * Sanitize naughty scripting elements
		 *
		 * Similar to above, only instead of looking for
		 * tags it looks for PHP and JavaScript commands
		 * that are disallowed.  Rather than removing the
		 * code, it simply converts the parenthesis to entities
		 * rendering the code un-executable.
		 *
		 * For example:	eval('some code')
		 * Becomes:		eval&#40;'some code'&#41;
		 *
		 */
		$str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);

		/*
		 * Final clean up
		 *
		 * This adds a bit of extra precaution in case
		 * something got through the above filters
		 *
		 */
		foreach ($this->never_allowed_str as $key => $val)
		{
			$str = str_replace($key, $val, $str);
		}

		foreach ($this->never_allowed_regex as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		/*
		 *  Images are Handled in a Special Way
		 *  - Essentially, we want to know that after all of the character conversion is done whether
		 *  any unwanted, likely XSS, code was found.  If not, we return TRUE, as the image is clean.
		 *  However, if the string post-conversion does not matched the string post-removal of XSS,
		 *  then it fails, as there was unwanted XSS code found and removed/changed during processing.
		 */

		if ($is_image === TRUE)
		{
			if ($str == $converted_string)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		log_message('debug', "XSS Filtering completed");
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Random Hash for protecting URLs
	 *
	 * @access	public
	 * @return	string
	 */
	public function xss_hash()
	{
		if ($this->xss_hash == '')
		{
			if (phpversion() >= 4.2)
				mt_srand();
			else
				mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

			$this->xss_hash = md5(time() + mt_rand(0, 1999999999));
		}

		return $this->xss_hash;
	}

	// --------------------------------------------------------------------

	/**
	 * Compact Exploded Words
	 *
	 * Callback function for xss_clean() to remove whitespace from
	 * things like j a v a s c r i p t
	 *
	 * @access	private
	 * @param	type
	 * @return	type
	 */
	private function _compact_exploded_words($matches)
	{
		return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
	}

	// --------------------------------------------------------------------

	/**
	 * Sanitize Naughty HTML
	 *
	 * Callback function for xss_clean() to remove naughty HTML elements
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _sanitize_naughty_html($matches)
	{
		// encode opening brace
		$str = '&lt;'.$matches[1].$matches[2].$matches[3];

		// encode captured opening or closing brace to prevent recursive vectors
		$str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * JS Link Removal
	 *
	 * Callback function for xss_clean() to sanitize links
	 * This limits the PCRE backtracks, making it more performance friendly
	 * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
	 * PHP 5.2+ on link-heavy strings
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _js_link_removal($match)
	{
		$attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));
		return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
	}

	/**
	 * JS Image Removal
	 *
	 * Callback function for xss_clean() to sanitize image tags
	 * This limits the PCRE backtracks, making it more performance friendly
	 * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
	 * PHP 5.2+ on image tag heavy strings
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _js_img_removal($match)
	{
		$attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));
		return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Attribute Conversion
	 *
	 * Used as a callback for XSS Clean
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _convert_attribute($match)
	{
		return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Filter Attributes
	 *
	 * Filters tag attributes for consistency and safety
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	private function _filter_attributes($str)
	{
		$out = '';

		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$out .= preg_replace("#/\*.*?\*/#s", '', $match);
			}
		}

		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * HTML Entity Decode Callback
	 *
	 * Used as a callback for XSS Clean
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _decode_entity($match)
	{
		return $this->entity_decode($match[0], strtoupper(config_item('charset')));
	}

	// --------------------------------------------------------------------

	/**
	 * HTML Entities Decode
	 *
	 * This function is a replacement for html_entity_decode()
	 *
	 * In some versions of PHP the native function does not work
	 * when UTF-8 is the specified character set, so this gives us
	 * a work-around.  More info here:
	 * http://bugs.php.net/bug.php?id=25670
	 *
	 * NOTE: html_entity_decode() has a bug in some PHP versions when UTF-8 is the
	 * character set, and the PHP developers said they were not back porting the
	 * fix to versions other than PHP 5.x.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function entity_decode($str, $charset='UTF-8')
	{
		if (stristr($str, '&') === FALSE) return $str;

		// The reason we are not using html_entity_decode() by itself is because
		// while it is not technically correct to leave out the semicolon
		// at the end of an entity most browsers will still interpret the entity
		// correctly.  html_entity_decode() does not convert entities without
		// semicolons, so we are left with our own little solution here. Bummer.

		if (function_exists('html_entity_decode') && (strtolower($charset) != 'utf-8' OR is_php('5.0.0')))
		{
			$str = html_entity_decode($str, ENT_COMPAT, $charset);
			$str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
			return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
		}

		// Numeric Entities
		$str = preg_replace('~&#x(0*[0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str);

		// Literal Entities - Slightly slow so we do another check
		if (stristr($str, '&') === FALSE)
		{
			$str = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Filename Security
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function sanitize_filename($str, $relative_path = FALSE)
	{
		$bad = array(
						"../",
						"<!--",
						"-->",
						"<",
						">",
						"'",
						'"',
						'&',
						'$',
						'#',
						'{',
						'}',
						'[',
						']',
						'=',
						';',
						'?',
						"%20",
						"%22",
						"%3c",		// <
						"%253c",	// <
						"%3e",		// >
						"%0e",		// >
						"%28",		// (
						"%29",		// )
						"%2528",	// (
						"%26",		// &
						"%24",		// $
						"%3f",		// ?
						"%3b",		// ;
						"%3d"		// =
					);

		if ( ! $relative_path)
		{
			$bad[] = './';
			$bad[] = '/';
		}

		return stripslashes(str_replace($bad, '', $str));
	}

}
// END Security Class

/* End of file Security.php */
/* Location: ./system/libraries/Security.php */