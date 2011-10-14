#############################
Upgrading from 2.0.1 to 2.0.2
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your "system" folder and replace
your index.php file. If any modifications were made to your index.php
they will need to be made fresh in this new one.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Remove loading calls for the Security Library
=====================================================

Security has been moved to the core and is now always loaded
automatically. Make sure you remove any loading calls as they will
result in PHP errors.

Step 3: Move MY_Security
=========================

If you are overriding or extending the Security library, you will need
to move it to application/core.

csrf_token_name and csrf_hash have changed to protected class
properties. Please use security->get_csrf_hash() and
security->get_csrf_token_name() to access those values.
