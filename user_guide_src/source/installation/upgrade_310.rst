#############################
Upgrading from 3.0.x to 3.1.x
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Change database connection handling
===========================================

"Loading" a database, whether by using the *config/autoload.php* settings
or manually via calling ``$this->load->database()`` or the less-known
``DB()`` function, will now throw a ``RuntimeException`` in case of a
failure.

In addition, being unable to set the configured character set is now also
considered a connection failure.

.. note:: This has been the case for most database drivers in the in the
	past as well (i.e. all but the 'mysql', 'mysqli' and 'postgre'
	drivers).

What this means is that if you're unable to connect to a database, or
have an erroneous character set configured, CodeIgniter will no longer
fail silently, but will throw an exception instead.

You may choose to explicitly catch it (and for that purpose you can't use
*config/autoload.php* to load the :doc:`Database Class <../database/index>`)
::

	try
	{
		$this->load->database();
	}
	catch (RuntimeException $e)
	{
		// Handle the failure
	}

Or you may leave it to CodeIgniter's default exception handler, which would
log the error message and display an error screen if you're running in
development mode.

Remove db_set_charset() calls
-----------------------------

With the above-mentioned changes, the purpose of the ``db_set_charset()``
method would now only be to change the connection character set at runtime.
That doesn't make sense and that's the reason why most database drivers
don't support it at all.
Thus, ``db_set_charset()`` is no longer necessary and is removed.
