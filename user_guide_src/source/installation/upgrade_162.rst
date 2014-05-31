#############################
Upgrading from 1.6.1 to 1.6.2
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

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Encryption Key
======================

If you are using sessions, open up application/config/config.php and
verify you've set an encryption key.

Step 3: Constants File
======================

Copy /application/config/constants.php to your installation, and modify
if necessary.

Step 4: Mimes File
==================

Replace /application/config/mimes.php with the dowloaded version. If
you've added custom mime types, you'll need to re-add them.

Step 5: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
