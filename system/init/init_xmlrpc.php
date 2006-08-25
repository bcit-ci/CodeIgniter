<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates XML-RPC request class
 *
 * @access	private called by the app controller
 */	

$config = array();
if (file_exists(APPPATH.'config/xmlrpc'.EXT))
{
	include_once(APPPATH.'config/xmlrpc'.EXT);
}

if ( ! class_exists('CI_XML_RPC'))
{		
	require_once(BASEPATH.'libraries/Xmlrpc'.EXT);		
}

$obj =& get_instance();
$obj->xmlrpc = new CI_XML_RPC($config);
$obj->ci_is_loaded[] = 'xmlrpc';

?>