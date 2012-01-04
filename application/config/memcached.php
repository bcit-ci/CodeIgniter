<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| To add another server, just add another set in the array.
|
| NOTE: Memcached doesn't seem to like hostnames. Try using an IP first. 
| -GWB
|
*/

$config = array(
	'default' => array(
		'hostname' => '127.0.0.1',
		'port'     => '11211',
		'weight'   => '1',
	),      
);  

/* End of file memcached.php */
/* Location: ./application/config/memcached.php */