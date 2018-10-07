#############################
Upgrading from 3.1.2 to 3.1.3
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Remove usage of nice_date() helper (deprecation)
========================================================

The :doc:`Date Helper <../helpers/date_helper>` function ``nice_date()`` is
no longer useful since the introduction of PHP's `DateTime classes
<https://secure.php.net/datetime>`_

You can replace it with the following:
::

	DateTime::createFromFormat($input_format, $input_date)->format($desired_output_format);

Thus, ``nice_date()`` is now deprecated and scheduled for removal in
CodeIgniter 3.2+.

.. note:: The function is still available, but you're strongly encouraged
	to remove its usage sooner rather than later.

Step 3: Remove usage of $config['standardize_newlines']
=======================================================

The :doc:`Input Library <../libraries/input>` would optionally replace
occurrences of `\r\n`, `\r`, `\n` in input data with whatever the ``PHP_EOL``
value is on your system - if you've set ``$config['standardize_newlines']``
to ``TRUE`` in your *application/config/config.php*.

This functionality is now deprecated and scheduled for removal in
CodeIgniter 3.2.+.

.. note:: The functionality is still available, but you're strongly
	encouraged to remove its usage sooner rather than later.
