<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates validation class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Validation'))
{
	require_once(BASEPATH.'libraries/Validation'.EXT);
}

$obj =& get_instance();
$obj->validation = new CI_Validation();
$obj->ci_is_loaded[] = 'validation';

?>