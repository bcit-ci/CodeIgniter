#############################
Upgrading from 3.1.6 to 3.1.7
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Remove usage of CAPTCHA helper extra parameters (deprecation)
=====================================================================

The :doc:`CAPTCHA Helper <../helpers/captcha_helper>` function
:php:func:`create_captcha()` allows passing of its ``img_path``, ``img_url``
and ``font_path`` options as the 2nd, 3rd and 4th parameters respectively.

This kind of usage is now deprecated and you should just pass the options
in question as part of the first parameter array.

.. note:: The functionality in question is still available, but you're
	strongly encouraged to remove its usage sooner rather than later.
