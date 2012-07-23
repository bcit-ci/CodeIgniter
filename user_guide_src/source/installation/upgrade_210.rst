#############################
Upgrading from 2.0.3 to 2.1.0
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your "system" folder.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Replace config/user_agents.php
======================================

This config file has been updated to contain more user agent types,
please copy it to _application/config/user_agents.php*.

Step 3: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.