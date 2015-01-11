#############################
Upgrading from 1.7.2 to 2.0.0
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

*******************
Update Instructions
*******************

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your "system" folder **except**
your application folder.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Adjust get_dir_file_info() where necessary
=====================================================

Version 2.0.0 brings a non-backwards compatible change to
get_dir_file_info() in the :doc:`File
Helper <../helpers/file_helper>`. Non-backwards compatible changes
are extremely rare in CodeIgniter, but this one we feel was warranted
due to how easy it was to create serious server performance issues. If
you *need* recursiveness where you are using this helper function,
change such instances, setting the second parameter, $top_level_only
to FALSE::

	get_dir_file_info('/path/to/directory', FALSE);

Step 3: Convert your Plugins to Helpers
=======================================

2.0.0 gets rid of the "Plugin" system as their functionality was
identical to Helpers, but non-extensible. You will need to rename your
plugin files from filename_pi.php to filename_helper.php, move them to
your helpers folder, and change all instances of::

	$this->load->plugin('foo');

to ::

	$this->load->helper('foo');


Step 4: Update stored encrypted data
====================================

.. note:: If your application does not use the Encrypt library, does
	not store Encrypted data permanently, or is on an environment that does
	not support Mcrypt, you may skip this step.

The Encrypt library has had a number of improvements, some for
encryption strength and some for performance, that has an unavoidable
consequence of making it no longer possible to decode encrypted data
produced by the original version of this library. To help with the
transition, a new method has been added, encode_from_legacy() that
will decode the data with the original algorithm and return a re-encoded
string using the improved methods. This will enable you to easily
replace stale encrypted data with fresh in your applications, either on
the fly or en masse.

Please read `how to use this
method <../libraries/encrypt.html#legacy>`_ in the Encrypt library
documentation.

Step 5: Remove loading calls for the compatibility helper.
==========================================================

The compatibility helper has been removed from the CodeIgniter core. All
methods in it should be natively available in supported PHP versions.

Step 6: Update Class extension
==============================

All core classes are now prefixed with CI\_. Update Models and
Controllers to extend CI_Model and CI_Controller, respectively.

Step 7: Update Parent Constructor calls
=======================================

All native CodeIgniter classes now use the PHP 5 \__construct()
convention. Please update extended libraries to call
parent::\__construct().

Step 8: Move any core extensions to application/core
====================================================

Any extensions to core classes (e.g. MY_Controller.php) in your
application/libraries folder must be moved to the new 
application/core folder.

Step 9: Update your user guide
==============================

Please replace your local copy of the user guide with the new version,
including the image files.


************
Update Notes
************

Please refer to the :ref:`2.0.0 Change Log <2.0.0-changelog>` for full
details, but here are some of the larger changes that are more likely to
impact your code:

- Scaffolding has been removed.
- The CAPTCHA plugin in now a :doc:`helper </helpers/captcha_helper>`.
- The JavaScript calendar plugin was removed.
- The *system/cache* and *system/logs* directories are now in the application
  directory.
- The Validation class has been removed.  Please see the
  :doc:`Form Validation library </libraries/form_validation>`
- "default" is now a reserved name.
- The xss_clean() function has moved to the :doc:`Security Class
  </libraries/security>`.
- do_xss_clean() now returns FALSE if the uploaded file fails XSS checks.
- The :doc:`Session Class </libraries/sessions>` requires now the use of an
  encryption key set in the config file.
- The following deprecated Active Record functions have been removed:
  ``orwhere``, ``orlike``, ``groupby``, ``orhaving``, ``orderby``,
  ``getwhere``.
- ``_drop_database()`` and ``_create_database()`` functions have been removed
  from the db utility drivers.
- The ``dohash()`` function of the :doc:`Security helper
  </helpers/security_helper>`
  has been renamed to ``do_hash()`` for naming consistency.

The config folder
=================

The following files have been changed:

- config.php
- database.php
- mimes.php
- routes.php
- user_agents.php

The following files have been added:

- foreign_chars.php
- profiler.php