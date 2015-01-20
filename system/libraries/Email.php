<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright		Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright		Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Email Class
 *
 * Permits email to be sent using Mail, Sendmail, or SMTP.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/email.html
 */
class CI_Email {

	var	$useragent		= "CodeIgniter";
	var	$mailpath		= "/usr/sbin/sendmail";	// Sendmail path
	var	$protocol		= "mail";	// mail/sendmail/smtp
	var	$smtp_host		= "";		// SMTP Server.  Example: mail.earthlink.net
	var	$smtp_user		= "";		// SMTP Username
	var	$smtp_pass		= "";		// SMTP Password
	var	$smtp_port		= "25";		// SMTP Port
	var	$smtp_timeout	= 5;		// SMTP Timeout in seconds
	var	$smtp_crypto	= "";		// SMTP Encryption. Can be null, tls or ssl.
	var	$wordwrap		= TRUE;		// TRUE/FALSE  Turns word-wrap on/off
	var	$wrapchars		= "76";		// Number of characters to wrap at.
	var	$mailtype		= "text";	// text/html  Defines email formatting
	var	$charset		= "utf-8";	// Default char set: iso-8859-1 or us-ascii
	var	$multipart		= "mixed";	// "mixed" (in the body) or "related" (separate)
	var $alt_message	= '';		// Alternative message for HTML emails
	var	$validate		= FALSE;	// TRUE/FALSE.  Enables email validation
	var	$priority		= "3";		// Default priority (1 - 5)
	var	$newline		= "\n";		// Default newline. "\r\n" or "\n" (Use "\r\n" to comply with RFC 822)
	var $crlf			= "\n";		// The RFC 2045 compliant CRLF for quoted-printable is "\r\n".  Apparently some servers,
									// even on the receiving end think they need to muck with CRLFs, so using "\n", while
									// distasteful, is the only thing that seems to work for all environments.
	var $send_multipart	= TRUE;		// TRUE/FALSE - Yahoo does not like multipart alternative, so this is an override.  Set to FALSE for Yahoo.
	var	$bcc_batch_mode	= FALSE;	// TRUE/FALSE  Turns on/off Bcc batch feature
	var	$bcc_batch_size	= 200;		// If bcc_batch_mode = TRUE, sets max number of Bccs in each batch
	var $_safe_mode		= FALSE;
	var	$_subject		= "";
	var	$_body			= "";
	var	$_finalbody		= "";
	var	$_alt_boundary	= "";
	var	$_atc_boundary	= "";
	var	$_header_str	= "";
	var	$_smtp_connect	= "";
	var	$_encoding		= "8bit";
	var $_IP			= FALSE;
	var	$_smtp_auth		= FALSE;
	var $_replyto_flag	= FALSE;
	var	$_debug_msg		= array();
	var	$_recipients	= array();
	var	$_cc_array		= array();
	var	$_bcc_array		= array();
	var	$_headers		= array();
	var	$_attach_name	= array();
	var	$_attach_type	= array();
	var	$_attach_disp	= array();
	var	$_protocols		= array('mail', 'sendmail', 'smtp');
	var	$_base_charsets	= array('us-ascii', 'iso-2022-');	// 7-bit charsets (excluding language suffix)
	var	$_bit_depths	= array('7bit', '8bit');
	var	$_priorities	= array('1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)');


