<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates trackback class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Trackback'))
{
	require_once(BASEPATH.'libraries/Trackback'.EXT);
}

$obj =& get_instance();
$obj->trackback = new CI_Trackback();
$obj->ci_is_loaded[] = 'trackback';

?>