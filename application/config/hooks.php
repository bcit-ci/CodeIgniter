<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['display_override'] = array(
    'class'    => 'Hooks',
    'function' => 'captureOutput',
    'filename' => 'Hooks.php',
    'filepath' => 'hooks'
);

/*$hook['pre_system'] = array(
    'class'    => 'Hooks',
    'function' => 'load_controllers_models',
    'filename' => 'Hooks.php',
    'filepath' => 'hooks',
    'params'   => array()
);*/
/* End of file hooks.php */
/* Location: ./application/config/hooks.php */