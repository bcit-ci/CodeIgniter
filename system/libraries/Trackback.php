<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
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
 * Trackback Class
 *
 * Trackback Sending/Receiving Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Trackbacks
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/trackback.html
 */
class CI_Trackback {

	var $time_format	= 'local';
	var $charset		= 'UTF-8';
	var $data			= array('url' => '', 'title' => '', 'excerpt' => '', 'blog_name' => '', 'charset' => '');
	var $convert_ascii	= TRUE;
	var $response		= '';
	var $error_msg		= array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		log_message('debug', "Trackback Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Send Trackback
	 *
	 * @access	public
	 * @param	array
	 * @return	bool
	 */
	function send($tb_data)
	{
		if ( ! is_array($tb_data))
		{
			$this->set_error('The send() method must be passed an array');
			return FALSE;
		}

		// Pre-process the Trackback Data
		foreach (array('url', 'title', 'excerpt', 'blog_name', 'ping_url') as $item)
		{
			if ( ! isset($tb_data[$item]))
			{
				$this->set_error('Required item missing: '.$item);
				return FALSE;
			}

			switch ($item)
			{
				case 'ping_url'	: $$item = $this->extract_urls($tb_data[$item]);
					break;
				case 'excerpt'	: $$item = $this->limit_characters($this->convert_xml(strip_tags(stripslashes($tb_data[$item]))));
					break;
				case 'url'		: $$item = str_replace('&#45;', '-', $this->convert_xml(strip_tags(stripslashes($tb_data[$item]))));
					break;
				default			: $$item = $this->convert_xml(strip_tags(stripslashes($tb_data[$item])));
					break;
			}

			// Convert High ASCII Characters
			if ($this->convert_ascii == TRUE)
			{
				if ($item == 'excerpt')
				{
					$$item = $this->convert_ascii($$item);
				}
				elseif ($item == 'title')
				{
					$$item = $this->convert_ascii($$item);
				}
				elseif($item == 'blog_name')
				{
					$$item = $this->convert_ascii($$item);
				}
			}
		}

		// Build the Trackback data string
		$charset = ( ! isset($tb_data['charset'])) ? $this->charset : $tb_data['charset'];

		$data = "url=".rawurlencode($url)."&title=".rawurlencode($title)."&blog_name=".rawurlencode($blog_name)."&excerpt=".rawurlencode($excerpt)."&charset=".rawurlencode($charset);

		// Send Trackback(s)
		$return = TRUE;
		if (count($ping_url) > 0)
		{
			foreach ($ping_url as $url)
			{
				if ($this->process($url, $data) == FALSE)
				{
					$return = FALSE;
				}
			}
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Receive Trackback  Data
	 *
	 * This function simply validates the incoming TB data.
	 * It returns FALSE on failure and TRUE on success.
	 * If the data is valid it is set to the $this->data array
	 * so that it can be inserted into a database.
	 *
	 * @access	public
	 * @return	bool
	 */
	function receive()
	{
		foreach (array('url', 'title', 'blog_name', 'excerpt') as $val)
		{
			if ( ! isset($_POST[$val]) OR $_POST[$val] == '')
			{
				$this->set_error('The following required POST variable is missing: '.$val);
				return FALSE;
			}

			$this->data['charset'] = ( ! isset($_POST['charset'])) ? 'auto' : strtoupper(trim($_POST['charset']));

			if ($val != 'url' && function_exists('mb_convert_encoding'))
			{
				$_POST[$val] = mb_convert_encoding($_POST[$val], $this->charset, $this->data['charset']);
			}

			$_POST[$val] = ($val != 'url') ? $this->convert_xml(strip_tags($_POST[$val])) : strip_tags($_POST[$val]);

			if ($val == 'excerpt')
			{
				$_POST['excerpt'] = $this->limit_characters($_POST['excerpt']);
			}

			$this->data[$val] = $_POST[$val];
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send Trackback Error Message
	 *
	 * Allows custom errors to be set.  By default it
	 * sends the "incomplete information" error, as that's
	 * the most common one.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function send_error($message = 'Incomplete Information')
	{
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">\n<response>\n<error>1</error>\n<message>".$message."</message>\n</response>";
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * Send Trackback Success Message
	 *
	 * This should be called when a trackback has been
	 * successfully received and inserted.
	 *
	 * @access	public
	 * @return	void
	 */
	function send_success()
	{
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">\n<response>\n<error>0</error>\n</response>";
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a particular item
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function data($item)
	{
		return ( ! isset($this->data[$item])) ? '' : $this->data[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Process Trackback
	 *
	 * Opens a socket connection and passes the data to
	 * the server.  Returns TRUE on success, FALSE on failure
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function process($url, $data)
	{
		$target = parse_url($url);

		// Open the socket
		if ( ! $fp = @fsockopen($target['host'], 80))
		{
			$this->set_error('Invalid Connection: '.$url);
			return FALSE;
		}

		// Build the path
		$ppath = ( ! isset($target['path'])) ? $url : $target['path'];

		$path = (isset($target['query']) && $target['query'] != "") ? $ppath.'?'.$target['query'] : $ppath;

		// Add the Trackback ID to the data string
		if ($id = $this->get_id($url))
		{
			$data = "tb_id=".$id."&".$data;
		}

		// Transfer the data
		fputs ($fp, "POST " . $path . " HTTP/1.0\r\n" );
		fputs ($fp, "Host: " . $target['host'] . "\r\n" );
		fputs ($fp, "Content-type: application/x-www-form-urlencoded\r\n" );
		fputs ($fp, "Content-length: " . strlen($data) . "\r\n" );
		fputs ($fp, "Connection: close\r\n\r\n" );
		fputs ($fp, $data);

		// Was it successful?
		$this->response = "";

		while( ! feof($fp))
		{
			$this->response .= fgets($fp, 128);
		}
		@fclose($fp);


		if (stristr($this->response, '<error>0</error>') === FALSE)
		{
			$message = 'An unknown error was encountered';

			if (preg_match("/<message>(.*?)<\/message>/is", $this->response, $match))
			{
				$message = trim($match['1']);
			}

			$this->set_error($message);
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Extract Trackback URLs
	 *
	 * This function lets multiple trackbacks be sent.
	 * It takes a string of URLs (separated by comma or
	 * space) and puts each URL into an array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function extract_urls($urls)
	{
		// Remove the pesky white space and replace with a comma.
		$urls = preg_replace("/\s*(\S+)\s*/", "\\1,", $urls);

		// If they use commas get rid of the doubles.
		$urls = str_replace(",,", ",", $urls);

		// Remove any comma that might be at the end
		if (substr($urls, -1) == ",")
		{
			$urls = substr($urls, 0, -1);
		}

		// Break into an array via commas
		$urls = preg_split('/[,]/', $urls);

		// Removes duplicates
		$urls = array_unique($urls);

		array_walk($urls, array($this, 'validate_url'));

		return $urls;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate URL
	 *
	 * Simply adds "http://" if missing
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function validate_url($url)
	{
		$url = trim($url);

		if (substr($url, 0, 4) != "http")
		{
			$url = "http://".$url;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Find the Trackback URL's ID
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function get_id($url)
	{
		$tb_id = "";

		if (strpos($url, '?') !== FALSE)
		{
			$tb_array = explode('/', $url);
			$tb_end   = $tb_array[count($tb_array)-1];

			if ( ! is_numeric($tb_end))
			{
				$tb_end  = $tb_array[count($tb_array)-2];
			}

			$tb_array = explode('=', $tb_end);
			$tb_id	= $tb_array[count($tb_array)-1];
		}
		else
		{
			$url = rtrim($url, '/');

			$tb_array = explode('/', $url);
			$tb_id	= $tb_array[count($tb_array)-1];

			if ( ! is_numeric($tb_id))
			{
				$tb_id  = $tb_array[count($tb_array)-2];
			}
		}

		if ( ! preg_match ("/^([0-9]+)$/", $tb_id))
		{
			return FALSE;
		}
		else
		{
			return $tb_id;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Convert Reserved XML characters to Entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function convert_xml($str)
	{
		$temp = '__TEMP_AMPERSANDS__';

		$str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
		$str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);

		$str = str_replace(array("&","<",">","\"", "'", "-"),
							array("&amp;", "&lt;", "&gt;", "&quot;", "&#39;", "&#45;"),
							$str);

		$str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
		$str = preg_replace("/$temp(\w+);/","&\\1;", $str);

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Character limiter
	 *
	 * Limits the string based on the character count. Will preserve complete words.
	 *
	 * @access	public
	 * @param	string
	 * @param	integer
	 * @param	string
	 * @return	string
	 */
	function limit_characters($str, $n = 500, $end_char = '&#8230;')
	{
		if (strlen($str) < $n)
		{
			return $str;
		}

		$str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

		if (strlen($str) <= $n)
		{
			return $str;
		}

		$out = "";
		foreach (explode(' ', trim($str)) as $val)
		{
			$out .= $val.' ';
			if (strlen($out) >= $n)
			{
				return trim($out).$end_char;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * High ASCII to Entities
	 *
	 * Converts Hight ascii text and MS Word special chars
	 * to character entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function convert_ascii($str)
	{
		$count	= 1;
		$out	= '';
		$temp	= array();

		for ($i = 0, $s = strlen($str); $i < $s; $i++)
		{
			$ordinal = ord($str[$i]);

			if ($ordinal < 128)
			{
				$out .= $str[$i];
			}
			else
			{
				if (count($temp) == 0)
				{
					$count = ($ordinal < 224) ? 2 : 3;
				}

				$temp[] = $ordinal;

				if (count($temp) == $count)
				{
					$number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);

					$out .= '&#'.$number.';';
					$count = 1;
					$temp = array();
				}
			}
		}

		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * Set error message
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function set_error($msg)
	{
		log_message('error', $msg);
		$this->error_msg[] = $msg;
	}

	// --------------------------------------------------------------------

	/**
	 * Show error messages
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function display_errors($open = '<p>', $close = '</p>')
	{
		$str = '';
		foreach ($this->error_msg as $val)
		{
			$str .= $open.$val.$close;
		}

		return $str;
	}

}
// END Trackback Class

/* End of file Trackback.php */
/* Location: ./system/libraries/Trackback.php */