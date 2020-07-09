#############################
Upgrading from 1.4.1 to 1.5.0
#############################

.. note:: The instructions on this page assume you are running version
	1.4.1. If you have not upgraded to that version please do so first.

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace these files and directories in your "system" folder with the new
versions:

-  application/config/user_agents.php (new file for 1.5)
-  application/config/smileys.php (new file for 1.5)
-  codeigniter/
-  database/ (new folder for 1.5. Replaces the "drivers" folder)
-  helpers/
-  language/
-  libraries/
-  scaffolding/

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Update your database.php file
=====================================

Open your application/config/database.php file and add these new items::


    $db['default']['cache_on'] = FALSE;
    $db['default']['cachedir'] = '';

Step 3: Update your config.php file
===================================

Open your application/config/config.php file and ADD these new items::


    /*
    |--------------------------------------------------------------------------
    | Class Extension Prefix
    |--------------------------------------------------------------------------
    |
    | This item allows you to set the filename/classname prefix when extending
    | native libraries.  For more information please see the user guide:
    |
    | https://codeigniter.com/userguide3/general/core_classes.html
    | https://codeigniter.com/userguide3/general/creating_libraries.html
    |
    */
    $config['subclass_prefix'] = 'MY_';

    /*
    |--------------------------------------------------------------------------
    | Rewrite PHP Short Tags
    |--------------------------------------------------------------------------
    |
    | If your PHP installation does not have short tag support enabled CI
    | can rewrite the tags on-the-fly, enabling you to utilize that syntax
    | in your view files.  Options are TRUE or FALSE (boolean)
    |
    */
    $config['rewrite_short_tags'] = FALSE;

In that same file REMOVE this item::


    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Error Logging
    |--------------------------------------------------------------------------
    |
    | If you would like errors or debug messages logged set this variable to
    | TRUE (boolean).  Note: You must set the file permissions on the "logs" folder
    | such that it is writable.
    |
    */
    $config['log_errors'] = FALSE;

Error logging is now disabled simply by setting the threshold to zero.

Step 4: Update your main index.php file
=======================================

If you are running a stock index.php file simply replace your version
with the new one.

If your index.php file has internal modifications, please add your
modifications to the new file and use it.

Step 5: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
