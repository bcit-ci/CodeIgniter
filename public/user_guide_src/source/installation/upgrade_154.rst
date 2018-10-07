#############################
Upgrading from 1.5.3 to 1.5.4
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace these files and directories in your "system" folder with the new
versions:

-  application/config/mimes.php
-  system/codeigniter
-  system/database
-  system/helpers
-  system/libraries
-  system/plugins

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Add charset to your config.php
======================================

Add the following to application/config/config.php

::

	/*
	|--------------------------------------------------------------------------
	| Default Character Set
	|--------------------------------------------------------------------------
	|
	| This determines which character set is used by default in various methods
	| that require a character set to be provided.
	|
	*/
	$config['charset'] = "UTF-8";

Step 3: Autoloading language files
==================================

If you want to autoload any language files, add this line to
application/config/autoload.php

::

	$autoload['language'] = array();

Step 4: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
