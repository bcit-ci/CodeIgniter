###################################
Upgrading From Beta 1.0 to Beta 1.1
###################################

To upgrade to Beta 1.1 please perform the following steps:

Step 1: Replace your index file
===============================

Replace your main index.php file with the new index.php file. Note: If
you have renamed your "system" folder you will need to edit this info in
the new file.

Step 2: Relocate your config folder
===================================

This version of CodeIgniter now permits multiple sets of "applications"
to all share a common set of backend files. In order to enable each
application to have its own configuration values, the config directory
must now reside inside of your application folder, so please move it
there.

Step 3: Replace directories
===========================

Replace the following directories with the new versions:

-  drivers
-  helpers
-  init
-  libraries
-  scaffolding

Step 4: Add the calendar language file
======================================

There is a new language file corresponding to the new calendaring class
which must be added to your language folder. Add the following item to
your version: language/english/calendar_lang.php

Step 5: Edit your config file
=============================

The original application/config/config.php file has a typo in it Open
the file and look for the items related to cookies::

	$conf['cookie_prefix']	= "";
	$conf['cookie_domain']	= "";
	$conf['cookie_path']	= "/";

Change the array name from $conf to $config, like this::

	$config['cookie_prefix']	= "";
	$config['cookie_domain']	= "";
	$config['cookie_path']	= "/";

Lastly, add the following new item to the config file (and edit the
option if needed)::

	
	/*
	|------------------------------------------------
	| URI PROTOCOL
	|------------------------------------------------
	|
	| This item determines which server global 
	| should be used to retrieve the URI string. The 
	| default setting of "auto" works for most servers.
	| If your links do not seem to work, try one of 
	| the other delicious flavors:
	| 
	| 'auto'	 Default - auto detects
	| 'path_info'	 Uses the PATH_INFO 
	| 'query_string'	Uses the QUERY_STRING
	*/

	$config['uri_protocol']	= "auto";

