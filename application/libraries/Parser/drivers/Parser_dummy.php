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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
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
class CI_Parser_dummy extends CI_Parser_driver {


	private $tmp;
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	protected function initialize()
	{
		$template_config = $this->template_config;	// get from parent's config
		$this->tmp = implode("\n", $template_config);
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
	public function parse($template, $data = array(), $return = FALSE)
	{
		echo $this->tmp . PHP_EOL;
		echo $template . PHP_EOL;
		print_r($data);
		if ($return)
			return 'dummy_return' . PHP_EOL;
		else
			echo 'dummy_parse' . PHP_EOL;
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
	public function parse_string($template, $data = array(), $return = FALSE)
	{
		echo $template . PHP_EOL;
		print_r($data);
		if ($return)
			return 'dummy_return_string' . PHP_EOL;
		else
			echo 'dummy_parse_string' . PHP_EOL;
	}
}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser/drivers/Parser_simple.php */