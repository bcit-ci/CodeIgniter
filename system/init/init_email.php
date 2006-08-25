<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates email class
 *
 * @access	private called by the app controller
 */	

$config = array();
if (file_exists(APPPATH.'config/email'.EXT))
{
	include_once(APPPATH.'config/email'.EXT);
}

if ( ! class_exists('CI_Email'))
{	
	require_once(BASEPATH.'libraries/Email'.EXT);		
}

$obj =& get_instance();
$obj->email = new CI_Email($config);
$obj->ci_is_loaded[] = 'email';

?>