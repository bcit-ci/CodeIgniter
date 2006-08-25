<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates XML-RPC server class
 *
 * @access	private called by the app controller
 */	

$config = array();
if (file_exists(APPPATH.'config/xmlrpcs'.EXT))
{
	include_once(APPPATH.'config/xmlrpcs'.EXT);
}

if ( ! class_exists('CI_XML_RPC_Server'))
{			
	require_once(BASEPATH.'libraries/Xmlrpc'.EXT);
	require_once(BASEPATH.'libraries/Xmlrpcs'.EXT);
}

$obj =& get_instance();
$obj->xmlrpc  = new CI_XML_RPC();
$obj->xmlrpcs = new CI_XML_RPC_Server($config);
$obj->ci_is_loaded[] = 'xmlrpc';
$obj->ci_is_loaded[] = 'xmlrpcs';

?>