<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates encryption class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Encrypt'))
{
	require_once(BASEPATH.'libraries/Encrypt'.EXT);
}

$obj =& get_instance();
$obj->encrypt = new CI_Encrypt();
$obj->ci_is_loaded[] = 'encrypt';

?>