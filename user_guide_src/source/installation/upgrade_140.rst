#############################
Upgrading from 1.3.3 to 1.4.0
#############################

.. note:: The instructions on this page assume you are running version
	1.3.3. If you have not upgraded to that version please do so first.

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace the following directories in your "system" folder with the new
versions:

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

-  application/config/**hooks.php**
-  application/config/**mimes.php**
-  codeigniter
-  drivers
-  helpers
-  init
-  language
-  libraries
-  scaffolding

Step 2: Update your config.php file
===================================

Open your application/config/config.php file and add these new items::



    /*
    |--------------------------------------------------------------------------
    | Enable/Disable System Hooks
    |--------------------------------------------------------------------------
    |
    | If you would like to use the "hooks" feature you must enable it by
    | setting this variable to TRUE (boolean).  See the user guide for details.
    |
    */
    $config['enable_hooks'] = FALSE;


    /*
    |--------------------------------------------------------------------------
    | Allowed URL Characters
    |--------------------------------------------------------------------------
    |
    | This lets you specify which characters are permitted within your URLs.
    | When someone tries to submit a URL with disallowed characters they will
    | get a warning message.
    |
    | As a security measure you are STRONGLY encouraged to restrict URLs to
    | as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
    |
    | Leave blank to allow all characters -- but only if you are insane.
    |
    | DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
    |
    */
    $config['permitted_uri_chars'] = 'a-z 0-9~%.:_-';

Step 3: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
