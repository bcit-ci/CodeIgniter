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
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser {

	/**
	 * Left delimiter character for pseudo vars
	 *
	 * @var string
	 */
	public $l_delim = '{';

	/**
	 * Right delimiter character for pseudo vars
	 *
	 * @var string
	 */
	public $r_delim = '}';

	/**
	 * Reference to CodeIgniter instance
	 *
	 * @var object
	 */
	protected $CI;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template, $data, $return = FALSE)
	{
		$template = $this->CI->load->view($template, $data, TRUE);

		return $this->_parse($template, $data, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a String
	 *
	 * Parses pseudo-variables contained in the specified string,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_string($template, $data, $return = FALSE)
	{
		return $this->_parse($template, $data, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	protected function _parse($template, $data, $return = FALSE)
	{
		if ($template === '')
		{
			return FALSE;
		}

		$replace = array();
		foreach ($data as $key => $val)
		{
			$replace = array_merge(
				$replace,
				is_array($val)
					? $this->_parse_pair($key, $val, $template)
					: $this->_parse_single($key, (string) $val, $template)
			);
		}

		unset($data);
		$template = strtr($template, $replace);

		if ($return === FALSE)
		{
			$this->CI->output->append_output($template);
		}

		return $template;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the left/right variable delimiters
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a single key/value
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _parse_single($key, $val, $string)
	{
		return array($this->l_delim.$key.$this->r_delim => (string) $val);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a tag pair
	 *
	 * Parses tag pairs: {some_tag} string... {/some_tag}
	 *
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	protected function _parse_pair($variable, $data, $string)
	{
		$replace = array();
		preg_match_all(
			'#'.preg_quote($this->l_delim.$variable.$this->r_delim).'(.+?)'.preg_quote($this->l_delim.'/'.$variable.$this->r_delim).'#s',
			$string,
			$matches,
			PREG_SET_ORDER
		);

		foreach ($matches as $match)
		{
			$str = '';
			foreach ($data as $row)
			{
				$temp = array();
				foreach ($row as $key => $val)
				{
					if (is_array($val))
					{
						$pair = $this->_parse_pair($key, $val, $match[1]);
						if ( ! empty($pair))
						{
							$temp = array_merge($temp, $pair);
						}

						continue;
					}

					$temp[$this->l_delim.$key.$this->r_delim] = $val;
				}

				$str .= strtr($match[1], $temp);
			}

			$replace[$match[0]] = $str;
		}

		return $replace;
	}

}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser.php */