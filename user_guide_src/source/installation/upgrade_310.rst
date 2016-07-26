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

Step 2: If you're using the 'odbc' database driver, check for usage of Query Builder
====================================================================================

:doc:`Query Builder <../database/query_builder>` functionality and ``escape()`` can
no longer be used with the 'odbc' database driver.

This is because, due to its nature, the `ODBC extension for PHP <https://secure.php.net/odbc>`_
does not provide a function that allows to safely escape user-supplied strings for usage
inside an SQL query (which our :doc:`Query Builder <../database/query_builder>` relies on).

Thus, user inputs MUST be bound, as shown in :doc:`Running Queries <../database/queries>`,
under the "Query Bindings" section.