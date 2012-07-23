#############################
Upgrading from 1.6.3 to 1.7.0
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

Step 2: Update your Session Table
=================================

If you are using the Session class in your application, AND if you are
storing session data to a database, you must add a new column named
user_data to your session table. Here is an example of what this column
might look like for MySQL::

	user_data text NOT NULL

To add this column you will run a query similar to this::

	ALTER TABLE `ci_sessions` ADD `user_data` text NOT NULL

You'll find more information regarding the new Session functionality in
the :doc:`Session class <../libraries/sessions>` page.

Step 3: Update your Validation Syntax
=====================================

This is an **optional**, but recommended step, for people currently
using the Validation class. CI 1.7 introduces a new :doc:`Form Validation
class <../libraries/form_validation>`, which deprecates the old
Validation library. We have left the old one in place so that existing
applications that use it will not break, but you are encouraged to
migrate to the new version as soon as possible. Please read the user
guide carefully as the new library works a little differently, and has
several new features.

Step 4: Update your user guide
==============================

Please replace your local copy of the user guide with the new version,
including the image files.
