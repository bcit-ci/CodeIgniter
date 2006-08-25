<?php

error_reporting(E_ALL);

/*
|------------------------------------------------
| SYSTEM FOLDER NAME
|------------------------------------------------
|
| This variable must contain the name of your "system"
| folder. Include the path if the folder is not in the same 
| directory as this file.  No trailing slash
*/

	$system_folder = "system";

/*
|------------------------------------------------
| APPLICATION FOLDER NAME
|------------------------------------------------
|
| If you want this front controller to use a specific
| "application" folder you can set its name here.
| By doing so you can have multiple applications share
| a common set of Code Igniter system files.
| Note: It is assumed that your application folder will
| be located within the main system/application folder.
| For example, lets say you have two applications, 
| "foo" and "bar":
|
|  system/application/foo/
|  system/application/foo/config/
|  system/application/foo/controllers/
|  system/application/foo/errors/
|  system/application/foo/scripts/
|  system/application/foo/views/
|  system/application/bar/
|  system/application/bar/config/
|  system/application/bar/controllers/
|  system/application/bar/errors/
|  system/application/bar/scripts/
|  system/application/bar/views/
|
| If you would like to use the "foo" application you'll
| set the variable like this:
|
|	$application_folder = "foo";
|
*/

	$application_folder = "";

/*
|================================================
| END OF USER CONFIGURABLE SETTINGS
|================================================
*/

if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
{
	$system_folder = str_replace("\\", "/", realpath(dirname(__FILE__))).'/'.$system_folder;
}

if ($application_folder != '')
{
	$application_folder .= '/';
}

// Older versions of PHP don't support this so we'll explicitly define it
if ( ! defined('E_STRICT'))
{
	define('E_STRICT', 2048);
}

define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_folder.'/');
define('APPPATH', BASEPATH.'application/'.$application_folder);

require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;
?>