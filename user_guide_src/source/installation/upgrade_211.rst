#############################
Upgrading from 2.1.0 to 2.1.1
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your "system" folder.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Replace config/mimes.php
================================

This config file has been updated to contain more user mime-types, please copy
it to _application/config/mimes.php*.

Step 3: Update your IP address tables
=====================================

This upgrade adds support for IPv6 IP addresses. In order to store them, you need
to enlarge your ip_address columns to 45 characters. For example, CodeIgniter's
session table will need to change

::

	ALTER TABLE ci_sessions CHANGE ip_address ip_address varchar(45) default '0' NOT NULL