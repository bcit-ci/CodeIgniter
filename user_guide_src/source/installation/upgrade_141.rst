#############################
Upgrading from 1.4.0 to 1.4.1
#############################

.. note:: The instructions on this page assume you are running version
	1.4.0. If you have not upgraded to that version please do so first.

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace the following directories in your "system" folder with the new
versions:

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

-  codeigniter
-  drivers
-  helpers
-  libraries

Step 2: Update your config.php file
===================================

Open your application/config/config.php file and add this new item::



    /*
    |--------------------------------------------------------------------------
    | Output Compression
    |--------------------------------------------------------------------------
    |
    | Enables Gzip output compression for faster page loads.  When enabled,
    | the output class will test whether your server supports Gzip.
    | Even if it does, however, not all browsers support compression
    | so enable only if you are reasonably sure your visitors can handle it.
    |
    | VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
    | means you are prematurely outputting something to your browser. It could
    | even be a line of whitespace at the end of one of your scripts.  For
    | compression to work, nothing can be sent before the output buffer is called
    | by the output class.  Do not "echo" any values with compression enabled.
    |
    */
    $config['compress_output'] = FALSE;

Step 3: Rename an Autoload Item
===============================

Open the following file: application/config/autoload.php

Find this array item::

	$autoload['core'] = array();

And rename it to this::

	$autoload['libraries'] = array();

This change was made to improve clarity since some users were not sure
that their own libraries could be auto-loaded.

Step 4: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
