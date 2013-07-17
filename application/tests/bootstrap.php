<?php

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 */

define('ENVIRONMENT', 'testing');

/*
 *---------------------------------------------------------------
 * DS CONSTANT VERIFICATION
 *---------------------------------------------------------------
 *  Verifying if DS constant don't exists
 *  - If yes, define it!
 */
if( !defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

/*
 *---------------------------------------------------------------
 * CODEIGNITER PATHS DEFINITIONS
 *---------------------------------------------------------------
 */

$system_path = '../../system';
$application_folder = '../../application';
$view_folder = $application_folder . '/views';

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (realpath($system_path) !== FALSE) {
    $system_path = realpath($system_path) . '/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/') . '/';

// Is the system path correct?
if (!is_dir($system_path)) {
    exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo(__FILE__, PATHINFO_BASENAME));
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


// The path to the "application" folder
if (is_dir($application_folder)) {
    define('APPPATH', $application_folder . '/');
}
else
{
    if (!is_dir(BASEPATH . $application_folder . '/')) {
        exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
    }

    define('APPPATH', BASEPATH . $application_folder . '/');
}

// The path to the "views" folder
if (is_dir($view_folder)) {
    define ('VIEWPATH', $view_folder . '/');
}
else
{
    if (!is_dir(APPPATH . 'views/')) {
        exit("Your view folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
    }

    define ('VIEWPATH', APPPATH . 'views/');
}
error_reporting(-1);



/*
 * --------------------------------------------------------------------
 * REQUIRE COMPOSER FOLDERS
 * --------------------------------------------------------------------
 *
 */
if ( file_exists(BASEPATH . '/../vendor' . DS . 'autoload.php') ) {
    require_once BASEPATH . '/../vendor' . DS . 'autoload.php';
}

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH . 'core/CodeIgniter.php';
/* End of file index.php */
/* Location: ./index.php */