#############################
Upgrading from 2.1.2 to 3.0.0
#############################

.. note:: These upgrade notes are for a version that is yet to be released.

Before performing an update you should take your site offline by replacing the index.php file with a static one.

*************************************
Step 1: Update your CodeIgniter files
*************************************

Replace all files and directories in your "system" folder and replace
your index.php file. If any modifications were made to your index.php
they will need to be made fresh in this new one.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

********************************
Step 2: Replace config/mimes.php
********************************

This config file has been updated to contain more user mime-types, please copy
it to _application/config/mimes.php*.

**************************************************************
Step 3: Remove $autoload['core'] from your config/autoload.php
**************************************************************

Use of the ``$autoload['core']`` config array has been deprecated as of CodeIgniter 1.4.1 and is now removed.
Move any entries that you might have listed there to ``$autoload['libraries']`` instead.

**************************************************************
Step 4: Add new session driver items to your config/config.php
**************************************************************

With the change from a single Session Library to the new Session Driver, two new config items have been added:

   -  ``$config['sess_driver']`` selects which driver to initially load. Options are:
       -  'cookie' (the default) for classic CodeIgniter cookie-based sessions
       -  'native' for native PHP Session support
       -  the name of a custom driver you have provided (see :doc:`Session Driver <../libraries/sessions>` for more info)
   -  ``$config['sess_valid_drivers']`` provides an array of additional custom drivers to make available for loading

As the new Session Driver library loads the classic Cookie driver by default and always makes 'cookie' and 'native'
available as valid drivers, neither of these configuration items are required. However, it is recommended that you
add them for clarity and ease of configuration in the future.

***************************************
Step 5: Update your config/database.php
***************************************

Due to 3.0.0's renaming of Active Record to Query Builder, inside your `config/database.php`, you will
need to rename the `$active_record` variable to `$query_builder`.

    $active_group = 'default';
    // $active_record = TRUE;
    $query_builder = TRUE;

*******************************
Step 6: Move your errors folder
*******************************

In version 3.0.0, the errors folder has been moved from _application/errors* to _application/views/errors*.

****************************************************************************
Step 7: Check the calls to Array Helper's element() and elements() functions
****************************************************************************

The default return value of these functions, when the required elements
don't exist, has been changed from FALSE to NULL.

***************************************************************
Step 8: Remove usage of (previously) deprecated functionalities
***************************************************************

In addition to the ``$autoload['core']`` configuration setting, there's a number of other functionalities
that have been removed in CodeIgniter 3.0.0:

The SHA1 library
================

The previously deprecated SHA1 library has been removed, alter your code to use PHP's native
``sha1()`` function to generate a SHA1 hash.

Additionally, the ``sha1()`` method in the :doc:`Encryption Library <../libraries/encryption>` has been removed.

The EXT constant
================

Usage of the ``EXT`` constant has been deprecated since dropping support for PHP 4. There's no
longer a need to maintain different filename extensions and in this new CodeIgniter version,
the ``EXT`` constant has been removed. Use just '.php' instead.

Smiley helper js_insert_smiley()
================================

:doc:`Smiley Helper <../helpers/smiley_helper>` function ``js_insert_smiley()`` has been deprecated
since CodeIgniter 1.7.2 and is now removed. You'll need to switch to ``smiley_js()`` instead.

Security helper do_hash()
=========================

:doc:`Security Helper <../helpers/security_helper>` function ``do_hash()`` is now just an alias for
PHP's native ``hash()`` function. It is deprecated and scheduled for removal in CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove it's usage sooner
	rather than later.

File helper read_file()
=======================

:doc:`File Helper <../helpers/file_helper>` function ``read_file()`` is now just an alias for
PHP's native ``file_get_contents()`` function. It is deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove it's usage sooner
	rather than later.

Date helper standard_date()
===========================

:doc:`Date Helper <../helpers/date_helper>` function ``standard_date()`` is being deprecated due
to the availability of native PHP `constants <http://www.php.net/manual/en/class.datetime.php#datetime.constants.types>`_,
which when combined with ``date()`` provide the same functionality. Furthermore, they have the
exact same names as the ones supported by ``standard_date()``. Here are examples of how to replace
it's usage:

::

	// Old way
	standard_date(); // defaults to standard_date('DATE_RFC822', now());

	// Replacement
	date(DATE_RFC822, now());

	// Old way
	standard_date('DATE_ATOM', $time);

	// Replacement
	date(DATE_ATOM, $time);

.. note:: This function is still available, but you're strongly encouraged to remove its' usage sooner
	rather than later as it is scheduled for removal in CodeIgniter 3.1+.

Pagination library 'anchor_class' setting
=========================================

The :doc:`Pagination Library <../libraries/pagination>` now supports adding pretty much any HTML
attribute to your anchors via the 'attributes' configuration setting. This includes passing the
'class' attribute and using the separate 'anchor_class' setting no longer makes sense.
As a result of that, the 'anchor_class' setting is now deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: This setting is still available, but you're strongly encouraged to remove its' usage sooner
	rather than later.

Email library
=============

The :doc:`Email library <../libraries/email>` will automatically clear the set parameters after successfully sending
emails. To override this behaviour, pass FALSE as the first parameter in the ``send()`` function:

::

	if ($this->email->send(FALSE))
 	{
 		// Parameters won't be cleared
 	}
