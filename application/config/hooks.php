<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once __DIR__ . "../../../vendor/autoload.php";
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = function () {
    $dotenv = Dotenv\Dotenv::create(APPPATH);
    $dotenv->load();
};
