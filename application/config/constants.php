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
 * @since		Version 1.0
 * @filesource
 */

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are presented below, for
| those who wish to make use of them.  The CodeIgniter defaults were 
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.  The CodeIgniter values are defined last so you can 
| set them to values used by any of the other conventions, and do so 
| by name instead of value.
|
*/

/*
 * standard C/C++ library (stdlibc):
 */
/*
define('LIBC_EXIT_SUCCESS', 0);
define('LIBC_EXIT_FAILURE', 1); // generic errors
*/

/* 
 * BSD sysexits.h 
 */
/*
define('SYS_EX_OK', 0); // successful termination
define('SYS_EX_USAGE', 64); // command line usage error
define('SYS_EX_DATAERR', 65); // data format error
define('SYS_EX_NOINPUT', 66); // cannot open input
define('SYS_EX_NOUSER', 67); // specified user unknown
define('SYS_EX_NOHOST', 68); // specified host name unknown
define('SYS_EX_UNAVAILABLE', 69); // service unavailable
define('SYS_EX_SOFTWARE', 70); // internal software error
define('SYS_EX_OSERR', 71); // system error (e.g., can't fork)
define('SYS_EX_OSFILE', 72); // critical OS file missing
define('SYS_EX_CANTCREAT', 73); // can't create (user) output file
define('SYS_EX_IOERR', 74); // input/output error
define('SYS_EX_TEMPFAIL', 75); // temporary failure; user is invited to retry
define('SYS_EX_PROTOCOL', 76); // remote error in protocol
define('SYS_EX_NOPERM', 77); // permission denied
define('SYS_EX_CONFIG', 78); // configuration error
*/

/*
 * Bash scripting 
 */
/*
define('BASH_EXIT_SUCCESS', 0);
define('BASH_EXIT_ERROR', 1);
define('BASH_EXIT_BUILTIN_MISUSE', 2);
define('BASH_EXIT_CANT_EXEC', 126);
define('BASH_EXIT_CMD_NOT_FOUND', 127);
define('BASH_EXIT_INVALID_EXIT', 128);
define('BASH_EXIT_SIG_HUP', 129);
define('BASH_EXIT_SIG_INT', 130);
define('BASH_EXIT_SIG_QUIT', 131);
define('BASH_EXIT_SIG_ILL', 132);
define('BASH_EXIT_SIG_TRAP', 133);
define('BASH_EXIT_SIG_ABRT', 134);
define('BASH_EXIT_SIG_BUS', 135);
define('BASH_EXIT_SIG_FPE', 136);
define('BASH_EXIT_SIG_KILL', 137);
define('BASH_EXIT_SIG_USR1', 138);
define('BASH_EXIT_SIG_SEGV', 139);
define('BASH_EXIT_SIG_USR2', 140);
define('BASH_EXIT_SIG_PIPE', 141);
define('BASH_EXIT_SIG_ALRM', 142);
define('BASH_EXIT_SIG_TERM', 143);
define('BASH_EXIT_SIG_STKFLT', 144);
define('BASH_EXIT_SIG_CHLD', 145);
define('BASH_EXIT_SIG_CONT', 146);
define('BASH_EXIT_SIG_STOP', 147);
define('BASH_EXIT_SIG_TSTP', 148);
define('BASH_EXIT_SIG_TTIN', 149);
define('BASH_EXIT_SIG_TTOU', 150);
define('BASH_EXIT_SIG_URG', 151);
define('BASH_EXIT_SIG_XCPU', 152);
define('BASH_EXIT_SIG_XFSZ', 153);
define('BASH_EXIT_SIG_VTALRM', 154);
define('BASH_EXIT_SIG_PROF', 155);
define('BASH_EXIT_SIG_WINCH', 156);
define('BASH_EXIT_SIG_IO', 157);
define('BASH_EXIT_SIG_PWR', 158);
define('BASH_EXIT_SIG_SYS', 159);
*/
/*
 * BASH_EXIT_OUTOFRANGE would be 255, and mean an exit status code beyond 
 * the range of 0-255 was given.  However, this code CANNOT BE USED IN PHP,
 * so it isn't actually defined, even in a comment.
 */

/*
 * CodeIgniter defaults
 */
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_FAILURE', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_404', 4); // file not found; convenience value
define('EXIT_UNK_FILE', 4); // file not found
define('EXIT_UNK_CLASS', 5); // unknown class
define('EXIT_UNK_MEMBER', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
 

/* End of file constants.php */
/* Location: ./application/config/constants.php */