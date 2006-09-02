<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| This file specifies which systems should be loaded by default.
|
| In order to keep the framework as light-weight as possible only the
| absolute minimal resources are loaded by default. For example,
| the database is not connected to automatically since no assumption
| is made regarding whether you intend to use it.  This file lets
| you globally define which systems you would like loaded with every
| request. In addition to core systems you can auto-load plugins,
| helper files, config files, and your own scripts.
|
| -------------------------------------------------------------------
| Instructions
| -------------------------------------------------------------------
|
| These are the things you can load automatically:
|
| 1. Core classes
| 2. Helper files
| 3. Plugins
| 4. Scripts
| 5. Custom config files
|
| Note: The items will be loaded in the order that they are defined
|
| Please read the user guide for more detailed information
*/

/*
| -------------------------------------------------------------------
|  Auto-load Core Classes
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['core'] = array('database', 'session', 'xmlrpc');
*/

$autoload['core'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Helper Files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['helper'] = array('url', 'file');
*/

$autoload['helper'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Plugins
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['plugin'] = array('captcha', 'js_calendar');
*/

$autoload['plugin'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Scripts
| -------------------------------------------------------------------
| The term "scripts" refers to you own PHP scripts that you've 
| placed in the application/scripts/ folder
|
| Prototype:
|
|	$autoload['script'] = array('my_script1', 'my_script2');
*/

$autoload['script'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Config files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['config'] = array('config1', 'config2');
|
| NOTE: This item is intended for use ONLY if you have created custom
| config files.  Otherwise, leave it blank.
|
*/

$autoload['config'] = array();



?>