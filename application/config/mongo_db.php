<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your MongoDB
| database.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['port']     The port to connect to the server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['host_db_flag'] Whether to append database flag while building dsn
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['result_set'] The return format of the result set: array, object
|	['options'] The options while performing database queries:
|			['write_concern'] See: http://www.php.net/manual/en/mongo.writeconcerns.php
|			['fsync']         See: http://www.php.net/manual/en/mongocollection.insert.php
|			['timeout']       See: http://www.php.net/manual/en/mongocursor.timeout.php
|	['autoinit'] Whether or not to automatically initialize the database.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
*/

$config['active_group'] = 'default';

$config['default'] = array(
	'dsn'		=> '',
	'hostname'	=> 'localhost',
	'port'		=> 27017,
	'username'	=> '',
	'password'	=> '',
	'database'	=> '',
	'host_db_flag'	=> FALSE,
	'db_debug'		=> FALSE,
	'result_set'	=> 'array',
	'options'	=> array(
		'write_concern'	=> 1,
		'fsync'			=> FALSE,
		'timeout'		=> 10000
	),
	'autoinit'	=> TRUE,
);