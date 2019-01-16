##############################
Upgrading from 3.1.9 to 3.1.10
##############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Check for calls to is_countable()
==========================================


PHP 7.3 introduces a native `is_countable() <https://secure.php.net/is_countable>`_
function, which creates a name collision with the ``is_countable()`` function
we've had in our :doc:`Inflector Helpers <../helpers/inflector_helper>`.

If you've been using the helper function in question, you should now rename
the calls to it to :php:func:`word_is_countable()`.
