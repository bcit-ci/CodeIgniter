<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates unit testing class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Unit_test'))
{
	require_once(BASEPATH.'libraries/Unit_test'.EXT);
}

$obj =& get_instance();
$obj->unit = new CI_Unit_test();
$obj->ci_is_loaded[] = 'unit';

?>