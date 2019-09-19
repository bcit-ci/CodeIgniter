###############################
Upgrading from 3.1.10 to 3.1.11
###############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Replace config/mimes.php
================================

This config file has received some updates. Please copy it to
*application/config/mimes.php*.
