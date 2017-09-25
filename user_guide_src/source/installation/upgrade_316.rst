#############################
Upgrading from 3.1.5 to 3.1.6
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Remove usage of the APC Cache driver (deprecation)
==========================================================

The :doc:`Cache Library <../libraries/caching>` APC driver is now
deprecated, as the APC extension is effectively dead, as explained in its
`PHP Manual page <https://secure.php.net/manual/en/intro.apc.php>`_.

If your application happens to be using it, you can switch to another
cache driver, as APC support will be removed in a future CodeIgniter
version.

.. note:: The driver is still available, but you're strongly encouraged
	to remove its usage sooner rather than later.
