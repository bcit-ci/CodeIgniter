#########################
Upgrading from 1.2 to 1.3
#########################

.. note:: The instructions on this page assume you are running version
	1.2. If you have not upgraded to that version please do so first.

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace the following directories in your "system" folder with the new
versions:

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

-  application/**models**/ (new for 1.3)
-  codeigniter (new for 1.3)
-  drivers
-  helpers
-  init
-  language
-  libraries
-  plugins
-  scaffolding

Step 2: Update your error files
===============================

Version 1.3 contains two new error templates located in
application/errors, and for naming consistency the other error templates
have been renamed.

If you **have not** customized any of the error templates simply replace
this folder:

-  application/errors/

If you **have** customized your error templates, rename them as follows:

-  404.php = error_404.php
-  error.php = error_general.php
-  error_db.php (new)
-  error_php.php (new)

Step 3: Update your index.php file
==================================

Please open your main index.php file (located at your root). At the very
bottom of the file, change this::

	require_once BASEPATH.'libraries/Front_controller'.EXT;

To this::

	require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;

Step 4: Update your config.php file
===================================

Open your application/config/config.php file and add these new items::


    /*
    |------------------------------------------------
    | URL suffix
    |------------------------------------------------
    |
    | This option allows you to add a suffix to all URLs.
    | For example, if a URL is this:
    |
    | example.com/index.php/products/view/shoes
    |
    | You can optionally add a suffix, like ".html",
    | making the page appear to be of a certain type:
    |
    | example.com/index.php/products/view/shoes.html
    |
    */
    $config['url_suffix'] = "";


    /*
    |------------------------------------------------
    | Enable Query Strings
    |------------------------------------------------
    |
    | By default CodeIgniter uses search-engine and
    | human-friendly segment based URLs:
    |
    | example.com/who/what/where/
    |
    | You can optionally enable standard query string
    | based URLs:
    |
    | example.com?who=me&what=something&where=here
    |
    | Options are: TRUE or FALSE (boolean)
    |
    | The two other items let you set the query string "words"
    | that will invoke your controllers and functions:
    | example.com/index.php?c=controller&m=function
    |
    */
    $config['enable_query_strings'] = FALSE;
    $config['controller_trigger'] = 'c';
    $config['function_trigger'] = 'm';

Step 5: Update your database.php file
=====================================

Open your application/config/database.php file and add these new items::


    $db['default']['dbprefix'] = "";
    $db['default']['active_r'] = TRUE;

Step 6: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
