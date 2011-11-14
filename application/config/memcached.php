<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| Memcached doesn't seem to like hostnames. Try use an IP first. -GWB
|
*/

$config['default'] = array(
	'hostname' => '127.0.0.1',
	'port'     => '11211',
	'weight'   => '1'        
);  

/* End of file memcached.php */
/* Location: ./application/config/memcached.php */