	/**
	 * Constructor - Sets Email Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		else
		{
			$this->_smtp_auth = ($this->smtp_user == '' AND $this->smtp_pass == '') ? FALSE : TRUE;
			$this->_safe_mode = ((boolean)@ini_get("safe_mode") === FALSE) ? FALSE : TRUE;
		}

		log_message('debug', "Email Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$method = 'set_'.$key;

				if (method_exists($this, $method))
				{
					$this->$method($val);
				}
				else
				{
					$this->$key = $val;
				}
			}
		}
		$this->clear();

		$this->_smtp_auth = ($this->smtp_user == '' AND $this->smtp_pass == '') ? FALSE : TRUE;
		$this->_safe_mode = ((boolean)@ini_get("safe_mode") === FALSE) ? FALSE : TRUE;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the Email Data
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear($clear_attachments = FALSE)
	{
		$this->_subject		= "";
		$this->_body		= "";
		$this->_finalbody	= "";
		$this->_header_str	= "";
		$this->_replyto_flag = FALSE;
		$this->_recipients	= array();
		$this->_cc_array	= array();
		$this->_bcc_array	= array();
		$this->_headers		= array();
		$this->_debug_msg	= array();

		$this->_set_header('User-Agent', $this->useragent);
		$this->_set_header('Date', $this->_set_date());

		if ($clear_attachments !== FALSE)
		{
			$this->_attach_name = array();
			$this->_attach_type = array();
			$this->_attach_disp = array();
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set FROM
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function from($from, $name = '')
	{
		if (preg_match( '/\<(.*)\>/', $from, $match))
		{
			$from = $match['1'];
		}

		if ($this->validate)
		{
			$this->validate_email($this->_str_to_array($from));
		}

		// prepare the display name
		if ($name != '')
		{
			// only use Q encoding if there are characters that would require it
			if ( ! preg_match('/[\200-\377]/', $name))
			{
				// add slashes for non-printing characters, slashes, and double quotes, and surround it in double quotes
				$name = '"'.addcslashes($name, "\0..\37\177'\"\\").'"';
			}
			else
			{
				$name = $this->_prep_q_encoding($name, TRUE);
			}
		}

		$this->_set_header('From', $name.' <'.$from.'>');
		$this->_set_header('Return-Path', '<'.$from.'>');

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Reply-to
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function reply_to($replyto, $name = '')
	{
		if (preg_match( '/\<(.*)\>/', $replyto, $match))
		{
			$replyto = $match['1'];
		}

		if ($this->validate)
		{
			$this->validate_email($this->_str_to_array($replyto));
		}

		if ($name == '')
		{
			$name = $replyto;
		}

		if (strncmp($name, '"', 1) != 0)
		{
			$name = '"'.$name.'"';
		}

		$this->_set_header('Reply-To', $name.' <'.$replyto.'>');
		$this->_replyto_flag = TRUE;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Recipients
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function to($to)
	{
		$to = $this->_str_to_array($to);
		$to = $this->clean_email($to);

		if ($this->validate)
		{
			$this->validate_email($to);
		}

		if ($this->_get_protocol() != 'mail')
		{
			$this->_set_header('To', implode(", ", $to));
		}

		switch ($this->_get_protocol())
		{
			case 'smtp'		:
				$this->_recipients = $to;
			break;
			case 'sendmail'	:
			case 'mail'		:
				$this->_recipients = implode(", ", $to);
			break;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set CC
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function cc($cc)
	{
		$cc = $this->_str_to_array($cc);
		$cc = $this->clean_email($cc);

		if ($this->validate)
		{
			$this->validate_email($cc);
		}

		$this->_set_header('Cc', implode(", ", $cc));

		if ($this->_get_protocol() == "smtp")
		{
			$this->_cc_array = $cc;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set BCC
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function bcc($bcc, $limit = '')
	{
		if ($limit != '' && is_numeric($limit))
		{
			$this->bcc_batch_mode = TRUE;
			$this->bcc_batch_size = $limit;
		}

		$bcc = $this->_str_to_array($bcc);
		$bcc = $this->clean_email($bcc);

		if ($this->validate)
		{
			$this->validate_email($bcc);
		}

		if (($this->_get_protocol() == "smtp") OR ($this->bcc_batch_mode && count($bcc) > $this->bcc_batch_size))
		{
			$this->_bcc_array = $bcc;
		}
		else
		{
			$this->_set_header('Bcc', implode(", ", $bcc));
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Email Subject
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function subject($subject)
	{
		$subject = $this->_prep_q_encoding($subject);
		$this->_set_header('Subject', $subject);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Body
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function message($body)
	{
		$this->_body = rtrim(str_replace("\r", "", $body));

		/* strip slashes only if magic quotes is ON
		   if we do it with magic quotes OFF, it strips real, user-inputted chars.

		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			 it will probably not exist in future versions at all.
		*/
		if ( ! is_php('5.4') && get_magic_quotes_gpc())
		{
			$this->_body = stripslashes($this->_body);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Assign file attachments
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function attach($filename, $disposition = 'attachment')
	{
		$this->_attach_name[] = $filename;
		$this->_attach_type[] = $this->_mime_types(pathinfo($filename, PATHINFO_EXTENSION));
		$this->_attach_disp[] = $disposition; // Can also be 'inline'  Not sure if it matters
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a Header Item
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	protected function _set_header($header, $value)
	{
		$this->_headers[$header] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Convert a String to an Array
	 *
	 * @access	protected
	 * @param	string
	 * @return	array
	 */
	protected function _str_to_array($email)
	{
		if ( ! is_array($email))
		{
			if (strpos($email, ',') !== FALSE)
			{
				$email = preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY);
			}
			else
			{
				$email = trim($email);
				settype($email, "array");
			}
		}
		return $email;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Multipart Value
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_alt_message($str = '')
	{
		$this->alt_message = $str;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Mailtype
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_mailtype($type = 'text')
	{
		$this->mailtype = ($type == 'html') ? 'html' : 'text';
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Wordwrap
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_wordwrap($wordwrap = TRUE)
	{
		$this->wordwrap = ($wordwrap === FALSE) ? FALSE : TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Protocol
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_protocol($protocol = 'mail')
	{
		$this->protocol = ( ! in_array($protocol, $this->_protocols, TRUE)) ? 'mail' : strtolower($protocol);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Priority
	 *
	 * @access	public
	 * @param	integer
	 * @return	void
	 */
	public function set_priority($n = 3)
	{
		if ( ! is_numeric($n))
		{
			$this->priority = 3;
			return;
		}

		if ($n < 1 OR $n > 5)
		{
			$this->priority = 3;
			return;
		}

		$this->priority = $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Newline Character
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_newline($newline = "\n")
	{
		if ($newline != "\n" AND $newline != "\r\n" AND $newline != "\r")
		{
			$this->newline	= "\n";
			return;
		}

		$this->newline	= $newline;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set CRLF
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_crlf($crlf = "\n")
	{
		if ($crlf != "\n" AND $crlf != "\r\n" AND $crlf != "\r")
		{
			$this->crlf	= "\n";
			return;
		}

		$this->crlf	= $crlf;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Message Boundary
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _set_boundaries()
	{
		$this->_alt_boundary = "B_ALT_".uniqid(''); // multipart/alternative
		$this->_atc_boundary = "B_ATC_".uniqid(''); // attachment boundary
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Message ID
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_message_id()
	{
		$from = $this->_headers['Return-Path'];
		$from = str_replace(">", "", $from);
		$from = str_replace("<", "", $from);

		return  "<".uniqid('').strstr($from, '@').">";
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mail Protocol
	 *
	 * @access	protected
	 * @param	bool
	 * @return	string
	 */
	protected function _get_protocol($return = TRUE)
	{
		$this->protocol = strtolower($this->protocol);
		$this->protocol = ( ! in_array($this->protocol, $this->_protocols, TRUE)) ? 'mail' : $this->protocol;

		if ($return == TRUE)
		{
			return $this->protocol;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mail Encoding
	 *
	 * @access	protected
	 * @param	bool
	 * @return	string
	 */
	protected function _get_encoding($return = TRUE)
	{
		$this->_encoding = ( ! in_array($this->_encoding, $this->_bit_depths)) ? '8bit' : $this->_encoding;

		foreach ($this->_base_charsets as $charset)
		{
			if (strncmp($charset, $this->charset, strlen($charset)) == 0)
			{
				$this->_encoding = '7bit';
			}
		}

		if ($return == TRUE)
		{
			return $this->_encoding;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get content type (text/html/attachment)
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_content_type()
	{
		if	($this->mailtype == 'html' &&  count($this->_attach_name) == 0)
		{
			return 'html';
		}
		elseif	($this->mailtype == 'html' &&  count($this->_attach_name)  > 0)
		{
			return 'html-attach';
		}
		elseif	($this->mailtype == 'text' &&  count($this->_attach_name)  > 0)
		{
			return 'plain-attach';
		}
		else
		{
			return 'plain';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set RFC 822 Date
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _set_date()
	{
		$timezone = date("Z");
		$operator = (strncmp($timezone, '-', 1) == 0) ? '-' : '+';
		$timezone = abs($timezone);
		$timezone = floor($timezone/3600) * 100 + ($timezone % 3600 ) / 60;

		return sprintf("%s %s%04d", date("D, j M Y H:i:s"), $operator, $timezone);
	}

	// --------------------------------------------------------------------

	/**
	 * Mime message
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_mime_message()
	{
		return "This is a multi-part message in MIME format.".$this->newline."Your email application may not support this format.";
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Email Address
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function validate_email($email)
	{
		if ( ! is_array($email))
		{
			$this->_set_error_message('lang:email_must_be_array');
			return FALSE;
		}

		foreach ($email as $val)
		{
			if ( ! $this->valid_email($val))
			{
				$this->_set_error_message('lang:email_invalid_address', $val);
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Email Validation
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($address)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Clean Extended Email Address: Joe Smith <joe@smith.com>
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function clean_email($email)
	{
		if ( ! is_array($email))
		{
			if (preg_match('/\<(.*)\>/', $email, $match))
			{
				return $match['1'];
			}
			else
			{
				return $email;
			}
		}

		$clean_email = array();

		foreach ($email as $addy)
		{
			if (preg_match( '/\<(.*)\>/', $addy, $match))
			{
				$clean_email[] = $match['1'];
			}
			else
			{
				$clean_email[] = $addy;
			}
		}

		return $clean_email;
	}

	// --------------------------------------------------------------------

	/**
	 * Build alternative plain text message
	 *
	 * This public function provides the raw message for use
	 * in plain-text headers of HTML-formatted emails.
	 * If the user hasn't specified his own alternative message
	 * it creates one by stripping the HTML
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_alt_message()
	{
		if ($this->alt_message != "")
		{
			return $this->word_wrap($this->alt_message, '76');
		}

		if (preg_match('/\<body.*?\>(.*)\<\/body\>/si', $this->_body, $match))
		{
			$body = $match['1'];
		}
		else
		{
			$body = $this->_body;
		}

		$body = trim(strip_tags($body));
		$body = preg_replace( '#<!--(.*)--\>#', "", $body);
		$body = str_replace("\t", "", $body);

		for ($i = 20; $i >= 3; $i--)
		{
			$n = "";

			for ($x = 1; $x <= $i; $x ++)
			{
				$n .= "\n";
			}

			$body = str_replace($n, "\n\n", $body);
		}

		return $this->word_wrap($body, '76');
	}

	// --------------------------------------------------------------------

	/**
	 * Word Wrap
	 *
	 * @access	public
	 * @param	string
	 * @param	integer
	 * @return	string
	 */
	public function word_wrap($str, $charlim = '')
	{
		// Se the character limit
		if ($charlim == '')
		{
			$charlim = ($this->wrapchars == "") ? "76" : $this->wrapchars;
		}

		// Reduce multiple spaces
		$str = preg_replace("| +|", " ", $str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// If the current word is surrounded by {unwrap} tags we'll
		// strip the entire chunk and replace it with a marker.
		$unwrap = array();
		if (preg_match_all("|(\{unwrap\}.+?\{/unwrap\})|s", $str, $matches))
		{
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$unwrap[] = $matches['1'][$i];
				$str = str_replace($matches['1'][$i], "{{unwrapped".$i."}}", $str);
			}
		}

		// Use PHP's native public function to do the initial wordwrap.
		// We set the cut flag to FALSE so that any individual words that are
		// too long get left alone.  In the next step we'll deal with them.
		$str = wordwrap($str, $charlim, "\n", FALSE);

		// Split the string into individual lines of text and cycle through them
		$output = "";
		foreach (explode("\n", $str) as $line)
		{
			// Is the line within the allowed character count?
			// If so we'll join it to the output and continue
			if (strlen($line) <= $charlim)
			{
				$output .= $line.$this->newline;
				continue;
			}

			$temp = '';
			while ((strlen($line)) > $charlim)
			{
				// If the over-length word is a URL we won't wrap it
				if (preg_match("!\[url.+\]|://|wwww.!", $line))
				{
					break;
				}

				// Trim the word down
				$temp .= substr($line, 0, $charlim-1);
				$line = substr($line, $charlim-1);
			}

			// If $temp contains data it means we had to split up an over-length
			// word into smaller chunks so we'll add it back to our current line
			if ($temp != '')
			{
				$output .= $temp.$this->newline.$line;
			}
			else
			{
				$output .= $line;
			}

			$output .= $this->newline;
		}

		// Put our markers back
		if (count($unwrap) > 0)
		{
			foreach ($unwrap as $key => $val)
			{
				$output = str_replace("{{unwrapped".$key."}}", $val, $output);
			}
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Build final headers
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _build_headers()
	{
		$this->_set_header('X-Sender', $this->clean_email($this->_headers['From']));
		$this->_set_header('X-Mailer', $this->useragent);
		$this->_set_header('X-Priority', $this->_priorities[$this->priority - 1]);
		$this->_set_header('Message-ID', $this->_get_message_id());
		$this->_set_header('Mime-Version', '1.0');
	}

	// --------------------------------------------------------------------

	/**
	 * Write Headers as a string
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _write_headers()
	{
		if ($this->protocol == 'mail')
		{
			$this->_subject = $this->_headers['Subject'];
			unset($this->_headers['Subject']);
		}

		reset($this->_headers);
		$this->_header_str = "";

		foreach ($this->_headers as $key => $val)
		{
			$val = trim($val);

			if ($val != "")
			{
				$this->_header_str .= $key.": ".$val.$this->newline;
			}
		}

		if ($this->_get_protocol() == 'mail')
		{
			$this->_header_str = rtrim($this->_header_str);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Build Final Body and attachments
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _build_message()
	{
		if ($this->wordwrap === TRUE  AND  $this->mailtype != 'html')
		{
			$this->_body = $this->word_wrap($this->_body);
		}

		$this->_set_boundaries();
		$this->_write_headers();

		$hdr = ($this->_get_protocol() == 'mail') ? $this->newline : '';
		$body = '';

		switch ($this->_get_content_type())
		{
			case 'plain' :

				$hdr .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
				$hdr .= "Content-Transfer-Encoding: " . $this->_get_encoding();

				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= rtrim($hdr);
					$this->_finalbody = $this->_body;
				}
				else
				{
					$this->_finalbody = $hdr . $this->newline . $this->newline . $this->_body;
				}

				return;

			break;
			case 'html' :

				if ($this->send_multipart === FALSE)
				{
					$hdr .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
					$hdr .= "Content-Transfer-Encoding: quoted-printable";
				}
				else
				{
					$hdr .= "Content-Type: multipart/alternative; boundary=\"" . $this->_alt_boundary . "\"" . $this->newline . $this->newline;

					$body .= $this->_get_mime_message() . $this->newline . $this->newline;
					$body .= "--" . $this->_alt_boundary . $this->newline;

					$body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
					$body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;
					$body .= $this->_get_alt_message() . $this->newline . $this->newline . "--" . $this->_alt_boundary . $this->newline;

					$body .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
					$body .= "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;
				}

				$this->_finalbody = $body . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;


				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= rtrim($hdr);
				}
				else
				{
					$this->_finalbody = $hdr . $this->_finalbody;
				}


				if ($this->send_multipart !== FALSE)
				{
					$this->_finalbody .= "--" . $this->_alt_boundary . "--";
				}

				return;

			break;
			case 'plain-attach' :

				$hdr .= "Content-Type: multipart/".$this->multipart."; boundary=\"" . $this->_atc_boundary."\"" . $this->newline . $this->newline;

				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= rtrim($hdr);
				}

				$body .= $this->_get_mime_message() . $this->newline . $this->newline;
				$body .= "--" . $this->_atc_boundary . $this->newline;

				$body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
				$body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;

				$body .= $this->_body . $this->newline . $this->newline;

			break;
			case 'html-attach' :

				$hdr .= "Content-Type: multipart/".$this->multipart."; boundary=\"" . $this->_atc_boundary."\"" . $this->newline . $this->newline;

				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= rtrim($hdr);
				}

				$body .= $this->_get_mime_message() . $this->newline . $this->newline;
				$body .= "--" . $this->_atc_boundary . $this->newline;

				$body .= "Content-Type: multipart/alternative; boundary=\"" . $this->_alt_boundary . "\"" . $this->newline .$this->newline;
				$body .= "--" . $this->_alt_boundary . $this->newline;

				$body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
				$body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;
				$body .= $this->_get_alt_message() . $this->newline . $this->newline . "--" . $this->_alt_boundary . $this->newline;

				$body .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
				$body .= "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;

				$body .= $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;
				$body .= "--" . $this->_alt_boundary . "--" . $this->newline . $this->newline;

			break;
		}

		$attachment = array();

		$z = 0;

		for ($i=0; $i < count($this->_attach_name); $i++)
		{
			$filename = $this->_attach_name[$i];
			$basename = basename($filename);
			$ctype = $this->_attach_type[$i];

			if ( ! file_exists($filename))
			{
				$this->_set_error_message('lang:email_attachment_missing', $filename);
				return FALSE;
			}

			$h  = "--".$this->_atc_boundary.$this->newline;
			$h .= "Content-type: ".$ctype."; ";
			$h .= "name=\"".$basename."\"".$this->newline;
			$h .= "Content-Disposition: ".$this->_attach_disp[$i].";".$this->newline;
			$h .= "Content-Transfer-Encoding: base64".$this->newline;

			$attachment[$z++] = $h;
			$file = filesize($filename) +1;

			if ( ! $fp = fopen($filename, FOPEN_READ))
			{
				$this->_set_error_message('lang:email_attachment_unreadable', $filename);
				return FALSE;
			}

			$attachment[$z++] = chunk_split(base64_encode(fread($fp, $file)));
			fclose($fp);
		}

		$body .= implode($this->newline, $attachment).$this->newline."--".$this->_atc_boundary."--";


		if ($this->_get_protocol() == 'mail')
		{
			$this->_finalbody = $body;
		}
		else
		{
			$this->_finalbody = $hdr . $body;
		}

		return;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Quoted Printable
	 *
	 * Prepares string for Quoted-Printable Content-Transfer-Encoding
	 * Refer to RFC 2045 http://www.ietf.org/rfc/rfc2045.txt
	 *
	 * @access	protected
	 * @param	string
	 * @param	integer
	 * @return	string
	 */
	protected function _prep_quoted_printable($str, $charlim = '')
	{
		// Set the character limit
		// Don't allow over 76, as that will make servers and MUAs barf
		// all over quoted-printable data
		if ($charlim == '' OR $charlim > '76')
		{
			$charlim = '76';
		}

		// Reduce multiple spaces
		$str = preg_replace("| +|", " ", $str);

		// kill nulls
		$str = preg_replace('/\x00+/', '', $str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// We are intentionally wrapping so mail servers will encode characters
		// properly and MUAs will behave, so {unwrap} must go!
		$str = str_replace(array('{unwrap}', '{/unwrap}'), '', $str);

		// Break into an array of lines
		$lines = explode("\n", $str);

		$escape = '=';
		$output = '';

		foreach ($lines as $line)
		{
			$length = strlen($line);
			$temp = '';

			// Loop through each character in the line to add soft-wrap
			// characters at the end of a line " =\r\n" and add the newly
			// processed line(s) to the output (see comment on $crlf class property)
			for ($i = 0; $i < $length; $i++)
			{
				// Grab the next character
				$char = substr($line, $i, 1);
				$ascii = ord($char);

				// Convert spaces and tabs but only if it's the end of the line
				if ($i == ($length - 1))
				{
					$char = ($ascii == '32' OR $ascii == '9') ? $escape.sprintf('%02s', dechex($ascii)) : $char;
				}

				// encode = signs
				if ($ascii == '61')
				{
					$char = $escape.strtoupper(sprintf('%02s', dechex($ascii)));  // =3D
				}

				// If we're at the character limit, add the line to the output,
				// reset our temp variable, and keep on chuggin'
				if ((strlen($temp) + strlen($char)) >= $charlim)
				{
					$output .= $temp.$escape.$this->crlf;
					$temp = '';
				}

				// Add the character to our temporary line
				$temp .= $char;
			}

			// Add our completed line to the output
			$output .= $temp.$this->crlf;
		}

		// get rid of extra CRLF tacked onto the end
		$output = substr($output, 0, strlen($this->crlf) * -1);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Q Encoding
	 *
	 * Performs "Q Encoding" on a string for use in email headers.  It's related
	 * but not identical to quoted-printable, so it has its own method
	 *
	 * @access	public
	 * @param	str
	 * @param	bool	// set to TRUE for processing From: headers
	 * @return	str
	 */
	protected function _prep_q_encoding($str, $from = FALSE)
	{
		$str = str_replace(array("\r", "\n"), array('', ''), $str);

		// Line length must not exceed 76 characters, so we adjust for
		// a space, 7 extra characters =??Q??=, and the charset that we will add to each line
		$limit = 75 - 7 - strlen($this->charset);

		// these special characters must be converted too
		$convert = array('_', '=', '?');

		if ($from === TRUE)
		{
			$convert[] = ',';
			$convert[] = ';';
		}

		$output = '';
		$temp = '';

		for ($i = 0, $length = strlen($str); $i < $length; $i++)
		{
			// Grab the next character
			$char = substr($str, $i, 1);
			$ascii = ord($char);

			// convert ALL non-printable ASCII characters and our specials
			if ($ascii < 32 OR $ascii > 126 OR in_array($char, $convert))
			{
				$char = '='.dechex($ascii);
			}

			// handle regular spaces a bit more compactly than =20
			if ($ascii == 32)
			{
				$char = '_';
			}

			// If we're at the character limit, add the line to the output,
			// reset our temp variable, and keep on chuggin'
			if ((strlen($temp) + strlen($char)) >= $limit)
			{
				$output .= $temp.$this->crlf;
				$temp = '';
			}

			// Add the character to our temporary line
			$temp .= $char;
		}

		$str = $output.$temp;

		// wrap each line with the shebang, charset, and transfer encoding
		// the preceding space on successive lines is required for header "folding"
		$str = trim(preg_replace('/^(.*)$/m', ' =?'.$this->charset.'?Q?$1?=', $str));

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Send Email
	 *
	 * @access	public
	 * @return	bool
	 */
	public function send()
	{
		if ($this->_replyto_flag == FALSE)
		{
			$this->reply_to($this->_headers['From']);
		}

		if (( ! isset($this->_recipients) AND ! isset($this->_headers['To']))  AND
			( ! isset($this->_bcc_array) AND ! isset($this->_headers['Bcc'])) AND
			( ! isset($this->_headers['Cc'])))
		{
			$this->_set_error_message('lang:email_no_recipients');
			return FALSE;
		}

		$this->_build_headers();

		if ($this->bcc_batch_mode  AND  count($this->_bcc_array) > 0)
		{
			if (count($this->_bcc_array) > $this->bcc_batch_size)
				return $this->batch_bcc_send();
		}

		$this->_build_message();

		if ( ! $this->_spool_email())
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Batch Bcc Send.  Sends groups of BCCs in batches
	 *
	 * @access	public
	 * @return	bool
	 */
	public function batch_bcc_send()
	{
		$float = $this->bcc_batch_size -1;

		$set = "";

		$chunk = array();

		for ($i = 0; $i < count($this->_bcc_array); $i++)
		{
			if (isset($this->_bcc_array[$i]))
			{
				$set .= ", ".$this->_bcc_array[$i];
			}

			if ($i == $float)
			{
				$chunk[] = substr($set, 1);
				$float = $float + $this->bcc_batch_size;
				$set = "";
			}

			if ($i == count($this->_bcc_array)-1)
			{
				$chunk[] = substr($set, 1);
			}
		}

		for ($i = 0; $i < count($chunk); $i++)
		{
			unset($this->_headers['Bcc']);
			unset($bcc);

			$bcc = $this->_str_to_array($chunk[$i]);
			$bcc = $this->clean_email($bcc);

			if ($this->protocol != 'smtp')
			{
				$this->_set_header('Bcc', implode(", ", $bcc));
			}
			else
			{
				$this->_bcc_array = $bcc;
			}

			$this->_build_message();
			$this->_spool_email();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Unwrap special elements
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _unwrap_specials()
	{
		$this->_finalbody = preg_replace_callback("/\{unwrap\}(.*?)\{\/unwrap\}/si", array($this, '_remove_nl_callback'), $this->_finalbody);
	}

	// --------------------------------------------------------------------

	/**
	 * Strip line-breaks via callback
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _remove_nl_callback($matches)
	{
		if (strpos($matches[1], "\r") !== FALSE OR strpos($matches[1], "\n") !== FALSE)
		{
			$matches[1] = str_replace(array("\r\n", "\r", "\n"), '', $matches[1]);
		}

		return $matches[1];
	}

	// --------------------------------------------------------------------

	/**
	 * Spool mail to the mail server
	 *
	 * @access	protected
	 * @return	bool
	 */
	protected function _spool_email()
	{
		$this->_unwrap_specials();

		switch ($this->_get_protocol())
		{
			case 'mail'	:

					if ( ! $this->_send_with_mail())
					{
						$this->_set_error_message('lang:email_send_failure_phpmail');
						return FALSE;
					}
			break;
			case 'sendmail'	:

					if ( ! $this->_send_with_sendmail())
					{
						$this->_set_error_message('lang:email_send_failure_sendmail');
						return FALSE;
					}
			break;
			case 'smtp'	:

					if ( ! $this->_send_with_smtp())
					{
						$this->_set_error_message('lang:email_send_failure_smtp');
						return FALSE;
					}
			break;

		}

		$this->_set_error_message('lang:email_sent', $this->_get_protocol());
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send using mail()
	 *
	 * @access	protected
	 * @return	bool
	 */
	protected function _send_with_mail()
	{
		if ($this->_safe_mode == TRUE)
		{
			if ( ! mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str))
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			// most documentation of sendmail using the "-f" flag lacks a space after it, however
			// we've encountered servers that seem to require it to be in place.

			if ( ! mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str, "-f ".$this->clean_email($this->_headers['From'])))
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Send using Sendmail
	 *
	 * @access	protected
	 * @return	bool
	 */
	protected function _send_with_sendmail()
	{
		$fp = @popen($this->mailpath . " -oi -f ".$this->clean_email($this->_headers['From'])." -t", 'w');

		if ($fp === FALSE OR $fp === NULL)
		{
			// server probably has popen disabled, so nothing we can do to get a verbose error.
			return FALSE;
		}

		fputs($fp, $this->_header_str);
		fputs($fp, $this->_finalbody);

		$status = pclose($fp);

		if (version_compare(PHP_VERSION, '4.2.3') == -1)
		{
			$status = $status >> 8 & 0xFF;
		}

		if ($status != 0)
		{
			$this->_set_error_message('lang:email_exit_status', $status);
			$this->_set_error_message('lang:email_no_socket');
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send using SMTP
	 *
	 * @access	protected
	 * @return	bool
	 */
	protected function _send_with_smtp()
	{
		if ($this->smtp_host == '')
		{
			$this->_set_error_message('lang:email_no_hostname');
			return FALSE;
		}

		$this->_smtp_connect();
		$this->_smtp_authenticate();

		$this->_send_command('from', $this->clean_email($this->_headers['From']));

		foreach ($this->_recipients as $val)
		{
			$this->_send_command('to', $val);
		}

		if (count($this->_cc_array) > 0)
		{
			foreach ($this->_cc_array as $val)
			{
				if ($val != "")
				{
					$this->_send_command('to', $val);
				}
			}
		}

		if (count($this->_bcc_array) > 0)
		{
			foreach ($this->_bcc_array as $val)
			{
				if ($val != "")
				{
					$this->_send_command('to', $val);
				}
			}
		}

		$this->_send_command('data');

		// perform dot transformation on any lines that begin with a dot
		$this->_send_data($this->_header_str . preg_replace('/^\./m', '..$1', $this->_finalbody));

		$this->_send_data('.');

		$reply = $this->_get_smtp_data();

		$this->_set_error_message($reply);

		if (strncmp($reply, '250', 3) != 0)
		{
			$this->_set_error_message('lang:email_smtp_error', $reply);
			return FALSE;
		}

		$this->_send_command('quit');
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * SMTP Connect
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _smtp_connect()
	{
		$ssl = NULL;
		if ($this->smtp_crypto == 'ssl')
			$ssl = 'ssl://';
		$this->_smtp_connect = fsockopen($ssl.$this->smtp_host,
										$this->smtp_port,
										$errno,
										$errstr,
										$this->smtp_timeout);

		if ( ! is_resource($this->_smtp_connect))
		{
			$this->_set_error_message('lang:email_smtp_error', $errno." ".$errstr);
			return FALSE;
		}

		$this->_set_error_message($this->_get_smtp_data());

		if ($this->smtp_crypto == 'tls')
		{
			$this->_send_command('hello');
			$this->_send_command('starttls');
			stream_socket_enable_crypto($this->_smtp_connect, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT);
		}

		return $this->_send_command('hello');
	}

	// --------------------------------------------------------------------

	/**
	 * Send SMTP command
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _send_command($cmd, $data = '')
	{
		switch ($cmd)
		{
			case 'hello' :

					if ($this->_smtp_auth OR $this->_get_encoding() == '8bit')
						$this->_send_data('EHLO '.$this->_get_hostname());
					else
						$this->_send_data('HELO '.$this->_get_hostname());

						$resp = 250;
			break;
			case 'starttls'	:

						$this->_send_data('STARTTLS');

						$resp = 220;
			break;
			case 'from' :

						$this->_send_data('MAIL FROM:<'.$data.'>');

						$resp = 250;
			break;
			case 'to'	:

						$this->_send_data('RCPT TO:<'.$data.'>');

						$resp = 250;
			break;
			case 'data'	:

						$this->_send_data('DATA');

						$resp = 354;
			break;
			case 'quit'	:

						$this->_send_data('QUIT');

						$resp = 221;
			break;
		}

		$reply = $this->_get_smtp_data();

		$this->_debug_msg[] = "<pre>".$cmd.": ".$reply."</pre>";

		if (substr($reply, 0, 3) != $resp)
		{
			$this->_set_error_message('lang:email_smtp_error', $reply);
			return FALSE;
		}

		if ($cmd == 'quit')
		{
			fclose($this->_smtp_connect);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 *  SMTP Authenticate
	 *
	 * @access	protected
	 * @return	bool
	 */
	protected function _smtp_authenticate()
	{
		if ( ! $this->_smtp_auth)
		{
			return TRUE;
		}

		if ($this->smtp_user == ""  AND  $this->smtp_pass == "")
		{
			$this->_set_error_message('lang:email_no_smtp_unpw');
			return FALSE;
		}

		$this->_send_data('AUTH LOGIN');

		$reply = $this->_get_smtp_data();

		if (strncmp($reply, '334', 3) != 0)
		{
			$this->_set_error_message('lang:email_failed_smtp_login', $reply);
			return FALSE;
		}

		$this->_send_data(base64_encode($this->smtp_user));

		$reply = $this->_get_smtp_data();

		if (strncmp($reply, '334', 3) != 0)
		{
			$this->_set_error_message('lang:email_smtp_auth_un', $reply);
			return FALSE;
		}

		$this->_send_data(base64_encode($this->smtp_pass));

		$reply = $this->_get_smtp_data();

		if (strncmp($reply, '235', 3) != 0)
		{
			$this->_set_error_message('lang:email_smtp_auth_pw', $reply);
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send SMTP data
	 *
	 * @access	protected
	 * @return	bool
	 */
	protected function _send_data($data)
	{
		if ( ! fwrite($this->_smtp_connect, $data . $this->newline))
		{
			$this->_set_error_message('lang:email_smtp_data_failure', $data);
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get SMTP data
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_smtp_data()
	{
		$data = "";

		while ($str = fgets($this->_smtp_connect, 512))
		{
			$data .= $str;

			if (substr($str, 3, 1) == " ")
			{
				break;
			}
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Hostname
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_hostname()
	{
		return (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'localhost.localdomain';
	}

	// --------------------------------------------------------------------

	/**
	 * Get IP
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_ip()
	{
		if ($this->_IP !== FALSE)
		{
			return $this->_IP;
		}

		$cip = (isset($_SERVER['HTTP_CLIENT_IP']) AND $_SERVER['HTTP_CLIENT_IP'] != "") ? $_SERVER['HTTP_CLIENT_IP'] : FALSE;
		$rip = (isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'] != "") ? $_SERVER['REMOTE_ADDR'] : FALSE;
		$fip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND $_SERVER['HTTP_X_FORWARDED_FOR'] != "") ? $_SERVER['HTTP_X_FORWARDED_FOR'] : FALSE;

		if ($cip && $rip)	$this->_IP = $cip;
		elseif ($rip)		$this->_IP = $rip;
		elseif ($cip)		$this->_IP = $cip;
		elseif ($fip)		$this->_IP = $fip;

		if (strpos($this->_IP, ',') !== FALSE)
		{
			$x = explode(',', $this->_IP);
			$this->_IP = end($x);
		}

		if ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $this->_IP))
		{
			$this->_IP = '0.0.0.0';
		}

		unset($cip);
		unset($rip);
		unset($fip);

		return $this->_IP;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Debug Message
	 *
	 * @access	public
	 * @return	string
	 */
	public function print_debugger()
	{
		$msg = '';

		if (count($this->_debug_msg) > 0)
		{
			foreach ($this->_debug_msg as $val)
			{
				$msg .= $val;
			}
		}

		$msg .= "<pre>".htmlspecialchars($this->_header_str)."\n".htmlspecialchars($this->_subject)."\n".htmlspecialchars($this->_finalbody).'</pre>';
		return $msg;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Message
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _set_error_message($msg, $val = '')
	{
		$CI =& get_instance();
		$CI->lang->load('email');

		if (substr($msg, 0, 5) != 'lang:' || FALSE === ($line = $CI->lang->line(substr($msg, 5))))
		{
			$this->_debug_msg[] = str_replace('%s', $val, $msg)."<br />";
		}
		else
		{
			$this->_debug_msg[] = str_replace('%s', $val, $line)."<br />";
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Mime Types
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _mime_types($ext = "")
	{
		$mimes = array(	'hqx'	=>	'application/mac-binhex40',
						'cpt'	=>	'application/mac-compactpro',
						'doc'	=>	'application/msword',
						'bin'	=>	'application/macbinary',
						'dms'	=>	'application/octet-stream',
						'lha'	=>	'application/octet-stream',
						'lzh'	=>	'application/octet-stream',
						'exe'	=>	'application/octet-stream',
						'class'	=>	'application/octet-stream',
						'psd'	=>	'application/octet-stream',
						'so'	=>	'application/octet-stream',
						'sea'	=>	'application/octet-stream',
						'dll'	=>	'application/octet-stream',
						'oda'	=>	'application/oda',
						'pdf'	=>	'application/pdf',
						'ai'	=>	'application/postscript',
						'eps'	=>	'application/postscript',
						'ps'	=>	'application/postscript',
						'smi'	=>	'application/smil',
						'smil'	=>	'application/smil',
						'mif'	=>	'application/vnd.mif',
						'xls'	=>	'application/vnd.ms-excel',
						'ppt'	=>	'application/vnd.ms-powerpoint',
						'wbxml'	=>	'application/vnd.wap.wbxml',
						'wmlc'	=>	'application/vnd.wap.wmlc',
						'dcr'	=>	'application/x-director',
						'dir'	=>	'application/x-director',
						'dxr'	=>	'application/x-director',
						'dvi'	=>	'application/x-dvi',
						'gtar'	=>	'application/x-gtar',
						'php'	=>	'application/x-httpd-php',
						'php4'	=>	'application/x-httpd-php',
						'php3'	=>	'application/x-httpd-php',
						'phtml'	=>	'application/x-httpd-php',
						'phps'	=>	'application/x-httpd-php-source',
						'js'	=>	'application/x-javascript',
						'swf'	=>	'application/x-shockwave-flash',
						'sit'	=>	'application/x-stuffit',
						'tar'	=>	'application/x-tar',
						'tgz'	=>	'application/x-tar',
						'xhtml'	=>	'application/xhtml+xml',
						'xht'	=>	'application/xhtml+xml',
						'zip'	=>	'application/zip',
						'mid'	=>	'audio/midi',
						'midi'	=>	'audio/midi',
						'mpga'	=>	'audio/mpeg',
						'mp2'	=>	'audio/mpeg',
						'mp3'	=>	'audio/mpeg',
						'aif'	=>	'audio/x-aiff',
						'aiff'	=>	'audio/x-aiff',
						'aifc'	=>	'audio/x-aiff',
						'ram'	=>	'audio/x-pn-realaudio',
						'rm'	=>	'audio/x-pn-realaudio',
						'rpm'	=>	'audio/x-pn-realaudio-plugin',
						'ra'	=>	'audio/x-realaudio',
						'rv'	=>	'video/vnd.rn-realvideo',
						'wav'	=>	'audio/x-wav',
						'bmp'	=>	'image/bmp',
						'gif'	=>	'image/gif',
						'jpeg'	=>	'image/jpeg',
						'jpg'	=>	'image/jpeg',
						'jpe'	=>	'image/jpeg',
						'png'	=>	'image/png',
						'tiff'	=>	'image/tiff',
						'tif'	=>	'image/tiff',
						'css'	=>	'text/css',
						'html'	=>	'text/html',
						'htm'	=>	'text/html',
						'shtml'	=>	'text/html',
						'txt'	=>	'text/plain',
						'text'	=>	'text/plain',
						'log'	=>	'text/plain',
						'rtx'	=>	'text/richtext',
						'rtf'	=>	'text/rtf',
						'xml'	=>	'text/xml',
						'xsl'	=>	'text/xml',
						'mpeg'	=>	'video/mpeg',
						'mpg'	=>	'video/mpeg',
						'mpe'	=>	'video/mpeg',
						'qt'	=>	'video/quicktime',
						'mov'	=>	'video/quicktime',
						'avi'	=>	'video/x-msvideo',
						'movie'	=>	'video/x-sgi-movie',
						'doc'	=>	'application/msword',
						'word'	=>	'application/msword',
						'xl'	=>	'application/excel',
						'eml'	=>	'message/rfc822'
					);

		return ( ! isset($mimes[strtolower($ext)])) ? "application/x-unknown-content-type" : $mimes[strtolower($ext)];
	}

}
// END CI_Email class

/* End of file Email.php */
/* Location: ./system/libraries/Email.php */
