<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates session class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Session'))
{
	require_once(BASEPATH.'libraries/Session'.EXT);
}

$obj =& get_instance();
$obj->session = new CI_Session();
$obj->ci_is_loaded[] = 'session';

?>