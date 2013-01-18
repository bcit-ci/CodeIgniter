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

***************************************************
Step 4: Move your Log class overrides or extensions
***************************************************

The Log Class is considered as a "core" class and is now located in the
**system/core/** directory. Therefore, in order for your Log class overrides
or extensions to work, you need to move them to **application/core/**::

	application/libraries/Log.php -> application/core/Log.php
	application/libraries/MY_Log.php -> application/core/MY_log.php

*********************************************************
Step 5: Convert your Session usage from library to driver
*********************************************************

When you load (or autoload) the Session library, you must now load it as a driver instead of a library. This means
calling ``$this->load->driver('session')`` instead of ``$this->load->library('session')`` and/or listing 'session'
in ``$autoload['drivers']`` instead of ``$autoload['libraries']``.

With the change from a single Session Library to the new Session Driver, two new config items have been added:

   -  ``$config['sess_driver']`` selects which driver to initially load. Options are:
       -  'cookie' (the default) for classic CodeIgniter cookie-based sessions
       -  'native' for native PHP Session support
       -  the name of a custom driver you have provided (see :doc:`Session Driver <../libraries/sessions>` for more info)
   -  ``$config['sess_valid_drivers']`` provides an array of additional custom drivers to make available for loading

As the new Session Driver library loads the classic Cookie driver by default and always makes 'cookie' and 'native'
available as valid drivers, neither of these configuration items are required. However, it is recommended that you
add them for clarity and ease of configuration in the future.

If you have written a Session extension, you must move it into a 'Session' sub-directory of 'libraries', following the
standard for Drivers. Also beware that some functions which are not part of the external Session API have moved into
the drivers, so your extension may have to be broken down into separate library and driver class extensions.

***************************************
Step 6: Update your config/database.php
***************************************

Due to 3.0.0's renaming of Active Record to Query Builder, inside your `config/database.php`, you will
need to rename the `$active_record` variable to `$query_builder`
::

	$active_group = 'default';
	// $active_record = TRUE;
	$query_builder = TRUE;

*******************************
Step 7: Move your errors folder
*******************************

In version 3.0.0, the errors folder has been moved from _application/errors* to _application/views/errors*.

*******************************************************
Step 8: Update your config/routes.php containing (:any)
*******************************************************

Historically, CodeIgniter has always provided the **:any** wildcard in routing,
with the intention of providing a way to match any character **within** an URI segment.

However, the **:any** wildcard is actually just an alias for a regular expression
and used to be executed in that manner as **.+**. This is considered a bug, as it
also matches the / (forward slash) character, which is the URI segment delimiter
and that was never the intention. In CodeIgniter 3, the **:any** wildcard will now
represent **[^/]+**, so that it will not match a forward slash.

There are certainly many developers that have utilized this bug as an actual feature.
If you're one of them and want to match a forward slash, please use the **.+**
regular expression::

	(.+)	// matches ANYTHING
	(:any)	// matches any character, except for '/'


****************************************************************************
Step 9: Check the calls to Array Helper's element() and elements() functions
****************************************************************************

The default return value of these functions, when the required elements
don't exist, has been changed from FALSE to NULL.

*************************************************************
Step 10: Update usage of Database Forge's drop_table() method
*************************************************************

Up until now, ``drop_table()`` added an IF EXISTS clause by default or it didn't work
at all with some drivers. In CodeIgniter 3.0, the IF EXISTS condition is no longer added
by default and has an optional second parameter that allows that instead and is set to
FALSE by default.

If your application relies on IF EXISTS, you'll have to change its usage.

::

	// Now produces just DROP TABLE `table_name`
	$this->dbforge->drop_table('table_name');

	// Produces DROP TABLE IF EXISTS `table_name`
	$this->dbforge->drop_table('table_name', TRUE);

.. note:: The given example users MySQL-specific syntax, but it should work across
	all drivers with the exception of ODBC.

***********************************************************
Step 11: Change usage of Email library with multiple emails
***********************************************************

The :doc:`Email Library <../libraries/email>` will automatically clear the
set parameters after successfully sending emails. To override this behaviour,
pass FALSE as the first parameter in the ``send()`` method:

::

	if ($this->email->send(FALSE))
 	{
 		// Parameters won't be cleared
 	}

***************************************************
Step 12: Update your Form_validation language lines
***************************************************

Two improvements have been made to the :doc:`Form Validation Library
<../libraries/form_validation>`'s :doc:`language <../libraries/language>`
files and error messages format:

 - :doc:`Language Library <../libraries/language>` line keys now must be
   prefixed with **form_validation_** in order to avoid collisions::

	// Old
	$lang['rule'] = ...

	// New
	$lang['form_validation_rule'] = ...

 - The error messages format has been changed to use named parameters, to
   allow more flexibility than what `sprintf()` offers::

	// Old
	'The %s field does not match the %s field.'

	// New
	'The {field} field does not match the {param} field.'

.. note:: The old formatting still works, but the non-prefixed line keys
	are DEPRECATED and scheduled for removal in CodeIgniter 3.1+.
	Therefore you're encouraged to update its usage sooner rather than
	later.

****************************************************************
Step 13: Remove usage of (previously) deprecated functionalities
****************************************************************

In addition to the ``$autoload['core']`` configuration setting, there's a
number of other functionalities that have been removed in CodeIgniter 3.0.0:

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

String helper repeater()
========================

:doc:`String Helper <../helpers/string_helper>` function :php:func:`repeater()` is now just an alias for
PHP's native ``str_repeat()`` function. It is deprecated and scheduled for removal in CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove it's usage sooner
	rather than later.

String helper trim_slashes()
============================

:doc:`String Helper <../helpers/string_helper>` function :php:func:`trim_slashes()` is now just an alias
for PHP's native ``trim()`` function (with a slash passed as its second argument). It is deprecated and
scheduled for removal in CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove it's usage sooner
	rather than later.

Email helper functions
======================

:doc:`Email Helper <../helpers/email_helper>` only has two functions

 - :php:func:`valid_email()`
 - :php:func:`send_email()`

Both of them are now aliases for PHP's native ``filter_var()`` and ``mail()`` functions, respectively.
Therefore the :doc:`Email Helper <../helpers/email_helper>` altogether is being deprecated and
is scheduled for removal in CodeIgniter 3.1+.

.. note:: These functions are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

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

String helper random_string() types 'unique' and 'encrypt'
==========================================================

When using the :doc:`String Helper <../helpers/string_helper>` function :php:func:`random_string()`,
you should no longer pass the **unique** and **encrypt** randomization types. They are only
aliases for **md5** and **sha1** respectively and are now deprecated and scheduled for removal
in CodeIgniter 3.1+.

.. note:: These options are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

URL helper url_title() separators 'dash' and 'underscore'
=========================================================

When using the :doc:`URL Helper <../helpers/url_helper>` function :php:func:`url_title()`, you
should no longer pass **dash** or **underscore** as the word separator. This function will
now accept any character and you should just pass the chosen character directly, so you
should write '-' instead of 'dash' and '_' instead of 'underscore'.

**dash** and **underscore** now act as aliases and are deprecated and scheduled for removal
in CodeIgniter 3.1+.

.. note:: These options are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

Database Forge method add_column() with an AFTER clause
=======================================================

If you have used the **third parameter** for :doc:`Database Forge <../database/forge>` method
``add_column()`` to add a field for an AFTER clause, then you should change its usage.

That third parameter has been deprecated and scheduled for removal in CodeIgniter 3.1+.

You should now put AFTER clause field names in the field definition array instead::

	// Old usage:
	$field = array(
		'new_field' => array('type' => 'TEXT')
	);

	$this->dbforge->add_column('table_name', $field, 'another_field');

	// New usage:
	$field = array(
		'new_field' => array('type' => 'TEXT', 'after' => 'another_field')
	);

	$this->dbforge->add_column('table_name', $field);

.. note:: The parameter is still available, but you're strongly encouraged to remove its usage
	sooner rather than later.

.. note:: This is for MySQL and CUBRID databases only! Other drivers don't support this
	clause and will silently ignore it.