<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates parser class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Parser'))
{
	require_once(BASEPATH.'libraries/Parser'.EXT);
}

$obj =& get_instance();
$obj->parser = new CI_Parser();
$obj->ci_is_loaded[] = 'parser';


?>