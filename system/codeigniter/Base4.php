<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.3
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * CI_BASE - For PHP 4
 * 
 * This file is used only when Code Igniter is being run under PHP 4.  
 * Since PHP 4 has such poor object handling we had to come up with 
 * a hack to resolve some scoping problems.  PHP 5 doesn't suffer from 
 * this problem so we load one of two files based on the version of 
 * PHP being run.
 * 
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	front-controller
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/
 */
 class CI_Base extends CI_Loader {

	function CI_Base()
	{
		global $OBJ; 
		parent::CI_Loader();
		$this->load =& $this;
		$OBJ = $this->load;
	}
}

function &get_instance()
{
	global $OBJ, $CI;
	
	if (is_object($CI))
	{
		return $CI;
	}
	else
	{
		return $OBJ->load;
	}
}

?>