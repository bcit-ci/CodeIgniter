<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates calendar class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Calendar'))
{
	require_once(BASEPATH.'libraries/Calendar'.EXT);
}

$obj =& get_instance();

$obj->calendar = new CI_Calendar();
$obj->ci_is_loaded[] = 'calendar';

?>