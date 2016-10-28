#############################
Upgrading from 3.1.1 to 3.1.2
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Update your "ci_sessions" database table
================================================

If you're using the :doc:`Session Library </libraries/sessions>` with the
'database' driver, you may have to ``ALTER`` your sessions table for your
sessions to continue to work.

.. note:: The table in question is not necessarily named "ci_sessions".
	It is what you've set as your ``$config['sess_save_path']``.

This will only affect you if you've changed your ``session.hash_function``
*php.ini* setting to something like 'sha512'. Or if you've been running
an older CodeIgniter version on PHP 7.1+.

It is recommended that you do this anyway, just to avoid potential issues
in the future if you do change your configuration.

Just execute the one of the following SQL queries, depending on your
database::

	// MySQL:
	ALTER TABLE ci_sessions CHANGE id id varchar(128) NOT NULL;

	// PostgreSQL
	ALTER TABLE ci_sessions ALTER COLUMN id SET DATA TYPE varchar(128);
