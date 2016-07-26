#############################
Upgrading from 3.0.6 to 3.1.0
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Check your PHP version
==============================

We recommend always running versions that are `currently supported
<https://secure.php.net/supported-versions.php>`_, which right now is at least PHP 5.6.

PHP 5.2.x versions are now officially not supported by CodeIgniter, and while 5.3.7+
may be at least runnable, we strongly discourage you from using any PHP versions below
the ones listed on the `PHP.net Supported Versions <https://secure.php.net/supported-versions.php>`_
page.

Step 3: If you're using the 'odbc' database driver, check for usage of Query Builder
====================================================================================

:doc:`Query Builder <../database/query_builder>` functionality and ``escape()`` can
no longer be used with the 'odbc' database driver.

This is because, due to its nature, the `ODBC extension for PHP <https://secure.php.net/odbc>`_
does not provide a function that allows to safely escape user-supplied strings for usage
inside an SQL query (which our :doc:`Query Builder <../database/query_builder>` relies on).

Thus, user inputs MUST be bound, as shown in :doc:`Running Queries <../database/queries>`,
under the "Query Bindings" section.