<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| MEMCACHED CONFIG SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your memcached
| server.
|
| For complete instructions please consult the 'Caching Class' page
| of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your memcached server.
|	['port'] The port used to connect to memcached
|	['weight'] The priority of the server (useful when using multiple
|	servers).
*/


$config['default']['hostname'] = 'localhost';
$config['default']['port'] = 11211;
$config['default']['weight'] = 90;

/* Add more servers for load balanced memcached setup
$config['backup']['hostname'] = 'anotherhost';
$config['backup']['port'] = 11211;
$config['backup']['weight'] = 10;
*/

/* End of file memcached.php */
/* Location: ./application/config/memcached.php */