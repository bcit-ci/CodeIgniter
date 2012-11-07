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
 * CodeIgniter Language Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/language_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('lang'))
{

	/**
	 * Lang
	 *
	 * Fetches a language variable and optionally replaces key->values
	 *
	 * @param	string	the language line
	 * @param	array	search keys -> replace values
	 * @return	string
	 */

	function lang($line, $repl=array())
	{
		$CI =& get_instance();
		$line = $CI->lang->line($line);

		if( count($repl) > 0)
		{
			$line = str_replace( array_keys($repl), array_values($repl), $line);
		}

		return $line;
	}

}

/* End of file language_helper.php */
/* Location: ./system/helpers/language_helper.php */