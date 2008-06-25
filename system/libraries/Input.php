<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Input
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/input.html
 */
class CI_Input {
	var $use_xss_clean		= FALSE;
	var $xss_hash			= '';
	var $ip_address			= FALSE;
	var $user_agent			= FALSE;
	var $allow_get_array	= FALSE;
	
	/* never allowed, string replacement */
	var $never_allowed_str = array(
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
	var $never_allowed_regex = array(
										"javascript\s*:"	=> '[removed]',
										"expression\s*\("	=> '[removed]', // CSS and IE
										"Redirect\s+302"	=> '[removed]'
									);
				
	/**
	 * Constructor
	 *
	 * Sets whether to globally enable the XSS processing
	 * and whether to allow the $_GET array
	 *
	 * @access	public
	 */
	function CI_Input()
	{
		log_message('debug', "Input Class Initialized");

		$CFG =& load_class('Config');
		$this->use_xss_clean	= ($CFG->item('global_xss_filtering') === TRUE) ? TRUE : FALSE;
		$this->allow_get_array	= ($CFG->item('enable_query_strings') === TRUE) ? TRUE : FALSE;
		$this->_sanitize_globals();
	}

	// --------------------------------------------------------------------

	/**
	 * Sanitize Globals
	 *
	 * This function does the following:
	 *
	 * Unsets $_GET data (if query strings are not enabled)
	 *
	 * Unsets all globals if register_globals is enabled
	 *
	 * Standardizes newline characters to \n
	 *
	 * @access	private
	 * @return	void
	 */
	function _sanitize_globals()
	{
		// Would kind of be "wrong" to unset any of these GLOBALS
		$protected = array('_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION', '_ENV', 'GLOBALS', 'HTTP_RAW_POST_DATA',
							'system_folder', 'application_folder', 'BM', 'EXT', 'CFG', 'URI', 'RTR', 'OUT', 'IN');

		// Unset globals for security. 
		// This is effectively the same as register_globals = off
		foreach (array($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV, (isset($_SESSION) && is_array($_SESSION)) ? $_SESSION : array()) as $global)
		{
			if ( ! is_array($global))
			{
				if ( ! in_array($global, $protected))
				{
					unset($GLOBALS[$global]);
				}
			}
			else
			{
				foreach ($global as $key => $val)
				{
					if ( ! in_array($key, $protected))
					{
						unset($GLOBALS[$key]);
					}
			
					if (is_array($val))
					{
						foreach($val as $k => $v)
						{
							if ( ! in_array($k, $protected))
							{
								unset($GLOBALS[$k]);
							}
						}
					}
				}
			}
		}

		// Is $_GET data allowed? If not we'll set the $_GET to an empty array
		if ($this->allow_get_array == FALSE)
		{
			$_GET = array();
		}
		else
		{
			$_GET = $this->_clean_input_data($_GET);
		}

		// Clean $_POST Data
		$_POST = $this->_clean_input_data($_POST);
		
		// Clean $_COOKIE Data
		$_COOKIE = $this->_clean_input_data($_COOKIE);

		log_message('debug', "Global POST and COOKIE data sanitized");
	}

	// --------------------------------------------------------------------

	/**
	 * Clean Input Data
	 *
	 * This is a helper function. It escapes data and
	 * standardizes newline characters to \n
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _clean_input_data($str)
	{
		if (is_array($str))
		{
			$new_array = array();
			foreach ($str as $key => $val)
			{
				$new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
			return $new_array;
		}

		// We strip slashes if magic quotes is on to keep things consistent
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}

		// Should we filter the input data?
		if ($this->use_xss_clean === TRUE)
		{
			$str = $this->xss_clean($str);
		}

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}
		
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Clean Keys
	 *
	 * This is a helper function. To prevent malicious users
	 * from trying to exploit keys we make sure that keys are
	 * only named with alpha-numeric text and a few other items.
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _clean_input_keys($str)
	{
		 if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
		 {
			exit('Disallowed Key Characters.');
		 }

		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Fetch from array
	 *
	 * This is a helper function to retrieve values from global arrays
	 *
	 * @access	private
	 * @param	array
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
	{
		if ( ! isset($array[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			return $this->xss_clean($array[$index]);
		}

		return $array[$index];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Fetch an item from the GET array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function get($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_GET, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the POST array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function post($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_POST, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from either the GET array or the POST
	 *
	 * @access	public
	 * @param	string	The index key
	 * @param	bool	XSS cleaning
	 * @return	string
	 */
	function get_post($index = '', $xss_clean = FALSE)
	{		
		if ( ! isset($_POST[$index]) )
		{
			return $this->get($index, $xss_clean);
		}
		else
		{
			return $this->post($index, $xss_clean);
		}		
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function cookie($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the SERVER array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function server($index = '', $xss_clean = FALSE)
	{
		return $this->_fetch_from_array($_SERVER, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the IP Address
	 *
	 * @access	public
	 * @return	string
	 */
	function ip_address()
	{
		if ($this->ip_address !== FALSE)
		{
			return $this->ip_address;
		}

		if ($this->server('REMOTE_ADDR') AND $this->server('HTTP_CLIENT_IP'))
		{
			 $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($this->server('REMOTE_ADDR'))
		{
			 $this->ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif ($this->server('HTTP_CLIENT_IP'))
		{
			 $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($this->server('HTTP_X_FORWARDED_FOR'))
		{
			 $this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if ($this->ip_address === FALSE)
		{
			$this->ip_address = '0.0.0.0';
			return $this->ip_address;
		}

		if (strstr($this->ip_address, ','))
		{
			$x = explode(',', $this->ip_address);
			$this->ip_address = end($x);
		}

		if ( ! $this->valid_ip($this->ip_address))
		{
			$this->ip_address = '0.0.0.0';
		}
		
		return $this->ip_address;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * Updated version suggested by Geert De Deckere
	 * 
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function valid_ip($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) != 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if (substr($ip_segments[0], 0, 1) == '0')
		{
			return FALSE;
		}
		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be 
			// longer than 3 digits or greater then 255
			if (preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * User Agent
	 *
	 * @access	public
	 * @return	string
	 */
	function user_agent()
	{
		if ($this->user_agent !== FALSE)
		{
			return $this->user_agent;
		}

		$this->user_agent = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

		return $this->user_agent;
	}

	// --------------------------------------------------------------------

	/**
	 * Filename Security
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function filename_security($str)
	{
		$bad = array(
						"../",
						"./",
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
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (  
						"%29", 		// ) 
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);

		return stripslashes(str_replace($bad, '', $str));
	}

	// --------------------------------------------------------------------

	/**
	 * XSS Clean
	 *
	 * Sanitizes data so that Cross Site Scripting Hacks can be
	 * prevented.  This function does a fair amount of work but
	 * it is extremely thorough, designed to prevent even the
	 * most obscure XSS attempts.  Nothing is ever 100% foolproof,
	 * of course, but I haven't been able to get anything passed
	 * the filter.
	 *
	 * Note: This function should only be used to deal with data
	 * upon submission.  It's not something that should
	 * be used for general runtime processing.
	 *
	 * This function was based in part on some code and ideas I
	 * got from Bitflux: http://blog.bitflux.ch/wiki/XSS_Prevention
	 *
	 * To help develop this script I used this great list of
	 * vulnerabilities along with a few other hacks I've
	 * harvested from examining vulnerabilities in other programs:
	 * http://ha.ckers.org/xss.html
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function xss_clean($str, $is_image = FALSE)
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
		$str = $this->_remove_invisible_characters($str);

		/*
		 * Protect GET variables in URLs
		 */
		 
		 // 901119URL5918AMP18930PROTECT8198
		 
		$str = preg_replace('|\&([a-z\_0-9]+)\=([a-z\_0-9]+)|i', $this->xss_hash()."\\1=\\2", $str);

		/*
		 * Validate standard character entities
		 *
		 * Add a semicolon if missing.  We do this to enable
		 * the conversion of entities to ASCII later.
		 *
		 */
		$str = preg_replace('#(&\#?[0-9a-z]+)[\x00-\x20]*;?#i', "\\1;", $str);

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
	 
		$str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, '_html_entity_decode_callback'), $str);

		/*
		 * Remove Invisible Characters Again!
		 */
		$str = $this->_remove_invisible_characters($str);
		
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
			$str = str_replace(array('<?php', '<?PHP'),  array('&lt;?php', '&lt;?PHP'), $str);
		}
		else
		{
			$str = str_replace(array('<?php', '<?PHP', '<?', '?'.'>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
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
				$str = preg_replace_callback("#<a\s*([^>]*?)(>|$)#si", array($this, '_js_link_removal'), $str);
			}
	
			if (preg_match("/<img/i", $str))
			{
				$str = preg_replace_callback("#<img\s*([^>]*?)(>|$)#si", array($this, '_js_img_removal'), $str);
			}
	
			if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str))
			{
				$str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
			}
		}
		while($original != $str);

		unset($original);

		/*
		 * Remove JavaScript Event Handlers
		 *
		 * Note: This code is a little blunt.  It removes
		 * the event handler and anything up to the closing >,
		 * but it's unlikely to be a problem.
		 *
		 */
		$event_handlers = array('on\w*','xmlns');

		if ($is_image === TRUE)
		{
			/*
			 * Adobe Photoshop puts XML metadata into JFIF images, including namespacing, 
			 * so we have to allow this for images. -Paul
			 */
			unset($event_handlers[array_search('xmlns', $event_handlers)]);
		}
		
		$str = preg_replace("#<([^><]+)(".implode('|', $event_handlers).")(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);
		
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
		$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
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
	function xss_hash()
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
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function _remove_invisible_characters($str)
	{
		static $non_displayables;
		
		if ( ! isset($non_displayables))
		{
			// every control character except newline (10), carriage return (13), and horizontal tab (09),
			// both as a URL encoded character (::shakes fist at IE and WebKit::), and the actual character
			$non_displayables = array(
										'/%0[0-8]/', '/[\x00-\x08]/',			// 00-08
										'/%11/', '/\x0b/', '/%12/', '/\x0c/',	// 11, 12
										'/%1[4-9]/', '/%2[0-9]/', '/%3[0-1]/',	// url encoded 14-31
										'/[\x0e-\x1f]/');						// 14-31
			
		}

		do
		{
			$cleaned = $str;
			$str = preg_replace($non_displayables, '', $str);
		}
		while ($cleaned != $str);

		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Compact Exploded Words
	 *
	 * Callback function for xss_clean() to remove whitespace from
	 * things like j a v a s c r i p t
	 *
	 * @access	public
	 * @param	type
	 * @return	type
	 */
	function _compact_exploded_words($matches)
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
	function _sanitize_naughty_html($matches)
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
	function _js_link_removal($match)
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
	function _js_img_removal($match)
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
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function _convert_attribute($match)
	{
		return str_replace(array('>', '<'), array('&gt;', '&lt;'), $match[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * HTML Entity Decode Callback
	 *
	 * Used as a callback for XSS Clean
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function _html_entity_decode_callback($match)
	{
		global $CFG;
		$charset = $CFG->item('charset');

		return $this->_html_entity_decode($match[0], strtoupper($charset));
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
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	/* -------------------------------------------------
	/*  Replacement for html_entity_decode()
	/* -------------------------------------------------*/

	/*
	NOTE: html_entity_decode() has a bug in some PHP versions when UTF-8 is the
	character set, and the PHP developers said they were not back porting the
	fix to versions other than PHP 5.x.
	*/
	function _html_entity_decode($str, $charset='UTF-8')
	{
		if (stristr($str, '&') === FALSE) return $str;

		// The reason we are not using html_entity_decode() by itself is because
		// while it is not technically correct to leave out the semicolon
		// at the end of an entity most browsers will still interpret the entity
		// correctly.  html_entity_decode() does not convert entities without
		// semicolons, so we are left with our own little solution here. Bummer.

		if (function_exists('html_entity_decode') && (strtolower($charset) != 'utf-8' OR version_compare(phpversion(), '5.0.0', '>=')))
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
	 * Filter Attributes
	 *
	 * Filters tag attributes for consistency and safety
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function _filter_attributes($str)
	{
		$out = '';

		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$out .= "{$match}";
			}			
		}

		return $out;
	}

	// --------------------------------------------------------------------

}
// END Input class

/* End of file Input.php */
/* Location: ./system/libraries/Input.php */