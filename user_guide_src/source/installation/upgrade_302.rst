#############################
Upgrading from 3.0.1 to 3.0.2
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Update your application/config/constants.php file
=========================================================

The *application/config/constants.php* file has been updated to check
if constants aren't already defined before doing that, making it easier
to add an environment-specific configuration.

.. note:: If you've made modifications to this file, please make a
	backup first and cross-check the differences first.