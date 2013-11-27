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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/view_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('title'))
{
	/**
	 * Title
	 *
	 * Get the view title based on the application name
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function title($title = NULL,$separator = '|')
	{

		$CI =& get_instance();
		$application_name = $CI->config->item('application_name');

		if($title == NULL){

			return $application_name;

		}else{

			return  ($application_name != '') ? ($application_name.' '.$separator.' '.$title) : $title;;

		}

	}
}



/* End of file view_helper.php */
/* Location: ./system/helpers/view_helper.php */