#############################
Upgrading from 3.0.0 to 3.0.1
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Update your CLI error templates
=======================================

Replace all files under your *application/views/errors/cli/* directory.
