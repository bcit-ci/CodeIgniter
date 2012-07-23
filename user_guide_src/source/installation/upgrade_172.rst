#############################
Upgrading from 1.7.1 to 1.7.2
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
-  system/language
-  system/libraries
-  index.php

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Remove header() from 404 error template
===============================================

If you are using header() in your 404 error template, such as the case
with the default error_404.php template shown below, remove that line
of code.

::

	<?php header("HTTP/1.1 404 Not Found"); ?>

404 status headers are now properly handled in the show_404() method
itself.

Step 3: Confirm your system_path
=================================

In your updated index.php file, confirm that the $system_path variable
is set to your application's system folder.

Step 4: Update your user guide
==============================

Please replace your local copy of the user guide with the new version,
including the image files.
