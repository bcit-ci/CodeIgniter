<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates image manipulation class
 *
 * @access	private called by the app controller
 */	

$config = array();
if (file_exists(APPPATH.'config/image_lib'.EXT))
{
	include_once(APPPATH.'config/image_lib'.EXT);
}

if ( ! class_exists('CI_Image_lib'))
{	
	require_once(BASEPATH.'libraries/Image_lib'.EXT);
}

$obj =& get_instance();
$obj->image_lib = new CI_Image_lib($config);	
$obj->ci_is_loaded[] = 'image_lib';

?>