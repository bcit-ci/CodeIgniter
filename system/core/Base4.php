<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.3
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CI_BASE - For PHP 4
 *
 * This file is used only when CodeIgniter is being run under PHP 4.
 *
 * In order to allow CI to work under PHP 4 we had to make the Loader class
 * the parent of the Controller Base class.  It's the only way we can
 * enable functions like $this->load->library('email') to instantiate
 * classes that can then be used within controllers as $this->email->send()
 *
 * PHP 4 also has trouble referencing the CI super object within application
 * constructors since objects do not exist until the class is fully
 * instantiated.  Basically PHP 4 sucks...
 *
 * Since PHP 5 doesn't suffer from this problem so we load one of
 * two files based on the version of PHP being run.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	front-controller
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */
 class CI_Base extends CI_Loader {

	function CI_Base()
	{
		// This allows syntax like $this->load->foo() to work
		parent::CI_Loader();
		$this->load =& $this;
		
		// This allows resources used within controller constructors to work
		global $OBJ;
		$OBJ = $this->load; // Do NOT use a reference.
	}
}

function &get_instance()
{
	global $CI, $OBJ;
	
	if (is_object($CI))
	{
		return $CI;
	}
	
	return $OBJ->load;
}


/* End of file Base4.php */
/* Location: ./system/codeigniter/Base4.php */