#############################
Upgrading from 1.5.4 to 1.6.0
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace these files and directories in your "system" folder with the new
versions:

-  system/codeigniter
-  system/database
-  system/helpers
-  system/libraries
-  system/plugins
-  system/language

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Add time_to_update to your config.php
===============================================

Add the following to application/config/config.php with the other
session configuration options

::

	$config['sess_time_to_update']         = 300;


Step 3: Add $autoload['model']
==============================

Add the following to application/config/autoload.php

::

	/*
	| -------------------------------------------------------------------
	| Auto-load Model files
	| -------------------------------------------------------------------
	| Prototype:
	|
	| $autoload['model'] = array('my_model');
	|
	*/

	$autoload['model'] = array();


Step 4: Add to your database.php
================================

Make the following changes to your application/config/database.php file:

Add the following variable above the database configuration options,
with $active_group

::

	$active_record = TRUE;


Remove the following from your database configuration options

::

	$db['default']['active_r'] = TRUE;


Add the following to your database configuration options

::

	$db['default']['char_set'] = "utf8";
	$db['default']['dbcollat'] = "utf8_general_ci";


Step 5: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
